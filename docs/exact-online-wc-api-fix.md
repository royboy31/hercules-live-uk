# Exact Online WooCommerce API Fix

**Date:** 2026-03-06
**Issue:** Exact Online (iwebdevelopment) integration unable to fetch WooCommerce data
**Error:** Cloudflare error code 1101 (Worker threw exception)

---

## Root Cause Analysis

### Primary Blocker: Edge Router TypeError (Error 1101)

The Cloudflare Edge Router worker (`hercules-edge-router-uk-production`) was crashing with:

```
TypeError: Request with a GET or HEAD method cannot have a body.
    at Object.fetch (index.js:223:26)
```

**Why it happened:**
- Exact Online's connector (`CodexConnectors/1.0 - WooCommerce`) sends GET requests with `Content-Length: 0` and `Content-Type: application/json;charset=utf-8` headers
- The edge router blindly passed `request.body` to the proxy Request constructor
- Cloudflare Workers runtime rejects GET/HEAD requests with a body, even if it's empty/null
- This caused **6,753 exceptions** over ~2 weeks (~480/day, 2 every 6 minutes)

**Exact Online request pattern observed via `wrangler tail`:**
```
URL: /wp-json/wc/v3/orders?modified_after=...&oauth_consumer_key=ck_...&oauth_nonce=...&oauth_signature=...&oauth_signature_method=HMAC-SHA256&oauth_timestamp=...&page=1&per_page=10
Method: GET
User-Agent: CodexConnectors/1.0 - WooCommerce
Content-Length: 0
Content-Type: application/json;charset=utf-8
Origin IP: 2600:1900:0:3f01::d00 (Google Cloud, NL)
Frequency: Every ~6 minutes, 2 requests each time
```

### Secondary Blocker: Legacy WC REST API Removed

WooCommerce 10.4.2 (installed on UK production) removed the legacy `/wc-api/v3/` endpoint in WooCommerce 9.0. A stub in core always returns:

```json
{"errors":{"code":"woocommerce_api_disabled","message":"The WooCommerce API is disabled on this site"}}
```

This stub ignores the `woocommerce_api_enabled` database option — it only checks if the `woocommerce-legacy-rest-api` extension plugin is installed.

---

## Fixes Applied

### 1. Edge Router Fix (workers/edge-router/src/index.ts)

**Change:** Only pass `request.body` for non-GET/HEAD methods.

```typescript
// Before (line 314-319):
const proxyRequest = new Request(targetUrl.toString(), {
  method: request.method,
  headers,
  body: request.body,
  redirect: 'manual',
});

// After:
const hasBody = request.method !== 'GET' && request.method !== 'HEAD';
const proxyRequest = new Request(targetUrl.toString(), {
  method: request.method,
  headers,
  body: hasBody ? request.body : undefined,
  redirect: 'manual',
});
```

**Deployed:** 2026-03-05 ~18:35 UTC
**Version ID:** 265004de-27bd-40c4-8e74-6399258752b2

### 2. WooCommerce Legacy REST API Plugin

**Installed:** `woocommerce-legacy-rest-api` v1.0.5 on production WordPress

```bash
ssh combel-uk
cd /var/www/vhosts/hercules-merchandise.co.uk/httpdocs
/opt/plesk/php/8.3/bin/php -d memory_limit=512M /usr/local/bin/wp plugin install woocommerce-legacy-rest-api --activate
```

This restores the `/wc-api/v3/` endpoint for integrations that still use the legacy API.

### 3. WooCommerce API Enabled Option

```sql
UPDATE wp_1202943_options SET option_value = 'yes' WHERE option_name = 'woocommerce_api_enabled';
```

Required by the legacy REST API plugin.

---

## Verification

### Edge Router Fix
- Simulated Exact Online's request pattern: `curl -H "Content-Length: 0" -H "Content-Type: application/json" https://hercules-merchandise.co.uk/wp-json/wc/v3/orders?per_page=1` returns HTTP 401 (auth required, not 1101)
- Cloudflare analytics show **zero exceptions** after deploy (previously ~2 every 6 minutes)

### Legacy REST API
- `curl https://hercules-merchandise.co.uk/wc-api/v3/` returns full route listing (was returning "disabled" error)

---

## Security Layers Investigated (Not Blocking)

| Layer | Status | Notes |
|-------|--------|-------|
| MalCare WAF | `fw.mode=3` (active) | 321KB rules, not blocking WC API calls |
| Wordfence WAF | `wafDisabled=true` | Fully disabled |
| .htaccess | WP Rocket caching only | No blocking rules |
| Cloudflare APO | Active | Cookie handling via edge router X-Edge-Cookies header |

---

## Related Files

- `workers/edge-router/src/index.ts` — Edge router (fix applied)
- `workers/edge-router/wrangler.toml` — Edge router config
- Production WordPress: `/var/www/vhosts/hercules-merchandise.co.uk/httpdocs/`
- Plugin: `wp-content/plugins/woocommerce-legacy-rest-api/`
