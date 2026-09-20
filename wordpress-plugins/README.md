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
