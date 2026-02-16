# Hercules Merchandise UK - Astro Headless Site

> **IMPORTANT: Working Directory**
> This project folder is: `/home/kamindu/hercules-headless-uk/`
> All file edits, builds, and deployments must happen within this directory.

## Project Overview

Headless e-commerce site built with **Astro + React** frontend and **WordPress/WooCommerce** backend. Uses Cloudflare Workers for hybrid routing between static Astro pages and dynamic WordPress pages.

**Region:** United Kingdom
**Language:** English (en_GB)
**Currency:** GBP (£)
**Status:** LIVE (since 2026-02-15)

---

## Quick Reference — Production (LIVE)

### URLs

| Service | URL |
|---------|-----|
| Live Site | https://hercules-merchandise.co.uk |
| Astro Frontend (Pages) | https://hercules-uk.pages.dev |
| WordPress Admin | https://hercules-merchandise.co.uk/wp-admin/ |
| Edge Router Worker | https://hercules-edge-router-uk.gilles-86d.workers.dev (env: production) |
| Product Sync Worker | https://hercules-product-sync-uk.gilles-86d.workers.dev (env: production) |
| Form Handler Worker | https://hercules-form-handler-uk-production.gilles-86d.workers.dev |

### Cloudflare Resources (Production)

| Resource | Name / ID |
|----------|-----------|
| Pages Project | `hercules-uk` |
| Edge Router Worker | `hercules-edge-router-uk` (production env) |
| Product Sync Worker | `hercules-product-sync-uk` (production env) |
| Form Handler Worker | `hercules-form-handler-uk` (production env) |
| KV Namespace | ID: `82f827d101734ce38ccc89629c7ae919` |
| R2 Bucket | `hercules-uk-form-uploads-prod` |

### Production WordPress Database

```
Host: localhost
Database: wp_t5phs
Table Prefix: wp_1202943_
```

> **Note:** Production DB username/password are stored on the server's wp-config.php. Not documented here for security.

### Production Server Path

```
/var/www/vhosts/hercules-merchandise.co.uk/httpdocs/
```

---

## Quick Reference — Staging

### URLs

| Service | URL |
|---------|-----|
| Staging Site | https://staging.hercules-merchandise.co.uk |
| Astro Frontend (Pages) | https://hercules-uk-staging-e9z.pages.dev |
| WordPress Admin | https://staging.hercules-merchandise.co.uk/wp-admin/ |
| Edge Router Worker | https://hercules-edge-router-uk.gilles-86d.workers.dev (default env) |
| Product Sync Worker | https://hercules-product-sync-uk.gilles-86d.workers.dev (default env) |
| Form Handler Worker | https://hercules-form-handler-uk.gilles-86d.workers.dev |

### Cloudflare Resources (Staging)

| Resource | Name / ID |
|----------|-----------|
| Pages Project | `hercules-uk-staging-e9z` |
| Edge Router Worker | `hercules-edge-router-uk` (default env) |
| Product Sync Worker | `hercules-product-sync-uk` (default env) |
| Form Handler Worker | `hercules-form-handler-uk` (default env) |
| KV Namespace | ID: `50743a0e269f4450b61bb690847534c4` |
| R2 Bucket | `hercules-uk-form-uploads` |

### Staging WordPress Database

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

### Staging Server Path

```
/var/www/vhosts/hercules-merchandise.co.uk/staging.hercules-merchandise.co.uk/
```

---

## GitHub Repository

```
https://github.com/royboy31/hercules-live-uk (private)
```

### Branches

| Branch | Purpose |
|--------|---------|
| `main` | Development — staging deploys manually |
| `production` | Production — auto-deploys to Cloudflare Pages via GitHub Actions |
| `dinukshi` | Feature branch |
| `perf/optimize-pagespeed` | PageSpeed optimization experiments |

---

## SSH Access

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
| `httpdocs/wp-content/mu-plugins/` | Production mu-plugins |
| `staging.hercules-merchandise.co.uk/wp-content/mu-plugins/` | Staging mu-plugins |

---

## API Credentials

### Cloudflare (Gilles's Account)

```
CLOUDFLARE_ACCOUNT_ID=86dfa0e10ca766f79d5042548fc2776f
CLOUDFLARE_API_TOKEN=ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp
```

### WooCommerce API (UK Staging)

```
WC_STORE_URL=https://staging.hercules-merchandise.co.uk
WC_CONSUMER_KEY=ck_1a7f55f2e141324051c303319c56333c99cfdbb7
WC_CONSUMER_SECRET=cs_5c661d7c8609a28de94c4a2ba6921b90ad816731
```

> **Note:** Production WooCommerce API keys were generated on 2026-02-15 and are stored in GitHub Actions secrets and worker secrets. Not duplicated here.

### Webhook Secret

```
hercules-webhook-secret-uk-2024
```

### Chathive API Key (UK)

```
TVkvsqiY5b5yazDk8h18ThCT
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
│         hercules-edge-router-uk.gilles-86d.workers.dev         │
│              (routes hercules-merchandise.co.uk/*)              │
└─────────────────────────────────────────────────────────────────┘
                    │                       │
         Astro Routes                WordPress Routes
    /, /collections/*,           /cart, /checkout,
    /products/*, /blogs/*        /wp-admin/*, /wp-json/*
                    │                       │
                    ▼                       ▼
┌───────────────────────────────┐   ┌───────────────────────────────┐
│   Cloudflare Pages            │   │   WordPress (UK)              │
│   hercules-uk.pages.dev       │   │   hercules-merchandise.co.uk  │
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
- `/contact-us`, `/quote-generator` - Contact pages

---

## Deployment

### Production Deployment (via GitHub Actions)

Pushes to `production` branch auto-deploy to Cloudflare Pages (`hercules-uk`):

```bash
# Merge main into production and push
git checkout production
git merge main
git push origin production

# GitHub Actions will build and deploy automatically
```

### Check Deployment Status

```bash
gh run list --repo royboy31/hercules-live-uk --limit 5
gh run watch --repo royboy31/hercules-live-uk
```

### Deploy Workers Manually (Production)

```bash
# Edge Router
cd /home/kamindu/hercules-headless-uk/workers/edge-router
CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
npx wrangler deploy --env production

# Product Sync
cd /home/kamindu/hercules-headless-uk/workers/product-sync
CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
npx wrangler deploy --env production

# Form Handler
cd /home/kamindu/hercules-headless-uk/workers/form-handler
CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
npx wrangler deploy --env production
```

### Deploy Workers (Staging — default env)

```bash
# Same commands without --env production
cd /home/kamindu/hercules-headless-uk/workers/edge-router
CLOUDFLARE_API_TOKEN="ZN0wjGH08jqnYCOvlpNH5Y-z--3FeL-63fnLndQp" \
CLOUDFLARE_ACCOUNT_ID="86dfa0e10ca766f79d5042548fc2776f" \
npx wrangler deploy
```

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
│   │   ├── GoogleReviewsBadge.astro
│   │   ├── CategoryProductCard.astro
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
│   ├── deploy.sh
│   └── sync-products.sh
├── docs/
│   └── google-apps-script.js
├── wordpress-updates/       # PHP scripts for WP server changes
├── .github/workflows/
│   └── deploy.yml           # Auto-deploy on push to production branch
├── astro.config.mjs
├── package.json
├── .env
└── CLAUDE.md                # This file
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

### Test Product Sync

```bash
# Staging
curl "https://hercules-product-sync-uk.gilles-86d.workers.dev/status"

# Production
curl "https://hercules-product-sync-uk.gilles-86d.workers.dev/status" # (production env uses same base URL with --env flag)
```

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

## mu-plugins Installed

All 19 plugins installed on both **staging and production** WordPress:

| Plugin | Purpose |
|--------|---------|
| `hercules-session-api.php` | Cart/session sync |
| `hercules-cart-api.php` | Cart remove endpoint |
| `hercules-cart-contents-fix.php` | Cart contents fix |
| `hercules-wishlist-api.php` | Wishlist API |
| `hercules-category-api.php` | Category details |
| `hercules-main-header-menu-api.php` | Menu REST API |
| `hercules-menu-webhooks.php` | Auto-rebuild triggers |
| `hercules-menu-icon-field.php` | Menu icon support |
| `hercules-pearl-steps-api.php` | Product config data |
| `pearl-rest-api-meta.php` | Product pricing/PDFs |
| `hercules-sticky-header.php` | WP sticky header |
| `hercules-custom-header.php` | WP custom header (matching Astro) |
| `hercules-brevo-mailer.php` | Email via Brevo |
| `hercules-edge-router-cookies.php` | Cookie handling |
| `hercules-email-fixes.php` | Email fixes |
| `hercules-google-reviews-badge.php` | Google reviews |
| `hercules-mini-cart-override.php` | Mini cart |
| `hercules-post-webhooks.php` | Post webhooks |
| `hercules-prevent-duplicate-email.php` | Duplicate email prevention |
| `hercules-dynamic-menu-shortcode.php` | Dynamic menu shortcode |

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
- WebP versions served (2,207 images converted on 2026-02-14)
- No WordPress access needed at runtime
- Daily sync at 3 AM UTC

### Deployment Flow
```
Develop on main → merge to production → GitHub Actions → Cloudflare Pages (hercules-uk)
```

### WordPress Changes
When making changes on the WordPress server (mu-plugins, theme files, email templates), always:
1. Create a `.bak-YYYYMMDD` backup before modifying
2. Apply to **both staging and production** servers
3. Document in the daily progress file

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
| Germany | https://hercules-merchandise.de | Live (Headless Astro + WordPress) |
| United Kingdom | https://hercules-merchandise.co.uk | Live (Headless Astro + WordPress) |
| France | https://hercules-merchandising.fr | Live (WordPress only) |
| Netherlands | https://hercules-merchandise.nl | Planned |

---

## SSH Comparison: UK vs German (DE)

| Item | UK | German (DE) |
|------|-----|-------------|
| SSH Alias | `ssh combel-uk` | `ssh combel` |
| User | `hercules-merchandise_722hr56m9xy` | `kamindu-de` |
| SSH Key | `~/.ssh/hercules_uk_merchandise` | `~/.ssh/combel_kamindu` |
| Production Path | `/var/www/vhosts/hercules-merchandise.co.uk/httpdocs/` | `/var/www/vhosts/hercules-merchandise.de/httpdocs/` |
| Staging Path | `.../staging.hercules-merchandise.co.uk/` | `.../staging.hercules-merchandise.de/` |
| Astro Project | `/home/kamindu/hercules-headless-uk/` | `/home/kamindu/hercules-headless-live/` |

---

*Last updated: 2026-02-16*
