# Today's Progress - 2026-02-19

---

## 1. Site Outage — All Products Gone (Post-Mortem & Fix)

### Issue
All products disappeared from the live site (`hercules-merchandise.co.uk`). Product pages returned 404 and search returned no results.

### Root Cause (3 bugs combined)

#### Bug 1 — Cron silently re-enabled on every `wrangler deploy`

The 3 AM cron had been disabled by the user in the Cloudflare dashboard. However, `wrangler.toml` still contained:

```toml
[env.production.triggers]
crons = ["0 3 * * *"]
```

`wrangler deploy` unconditionally overwrites the worker's trigger configuration from `wrangler.toml`, overriding any manual dashboard setting. On Feb 18 the worker was deployed 3 times — the cron was silently re-enabled on the first deploy.

#### Bug 2 — Delta sync unconditionally overwrote `product:index` with a filtered (potentially empty) list

The Feb 18 `scheduled()` rewrite introduced a delta sync — only fetching products modified since `last_delta_sync`. At 3 AM on Feb 19, `lastSyncAt` was set and WooCommerce returned **0 modified products**. Inside `syncAllProducts()`, this code ran unconditionally:

```typescript
// Runs even when allProducts = [] (delta sync with no changes)
if (offset === 0) {
  const productIndex = allProducts.map(...); // [] when nothing modified
  await env.PRODUCTS_KV.put('product:index', JSON.stringify(productIndex)); // wrote [] ← BUG
}
```

`product:index` was overwritten with `[]`, erasing all 98 products from the KV index.

#### Bug 3 — Rebuild always triggered even with 0 product changes

Categories and posts are synced unconditionally in the scheduled handler, so `categoryResult.synced > 0` is always true. This meant a site rebuild fired even though no products had changed:

```typescript
if (totalSynced > 0 || categoryResult.synced > 0 || postResult.synced > 0) {
  await triggerSiteRebuild(env); // always triggered
}
```

The Astro build fetched `product:index = []` and deployed a site with zero product pages.

### Timeline

| Time | Event |
|------|-------|
| Feb 18 daytime | Worker deployed 3× via wrangler → cron silently re-enabled |
| Feb 19 03:00 UTC | Cron fires → delta sync → 0 modified products → `product:index = []` |
| Feb 19 03:01 UTC | Rebuild triggered (categories synced) → site built with 0 products → LIVE |

---

## 2. Recovery Steps

### Step 1 — Disabled cron in `wrangler.toml`

**File:** `workers/product-sync/wrangler.toml`

Commented out both `[triggers]` and `[env.production.triggers]` cron entries so future `wrangler deploy` runs can never silently re-enable the cron.

```toml
# [env.production.triggers]
# crons = ["0 3 * * *"]
```

**Commit:** `06e23a1` — "Disable product-sync cron to prevent uncontrolled scheduled syncs"

### Step 2 — Restored clean worker code

Discarded uncommitted Feb 18 local changes to `workers/product-sync/src/index.ts` (the delta sync changes were never committed). Restored the committed pre-delta-sync version via `git checkout workers/product-sync/src/index.ts`.

Redeployed the clean worker to production:
- Version ID: `b0a1ece1-3d61-4fa9-9678-3c0b1c5faf14`
- No cron triggers (confirmed in deploy output)

### Step 3 — Rebuilt `product:index` from existing KV entries

Individual product entries (`product:10105`, `product:10122`, etc.) were **not** deleted by the delta sync — only `product:index` was wiped. Reconstructed the index by reading all 98 `product:{id}` KV entries via the Cloudflare KV REST API and writing a single `product:index` entry.

- **98 reads + 1 write** — well within daily KV limits
- No full sync required

### Step 4 — Triggered fresh site rebuild

After KV was repaired, a fresh GitHub Actions build was dispatched manually (`workflow_dispatch`). The build fetched the correct `product:index` (98 products) and deployed a working site.

---

## 2. Full Partial Sync Coverage — Categories and ACF/PDF Fields

### Issue
Two content types had no partial sync on update:
1. **Product categories** — no WordPress plugin triggered the category webhook endpoints that already existed in the worker
2. **ACF/PDF fields** — `woocommerce_update_product` fires at `save_post` priority 10, but ACF saves its custom fields at priority 20. This meant the worker synced the product from the API *before* PDF URLs, addon options, and badge fields were committed to the database.

### Fix 1 — New `hercules-category-webhooks.php`

**File:** `wordpress-updates/hercules-category-webhooks.php` (NEW)

Hooks into WooCommerce product category taxonomy events and sends to the worker endpoints that were already implemented but never called:

| WordPress Hook | Worker Endpoint |
|---------------|----------------|
| `created_product_cat` | `/webhook/category-create` |
| `edited_product_cat` | `/webhook/category-update` |
| `delete_product_cat` | `/webhook/category-delete` |

Worker handles: updates `category:{id}`, `category:slug:{slug}`, `category:index`, caches category image, triggers rebuild (debounced 5 min).

### Fix 2 — ACF late-save hook in `hercules-product-webhooks.php`

**File:** `wordpress-updates/hercules-product-webhooks.php` (UPDATED)

Added `acf/save_post` hook at **priority 30** (after ACF commits data at priority 20). This sends a second product sync webhook after all ACF fields are saved, ensuring `pdf_url`, `pdf_2_url`, addon options, and badge meta are up-to-date when the worker fetches the product.

```php
// Priority 30 = after ACF saves (priority 20) = after woocommerce_update_product (priority 10)
add_action('acf/save_post', array($this, 'on_acf_save'), 30);
```

Filters to `product` post type only, skips autosaves and revisions.

### Partial Sync Coverage — Before vs After

| Content Type | Before | After |
|-------------|--------|-------|
| Products (create/update/delete) | ✅ | ✅ |
| Product variations | ✅ | ✅ |
| ACF fields (PDFs, addons, badges) | ⚠️ Timing bug | ✅ Fixed |
| Categories (create/update/delete) | ❌ No plugin | ✅ Fixed |
| Posts (create/update/delete) | ✅ | ✅ |
| Menus | ✅ (rebuild trigger) | ✅ |
| Product attributes (term renames) | ❌ | ❌ (rare — manual sync) |

### Deployment
- Both plugins deployed to production and staging mu-plugins via SSH
- Backups created as `.bak-20260219`
- PHP syntax verified on server

---

## 3. Files Modified Today

| File | Change |
|------|--------|
| `workers/product-sync/wrangler.toml` | Disabled cron triggers in both default and production envs |
| `wordpress-updates/hercules-category-webhooks.php` | NEW — category create/update/delete webhook plugin |
| `wordpress-updates/hercules-product-webhooks.php` | Added `acf/save_post` hook at priority 30 for PDF/ACF field sync |

---

## 4. Worker Deployments Today

| Version ID | Change |
|------------|--------|
| `b0a1ece1` | Restored clean pre-delta-sync worker, cron disabled |

## 6. WordPress Server Deployments Today

| File | Environments | Change |
|------|-------------|--------|
| `mu-plugins/hercules-category-webhooks.php` | Production + Staging | NEW |
| `mu-plugins/hercules-product-webhooks.php` | Production + Staging | Added ACF hook |

---

## Outstanding Items

1. **Delta sync code improvements** — the Feb 18 delta sync had the right idea (reduce KV writes) but had the `product:index` overwrite bug. A corrected version should skip writing `product:index` entirely when called with `modifiedAfter` set. Consider implementing this properly when ready.
2. **Product 13480 print thumbnails** — thumbnail images still need to be assigned to print attribute terms in WordPress admin for the Recycled Stock Sports Bag product (WP admin data entry task)
3. **Staging missing 1 product** — `recycled-stock-sports-bag` not synced to staging worker KV
4. **5 menu label differences** — Minor capitalization mismatches between Astro `menu-data.ts` and WordPress menu API (cosmetic only)

---

*Last updated: 2026-02-19*
