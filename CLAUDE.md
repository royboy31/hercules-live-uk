# Hercules Merchandise UK - Astro Headless Site

> **IMPORTANT: Working Directory**
> This project folder is: `/home/kamindu/hercules-headless-uk/`
> All file edits, builds, and deployments must happen within this directory.

## Project Overview

Headless e-commerce site built with **Astro + React** frontend and **WordPress/WooCommerce** backend. Uses Cloudflare Workers for hybrid routing between static Astro pages and dynamic WordPress pages.

**Region:** United Kingdom
**Language:** English (en_GB)
**Currency:** GBP (£)

---

## Quick Reference

### URLs

| Environment | URL |
|-------------|-----|
| Staging Site | https://staging.hercules-merchandise.co.uk |
| Astro Pages (Staging) | https://hercules-uk-staging.pages.dev (to be deployed) |
| WordPress Backend | https://staging.hercules-merchandise.co.uk/wp-admin/ |
| Edge Router Worker | https://hercules-edge-router-uk.workers.dev (to be deployed) |
| Product Sync Worker | https://hercules-product-sync-uk.workers.dev (to be deployed) |
| Form Handler Worker | https://hercules-form-handler-uk.workers.dev (to be deployed) |

### GitHub Repository

```
https://github.com/royboy31/hercules-live-uk (to be created)
```

### Cloudflare Resources

| Resource | Name |
|----------|------|
| Pages Project | hercules-uk-staging (to be created) |
| Edge Router Worker | hercules-edge-router-uk (to be deployed) |
| Product Sync Worker | hercules-product-sync-uk (to be deployed) |
| Form Handler Worker | hercules-form-handler-uk (to be deployed) |
| KV Namespace | hercules-uk-products-kv (to be created) |
| R2 Bucket | hercules-uk-form-uploads (to be created) |

---

## SSH Access

### UK Staging Server

```bash
# Quick access (configured in ~/.ssh/config)
ssh combel-uk

# Full command
ssh -i ~/.ssh/hercules_uk_merchandise hercules-merchandise_722hr56m9xy@136.144.235.35
```

**SSH Config Entry (~/.ssh/config):**
```
Host combel-uk
    HostName 136.144.235.35
    User hercules-merchandise_722hr56m9xy
    Port 22
    IdentityFile ~/.ssh/hercules_uk_merchandise
    StrictHostKeyChecking no
    UserKnownHostsFile /dev/null
```

### Server Paths

| Path | Description |
|------|-------------|
| `/var/www/vhosts/hercules-merchandise.co.uk/` | UK site root |
| `/var/www/vhosts/hercules-merchandise.co.uk/httpdocs/` | Production WordPress |
| `/var/www/vhosts/hercules-merchandise.co.uk/staging.hercules-merchandise.co.uk/` | Staging WordPress |
| `staging.hercules-merchandise.co.uk/wp-content/mu-plugins/` | mu-plugins folder |
| `staging.hercules-merchandise.co.uk/wp-content/themes/` | Themes folder |

---

## Database Credentials

### UK Staging Database

```
Host: localhost
Database: wp_xpq9e
Username: wp_5fpv9
Password: 0_Jr5A8Zj6k^0D&W
Table Prefix: wp_1202943_
```

**Quick MySQL Access:**
```bash
ssh combel-uk "mysql -u wp_5fpv9 -p'0_Jr5A8Zj6k^0D&W' wp_xpq9e"
```

**Common Queries:**
```bash
# Product count
ssh combel-uk "mysql -u wp_5fpv9 -p'0_Jr5A8Zj6k^0D&W' wp_xpq9e -e \"SELECT COUNT(*) FROM wp_1202943_posts WHERE post_type='product' AND post_status='publish';\""

# Check site URL
ssh combel-uk "mysql -u wp_5fpv9 -p'0_Jr5A8Zj6k^0D&W' wp_xpq9e -e \"SELECT option_value FROM wp_1202943_options WHERE option_name IN ('siteurl','home');\""
```

---

## API Credentials

### Cloudflare

```
CLOUDFLARE_ACCOUNT_ID=d6d3df04acc98efe34f43e42636a3dfc
CLOUDFLARE_API_TOKEN=<to-be-configured>
```

### WooCommerce API (UK Store)

```
WC_STORE_URL=https://staging.hercules-merchandise.co.uk
WC_CONSUMER_KEY=<to-be-generated>
WC_CONSUMER_SECRET=<to-be-generated>
```

**To generate WooCommerce API keys:**
1. Go to: https://staging.hercules-merchandise.co.uk/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys
2. Click "Add key"
3. Description: "Hercules Headless UK"
4. User: Admin
5. Permissions: Read/Write
6. Generate and save keys

### Webhook Secret

```
hercules-webhook-secret-uk-2024
```

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     User Browser                                │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   Cloudflare Edge Router                        │
│              (hercules-edge-router-uk.workers.dev)              │
└─────────────────────────────────────────────────────────────────┘
                    │                       │
         Astro Routes                WordPress Routes
    /, /collections/*,           /cart, /checkout,
    /products/*, /blogs/*        /wp-admin/*, /wp-json/*
                    │                       │
                    ▼                       ▼
┌───────────────────────────────┐   ┌───────────────────────────────┐
│   Cloudflare Pages            │   │   WordPress (UK)              │
│   hercules-uk-staging.pages   │   │   staging.hercules-merchandise│
│   .dev                        │   │   .co.uk                      │
└───────────────────────────────┘   └───────────────────────────────┘
```

### Edge Router Routing

**Astro (Static):**
- `/` - Homepage
- `/collections/*` - Category pages
- `/products/*` - Product detail pages
- `/blogs/*` - Blog pages
- `/wishlist` - Wishlist page

**WordPress (Dynamic):**
- `/cart`, `/checkout` - Shopping cart/checkout
- `/my-account` - User account
- `/wp-admin/*` - Admin panel
- `/wp-json/*` - REST API
- `/contact`, `/quote-generator` - Contact pages

---

## Project Structure

```
hercules-headless-uk/
├── src/
│   ├── components/          # Astro & React components
│   │   ├── Header.astro
│   │   ├── StickyHeader.astro
│   │   ├── MobileMenu.astro
│   │   ├── ProductConfigurator.tsx
│   │   ├── ProductSearch.tsx
│   │   ├── UserSession.tsx
│   │   ├── WishlistButton.tsx
│   │   ├── ContactFormPopup.tsx
│   │   └── ...
│   ├── pages/
│   │   ├── index.astro
│   │   ├── collections/[slug].astro
│   │   ├── products/[slug].astro
│   │   ├── blogs/[slug].astro
│   │   ├── wishlist.astro
│   │   └── 404.astro
│   ├── layouts/
│   │   └── BaseLayout.astro
│   ├── config/
│   │   ├── seo.ts
│   │   └── hreflang-mappings.ts
│   ├── data/
│   │   ├── menu-data.ts
│   │   └── homepage-products.json
│   └── styles/
│       ├── global.css
│       ├── fonts.css
│       └── steps.css
├── workers/
│   ├── edge-router/         # Hybrid routing worker
│   ├── product-sync/        # WooCommerce sync worker
│   └── form-handler/        # Contact form worker
├── functions/               # Cloudflare Pages Functions
│   └── api/
│       ├── contact.ts
│       └── newsletter.ts
├── public/
│   ├── images/
│   └── fonts/
├── scripts/
│   ├── deploy.sh            # Deployment script
│   └── sync-products.sh     # Product sync script
├── .github/workflows/
│   └── deploy.yml           # Auto-deploy on push
├── astro.config.mjs
├── package.json
├── .env
├── CLAUDE.md                # This file
└── UK-STAGING-IMPLEMENTATION-PLAN.md
```

---

## Deployment

### Standard Deployment (GitHub)

```bash
# Stage, commit, and push
git add -A && git commit -m "Your message" && git push origin main

# GitHub Actions will automatically build and deploy
```

### Check Deployment Status

```bash
gh run list --limit 5
gh run watch
```

### Deploy Workers Manually

```bash
# Edge Router
cd /home/kamindu/hercules-headless-uk/workers/edge-router
npx wrangler deploy

# Product Sync
cd /home/kamindu/hercules-headless-uk/workers/product-sync
npx wrangler deploy

# Form Handler
cd /home/kamindu/hercules-headless-uk/workers/form-handler
npx wrangler deploy
```

---

## WordPress REST API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/wp-json/hercules/v1/session` | GET | Cart count, user state |
| `/wp-json/hercules/v1/cart/remove` | POST | Remove cart item |
| `/wp-json/hercules/v1/wishlist` | GET | Get wishlist |
| `/wp-json/hercules/v1/wishlist/toggle` | POST | Add/remove wishlist |
| `/wp-json/hercules/v1/categories` | GET | All categories |
| `/wp-json/hercules/v1/category/{slug}` | GET | Single category |
| `/wp-json/hercules/v1/main-header-menu` | GET | Menu structure |
| `/wp-json/hercules/v1/product-config/{id}` | GET | Product config by ID |
| `/wp-json/hercules/v1/product-config-by-slug/{slug}` | GET | Product config by slug |

**Note:** These endpoints require the Hercules mu-plugins to be installed.

---

## Product Sync Worker API

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/products` | GET | All products |
| `/product/{slug}` | GET | Single product |
| `/categories` | GET | All categories |
| `/category/{slug}` | GET | Single category |
| `/search?q={query}` | GET | Product search |
| `/image/{slug}` | GET | Cached product image |
| `/status` | GET | Last sync time |
| `/sync` | POST | Trigger full sync |
| `/trigger-rebuild` | POST | Trigger GitHub Actions |

---

## Regional Settings

| Setting | Value |
|---------|-------|
| Locale | en_GB |
| Language | English |
| Currency | GBP (£) |
| Currency Position | Left (£99.99) |
| Decimal Separator | Period (.) |
| Thousand Separator | Comma (,) |
| VAT Rate | 20% |
| Timezone | GMT/BST |

---

## mu-plugins Installed ✅

All plugins are installed in `/wp-content/mu-plugins/` on the UK staging WordPress (installed 2026-02-03):

| Plugin | Purpose | Status |
|--------|---------|--------|
| `hercules-session-api.php` | Cart/session sync | ✅ Installed |
| `hercules-cart-api.php` | Cart remove endpoint | ✅ Installed |
| `hercules-cart-contents-fix.php` | Cart contents fix | ✅ Installed |
| `hercules-wishlist-api.php` | Wishlist API | ✅ Installed |
| `hercules-category-api.php` | Category details | ✅ Installed |
| `hercules-main-header-menu-api.php` | Menu REST API | ✅ Installed |
| `hercules-menu-webhooks.php` | Auto-rebuild triggers | ✅ Installed |
| `hercules-menu-icon-field.php` | Menu icon support | ✅ Installed |
| `hercules-pearl-steps-api.php` | Product config data | ✅ Installed |
| `pearl-rest-api-meta.php` | Product pricing/PDFs | ✅ Installed |
| `hercules-sticky-header.php` | WP sticky header | ✅ Installed |
| `hercules-brevo-mailer.php` | Email via Brevo | ✅ Installed |
| `hercules-edge-router-cookies.php` | Cookie handling | ✅ Installed |
| `hercules-email-fixes.php` | Email fixes | ✅ Installed |
| `hercules-google-reviews-badge.php` | Google reviews | ✅ Installed |
| `hercules-mini-cart-override.php` | Mini cart | ✅ Installed |
| `hercules-post-webhooks.php` | Post webhooks | ✅ Installed |
| `hercules-prevent-duplicate-email.php` | Duplicate email prevention | ✅ Installed |
| `hercules-dynamic-menu-shortcode.php` | Dynamic menu shortcode | ✅ Installed |

**Verified:** All REST API endpoints working (session, categories, menu, product-config)

---

## Important Notes

### Session/Cookie Handling
- Cloudflare APO strips WooCommerce cookies
- Edge Router copies cookies to `X-Edge-Cookies` header
- WordPress mu-plugin restores cookies from this header
- Cart sync only works via Edge Router (same domain)

### Menu Sync
- Menu data fetched at Astro build time
- Changes require site rebuild
- `hercules-menu-webhooks.php` auto-triggers rebuild

### Image Caching
- All product images cached in Cloudflare KV
- No WordPress access needed at runtime
- Daily sync scheduled

---

## Development

### Local Dev Server

```bash
cd /home/kamindu/hercules-headless-uk
npm run dev
# http://localhost:4321
```

### Build

```bash
npm run build
```

### Test Product Sync (after worker deployment)

```bash
# Check status
curl "https://hercules-product-sync-uk.workers.dev/status"

# Trigger sync
curl -X POST "https://hercules-product-sync-uk.workers.dev/sync"
```

---

## Design Values

### Colors
- Primary: `#253461`
- Accent/CTA: `#10C99E`
- Secondary Blue: `#469ADC`
- Link Blue: `#00AEEF`

### Typography
- Headings: Jost (500-600 weight)
- Body: Roboto (400 weight)
- Nav: Jost 15px, 500 weight, uppercase

### Layout
- Container: 1280px max-width
- Border radius: 15-20px (cards), 50px (buttons)

---

## Related Sites

| Region | URL | Status |
|--------|-----|--------|
| Germany | https://hercules-merchandise.de | Live |
| United Kingdom | https://hercules-merchandise.co.uk | Live (WordPress only) |
| France | https://hercules-merchandising.fr | Live (WordPress only) |
| Netherlands | https://hercules-merchandise.nl | Planned |

---

## Comparison: German vs UK SSH

| Item | German (DE) | UK |
|------|-------------|-----|
| SSH Alias | `ssh combel` | `ssh combel-uk` |
| User | `kamindu-de` | `hercules-merchandise_722hr56m9xy` |
| SSH Key | `~/.ssh/combel_kamindu` | `~/.ssh/hercules_uk_merchandise` |
| Site Path | `/var/www/vhosts/hercules-merchandise.de/` | `/var/www/vhosts/hercules-merchandise.co.uk/` |
| Staging Path | `staging.hercules-merchandise.de/` | `staging.hercules-merchandise.co.uk/` |

---

*Last updated: 2026-02-03*
