# Progress Report - 2026-02-06

## Summary

Completed custom WordPress header deployment, menu icon migration, Trustindex widget update, and Google Reviews badge UK localization for the Hercules Merchandise UK staging site.

---

## 1. Custom Header mu-plugin (WordPress)

**File:** `wp-content/mu-plugins/hercules-custom-header.php` (UK staging server)

- Created and deployed a 1007-line PHP mu-plugin that replaces the Elementor header with a custom 3-row header:
  - **Row 1:** Top bar with USPs (Free Design Service, Fast Delivery, etc.) + Google Reviews badge
  - **Row 2:** Logo, search bar, account/cart/wishlist icons
  - **Row 3:** Navigation with mega menu dropdowns (Sports, Products, Themes) + direct links + CONTACT US button
- Elementor header hidden via CSS (`.elementor-location-header { display: none !important; }`)
- Custom header only shown on desktop (min-width: 769px), mobile keeps Elementor header

### Menu Icon Sourcing Fix

- Initially used a hardcoded icon map; **rewrote** `hercules_custom_header_get_menu_items()` to read icons from WordPress custom field `_menu_item_icon_url` (set via `hercules-menu-icon-field.php`)
- Icons now come dynamically from each menu item's custom field in the WordPress admin

---

## 2. Menu Icons Migration (DE to UK)

- Created `/wp-content/uploads/hercules-menu-icons/` on UK server
- Copied **37 SVG icon files** from the German server via tar archive
- Copied `google-reviews-badge.svg` from the German server
- Populated **36 `_menu_item_icon_url` meta values** in the UK database (menu item IDs 10983-11026) via SQL INSERT, matching the German site's icon URL structure with UK domain paths

### Verified all mega menus working:
- SPORTS: Football, Rugby, Basketball, etc. (all with icons)
- PRODUCTS: Sportswear, Scarves, Beanies, etc. (all with icons)
- THEMES: Summer, Winter, Sustainable, etc. (all with icons)

---

## 3. Trustindex Widget Update

Replaced the old Google Reviews widget ID across the entire site.

| File | Old Widget ID | New Widget ID |
|------|--------------|---------------|
| `src/components/CustomerReviews.astro` | `72406ce58900366af2861a4ccab` | `cb5ae3e497fe7730a8269155c1e` |
| `src/pages/products/[slug].astro` | `72406ce58900366af2861a4ccab` | `cb5ae3e497fe7730a8269155c1e` |

WordPress pages already had the correct new widget ID.

---

## 4. Google Reviews Badge - UK Localization

### Astro Site Changes

**`src/lib/google-places.ts`:**

| Setting | Before (German) | After (UK) |
|---------|-----------------|------------|
| Place ID | `ChIJtRY2nLY44EcRbp9kBiuZYKI` | `ChIJTbL9EV93O6ERpK8bY8ZrpZM` |
| Business Name | Hercules Merchandise Deutschland | Hercules Merchandise UK |
| Rating | 5.0 | 4.9 |
| Review Count | 56 | 135 |
| Google Maps URL | German listing | UK listing (`0xa13b775f11fdb24d:0x93a56bc6631bafa4`) |
| `formatRating()` | German comma format (4,9) | English decimal (4.9) |
| `formatReviewCount()` | "Bewertungen" | "Reviews" |

**`.env`:**
- `GOOGLE_REVIEWS_RATING=4.9` (was 5.0)
- `GOOGLE_REVIEWS_COUNT=135` (was 0)

**`src/data/google-reviews.json`:**
- Replaced 6 German review texts with English equivalents

### WordPress mu-plugin Changes

**`hercules-google-reviews-badge.php`:**
- Rating: 5.0 -> 4.9
- Title: "5 Sterne aus 56+ Bewertungen" -> "4.9 stars from 135+ Reviews"
- aria-label: "Google Bewertungen: 5 von 5 Sternen" -> "Google Reviews: 4.9 out of 5 stars"

**`hercules-custom-header.php`:**
- Google Maps link updated from generic `Hercules+Merchandise` URL to specific UK listing URL

### How the UK Place ID was found:
- Queried `trustindex-google-page-details` option from the WordPress database
- Trustindex had the correct Place ID: `ChIJTbL9EV93O6ERpK8bY8ZrpZM`
- Rating: 4.9 with 135 reviews

---

## 5. Build & Deployment

- Astro build: **156 pages** built successfully
- Verified badge renders **4.9** rating with 5 gold stars in both TopBar and Header
- Badge links point to UK Google Maps listing
- **Deployed to Cloudflare Pages:** `hercules-uk-staging`
- Deployment URL: https://411997ee.hercules-uk-staging-e9z.pages.dev

---

## Files Modified Today

### Astro Site (local)
| File | Change |
|------|--------|
| `src/lib/google-places.ts` | UK Place ID, fallback data, English format functions |
| `src/data/google-reviews.json` | English review text |
| `src/components/CustomerReviews.astro` | New Trustindex widget ID |
| `src/pages/products/[slug].astro` | New Trustindex widget ID |
| `.env` | Updated rating (4.9) and review count (135) |

### WordPress mu-plugins (UK staging server)
| File | Change |
|------|--------|
| `hercules-custom-header.php` | New file - custom 3-row header replacing Elementor |
| `hercules-google-reviews-badge.php` | Rating 4.9, English text |

### UK Database
| Change | Details |
|--------|---------|
| `wp_1202943_postmeta` | Inserted 36 `_menu_item_icon_url` rows for menu items 10983-11026 |

---

## Pending / Next Steps

- Set up `GOOGLE_PLACES_API_KEY` in `.env` to fetch live rating data at build time (currently uses static fallback)
- Mobile header still uses Elementor (custom header is desktop-only)
- Edge Router worker deployment for hybrid routing
- GitHub repository setup and CI/CD pipeline
