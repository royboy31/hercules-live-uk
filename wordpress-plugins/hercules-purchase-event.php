<?php
/**
 * Plugin Name: Hercules WooCommerce Purchase Event
 * Description: Sends one GA4-style gtag purchase event from the order-received page for the GTM purchase tags to trigger on. Deactivate to stop it.
 * Version: 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Order statuses that must NOT count as a purchase.
 *
 * 'on-hold' is deliberately absent: offline payment methods (bank transfer)
 * create accepted orders in that status. Add 'on-hold' here if only captured
 * online payments should count as conversions.
 */
function hercules_purchase_blocked_statuses() {
    return apply_filters(
        'hercules_purchase_blocked_statuses',
        array( 'failed', 'cancelled', 'refunded', 'pending' )
    );
}

/**
 * True while the old baseline mu-plugin still owns the purchase event.
 *
 * wc-ga4-gtag-baseline.php fires its own purchase on woocommerce_thankyou.
 * Emitting ours as well would send two purchase events for one order, so we
 * stand down until that block is removed and WC_GA4_PURCHASE_DISABLED defined.
 */
function hercules_purchase_conflict() {
    return function_exists( 'wcga4_print_footer_js' ) && ! defined( 'WC_GA4_PURCHASE_DISABLED' );
}

add_action( 'admin_notices', function () {
    if ( ! hercules_purchase_conflict() ) return;
    echo '<div class="notice notice-warning"><p><strong>Hercules Purchase Event</strong> is active but sending nothing: '
       . '<code>wp-content/mu-plugins/wc-ga4-gtag-baseline.php</code> still fires its own purchase event. '
       . 'Remove that block and <code>define( \'WC_GA4_PURCHASE_DISABLED\', true );</code> to hand over.</p></div>';
} );

/**
 * Resolve the order behind the current order-received page.
 *
 * We deliberately do NOT use the woocommerce_thankyou action. The checkout page
 * on all three Hercules sites is an Elementor page built on the Elementor Pro
 * woocommerce-checkout-page widget, which renders the order-received view itself
 * and never fires that action. The theme's own custom_thank_you_message, hooked
 * at priority 5, produces no output there either. Reading the endpoint from
 * wp_footer is what Google's own Listings & Ads plugin does, and it works whether
 * Elementor or the WooCommerce core template renders the page.
 *
 * @return WC_Order|false
 */
function hercules_purchase_current_order() {
    global $wp;

    if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
        return false;
    }

    $order_id = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
    $order_id = absint( apply_filters( 'woocommerce_thankyou_order_id', $order_id ) );

    if ( ! $order_id || ! function_exists( 'wc_get_order' ) ) {
        return false;
    }

    $order = wc_get_order( $order_id );

    if ( ! $order ) {
        return false;
    }

    // Validate the key the way WooCommerce does, so a guessed order ID can never
    // put someone else's order values into dataLayer.
    $order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : '';
    $order_key = apply_filters( 'woocommerce_thankyou_order_key', $order_key );

    if ( ! $order_key || ! hash_equals( (string) $order->get_order_key(), (string) $order_key ) ) {
        return false;
    }

    return $order;
}

add_action( 'wp_footer', 'hercules_output_purchase_event', 20 );

function hercules_output_purchase_event() {
    static $done = false;

    if ( $done ) {
        return;
    }

    $done = true;

    $order = hercules_purchase_current_order();

    if ( ! $order ) {
        return;
    }

    if ( hercules_purchase_conflict() ) {
        echo PHP_EOL . '<!-- Hercules purchase event skipped: wc-ga4-gtag-baseline.php still owns it -->' . PHP_EOL;
        return;
    }

    if ( $order->has_status( hercules_purchase_blocked_statuses() ) ) {
        return;
    }

    $items = array();
    $value = 0.0;

    foreach ( $order->get_items() as $item ) {
        $quantity = max( 1, (int) $item->get_quantity() );

        // Google Ads product reporting matches item_id against the Merchant Center offer id, which
        // Google for WooCommerce builds as gla_<variation id, or product id for a simple product>.
        // Changed 2026-09-20 from the SKU, which matched nothing in Merchant Center. Same change as
        // FR 1.2.0 (2026-09-17). Verified on UK before shipping: the 12 most recent order lines all
        // produce an id present in _wc_gla_google_ids.
        $offer_product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
        $adapter          = '\\Automattic\\WooCommerce\\GoogleListingsAndAds\\Product\\WCProductAdapter';

        $item_id = class_exists( $adapter )
            ? $adapter::get_google_product_offer_id( 'gla', (int) $offer_product_id )
            : 'gla_' . $offer_product_id;

        $event_item = array(
            'item_id'   => (string) $item_id,
            'item_name' => wp_strip_all_tags( $item->get_name() ),
            'price'     => (float) wc_format_decimal( $item->get_total() / $quantity, wc_get_price_decimals() ),
            'quantity'  => $quantity,
        );

        if ( $item->get_variation_id() ) {
            $event_item['item_variant'] = (string) $item->get_variation_id();
        }

        $value  += (float) $item->get_total();
        $items[] = $event_item;
    }

    // value = sum of the discounted line totals, excluding tax and shipping,
    // which are sent as their own fields (GA4 purchase spec).
    // Nothing here carries a customer name, email, phone or address.
    $payload = array(
        'transaction_id' => (string) $order->get_order_number(),
        'value'          => (float) wc_format_decimal( $value, wc_get_price_decimals() ),
        'currency'       => $order->get_currency(),
        'tax'            => (float) $order->get_total_tax(),
        'shipping'       => (float) $order->get_shipping_total(),
        'coupon'         => implode( ',', $order->get_coupon_codes() ),
        'items'          => $items,
    );
    ?>
    <!-- Hercules purchase event v1.2 -->
    <script>
    (function () {
        var payload = <?php echo wp_json_encode( $payload ); ?>;
        var storageKey = 'hercules_purchase_sent_' + payload.transaction_id;

        try {
            if (window.localStorage.getItem(storageKey)) {
                return;
            }
        } catch (error) {
            // Storage unavailable. The stable transaction ID still lets GA4 and
            // Google Ads deduplicate on their side.
        }

        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () {
            window.dataLayer.push(arguments);
        };

        function readCookie(name) {
            var prefix = name + '=';
            var parts = document.cookie.split(';');

            for (var index = 0; index < parts.length; index += 1) {
                var part = parts[index].trim();

                if (part.indexOf(prefix) === 0) {
                    var raw = part.substring(prefix.length);

                    // A malformed percent sequence makes decodeURIComponent throw, which would
                    // abort this whole IIFE before the purchase push. Treat it as an
                    // unrecognized value instead of losing the conversion.
                    try {
                        return decodeURIComponent(raw);
                    } catch (error) {
                        return raw;
                    }
                }
            }

            return '';
        }

        var marketingConsent  = readCookie('cmplz_marketing') === 'allow';
        var statisticsConsent = readCookie('cmplz_statistics') === 'allow';

        window.gtag('consent', 'update', {
            analytics_storage:  statisticsConsent ? 'granted' : 'denied',
            ad_storage:         marketingConsent ? 'granted' : 'denied',
            ad_user_data:       marketingConsent ? 'granted' : 'denied',
            ad_personalization: marketingConsent ? 'granted' : 'denied'
        });

        window.gtag('event', 'purchase', payload);

        try {
            window.localStorage.setItem(storageKey, '1');
        } catch (error) {
            // No action needed.
        }
    }());
    </script>
    <?php
}
