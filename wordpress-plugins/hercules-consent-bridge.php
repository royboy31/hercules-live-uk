<?php
/**
 * Plugin Name: Hercules consent bridge UK (site-wide)
 * Description: Sets the Google Consent Mode default on the WordPress pages, then carries the
 *              visitor's recorded Complianz choice into it, both on page load and the moment
 *              the banner is answered. Ported from the FR mu-plugin of the same name, with the
 *              live-banner listener added because UK, unlike FR, does have a Complianz banner
 *              on the money pages.
 *
 * WHY THIS IS NEEDED ON UK
 *
 * The served HTML of /cart/, /thank-you/, /quote-generator/ and /my-account/ contains no
 * `gtag('consent','default', ...)` at all (checked 2026-09-20, origin response, x-cache-status
 * MISS). The Astro pages set one inline in BaseLayout.astro; the WordPress pages do not.
 * Complianz is configured with consent-mode on, but it delegates the defaults to its GTM
 * template, and Astra found tag 61 "Complianz consent", tag 47 "Consent Mode" and tag 48
 * "Cookie Banner" all PAUSED in container GTM-TW5HR72 (live version 59).
 *
 * With no default, Google Consent Mode treats storage as granted. So on the UK money path the
 * banner is displayed, the choice is recorded in the cmplz_* cookies, and nothing acts on it.
 *
 * ORDERING on wp_head, verified by dumping $wp_filter on the live site:
 *
 *   1      this file, `consent default`
 *   10     CustomCSSandJS::print_frontend-html-header-both -> the GTM-TW5HR72 container snippet
 *   10     Complianz -> banner CSS, and its own copy of the container snippet later in the body
 *   1e6    this file, `consent update` with the visitor's recorded choice
 *
 * The default has to be earlier than 10 or the container's first tags run unconfigured.
 *
 * ROLLBACK: delete this file. It adds two inline scripts and nothing else; no option is
 * written, no other plugin is touched.
 */

defined( 'ABSPATH' ) || exit;

// Denied default, ahead of the container. Region list matches the FR bridge, which is the
// EU/EEA plus GB and CH, i.e. the scope of Google's EU user consent policy.
add_action(
	'wp_head',
	function () {
		?>
<!-- Hercules consent default, ahead of the GTM container -->
<script>
window.dataLayer = window.dataLayer || [];
if ( typeof window.gtag !== 'function' ) {
	window.gtag = function () { window.dataLayer.push( arguments ); };
}
window.gtag( 'consent', 'default', {
	analytics_storage: 'denied',
	ad_storage: 'denied',
	ad_user_data: 'denied',
	ad_personalization: 'denied',
	region: ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IE', 'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'CH'],
	wait_for_update: 500
} );
</script>
		<?php
	},
	1
);

add_action(
	'wp_head',
	function () {
		?>
<!-- Hercules consent bridge -->
<script>
(function () {
	function readCookie( name ) {
		var prefix = name + '=';
		var parts  = document.cookie.split( ';' );

		for ( var index = 0; index < parts.length; index += 1 ) {
			var part = parts[ index ].trim();

			if ( part.indexOf( prefix ) === 0 ) {
				var raw = part.substring( prefix.length );

				// A malformed percent sequence makes decodeURIComponent throw. Treat it as an
				// unrecognized value rather than letting it abort the whole bridge.
				try {
					return decodeURIComponent( raw );
				} catch ( e ) {
					return raw;
				}
			}
		}

		return '';
	}

	function ensureGtag() {
		if ( typeof window.gtag !== 'function' ) {
			window.dataLayer = window.dataLayer || [];
			window.gtag = function () { window.dataLayer.push( arguments ); };
		}
	}

	function update( statisticsAllowed, marketingAllowed ) {
		ensureGtag();
		window.gtag( 'consent', 'update', {
			analytics_storage:  statisticsAllowed ? 'granted' : 'denied',
			ad_storage:         marketingAllowed  ? 'granted' : 'denied',
			ad_user_data:       marketingAllowed  ? 'granted' : 'denied',
			ad_personalization: marketingAllowed  ? 'granted' : 'denied'
		} );
	}

	// 1. A choice already recorded in a previous visit, or on an Astro page in this browser.
	//    Astro's CookieConsent.tsx writes the same cmplz_* cookies with path=/, so the two
	//    stacks share one decision.
	var statistics = readCookie( 'cmplz_statistics' );
	var marketing  = readCookie( 'cmplz_marketing' );

	// No choice recorded yet: leave the denied default in place, do not decide for the visitor.
	if ( statistics !== '' || marketing !== '' ) {
		update( statistics === 'allow', marketing === 'allow' );
	}

	// 2. The visitor answers the banner during this page load. Without this the choice would
	//    only take effect on the next navigation, which on the money path can be the order
	//    confirmation, i.e. one page too late.
	//    cmplz_fire_categories is Complianz's own event on the WordPress pages.
	document.addEventListener( 'cmplz_fire_categories', function ( event ) {
		var categories = ( event.detail && event.detail.categories ) || [];
		update( categories.indexOf( 'statistics' ) !== -1, categories.indexOf( 'marketing' ) !== -1 );
	} );

	//    cmplz_consent_updated is dispatched by the Astro banner (src/components/CookieConsent.tsx),
	//    harmless here, present so one file covers both banners.
	window.addEventListener( 'cmplz_consent_updated', function ( event ) {
		var detail = event.detail || {};
		update( !! detail.statistics, !! detail.marketing );
	} );

	// 3. The visitor withdraws consent from the banner's revoke link. Complianz fires this
	//    instead of cmplz_fire_categories, so without it the grant would stand until the next
	//    page load. Same event its own consent-mode template listens to.
	document.addEventListener( 'cmplz_revoke', function () {
		update( false, false );
	} );
})();
</script>
		<?php
	},
	1000000
);
