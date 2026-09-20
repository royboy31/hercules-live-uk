# WordPress plugin sources kept in this repo

The UK WordPress install is **not** a git checkout. `httpdocs/` has no `.git`, so anything
edited on the server exists only on the server. This folder is the canonical copy of the
WordPress-side files this project changes, so a change is not lost when someone edits the
same file from an older local copy.

**The live server is what runs. These files are snapshots taken at deploy time.**
Before editing any file below, diff your copy against the server first.

Server: `ssh hercules`, docroot `/var/www/vhosts/hercules-merchandise.co.uk/httpdocs/`

| File here | Lives on the server at |
|---|---|
| `hercules-purchase-event.php` | `wp-content/plugins/hercules-purchase-event.php` |
| `pearl-wc-steps-variation/includes/ajax-handler.php` | `wp-content/plugins/pearl-wc-steps-variation/includes/ajax-handler.php` |
| `pearl-wc-steps-variation/assets/js/quote-form.js` | `wp-content/plugins/pearl-wc-steps-variation/assets/js/quote-form.js` |
| `pearl-wc-steps-variation/includes/quote-shortcode.php` | `wp-content/plugins/pearl-wc-steps-variation/includes/quote-shortcode.php` |
| `hercules-category-meta.php` | `wp-content/mu-plugins/hercules-category-meta.php` |
| `hercules-consent-bridge.php` | `wp-content/mu-plugins/hercules-consent-bridge.php` |

`pearl-wc-steps-variation` is a custom plugin (no `Update URI`, not on wordpress.org), so
WordPress will never auto-update over these edits. The realistic way to lose them is a manual
upload from a stale copy, or a backup restore.

## Conversion tracking, added 2026-09-20

Three edits feed the Google Ads and GA4 conversions in container `GTM-TW5HR72`. **Removing any
of them silently stops a conversion from being counted. Nothing on the site breaks, so nobody
notices.** If you have to touch these files, keep the marked blocks.

1. `hercules-purchase-event.php` 1.2.0
   - `item_id` is the Merchant Center offer id (`gla_<variation id>`), not the SKU. Google Ads
     product reporting matches on the offer id; the SKU matched nothing.
   - `readCookie` wraps `decodeURIComponent` in a `try/catch`. Without it a malformed `cmplz_*`
     cookie throws and the whole purchase event is lost.
2. `ajax-handler.php`, the quote-creation `wp_send_json_success` response
   - returns `quote_subtotal` (ex tax, same basis as the purchase value) and `currency`.
3. `quote-form.js`
   - `pushQuoteLead()` pushes `generate_lead` / `lead_type: quote_generator` with `quote_id`,
     `value`, `currency`, called from both accepted-creation branches (first submit and the
     existing-account login retry), before the redirect to `/thank-you/`.
4. `quote-shortcode.php`
   - the `wp_enqueue_script('pearl-quote-form', ...)` version was bumped from the hardcoded
     `1.0` to `1.1`. It is hardcoded, so editing `quote-form.js` without bumping it here keeps
     serving the cached bundle and the change never reaches a returning visitor.

Pre-change backups of all four are on the server in
`/var/www/vhosts/hercules-merchandise.co.uk/backups/tracking-20260920/`.

## Consent bridge, added 2026-09-20

`hercules-consent-bridge.php` is a new mu-plugin. Before it, the served WordPress HTML carried
no `gtag('consent','default', ...)` at all: the banner asked, the choice was stored in the
`cmplz_*` cookies, and nothing acted on it, so Google Consent Mode treated storage as granted
whatever the visitor clicked. The three Complianz consent tags in `GTM-TW5HR72` (61, 47, 48)
are paused, so the container was not supplying the defaults either.

It declares the denied default at `wp_head` priority 1 (ahead of the container at 10), then
applies the recorded choice at priority 1000000 and on Complianz's own `cmplz_fire_categories`
and `cmplz_revoke` events, so a click on the banner takes effect in the same page load.

Verified on production 2026-09-20 in a clean browser profile: default `G100`, Accept produces a
granted `consent update` in the same page load and GA4 + Ads fire normally, Deny keeps `gcs=G100`
cookieless pings. **Rollback is deleting the file**; it writes no option and touches nothing else.
