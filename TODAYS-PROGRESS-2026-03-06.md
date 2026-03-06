# Today's Progress - 2026-03-06

---

## 1. Exact Online / WooCommerce API Integration Fix

**Context:** The Exact Online accounting integration (iwebdevelopment WooCommerce connector) was unable to fetch WooCommerce data from the UK site, receiving Cloudflare error 1101.

### Investigation

1. **Tested all API endpoints** — modern REST API (`/wp-json/wc/v3/`), legacy API (`/wc-api/v3/`), and custom Hercules endpoints
2. **Checked security layers** — MalCare WAF (active, mode 3), Wordfence (disabled), .htaccess (clean), Cloudflare APO
3. **Queried Cloudflare analytics** — Found 6,753 `scriptThrewException` errors and 56 `exceededResources` errors on the edge router since Feb 20
4. **Captured live exception via `wrangler tail`** — Identified the exact error and Exact Online's request pattern

### Root Cause

The Edge Router worker crashed with `TypeError: Request with a GET or HEAD method cannot have a body` when Exact Online sent GET requests with `Content-Length: 0` and `Content-Type: application/json` headers. The edge router passed `request.body` through to the proxy request, which the Workers runtime rejects for GET/HEAD.

**Pattern:** 2 exceptions every ~6 minutes, 24/7 (~480/day)

### Fixes Applied

#### A. Edge Router — Body handling fix
- **File:** `workers/edge-router/src/index.ts`
- Only pass `request.body` for non-GET/HEAD methods (`body: hasBody ? request.body : undefined`)
- **Deployed** to production (version `265004de-27bd-40c4-8e74-6399258752b2`)
- **Result:** Zero exceptions after deploy, confirmed via Cloudflare analytics

#### B. WooCommerce Legacy REST API plugin installed
- WooCommerce 10.4.2 removed the `/wc-api/v3/` endpoint (since WC 9.0)
- Installed `woocommerce-legacy-rest-api` v1.0.5 on production WordPress
- Legacy API now returns full route listing instead of "disabled" error

#### C. Database option updated
- Set `woocommerce_api_enabled = yes` in `wp_1202943_options` (required by legacy plugin)

### Documentation
- Detailed fix documented in `docs/exact-online-wc-api-fix.md`

---

## 2. Edge Router Body Fix — Applied to DE and FR Sites

**Context:** The same GET/HEAD body bug existed on the DE and FR edge routers.

### Investigation

- DE production domain (`hercules-merchandise.de`) routes to worker `hercules-edge-router-live` (not `hercules-edge-router`)
- FR production domain (`hercules-merchandising.fr`) routes to worker `hercules-edge-router-fr-prod` (not `hercules-edge-router-fr`)
- Initial deploy only updated staging workers; production workers required separate deployment

### Fixes Applied

| Site | Worker | File | Version |
|------|--------|------|---------|
| DE staging | `hercules-edge-router` | `hercules-headless-live/workers/edge-router/src/index.ts` | `81f8640b` |
| DE production | `hercules-edge-router-live` | Same source | `4421dda9` |
| FR staging | `hercules-edge-router-fr` | `hercules-headless-fr/workers/edge-router/src/index.ts` | `a938572e` |
| FR production | `hercules-edge-router-fr-prod` | Same source | `b00edcf0` |

### Verification

All sites tested with `Content-Length: 0` + `Content-Type: application/json` (Exact Online pattern):

| Site | Before | After |
|------|--------|-------|
| UK `hercules-merchandise.co.uk` | 200 (already fixed) | 200 |
| DE `hercules-merchandise.de` | 1101 error | 200 |
| FR `hercules-merchandising.fr` | 1101 error | 200 |

### Note on Legacy WC API

- `/wc-api/v3/` returns 404 on DE and FR (legacy plugin not installed)
- Only UK has the `woocommerce-legacy-rest-api` plugin
- DE/FR may need it if Exact Online uses the legacy API there

---

## 3. Google Search Index — Spam URL Cleanup (UK)

**Context:** Old hacked/spam URLs from a previous WordPress compromise are indexed in Google, returning HTTP 200 (homepage) instead of 404/410. Google keeps them indexed because they appear valid.

### Spam Patterns Identified

- **Query params:** `?t=`, `?l=`, `?.shtml`
- **Path prefixes:** `/itemgoods/`, `/ctg/`, `/f/`, `/mbr/`, `/odr/`, `/evercompare/`, `/categoryindex/`

### Fixes Applied

#### A. Edge Router — 410 Gone for spam URLs
- **File:** `workers/edge-router/src/index.ts`
- Added spam path prefix check (returns 410 immediately, before any routing)
- Added spam query parameter check with safeguards for legitimate WooCommerce params (`wc-ajax`, `add-to-cart`, `removed_item`)
- Response includes `Cache-Control: public, max-age=86400` for edge caching
- **Deployed** to production worker `hercules-edge-router-uk-production` (version `6d9942af`)

#### B. Robots.txt — Disallow spam patterns
- **File:** `public/robots.txt`
- Added `Disallow` rules for all spam path prefixes and query patterns
- Will go live on next Astro build/deploy

### Verification

**Spam URLs (all return 410 Gone):**

| Pattern | Status |
|---------|--------|
| `?t=spam` | 410 |
| `?l=spam` | 410 |
| `?.shtml` | 410 |
| `/itemgoods/*` | 410 |
| `/ctg/*` | 410 |
| `/f/*` | 410 |
| `/mbr/*` | 410 |
| `/odr/*` | 410 |
| `/evercompare/*` | 410 |
| `/categoryindex/*` | 410 |

**Legitimate URLs (all unaffected):**

| URL | Status |
|-----|--------|
| Homepage `/` | 200 |
| `/collections/*` | 200 |
| `/cart/` | 200 |
| `/wp-json/wc/v3/` | 200 |
| `?wc-ajax=*` | 200 |
| `/robots.txt` | 200 |

### Remaining Manual Step
- Submit bulk removal requests in Google Search Console for `?t=`, `/itemgoods/`, `?.shtml` URL prefixes

---

## Summary

| Task | Status |
|------|--------|
| Exact Online error 1101 fix (UK edge router) | Done |
| WooCommerce Legacy REST API plugin install (UK) | Done |
| WooCommerce API enabled in DB (UK) | Done |
| Fix documentation | Done |
| Edge router body fix — DE (staging + production) | Done |
| Edge router body fix — FR (staging + production) | Done |
| Spam URL 410 blocking (UK edge router) | Done |
| Robots.txt spam disallow rules | Done (pending Astro deploy) |
| GSC bulk removal requests | Manual — pending |
