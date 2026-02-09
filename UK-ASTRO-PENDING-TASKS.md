# UK Astro Site — Pending Tasks

> Last updated: 2026-02-07

---

## 2. DNS Configuration (CRITICAL — Go-Live Blocker)
- [ ] Create DNS A record `origin.hercules-merchandise.de` → `136.144.235.35`
- [ ] Activate Edge Router on production domain
- [ ] Full site testing via Edge Router
- [ ] Verify cart/checkout flow
- [ ] Verify form submissions

---

## ~~4. Mobile Navigation (CRITICAL)~~ ✅ DONE
- [x] Implement hamburger menu for mobile viewports
- [x] Add mobile menu toggle JavaScript
- [x] Test on multiple breakpoints (320px, 375px, 414px, 768px)
- [x] Fix body scroll lock when menu is open (added `overflow: hidden` to `body.mobile-menu-open`)

---

## ~~5. Hero Slider Arrows (HIGH)~~ ✅ DONE
- [x] Add Previous/Next navigation arrows to hero slider (Slider.astro lines 73-78, custom SVG arrows)
- [x] Match production functionality (Swiper with fade effect, autoplay, navigation, pagination)
- [x] Responsive arrow sizing for mobile (44px touch targets)

---

## 6. Meta Tags & Integration Scripts (HIGH)
- [ ] Remove any DE references — this is the UK site
- [ ] Fix GA4 Measurement ID — currently placeholder `G-XXXXXXXXX`, needs real ID from UK site
- [ ] Add Google Tag Manager — `GTM-TW5HR72` (head script + noscript iframe in body) — completely missing
- [ ] Add Google Ads Conversion Tracking — `AW-11156667431` — completely missing
- [ ] Add Microsoft Clarity — project ID `j9gd5ystsk` — completely missing
- [ ] Add Google Consent Mode V2 — consent defaults (`analytics_storage: denied`, `ad_storage: denied`, `ad_user_data: denied`, `ad_personalization: denied`) for EU/GB regions — completely missing
- [ ] Add JSON-LD Place schema — UK address: 8 Northumberland Avenue, WC2N 5BY, London — missing
- [ ] Fix ChatHive language — currently set to `"de"` (German), needs to be `"en"` for UK site
- [ ] Update cookie consent banner language — currently in German, needs English for UK

---

## ~~7. Full Product Sync (MEDIUM)~~ ✅ DONE

### Final Status (2026-02-08 05:18 GMT)

**✅ Successfully Synced: 97/97 products (100%)**
**✅ All categories synced: 40 categories**
**✅ Site rebuilt and deployed with all products**

#### Deployed Resources
- **Worker URL**: https://hercules-product-sync-uk.gilles-86d.workers.dev
- **Latest Deployment**: https://93d0cee1.hercules-uk-staging-e9z.pages.dev (deployed 2026-02-08)
- **Previous Deployment**: https://fcf3a880.hercules-uk-staging-e9z.pages.dev
- **KV Namespace**: 50743a0e269f4450b61bb690847534c99cfdbb7

#### WordPress Source Details
- **Source Site**: staging.hercules-merchandise.co.uk
- **Total Products**: 97 (actual count from WordPress)
- **Categories**: 40 (all synced ✓)
- **Featured Products**: 10 (IDs: 12571, 12270, 11782, 11776, 11775, 11768, 11762, 11720, 11695, 11668)

#### Sync Results
- **Products 0-77**: ✅ Synced on 2026-02-07
- **Products 78-96**: ✅ Synced on 2026-02-08 (after KV limit reset)
- **Offsets 97-114**: No products (WordPress has 97 total products)
- **Last Sync**: 2026-02-08 05:17:54 GMT
- **Status**: All available products synced successfully ✅

#### API Credentials Configured
```
WC_STORE_URL=https://staging.hercules-merchandise.co.uk
WC_CONSUMER_KEY=ck_1a7f55f2e141324051c303319c56333c99cfdbb7
WC_CONSUMER_SECRET=cs_5c661d7c8609a28de94c4a2ba6921b90ad816731
WEBHOOK_SECRET=hercules-webhook-secret-uk-2024
```

### Task Checklist
- [x] Deploy product sync worker
- [x] Configure WooCommerce API credentials
- [x] Run full product sync from UK staging site (97 products total)
- [x] Mark 10 products as featured (badges) for testing
- [x] Complete remaining products sync (offsets 78-96)
- [x] Rebuild and deploy with all 97 products
- [x] Verify PDFs are showing after sync (46 products have PDFs, all working ✓)
- [x] Verify category pages and product counts (all correct ✓)
- [x] Test product detail pages (rendering correctly ✓)
- [ ] Test webhook flow (product update → sync → rebuild)

---

## ~~8. Cart localStorage Testing (MEDIUM)~~ ✅ DONE
- [x] Add item to cart → badge updates instantly
- [x] Remove item from mini cart → badge updates (added × remove buttons with hover effects)
- [x] Navigate between pages → count persists (no flicker)
- [x] Open new tab → cart count matches
- [x] Refresh page → cart count persists
- [x] Clear cookies → cart data remains (localStorage)
- [x] Visit /cart/ → shows accurate server data
- [x] First visit (incognito) → fetches from API once, then persists
- [x] Fixed currency from € to £ (UK format with 0.00 decimal)

---

## 9. Animation & Accessibility (LOW)
- [ ] Enhance hover animations (add scale transforms, longer transitions)
- [ ] Add scroll animations (fade-in effects on scroll)
- [ ] Review heading weights (consider 600 vs 700 for consistency)
- [ ] Trailing slash consistency
- [ ] Remove unused preload hints
