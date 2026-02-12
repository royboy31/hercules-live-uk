# Today's Progress - 2026-02-11

---

## 1. Mobile Header Fix for WordPress Pages

### Issue
WordPress pages (/cart, /my-account, /checkout, etc.) had no mobile header on staging. The mu-plugin `hercules-custom-header.php` v1.0 didn't include mobile-responsive markup.

### Fix
Updated `hercules-custom-header.php` from v1.0 to v1.1 on the staging server to include mobile header markup matching the Astro site's responsive header.

---

## 2. Mobile Google Reviews Badge Size Fix

### Issue
The Google Reviews badge in the WordPress header was smaller on mobile compared to the Astro version.

### Fix
Updated badge sizing in the mu-plugin to match the Astro implementation.

---

## 3. Email Header "Contact Us" Bug Fix

### Issue
Contact form emails (sent via Cloudflare form handler worker) showed `☎ Contact Us` instead of the actual phone number in the email header.

### Root Cause
In `workers/form-handler/src/index.ts`, the `getEmailHeader()` function at line 339 had:
```html
<a href="tel:+441onal">Contact Us</a>
```
A corrupted tel href and "Contact Us" text instead of the actual phone number.

### Fix
Changed to the correct phone number:
```html
<a href="tel:+442039664881">(+44) 0203 9664881</a>
```

Worker redeployed to `https://hercules-form-handler-uk.gilles-86d.workers.dev`.

---

## 4. WordPress Contact Form Branded Email Template

### Issue
The WordPress header contact form (Elementor popup ID 5735) and Contact Us page form (ID 6239) were sending plain/simple email templates without branded headers or footers.

### Fix
Updated both Elementor forms' email templates in the database to include:
- **Branded header**: Logo + company email + phone number
- **Structured content table**: Name, Email, Phone, Message, Date, Time, Page
- **CTA section**: Reply prompt with contact link
- **Branded footer**: Site links (Home | My Account | Contact) + contact details

Changes applied via PHP scripts executed on the server:
- Popup ID 5735: Updated `email_content` + `email_content_2`, set `email_content_type` to `html`
- Page ID 6239: Updated `email_content`, set `email_content_type` to `html`, kept `email_content_2` as plain text auto-reply

---

## 5. Phone Number Standardization Across All Email Templates

### Issue
Phone number format was inconsistent across email templates. Some had `+44 2032 0966 4881` with `tel:+44203209664881`.

### Correct Format
- Display: `(+44) 0203 9664881`
- Tel href: `tel:+442039664881`
- Reference: `src/components/Footer.astro` already had this format

### Files Updated

#### WooCommerce Email Templates (14 files × 2 locations = 28 instances)
Path: `staging.hercules-merchandise.co.uk/wp-content/themes/hello-theme-child-master/woocommerce/emails/`
- admin-new-order.php
- customer-note.php
- customer-on-hold-order.php
- customer-refunded-order.php
- admin-failed-order.php
- customer-completed-order.php
- customer-failed-order.php
- pdf-order-notification.php
- customer-new-account.php
- admin-cancelled-order.php
- customer-invoice.php
- customer-credit-note.php
- customer-processing-order.php
- customer-reset-password.php

#### Pearl Plugin Email Templates (3 files × 2 locations = 6 instances)
Path: `staging.hercules-merchandise.co.uk/wp-content/plugins/pearl-wc-steps-variation/includes/mail_templates/`
- email-quote-template.php
- pdf-template.php
- otp-email.php

#### Cloudflare Form Handler Worker
File: `workers/form-handler/src/index.ts`
- `getEmailHeader()`: Updated phone number display and tel href
- `getEmailFooter()`: Added phone number (was missing)
- Worker redeployed

#### Elementor Form Database Entries
- Popup ID 5735: Updated header + footer phone numbers
- Page ID 6239: Updated header + footer phone numbers

### Verification
Grep confirmed zero instances of old format `+44 2032 0966 4881` remain across all templates and worker code.

---

## Files Modified

### Local Files
| File | Change |
|------|--------|
| `workers/form-handler/src/index.ts` | Fixed phone number in `getEmailHeader()`, added phone to `getEmailFooter()` |

### Server Files Modified (SSH)
| File | Change |
|------|--------|
| `hercules-custom-header.php` | v1.0 → v1.1: Added mobile header support |
| `hercules-google-reviews-badge.php` | Fixed mobile badge sizing |
| 14 WooCommerce email templates | Phone number standardized to `(+44) 0203 9664881` |
| 3 Pearl plugin email templates | Phone number standardized to `(+44) 0203 9664881` |

### Database Changes
| Entry | Change |
|-------|--------|
| Elementor popup 5735 `_elementor_data` | Branded email template + correct phone number |
| Elementor page 6239 `_elementor_data` | Branded email template + correct phone number |

### Workers Deployed
| Worker | URL |
|--------|-----|
| Form Handler | `https://hercules-form-handler-uk.gilles-86d.workers.dev` |

---

## 6. Admin New Order Email Subject Fix

### Issue
The admin new order email subject line showed `{customer_first_name}` as raw text instead of the actual customer name.

### Root Cause
The `WC_Email_New_Order` class only supports `{order_date}` and `{order_number}` placeholders natively. The subject was set to `New Order #{order_number} from {customer_first_name}` but `{customer_first_name}` was never resolved.

### Fix
Added a `woocommerce_email_subject_new_order` filter in `hercules-email-fixes.php` mu-plugin that replaces `{customer_first_name}`, `{customer_last_name}`, and `{customer_full_name}` with actual billing name from the order.

### Verification
Sent test email for order #13484 — subject correctly shows "New Order #13484 from kamindu". All other email subjects checked — they only use natively-supported placeholders (`{order_number}`, `{site_title}`).

---

## 7. Remove Price from Search Results

### Issue
Product search results (in both Astro and WordPress headers) displayed price below the product title.

### Fix
Removed price display from all three locations:
- **Astro** (`src/components/ProductSearch.tsx`): Removed price JSX + `.product-price` CSS
- **WordPress custom header** (`hercules-custom-header.php`): Removed price JS rendering line
- **WordPress sticky header** (`hercules-sticky-header.php`): Removed `.herc-search-product-price` CSS

Astro changes deployed to Cloudflare Pages.

---

## 8. Urgent Quote & Custom Quantity Email Delivery Test

### Task
Verified that "Urgent Delivery Request" (`expressdelivery`) and "Custom Quantity Request" (`quantity_request`) emails from Astro product pages are delivered correctly via the Cloudflare form handler worker.

### Results
Both form types submitted successfully via `https://hercules-form-handler-uk.gilles-86d.workers.dev/contact`:
- **Quantity Request**: Subject "Quote Request: Baseball Cap - 5000 pcs" → TO admin, Reply-To customer
- **Express Delivery**: Subject "Urgent Quote Request - Baseball Cap" → TO admin, Reply-To customer

Both confirmed `{"success":true}` with Google Sheets + Brevo email delivery.

### Customer Copy
Neither form type sends a copy to the customer — **matches live WordPress behavior** (verified by reading `max_quantity_request.php` and `urgent_quote.php` in the Pearl plugin on live).

---

## 9. Live Product Variation Bug Investigation

### Issue
Product "Recycled Stock Sports Bag" (ID 13480, slug: `custom-football-bag-copy`) on the live site — `steps.js` couldn't identify variations. Attribute selection had no effect.

### Root Cause
All 3 variation posts had `post_status = 'private'` instead of `'publish'`:
- #13491: Digital 3D print → private
- #13492: Digital print → private
- #13493: No print → private

WooCommerce's `variation_is_visible()` requires `post_status === 'publish'`. With `private` status:
- `get_available_variations()` returns 0 variations
- `pearlWCData.variations` is an empty array passed to `steps.js`
- Client-side `attrsMatch()` has nothing to match against
- AJAX fallback also fails (`get_matching_variation()` skips non-purchasable variations)

### Likely Cause
Product was duplicated (name ends in "-copy"), and the duplication set variation status to `private`.

### Fix
Change variation posts 13491, 13492, 13493 from `private` to `publish` via WP Admin: **Products → Recycled Stock Sports Bag → Product data → Variations tab → set each to Published**.

---

## 10. "Made in UK" Product Badge

### Issue
The live UK WordPress site has a "Made in UK" badge on product pages (alongside "Green Product" and "Made in Europe"), but it was missing from the staging Astro site.

### Root Cause
The product sync worker only extracted `green_product` and `made_in_europe` meta fields from WooCommerce — `made_in_uk` was not included.

### Fix
1. **Product Sync Worker** (`workers/product-sync/src/index.ts`):
   - Added `made_in_uk: boolean` to the product type interface
   - Added `getMeta('made_in_uk')` extraction in 3 places (single product transform, bulk sync index, single product index update)
   - Deployed worker and triggered full resync (97 products)

2. **Badge SVG** (`public/images/badges/made-in-uk.svg`):
   - Downloaded from WordPress uploads: `https://hercules-merchandise.co.uk/wp-content/uploads/2025/09/large-Made-in-the-UK-1.svg`

3. **Product Page** (`src/pages/products/[slug].astro`):
   - Added `made_in_uk` badge rendering in `.product-badges` div

4. **Category Product Card** (`src/components/CategoryProductCard.astro`):
   - Added `made_in_uk?: boolean` to product type
   - Added `isMadeInUk` flag and badge rendering with `.kd-badge-uk` styling (50px width)

### Verification
Badge visible on staging: `https://staging.hercules-merchandise.co.uk/products/custom-hd-football-made-in-the-uk/`

---

## 11. Badge Size Adjustment (Collection Pages)

### Issue
Green Option and Made in UK badges were 60px/55px wide on collection pages — requested to be 50px.

### Fix
Updated `.kd-badge-eco` and `.kd-badge-uk` widths from 60px/55px to 50px in `src/components/CategoryProductCard.astro`.

---

## 12. /contact → /contact-us URL Migration

### Issue
The contact page URL changed from `/contact` to `/contact-us` (Astro page is `contact-us.astro`), but old `/contact` links remained across the site, email templates, and WordPress.

### Fix

| Location | Change |
|----------|--------|
| `workers/form-handler/src/index.ts` (line 349) | Email footer: `${SITE_URL}/contact/` → `${SITE_URL}/contact-us/` |
| `workers/form-handler/src/index.ts` (line 592) | Quote CTA: `${SITE_URL}/contact/` → `${SITE_URL}/contact-us/` |
| `workers/edge-router/src/index.ts` | Removed `/contact` from WORDPRESS_PATHS |
| `workers/edge-router/src/index.ts` | Added 301 redirect: `/contact` and `/contact/` → `/contact-us/` |
| Server: `hercules-custom-header.php` (2 links) | `href="/contact/"` → `href="/contact-us/"` |
| Server: `functions.php` (line 586) | Thank you page: `href='/contact'` → `href='/contact-us'` |
| Server: `woocommerce/emails/customer-failed-order.php` | `$site_url . '/contact'` → `$site_url . '/contact-us'` |

---

## 13. Green Option Badge SVG Replacement

### Issue
The green option badge used the German version. Replaced with English version (`Green-Option-EN.svg`) showing "GREEN OPTION AVAILABLE".

### Fix
Replaced `public/images/badges/green-option.svg` with the new English SVG. Purged Cloudflare cache for the file.

---

## 14. Chathive Chat Widget API Key

### Issue
User requested switching from German site's Chathive widget to the UK live site's widget.

### Investigation
- Staging had German key: `Y6bGE4l2M1Yp6Wm-sYqpRpsq`
- UK live site key: `TVkvsqiY5b5yazDk8h18ThCT` (with language set to `"nl"` — likely misconfigured)
- Switched to UK key but widget didn't render — the UK key is domain-restricted to `hercules-merchandise.co.uk` and won't work on `staging.hercules-merchandise.co.uk`

### Resolution
Reverted to the German key which works on staging. The UK key should be set when the site goes live on the main domain, or the staging subdomain needs to be added as an allowed domain in the Chathive dashboard.

---

## Files Modified

### Local Files
| File | Change |
|------|--------|
| `workers/form-handler/src/index.ts` | Fixed phone number, /contact → /contact-us in email templates |
| `workers/edge-router/src/index.ts` | Removed /contact from WP paths, added 301 redirect to /contact-us |
| `workers/product-sync/src/index.ts` | Added `made_in_uk` badge field extraction |
| `src/pages/products/[slug].astro` | Added Made in UK badge rendering |
| `src/components/CategoryProductCard.astro` | Added Made in UK badge, adjusted badge sizes to 50px |
| `src/components/ProductSearch.tsx` | Removed price from search results + CSS cleanup |
| `src/layouts/BaseLayout.astro` | Chathive API key (attempted UK key, reverted) |
| `public/images/badges/green-option.svg` | Replaced with English version |
| `public/images/badges/made-in-uk.svg` | New file — downloaded from WP uploads |

### Server Files Modified (SSH)
| File | Change |
|------|--------|
| `hercules-custom-header.php` | v1.0 → v1.1: Added mobile header support; removed search price; /contact → /contact-us |
| `hercules-google-reviews-badge.php` | Fixed mobile badge sizing |
| `hercules-sticky-header.php` | Removed search price CSS |
| `hercules-email-fixes.php` | Added `{customer_first_name}` placeholder support for new order email subject |
| `functions.php` | Thank you page /contact → /contact-us |
| `woocommerce/emails/customer-failed-order.php` | /contact → /contact-us |
| 14 WooCommerce email templates | Phone number standardized to `(+44) 0203 9664881` |
| 3 Pearl plugin email templates | Phone number standardized to `(+44) 0203 9664881` |

### Database Changes
| Entry | Change |
|-------|--------|
| Elementor popup 5735 `_elementor_data` | Branded email template + correct phone number |
| Elementor page 6239 `_elementor_data` | Branded email template + correct phone number |

### Workers Deployed
| Worker | URL |
|--------|-----|
| Product Sync | `https://hercules-product-sync-uk.gilles-86d.workers.dev` |
| Form Handler | `https://hercules-form-handler-uk.gilles-86d.workers.dev` |
| Edge Router | `https://hercules-edge-router-uk.gilles-86d.workers.dev` |

### Astro Deployed
Multiple deployments throughout the session to `hercules-uk-staging` Cloudflare Pages.

---

## Summary

| Task | Status |
|------|--------|
| Mobile header for WordPress pages | ✅ Fixed |
| Mobile Google Reviews badge size | ✅ Fixed |
| Email header "Contact Us" bug | ✅ Fixed |
| WordPress contact form branded template | ✅ Fixed |
| Phone number standardization (all templates) | ✅ Complete |
| Admin new order email subject placeholder | ✅ Fixed |
| Remove price from search results | ✅ Fixed + Deployed |
| Urgent quote & custom quantity email delivery | ✅ Verified (matches live) |
| Live product variation bug (football bag) | 🔍 Investigated — needs WP Admin fix |
| Made in UK product badge | ✅ Added |
| Badge size adjustment (50px) | ✅ Fixed |
| /contact → /contact-us migration + 301 | ✅ Complete |
| Green Option badge SVG (English) | ✅ Replaced |
| Chathive widget UK key | ⚠️ Domain-restricted, reverted to working key |

---

## Pending / Known Issues

1. **Live: Football bag variations** — Variations 13491/13492/13493 need status changed from `private` to `publish` in WP Admin
2. **Chathive UK key** — `TVkvsqiY5b5yazDk8h18ThCT` is domain-restricted; add `staging.hercules-merchandise.co.uk` in Chathive dashboard or swap key at go-live
3. **ClickCease in GTM** — needs removal from GTM container `GTM-TW5HR72` (loaded directly now)
4. **Cloudflare Web Analytics** — auto-injected beacon needs dashboard config
5. **TrustIndex cache** — can't proxy (breaks widget)
6. **Collections/caps 404** — "caps" category doesn't exist in WordPress (closest: "headwear")
7. **Blog post veja 404** — slug mismatch between link and actual post slug
8. **Blog listing mobile LCP: 9.6s** — `loading="lazy"` on LCP image, needs `fetchpriority="high"` + WebP
9. **Color contrast failures** — `#00AEEF` (2.52:1) and `#10C99E` (2.12:1) fail WCAG AA
10. **Live site email settings** — Still has old personal emails but live is RESTRICTED

---
