# Today's Progress - 2026-02-21

---

## 1. Google Reviews Widget — TrustIndex Redirect Fix

**Problem:** The TrustIndex widget on the "What Customers Say About Us" section links reviews through a TrustIndex redirect URL:

```
https://admin.trustindex.io/api/googleReview?place-id=ChIJTbL993,688,180EV93O6ERpK8bY8ZrpZM
```

This endpoint returns **HTTP 404** consistently — regardless of headers, referrer, or place ID encoding. In some browsers this fails silently; in others the user sees an error. No reviewer click ever reached Google Maps.

**Investigation:**
- Traced the TrustIndex loader script (`cdn.trustindex.io/loader.js?cb5ae3e497fe7730a8269155c1e`) to understand how review links are assigned
- Found that links are stored as `data-platform-page-url` attributes on review items and as `href` on the "See all reviews" button — all pointing to the broken TrustIndex redirect
- TrustIndex's `openWindow()` method creates a temporary `<a>` and clicks it programmatically, so standard link interception works

**Fix:** Added a `MutationObserver` in `CustomerReviews.astro` that fires whenever TrustIndex injects DOM nodes into the widget container. On each mutation it:
1. Finds all `<a href*="admin.trustindex.io">` links and replaces the href
2. Finds all `[data-platform-page-url*="admin.trustindex.io"]` elements and replaces the attribute

Both are replaced with the direct Google Maps listing URL for Hercules Merchandise UK:

```
https://www.google.com/maps/place/Hercules+Merchandise+UK/@45.8096575,-9.3986686,4z/data=!3m1!4b1!4m8!3m7!1s0xa13b775f11fdb24d:0x93a56bc6631bafa4!8m2!3d47.73855!4d11.5749774!9m1!1b1!16s%2Fg%2F11vdq3h_zp?entry=ttu&g_ep=EgoyMDI2MDIxOC4wIKXMDSoASAFQAw%3D%3D
```

The MutationObserver is started as soon as the TrustIndex script tag is appended to the container, so it catches all widget DOM insertions including lazy-rendered review cards.

TrustIndex still renders the full widget UI — only the broken redirect links are intercepted and replaced.

---

## 2. Files Modified Today

| File | Change |
|------|--------|
| `src/components/CustomerReviews.astro` | Added `MutationObserver` to intercept TrustIndex redirect links and replace with direct Google Maps URL |

---

## 3. Deployments Today

| Commit | Description |
|--------|-------------|
| `76acd37` | Fix Google review links — initial implementation with place_id URL |
| `dc6bba3` | Update Google Maps URL to correct full Hercules Merchandise UK listing URL |

Both deployed via GitHub Actions to Cloudflare Pages (`hercules-uk`) — all green, 156 pages built.

---

## 4. Outstanding Items (carried forward)

1. **PDF KV update + rebuild** — Update KV for products 4280, 4156, 4184 (stale pdf_2 values), verify all 6 products correct, then trigger site rebuild
2. **Product 13480 print thumbnails** — thumbnail images still need to be assigned to print attribute terms in WordPress admin for the Recycled Stock Sports Bag
3. **Staging missing `recycled-stock-sports-bag`** — must be created in staging WordPress first; once done the webhook will sync it automatically
4. **5 menu label differences** — Minor capitalization mismatches between Astro `menu-data.ts` and WordPress menu API (cosmetic only)
5. **Apply all fixes to DE** — shutdown hook PHP, `forceRefresh: false`, debounce 60s, and `product:index` guards still need porting to `hercules-headless-live`

---

*Last updated: 2026-02-21*
