# UK Staging Site Implementation Plan
## staging.hercules-merchandise.co.uk

This plan covers setting up a complete UK version of the Hercules Merchandise headless e-commerce site, including:
- Claude training documentation (CLAUDE.md)
- All Cloudflare infrastructure (Pages, Workers, KV, R2)
- WordPress/WooCommerce backend customizations
- Frontend localization and configuration
- GitHub CI/CD setup

**Project Folder:** `/home/kamindu/hercules-headless-uk`
**GitHub Repo:** `royboy31/hercules-live-uk`

---

## Prerequisites Checklist

Before starting, ensure you have:
- [ ] UK WordPress staging site access (wp-admin credentials)
- [ ] New GitHub repository created: `royboy31/hercules-live-uk`
- [ ] Cloudflare account access with API token
- [ ] Brevo API key (can use same account with UK sender)
- [ ] UK phone number for contact (format: +44 XXXX XXXXXX)

---

## PHASE 1: Infrastructure Setup

### 1.1 Create New GitHub Repository
```bash
# Create new repo: hercules-live-uk (private)
# Copy existing folder content to new location
```

### 1.2 Create Cloudflare Resources

**Cloudflare Pages Project:**
- Project Name: `hercules-uk-staging`
- Will produce: `https://hercules-uk-staging.pages.dev`

**Cloudflare Workers (3 workers):**
1. `hercules-edge-router-uk` - Hybrid routing
2. `hercules-product-sync-uk` - Product data sync
3. `hercules-form-handler-uk` - Form submissions

**Cloudflare KV Namespace:**
- Name: `hercules-uk-products-kv`
- For caching product data

**Cloudflare R2 Bucket:**
- Name: `hercules-uk-form-uploads`
- For form file attachments

---

## PHASE 2: WordPress/WooCommerce Backend Setup

This phase covers all the WordPress customizations needed to make the headless architecture work.

### 2.1 WooCommerce REST API Setup

**In WordPress Admin → WooCommerce → Settings → Advanced → REST API:**

1. Create new API key:
   - Description: `Hercules Headless UK`
   - User: Admin user
   - Permissions: `Read/Write`
   - Save the Consumer Key and Consumer Secret

### 2.2 Install Required mu-plugins

**Copy these files from German WordPress to UK `/wp-content/mu-plugins/`:**

| Plugin File | Purpose |
|-------------|---------|
| `hercules-session-api.php` | Session endpoint for cart count, user state |
| `hercules-cart-api.php` | Cart remove item endpoint |
| `hercules-wishlist-api.php` | Wishlist get/toggle endpoints |
| `hercules-category-api.php` | Categories with second_description & FAQ |
| `hercules-category-meta.php` | Second description in WooCommerce REST |
| `hercules-main-header-menu-api.php` | Main header menu with icons |
| `hercules-menu-webhooks.php` | Auto-rebuild Astro on menu changes |
| `hercules-sticky-header.php` | Sticky header for WordPress pages |
| `hercules-pearl-steps-api.php` | Pearl WC Steps product config data |
| `pearl-rest-api-meta.php` | Product pricing/PDFs in REST API |

**Source files in repo:**
- `/home/kamindu/hercules-headless-live/wordpress-plugins/hercules-category-meta.php`
- `/home/kamindu/hercules-headless-live/wordpress-updates/hercules-category-api.php`
- `/home/kamindu/hercules-headless-live/wp-plugins/hercules-pearl-steps-api.php`

**Other mu-plugins are on the German WordPress server at:**
```
/var/www/vhosts/hercules-merchandise.de/httpdocs/wp-content/mu-plugins/
```

**Custom REST API Endpoints Created:**
```
GET  /wp-json/hercules/v1/session                         # Cart count, user login state
POST /wp-json/hercules/v1/cart/remove                     # Remove item from cart
GET  /wp-json/hercules/v1/wishlist                        # Get user wishlist
POST /wp-json/hercules/v1/wishlist/toggle                 # Add/remove from wishlist
GET  /wp-json/hercules/v1/categories                      # All product categories
GET  /wp-json/hercules/v1/category/{slug}                 # Single category + FAQ
GET  /wp-json/hercules/v1/main-header-menu                # Menu structure with icons
GET  /wp-json/hercules/v1/product-config/{id}             # Product config by ID
GET  /wp-json/hercules/v1/product-config-by-slug/{slug}   # Product config by slug
```

**Localization Required in mu-plugins:**
- `hercules-pearl-steps-api.php` line 249: Change `'5 Wochen'` → `'5 weeks'`
- Any other German text in API responses

### 2.3 Menu Configuration

**In WordPress Admin → Appearance → Menus:**

1. **Create Main Header Menu:**
   - Menu name: `Main Header Menu`
   - Location: `Primary Menu`

2. **Add Menu Items with Icons:**
   - Each menu item needs custom field for icon (SVG or class)
   - Categories: Sports, Products, Themes (with dropdown children)
   - Structure mirrors German site menu

3. **Icon Visibility Settings:**
   - Enable ACF or custom meta for menu items
   - Add fields: `menu_icon_svg`, `show_icon`

### 2.4 Header & Sticky Header Elements

**WordPress Customizations for:**

| Element | Implementation |
|---------|----------------|
| Logo | Upload UK logo or use same logo |
| Menu | Configured via WordPress menu system |
| Search | Redirects to Astro search |
| Wishlist icon | Links to `/wishlist` |
| Mini cart | Shows cart count from session API |
| User login dot | Green dot indicator from session API |

### 2.5 Cart & Checkout Pages

**WordPress Pages to Create:**
- `/cart` - Shopping cart page (WooCommerce shortcode: `[woocommerce_cart]`)
- `/checkout` - Checkout page (WooCommerce shortcode: `[woocommerce_checkout]`)
- `/my-account` - User account (WooCommerce shortcode: `[woocommerce_my_account]`)

**Cart Page Customizations:**
- Same styling as German site
- Currency in GBP
- "Continue Shopping" links back to Astro

### 2.6 Quote Generator Page

**WordPress Page: `/quote-generator`**
- Custom template or ACF Pro page builder
- Form fields matching German quote generator
- Integrates with Form Handler Worker

### 2.7 Contact Page

**WordPress Page: `/contact`**
- Contact form (can use headless form or WPForms)
- UK address and phone number
- Map embed (if applicable)

### 2.8 WooCommerce Settings

**Currency Settings:**
- Currency: British Pound (£)
- Currency position: Left
- Thousand separator: ,
- Decimal separator: .
- Number of decimals: 2

**Shipping Settings:**
- Configure UK shipping zones
- Set up shipping methods for UK

**Tax Settings:**
- Enable UK VAT (20%)
- Configure tax display settings

### 2.9 Theme Customizations

If using a custom theme or child theme:
- Header template with headless integrations
- Footer template
- Cart page template
- Checkout page template
- Add JavaScript for mini cart updates

### 2.10 Required WordPress Plugins

| Plugin | Purpose |
|--------|---------|
| WooCommerce | E-commerce functionality |
| ACF Pro | Custom fields for products/menus |
| Product Add-Ons (Pearl) | Product configurator options |
| WooCommerce REST API | Already included in WooCommerce |

---

## PHASE 3: Configuration Files

### 3.1 Environment Variables (`.env`)

**File: `.env`**
```env
CLOUDFLARE_API_TOKEN=<same-token>
CLOUDFLARE_ACCOUNT_ID=d6d3df04acc98efe34f43e42636a3dfc

# WooCommerce REST API (UK Store)
WC_STORE_URL=https://staging.hercules-merchandise.co.uk
WC_CONSUMER_KEY=<uk-consumer-key>
WC_CONSUMER_SECRET=<uk-consumer-secret>

# Google Reviews (UK)
GOOGLE_REVIEWS_RATING=5.0
GOOGLE_REVIEWS_COUNT=0
```

### 3.2 Astro Configuration (`astro.config.mjs`)

**Changes:**
- `site: 'https://staging.hercules-merchandise.co.uk'`
- Update redirect patterns for English slugs

### 3.3 SEO Configuration (`src/config/seo.ts`)

**Changes:**
```typescript
export const seoConfig = {
  siteName: 'Hercules Merchandise UK',
  siteUrl: 'https://staging.hercules-merchandise.co.uk',
  locale: 'en_GB',
  language: 'en',
  phone: '+44 XXXX XXXXXX',
  // ... organization details for UK
}
```

---

## PHASE 4: Worker Configurations

### 4.1 Edge Router (`workers/edge-router/wrangler.toml`)

```toml
name = "hercules-edge-router-uk"

[vars]
ASTRO_ORIGIN = "https://hercules-uk-staging.pages.dev"
WORDPRESS_ORIGIN = "https://staging.hercules-merchandise.co.uk"
```

**Route Updates (`workers/edge-router/src/index.ts`):**
| German Route | UK Route |
|--------------|----------|
| `/kollektionen/*` | `/collections/*` |
| `/produkte/*` | `/products/*` |
| `/warenkorb` | `/cart` |
| `/kasse` | `/checkout` |
| `/mein-konto` | `/my-account` |
| `/kontakt` | `/contact` |
| `/angebotsgenerator` | `/quote-generator` |

### 4.2 Product Sync Worker (`workers/product-sync/wrangler.toml`)

```toml
name = "hercules-product-sync-uk"

[[kv_namespaces]]
binding = "PRODUCTS_KV"
id = "<new-uk-kv-namespace-id>"

[vars]
WC_STORE_URL = "https://staging.hercules-merchandise.co.uk"
GITHUB_ACTIONS_URL = "https://api.github.com/repos/royboy31/hercules-live-uk/dispatches"
```

### 4.3 Form Handler Worker (`workers/form-handler/wrangler.toml`)

```toml
name = "hercules-form-handler-uk"

[[r2_buckets]]
binding = "FORM_UPLOADS"
bucket_name = "hercules-uk-form-uploads"

[vars]
ALLOWED_ORIGINS = "https://staging.hercules-merchandise.co.uk,https://hercules-uk-staging.pages.dev"
```

---

## PHASE 5: Language Localization

### 5.1 Files Requiring English Translation

| File | Changes Required |
|------|-----------------|
| `src/data/menu-data.ts` | All menu labels (Sportarten→Sports, Produkte→Products, Themen→Themes) |
| `src/components/Footer.astro` | Footer text, contact info, legal links |
| `src/components/HerculesMerchandise.astro` | About section (all German paragraphs) |
| `src/components/ContactFormPopup.tsx` | Form labels, placeholders, validation messages |
| `src/components/ProductConfigurator.tsx` | Step labels, button text |
| `src/pages/index.astro` | Homepage meta title/description |
| `src/pages/wishlist.astro` | Wishlist page labels |
| `src/pages/404.astro` | Error page text |
| `src/pages/blogs/index.astro` | Blog page labels |
| `functions/lib/brevo.ts` | Email templates (3 templates) |

### 5.2 Menu Data Translation (`src/data/menu-data.ts`)

**Sports Categories:**
| German | English |
|--------|---------|
| Fußball | Football |
| Basketball | Basketball |
| Handball | Handball |
| Tennis | Tennis |
| Eishockey | Ice Hockey |
| Laufen | Running |
| Schwimmen | Swimming |
| Tanzen | Dance |
| Golf | Golf |
| Reiten | Equestrian |
| American Football | American Football |

**Product Categories:**
| German | English |
|--------|---------|
| T-Shirts | T-Shirts |
| Poloshirts | Polo Shirts |
| Sweatshirts | Sweatshirts |
| Hoodies | Hoodies |
| Jacken | Jackets |
| Hosen | Trousers |
| Shorts | Shorts |
| Socken | Socks |
| Caps | Caps |
| Beanies | Beanies |
| Taschen | Bags |
| Accessoires | Accessories |
| Trinkflaschen | Water Bottles |

### 5.3 Email Templates (`functions/lib/brevo.ts`)

**Contact Form Email:**
- Subject: "New Contact Request" (was "Neue Kontaktanfrage")
- All field labels in English

**Quote Request Email:**
- Subject: "New Quote Request" (was "Neue Angebotsanfrage")
- Product details labels in English

**Newsletter Email:**
- Subject: "New Newsletter Signup" (was "Neue Newsletter-Anmeldung")

---

## PHASE 6: Currency & Regional Settings

### 6.1 Currency Changes (EUR → GBP)

**Files to update:**
- `src/components/CategoryProductCard.astro` - `€` → `£`
- `src/components/ProductCard.astro` - `€` → `£`
- `src/components/ProductConfigurator.tsx` - `€` → `£`
- `src/components/UserSession.tsx` - `€` → `£`
- `src/pages/produkte/[slug].astro` → `src/pages/products/[slug].astro` - `€` → `£`
- `src/lib/cartStore.ts` - Default currency

### 6.2 Price Formatting

```typescript
// German format: € 99,99
// UK format: £99.99

// Change from:
price.toFixed(2).replace('.', ',')
// To:
price.toFixed(2)
```

### 6.3 Regional Settings

| Setting | German (Current) | UK (New) |
|---------|------------------|----------|
| Locale | de_DE | en_GB |
| Currency | EUR (€) | GBP (£) |
| Decimal | Comma (,) | Period (.) |
| Phone | +49 8001833745 | +44 XXXX XXXXXX |
| Timezone | CET | GMT/BST |

---

## PHASE 7: URL Structure & Routing

### 7.1 Page Path Changes

**Rename/Create Pages:**
```
src/pages/produkte/[slug].astro  →  src/pages/products/[slug].astro
src/pages/kollektionen/[slug].astro  →  src/pages/collections/[slug].astro
```

### 7.2 Hreflang Mappings (`src/data/hreflang-mappings.ts`)

Update to reflect UK as primary:
- Set UK URLs as default
- Update all product slug mappings for EN versions

### 7.3 301 Redirects (Edge Router)

Configure redirects from German paths to English:
```
/kollektionen/* → /collections/*
/produkte/* → /products/*
/blogs/nachrichten/* → /blogs/news/*
```

---

## PHASE 8: GitHub Actions CI/CD

### 8.1 Workflow File (`.github/workflows/deploy.yml`)

```yaml
name: Deploy to Cloudflare Pages

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'

      - name: Install dependencies
        run: npm ci

      - name: Build
        run: npm run build
        env:
          WC_CONSUMER_KEY: ${{ secrets.WC_CONSUMER_KEY }}
          WC_CONSUMER_SECRET: ${{ secrets.WC_CONSUMER_SECRET }}

      - name: Deploy to Cloudflare Pages
        uses: cloudflare/pages-action@v1
        with:
          apiToken: ${{ secrets.CLOUDFLARE_API_TOKEN }}
          accountId: ${{ secrets.CLOUDFLARE_ACCOUNT_ID }}
          projectName: hercules-uk-staging
          directory: dist
```

### 8.2 GitHub Secrets Required

| Secret | Description |
|--------|-------------|
| CLOUDFLARE_API_TOKEN | Cloudflare API token |
| CLOUDFLARE_ACCOUNT_ID | `d6d3df04acc98efe34f43e42636a3dfc` |
| WC_CONSUMER_KEY | UK WooCommerce consumer key |
| WC_CONSUMER_SECRET | UK WooCommerce consumer secret |
| GITHUB_PAT | For product sync webhook |

---

## PHASE 9: CLAUDE.md Training Document

### 9.1 Create New CLAUDE.md

**File: `CLAUDE.md`**

```markdown
# HERCULES MERCHANDISE UK - CLAUDE PROJECT GUIDE

## Quick Reference

| Resource | URL |
|----------|-----|
| Staging Site | https://staging.hercules-merchandise.co.uk |
| Astro Pages | https://hercules-uk-staging.pages.dev |
| WordPress Backend | https://staging.hercules-merchandise.co.uk/wp-admin |
| Edge Router Worker | https://hercules-edge-router-uk.<account>.workers.dev |
| Product Sync Worker | https://hercules-product-sync-uk.<account>.workers.dev |
| Form Handler Worker | https://hercules-form-handler-uk.<account>.workers.dev |
| GitHub Repo | https://github.com/royboy31/hercules-live-uk |

## Architecture

```
User Browser
    ↓
Cloudflare Edge Router (hercules-edge-router-uk)
    ↙                                           ↘
Astro Routes (Static)                    WordPress Routes (Dynamic)
/                                        /cart, /checkout, /my-account
/collections/*                           /wp-admin, /wp-json
/products/*                              /contact, /quote-generator
/blogs/*
    ↓                                          ↓
Cloudflare Pages                         WordPress (UK)
(hercules-uk-staging.pages.dev)         (staging.hercules-merchandise.co.uk)
```

## Tech Stack
- Frontend: Astro 5.x + React 19 + Preact
- Backend: WordPress/WooCommerce
- Hosting: Cloudflare Pages + Workers
- Email: Brevo
- Language: English (en_GB)
- Currency: GBP (£)

## API Credentials

### WooCommerce REST API
- Store URL: `https://staging.hercules-merchandise.co.uk`
- Consumer Key: `<to-be-configured>`
- Consumer Secret: `<to-be-configured>`

### Cloudflare
- Account ID: `d6d3df04acc98efe34f43e42636a3dfc`
- KV Namespace: `<uk-kv-id>`
- R2 Bucket: `hercules-uk-form-uploads`

## Routing Rules

**Astro (Static) Routes:**
- `/` - Homepage
- `/collections/*` - Category pages
- `/products/*` - Product pages
- `/blogs/*` - Blog articles
- `/wishlist` - Wishlist

**WordPress (Dynamic) Routes:**
- `/cart` - Shopping cart
- `/checkout` - Checkout
- `/my-account` - User account
- `/contact` - Contact page
- `/quote-generator` - Quote requests
- `/wp-admin/*` - WordPress admin

## Common Commands

```bash
# Development
npm run dev

# Build
npm run build

# Deploy Workers
cd workers/edge-router && npx wrangler deploy
cd workers/product-sync && npx wrangler deploy
cd workers/form-handler && npx wrangler deploy

# Trigger product sync
curl https://hercules-product-sync-uk.<account>.workers.dev/trigger-sync

# Check sync status
curl https://hercules-product-sync-uk.<account>.workers.dev/status
```

## Related Sites
- Germany: https://hercules-merchandise.de
- France: https://hercules-merchandising.fr
- Netherlands: https://hercules-merchandise.nl
```

---

## PHASE 10: Data Sync Setup

### 10.1 Initial Product Sync

After deploying the product sync worker:

```bash
# Trigger initial full sync
curl -X POST https://hercules-product-sync-uk.<account>.workers.dev/sync

# Verify sync status
curl https://hercules-product-sync-uk.<account>.workers.dev/status

# Check product count
curl https://hercules-product-sync-uk.<account>.workers.dev/products | jq '.length'
```

### 10.2 WooCommerce Webhook Setup

In WordPress Admin → WooCommerce → Settings → Advanced → Webhooks:

| Webhook | Topic | Delivery URL |
|---------|-------|--------------|
| Product Created | Product created | `https://hercules-product-sync-uk.../webhook` |
| Product Updated | Product updated | `https://hercules-product-sync-uk.../webhook` |
| Product Deleted | Product deleted | `https://hercules-product-sync-uk.../webhook` |

---

## PHASE 11: Deployment Checklist

### 11.1 Pre-Deployment

- [ ] GitHub repo created and code pushed
- [ ] Cloudflare Pages project created
- [ ] All 3 workers deployed
- [ ] KV namespace created and bound
- [ ] R2 bucket created and bound
- [ ] Worker secrets configured (BREVO_API_KEY, WC keys)
- [ ] GitHub secrets configured

### 11.2 Initial Deployment

```bash
# 1. Deploy Edge Router
cd workers/edge-router
npx wrangler deploy

# 2. Deploy Product Sync
cd ../product-sync
npx wrangler deploy

# 3. Deploy Form Handler
cd ../form-handler
npx wrangler deploy

# 4. Trigger product sync
curl -X POST https://hercules-product-sync-uk.../sync

# 5. Push to main branch (triggers Pages deploy)
git push origin main
```

### 11.3 Post-Deployment Verification

- [ ] Homepage loads at staging URL
- [ ] Products display with correct GBP prices
- [ ] Collections pages work
- [ ] Product detail pages work
- [ ] Add to cart works (redirects to WordPress)
- [ ] Contact form submits successfully
- [ ] Newsletter signup works
- [ ] Language is all English
- [ ] Hreflang tags are correct

---

## Implementation Order

### Step 1: Infrastructure Setup
- Copy folder from `/home/kamindu/hercules-headless-live/` to `/home/kamindu/hercules-headless-uk/`
- Create GitHub repo `royboy31/hercules-live-uk`
- Create Cloudflare resources:
  - Pages project: `hercules-uk-staging`
  - KV namespace: `hercules-uk-products-kv`
  - R2 bucket: `hercules-uk-form-uploads`

### Step 2: WordPress Backend Setup
- Generate WooCommerce REST API credentials
- Install mu-plugins for headless API endpoints
- Configure menus with icons
- Set up Cart, Checkout, My Account pages
- Create Quote Generator and Contact pages
- Configure WooCommerce settings (GBP currency, UK shipping, VAT)

### Step 3: Configuration Updates
- Update `.env` with UK credentials
- Update `astro.config.mjs` with UK site URL
- Update `src/config/seo.ts` with UK locale/phone
- Update all worker wrangler.toml files
- Update edge router routes (German → English paths)

### Step 4: Language Localization
- Translate `src/data/menu-data.ts`
- Translate all component text (Footer, Header, forms)
- Translate email templates in `functions/lib/brevo.ts`
- Change currency symbol (€ → £) in all price displays

### Step 5: URL Structure Changes
- Rename `src/pages/produkte/` → `src/pages/products/`
- Rename `src/pages/kollektionen/` → `src/pages/collections/`
- Update hreflang mappings
- Configure 301 redirects in edge router

### Step 6: Create CLAUDE.md
- Write comprehensive CLAUDE.md for the UK project
- Include all URLs, credentials, architecture overview
- Document common commands and procedures

### Step 7: GitHub & CI/CD Setup
- Initialize git repo and push to GitHub
- Configure GitHub secrets
- Update deploy.yml workflow

### Step 8: Deploy Workers
```bash
cd workers/edge-router && npx wrangler deploy
cd workers/product-sync && npx wrangler deploy
cd workers/form-handler && npx wrangler deploy
```

### Step 9: Data Sync & Webhooks
- Trigger initial product sync
- Configure WooCommerce webhooks
- Verify product data in KV

### Step 10: Testing & Verification
- Full site walkthrough
- Test all forms (contact, newsletter, quote)
- Test cart/checkout flow
- Mobile responsiveness
- Cross-browser testing

---

## Files to Modify (Complete List)

### Configuration (5 files)
1. `.env`
2. `astro.config.mjs`
3. `src/config/seo.ts`
4. `package.json` (if needed)
5. `CLAUDE.md` (create new)

### Workers (6 files)
6. `workers/edge-router/wrangler.toml`
7. `workers/edge-router/src/index.ts`
8. `workers/product-sync/wrangler.toml`
9. `workers/product-sync/src/index.ts`
10. `workers/form-handler/wrangler.toml`
11. `workers/form-handler/src/index.ts`

### Components (12 files)
12. `src/components/Footer.astro`
13. `src/components/Header.astro`
14. `src/components/HerculesMerchandise.astro`
15. `src/components/MobileMenu.astro`
16. `src/components/StickyHeader.astro`
17. `src/components/ContactFormPopup.tsx`
18. `src/components/ProductConfigurator.tsx`
19. `src/components/CategoryProductCard.astro`
20. `src/components/ProductCard.astro`
21. `src/components/UserSession.tsx`
22. `src/components/NewsletterForm.astro`
23. `src/components/QuantityRequestForm.tsx`

### Data Files (3 files)
24. `src/data/menu-data.ts`
25. `src/data/hreflang-mappings.ts`
26. `src/data/homepage-products.json`

### Pages (5+ files)
27. `src/pages/index.astro`
28. `src/pages/wishlist.astro`
29. `src/pages/404.astro`
30. `src/pages/blogs/index.astro`
31. Rename: `src/pages/produkte/` → `src/pages/products/`
32. Rename: `src/pages/kollektionen/` → `src/pages/collections/`

### Functions (3 files)
33. `functions/lib/brevo.ts`
34. `functions/api/contact.ts`
35. `functions/api/newsletter.ts`

### CI/CD (1 file)
36. `.github/workflows/deploy.yml`

---

## Verification Steps

1. **Build Test:** `npm run build` completes without errors
2. **Dev Server:** `npm run dev` shows English content
3. **Worker Deploy:** All 3 workers deploy successfully
4. **Product Sync:** `/status` endpoint returns valid timestamp
5. **Pages Deploy:** GitHub push triggers successful deployment
6. **Site Test:** All pages load with English content and GBP prices
7. **Forms Test:** Contact and newsletter forms submit successfully
8. **Cart Test:** Add to cart redirects to WordPress cart
