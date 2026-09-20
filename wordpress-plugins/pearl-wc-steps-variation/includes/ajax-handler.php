<?php
// Ensure this file is executed within WordPress.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Find matching variation and calculate prorated price.
 */
add_action('wp_ajax_pearl_wc_find_variation', 'pearl_wc_find_variation');
add_action('wp_ajax_nopriv_pearl_wc_find_variation', 'pearl_wc_find_variation');

include_once('pdf-generator.php');

function pearl_wc_find_variation()
{
    // Verify the nonce.
    check_ajax_referer('pearl_wc_nonce', 'nonce');

    $product_id   = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $attributes   = isset($_POST['attributes']) ? json_decode(stripslashes($_POST['attributes']), true) : [];
    $selected_qty = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;

    $product = wc_get_product($product_id);
    if (! $product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid product or not variable.');
    }

    $variation_id = $product->get_matching_variation($attributes);
    if ($variation_id) {
        $variation = wc_get_product($variation_id);

        $conditional_prices = get_post_meta($variation_id, '_conditional_prices', true);
        $lead_time = get_post_meta($variation_id, '_lead_time', true);

        if (!is_array($conditional_prices)) {
            $conditional_prices = array();
        }

        $prorated_price = false;
        if ($selected_qty > 0 && !empty($conditional_prices)) {
            usort($conditional_prices, function ($a, $b) {
                return intval($a['qty']) - intval($b['qty']);
            });
            $below = null;
            $above = null;
            $exact_match = false;
            foreach ($conditional_prices as $row) {
                $row_qty = floatval($row['qty']);
                if ($row_qty === $selected_qty) {
                    $prorated_price = floatval($row['price']);
                    $exact_match = true;
                    break;
                }
                if ($row_qty < $selected_qty) {
                    $below = $row;
                }
                if ($row_qty > $selected_qty) {
                    $above = $row;
                    break;
                }
            }
            if (!$exact_match) {
                if ($below && $above && ($above['qty'] - $below['qty']) != 0) {
                    $priceA    = floatval($below['price']);
                    $priceB    = floatval($above['price']);
                    $below_qty = floatval($below['qty']);
                    $above_qty = floatval($above['qty']);
                    $prorated_price = $priceA + (($priceB - $priceA) * ($selected_qty - $below_qty)) / ($above_qty - $below_qty);
                } elseif ($below) {
                    $prorated_price = floatval($below['price']);
                } elseif ($above) {
                    $prorated_price = floatval($above['price']);
                }
            }
        }

        $final_price_html = $prorated_price !== false ? wc_price($prorated_price) : $variation->get_price_html();

        wp_send_json_success([
            'variation_id'       => $variation_id,
            'price'              => $final_price_html,
            'price_num'          => floatval($prorated_price),
            'attributes'         => $variation->get_variation_attributes(),
            'conditional_prices' => $conditional_prices,
            'lead_time' => $lead_time
        ]);
    } else {
        wp_send_json_error('No matching variation found.' . json_encode($attributes));
    }
}

/**
 * Add to cart with custom price.
 */
add_action('wp_ajax_pearl_wc_add_to_cart_custom', 'pearl_wc_add_to_cart_custom');
add_action('wp_ajax_nopriv_pearl_wc_add_to_cart_custom', 'pearl_wc_add_to_cart_custom');

function pearl_wc_add_to_cart_custom() {

    // === Same-origin guard (Origin preferred, Referer fallback) ===
    $site_url   = get_site_url(null, '', is_ssl() ? 'https' : 'http');
    $site_host  = wp_parse_url($site_url, PHP_URL_HOST);
    $site_scheme= wp_parse_url($site_url, PHP_URL_SCHEME);

    $is_same_origin = false;

    if ( ! empty($_SERVER['HTTP_ORIGIN']) ) {
        $origin_host   = wp_parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
        $origin_scheme = wp_parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_SCHEME);
        if ( $origin_host === $site_host && $origin_scheme === $site_scheme ) {
            $is_same_origin = true;
        }
    }

    if ( ! $is_same_origin && ! empty($_SERVER['HTTP_REFERER']) ) {
        $ref_host   = wp_parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        $ref_scheme = wp_parse_url($_SERVER['HTTP_REFERER'], PHP_URL_SCHEME);
        if ( $ref_host === $site_host && $ref_scheme === $site_scheme ) {
            $is_same_origin = true;
        }
    }

    if ( ! $is_same_origin ) {
        wp_send_json_error(
            array( 'message' => 'Forbidden: cross-origin request blocked.' ),
            403
        );
    }

    // Remove any forced 403 testing lines if present.
    // (Your previous test block has been omitted.)

    // --- Handler logic continues ---
    error_log('pearl_wc_add_to_cart_custom nw logic POST: ' . print_r($_POST, true));

    $product_id   = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $quantity     = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 1;
    $custom_price = isset($_POST['price_num']) ? floatval($_POST['price_num']) : 0;

    $addons = array();
    if ( isset($_POST['addons']) ) {
        $raw = is_string($_POST['addons']) ? stripslashes($_POST['addons']) : $_POST['addons'];
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        $addons = is_array($decoded) ? $decoded : array();
    }

    $addon_price_per_piece = isset($_POST['addonsPricePerpiece']) ? floatval($_POST['addonsPricePerpiece']) : 0;
    $min_Qty               = isset($_POST['minQty']) ? floatval($_POST['minQty']) : 1;

    $product = wc_get_product($product_id);
    if (! $product) {
        error_log('Product not found.');
        wp_send_json_error('Product not found.');
    }

    $variation = wc_get_product($variation_id);
    if (! $variation) {
        error_log('Variation not found.');
        wp_send_json_error('Variation not found.');
    }

    if (! WC()->cart) {
        error_log('Cart not initialized.');
        wp_send_json_error('Cart not initialized.');
    }

    $variation_attributes = $variation->get_variation_attributes();
    error_log('Variation Attributes: ' . print_r($variation_attributes, true));

    $cart_item_data = array(
        'custom_price'           => $custom_price,
        'addons'                 => $addons,
        'addon_price_per_piece'  => $addon_price_per_piece,
        'min_qty'                => $min_Qty,
    );

    $cart_item_key = WC()->cart->add_to_cart(
        $product_id,
        $quantity,
        $variation_id,
        $variation_attributes,
        $cart_item_data
    );

    if ($cart_item_key) {
        error_log('Added to cart with new logic. Cart item key: ' . $cart_item_key);
        WC()->cart->calculate_totals();
        WC()->session->save_data();
        wp_send_json_success('Product added to cart with custom price.');
    } else {
        error_log('Failed to add product to cart. Cart data: ' . print_r(WC()->cart->get_cart(), true));
        wp_send_json_error('Failed to add product to cart.');
    }
}

// ===============================fallback rest api call =========================


// Register REST route
add_action('rest_api_init', function () {
    register_rest_route('pearl/v1', '/add-to-cart', array(
        'methods'             => WP_REST_Server::CREATABLE, // POST
        'callback'            => 'pearl_rest_add_to_cart_custom',
        // Guests allowed; we validate nonce conditionally inside the callback
        'permission_callback' => '__return_true',
        'args'                => array(
            'product_id' => array('required' => true),
            'variation_id' => array('required' => true),
            'quantity' => array('required' => true),
            'price_num' => array('required' => true),
            // optional fields:
            'addons' => array('required' => false),
            'addonsPricePerpiece' => array('required' => false),
            'minQty' => array('required' => false),
            // nonce can be supplied as: X-WP-Nonce header, _wpnonce, or nonce param
            'nonce' => array('required' => false),
            '_wpnonce' => array('required' => false),
        ),
    ));
});

/**
 * REST: add to cart with custom price (mirrors admin-ajax handler).
 *
 * Accepts JSON (`Content-Type: application/json`) or form-urlencoded bodies.
 * Returns: { success: true/false, data: "message" }
 */
function pearl_rest_add_to_cart_custom( WP_REST_Request $request ) {

    // ---- Security: accept nonce from header or params; enforce only for logged-in users
    $nonce = $request->get_header('x_wp_nonce')
          ?: $request->get_param('_wpnonce')
          ?: $request->get_param('nonce');

    if ( is_user_logged_in() ) {
        $valid = false;
        if ( $nonce ) {
            // Accept both default REST nonce ('wp_rest') and your custom action nonce ('pearl_wc_nonce')
            $valid = wp_verify_nonce( $nonce, 'wp_rest' ) || wp_verify_nonce( $nonce, 'pearl_wc_nonce' );
        }
        if ( ! $valid ) {
            return new WP_REST_Response( array(
                'success' => false,
                'data'    => 'Invalid security token.',
            ), 403 );
        }
    }

    // ---- Ensure Woo session/cart exist in REST context (for guests too)
    if ( function_exists('wc_load_cart') ) {
        wc_load_cart();
    } else {
        if ( null === WC()->session ) {
            WC()->initialize_session();
        }
        if ( null === WC()->cart ) {
            WC()->cart = new WC_Cart();
        }
    }

    // ---- Params
    $product_id   = absint( $request->get_param('product_id') );
    $variation_id = absint( $request->get_param('variation_id') );
    $quantity     = (float) ( $request->get_param('quantity') ?? 1 );
    $custom_price = (float) ( $request->get_param('price_num') ?? 0 );

    // addons may arrive as array or JSON string
    $addons_raw = $request->get_param('addons');
    if ( is_string($addons_raw) ) {
        $decoded = json_decode( stripslashes($addons_raw), true );
        $addons  = is_array($decoded) ? $decoded : array();
    } else {
        $addons  = is_array($addons_raw) ? $addons_raw : array();
    }

    $addon_price_per_piece = (float) ( $request->get_param('addonsPricePerpiece') ?? 0 );
    $min_Qty               = (float) ( $request->get_param('minQty') ?? 1 );

    // ---- Validate product/variation/cart
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return new WP_REST_Response( array(
            'success' => false,
            'data'    => 'Product not found.',
        ), 200 );
    }

    $variation = wc_get_product( $variation_id );
    if ( ! $variation ) {
        return new WP_REST_Response( array(
            'success' => false,
            'data'    => 'Variation not found.',
        ), 200 );
    }

    if ( ! WC()->cart ) {
        return new WP_REST_Response( array(
            'success' => false,
            'data'    => 'Cart not initialized.',
        ), 200 );
    }

    // ---- Build cart item and add
    $variation_attributes = $variation->get_variation_attributes();

    $cart_item_data = array(
        'custom_price'          => $custom_price,
        'addons'                => $addons,
        'addon_price_per_piece' => $addon_price_per_piece,
        'min_qty'               => $min_Qty,
    );

    $cart_item_key = WC()->cart->add_to_cart(
        $product_id,
        $quantity,
        $variation_id,
        $variation_attributes,
        $cart_item_data
    );

    if ( $cart_item_key ) {
        // Keep parity with your ajax handler
        WC()->cart->calculate_totals();
        if ( WC()->session ) {
            WC()->session->save_data();
        }

        return new WP_REST_Response( array(
            'success' => true,
            'data'    => 'Product added to cart with custom price.',
        ), 200 );
    }

    return new WP_REST_Response( array(
        'success' => false,
        'data'    => 'Failed to add product to cart.',
    ), 200 );
}


// ===============================fallback rest api call =========================

/**
 * Register a quote request.
 */
add_action('wp_ajax_pearl_register_quote_customer', 'pearl_register_quote_customer');
add_action('wp_ajax_nopriv_pearl_register_quote_customer', 'pearl_register_quote_customer');

function pearl_register_quote_customer()
{
    // Collect basic fields
    $customer_type     = sanitize_text_field($_POST['customer-type'] ?? 'individual');
    $first_name        = sanitize_text_field($_POST['first-name'] ?? '');
    $surname           = sanitize_text_field($_POST['surname'] ?? '');
    $customer_email    = sanitize_email($_POST['customer_email'] ?? '');
    $customer_phone    = sanitize_text_field($_POST['customer_phone'] ?? '');
    $country = sanitize_textarea_field($_POST['country'] ?? '');
    $created_date      = current_time('mysql');

    // Company / Association
    $company_name      = sanitize_text_field($_POST['company_name'] ?? '');
    $association_name  = sanitize_text_field($_POST['association_name'] ?? '');
    $vat_number        = sanitize_text_field($_POST['vat_number'] ?? '');

    // Address (optional)
    $postal_code       = sanitize_text_field($_POST['postal-code'] ?? '');
    $house_number      = sanitize_text_field($_POST['house-number'] ?? '');
    $street            = sanitize_text_field($_POST['street'] ?? '');
    $city              = sanitize_text_field($_POST['city'] ?? '');
    $customer_address  = trim("$house_number, $street, $city");

    // quotation name

    $quotation_name = sanitize_text_field($_POST['quotation_name']);

    $user_id  = 0;
    $username = '';

       // design request description
       $designReqDesc = sanitize_text_field($_POST['designReqDesc'] ?? '');

    // new user information

    $new_user = [];


    if (! is_user_logged_in()) {
        $existing_user = get_user_by('email', $customer_email);
        if (! $existing_user) {

             // Generate password
             $temporary_password = wp_generate_password(12, false);

             // ✅ Inject names & billing meta BEFORE Woo creates the account (and sends the email)
             $injector = function ($data) use ($first_name, $surname, $customer_email, $customer_phone, $country) {
 
                 // Core name fields for wp_insert_user()
                 if ($first_name) {
                     $data['first_name'] = $first_name;
                 }
                 if ($surname) {
                     $data['last_name']  = $surname;
                 }
 
                 // Nice display name fallback
                 $full = trim($first_name . ' ' . $surname);
                 if ($full !== '') {
                     $data['display_name'] = $full;
                 }
 
                 // Ensure billing meta exists at insert time
                 $meta = isset($data['meta_input']) && is_array($data['meta_input']) ? $data['meta_input'] : [];
                 if ($first_name) {
                     $meta['billing_first_name'] = $first_name;
                 }
                 if ($surname) {
                     $meta['billing_last_name']  = $surname;
                 }
                 if (!empty($customer_phone)) {
                     $meta['billing_phone']   = $customer_phone;
                 }
                 if (!empty($country)) {
                     $meta['billing_country'] = $country;
                 }
 
                 $data['meta_input'] = $meta;
 
                 return $data;
             };
             add_filter('woocommerce_new_customer_data', $injector, 10, 1);
 
             // Username = email; this call will trigger Woo's new-account email
             $user_id = wc_create_new_customer($customer_email, $customer_email, $temporary_password);
 
             // Always remove the one-off filter
             remove_filter('woocommerce_new_customer_data', $injector, 10);
 
             if (is_wp_error($user_id)) {
                 wp_send_json_error('Error creating user account.');
             }
 
             // ✅ Already have names/meta, so no need for wp_update_user() now
 
             // Log them in
             wc_set_customer_auth_cookie($user_id);
             wp_set_current_user($user_id);
        } else {
            // wp_set_current_user($existing_user->ID);
            // wp_set_auth_cookie($existing_user->ID);
            // $user_id  = $existing_user->ID;
            // $username = $existing_user->user_login;
            wp_send_json_error('existing_account');
        }
    } else {
        $current_user = wp_get_current_user();
        $user_id  = $current_user->ID;
        $username = $current_user->user_login;
    }

    update_user_meta($user_id, 'billing_first_name', $first_name);
    update_user_meta($user_id, 'billing_last_name', $surname);
    update_user_meta($user_id, 'billing_phone', $customer_phone);
    update_user_meta($user_id, 'billing_address_1', $customer_address);
    update_user_meta($user_id, 'billing_postcode', $postal_code);
    update_user_meta($user_id, 'billing_city', $city);
    update_user_meta($user_id, 'billing_country', $country);
    if ($customer_type === 'company') {
        update_user_meta($user_id, 'billing_company', $company_name);
    }

    if ($customer_type === 'association') {
        update_user_meta($user_id, 'billing_company', $association_name); // optional
    }

    // Persist customer type on user profile
    update_user_meta($user_id, 'customer_type', $customer_type);

    update_user_meta($user_id, 'billing_address_2', $house_number);

    // Capture quote cart items
    $cart = WC()->cart;
    $quote_products = array();
    if ($cart && ! $cart->is_empty()) {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            if (! $product) {
                continue;
            }
            $quantity     = $cart_item['quantity'];
            $product_name = $product->get_name();
            $variant_id   = absint($product->get_id());
            $product_id   = absint($product->get_parent_id());
            $addons = isset($cart_item['addons']) && is_array($cart_item['addons']) ? $cart_item['addons'] : [];
            $addone_price_per_piece = isset($cart_item['addon_price_per_piece']) ? $cart_item['addon_price_per_piece'] : 0;
            $min_qty = isset($cart_item['min_qty']) ? $cart_item['min_qty'] : 1;
            $custom_price = isset($cart_item['custom_price']) ? $cart_item['custom_price'] : 1;
            $conditional_prices = get_post_meta($variant_id, '_conditional_prices', true);
            $product_image = wp_get_attachment_image_url($product->get_image_id(), 'medium'); // or 'thumbnail'


            $variation_str = '';
            if ($product->is_type('variation')) {
                $attributes = $product->get_variation_attributes();
                $attrs_arr  = array();
                foreach ($attributes as $attr_key => $attr_value) {
                    $clean_key = str_replace('attribute_', '', $attr_key);
                    $label = wc_attribute_label($clean_key);
                    if (! $label) {
                        $label = ucwords(str_replace('_', ' ', $clean_key));
                    }
                    $attrs_arr[] = $label . ': ' . $attr_value;
                }
                $variation_str = implode(', ', $attrs_arr);
            }
            // Fetch addon config with price tables for savings calculation
            $addon_config = [];
            $product_addon_meta = get_post_meta($product_id, '_product_addon_options', true) ?: [];
            if (!empty($addons) && !empty($product_addon_meta)) {
                foreach ($addons as $addon_id => $selected_value) {
                    $addon_id_int = (int) $addon_id;
                    // Support both single string and array (multi-select) values
                    $selected_names = is_array($selected_value) ? $selected_value : [$selected_value];
                    if (isset($product_addon_meta[$addon_id_int]['options'])) {
                        foreach ($product_addon_meta[$addon_id_int]['options'] as $opt) {
                            if (in_array($opt['name'], $selected_names, true) && !empty($opt['price_table'])) {
                                $addon_config[] = $opt['price_table'];
                            }
                        }
                    }
                }
            }

            $quote_products[] = array(
                'product_id'   => $product_id,
                'varient_id'   => $variant_id,
                'product_name' => $product_name,
                'variation'    => $variation_str,
                'quantity'     => $quantity,
                'addons'        => $addons,
                'addone_price_per_piece' => $addone_price_per_piece,
                'addon_price_tables' => $addon_config,
                'min_qty' => $min_qty,
                'custom_price' => $custom_price,
                'conditional_prices' => $conditional_prices,
                'image_url'    => $product_image //
            );
        }
    }

    $quotation_amount = (float) WC()->cart->get_total('edit');   // numeric (not formatted string)
    $quotation_amount_without_tax = WC()->cart->get_subtotal();

    // Record the effective VAT rate of this cart. Without it the quote PDF/email have to
    // infer VAT as (total - subtotal), which is wrong for distributors: get_total() counts
    // the negative discount fee while get_subtotal() does not, so the subtraction yields a
    // NEGATIVE VAT. Derived from the cart itself so a zero-rated cart records 0.
    $quote_cart_net = (float) WC()->cart->get_cart_contents_total() + (float) WC()->cart->get_fee_total();
    $quote_cart_tax = (float) WC()->cart->get_cart_contents_tax() + (float) WC()->cart->get_fee_tax();
    $tax_percentage = ($quote_cart_net > 0) ? round(($quote_cart_tax / $quote_cart_net) * 100, 2) : 0;
    // $post_title = sprintf('%s Quote - %s', $first_name, date('Y-m-d'));
    $existing_quote = get_page_by_title($quotation_name, OBJECT, 'quote_request');

    if ($existing_quote) {
        // If exists, fallback to default naming
        $post_title = sprintf('%s Quote - %s', $first_name, date('Y-m-d'));
    } else {
        $post_title = $quotation_name;
    }

    $quote_post_id = wp_insert_post(array(
        'post_type'   => 'quote_request',
        'post_status' => 'publish',
        'post_title'  => $post_title,
    ));

    if (is_wp_error($quote_post_id)) {
        wp_send_json_error('Error creating quote request post.');
    }

    // upload images
    $uploaded_files = $_FILES['design_upload'] ?? null;
    $uploaded_file_ids = [];

    if ($uploaded_files && is_array($uploaded_files['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        for ($i = 0; $i < count($uploaded_files['name']); $i++) {
            if ($uploaded_files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file_array = array(
                'name'     => sanitize_file_name($uploaded_files['name'][$i]),
                'type'     => $uploaded_files['type'][$i],
                'tmp_name' => $uploaded_files['tmp_name'][$i],
                'error'    => $uploaded_files['error'][$i],
                'size'     => $uploaded_files['size'][$i]
            );

            // Upload the file to the media library
            $attachment_id = media_handle_sideload($file_array, $quote_post_id);

            if (!is_wp_error($attachment_id)) {
                $uploaded_file_ids[] = $attachment_id;
            }
        }
    }

    // Save the uploaded file IDs or URLs to post meta
    if (!empty($uploaded_file_ids)) {
       // Persist uploaded design files on the quote
       update_post_meta($quote_post_id, '_design_uploads', $uploaded_file_ids);
    
       // ---------------- Build & send the admin email via the new template ----------------
       $admin_email   = get_option('admin_email');
       $admin_subject = 'New Design Request Received from '.$first_name.' '.$surname.'';
   
       // Variables expected by the template:
       // $quote_products, $first_name, $surname, $company_name, $customer_email, $customer_phone,
       // $quotation_amount, $quotation_amount_without_tax, $quote_post_id, $user_id,
       // $designReqDesc (optional), $uploaded_file_ids (optional)
   
       // Render template HTML
       ob_start();
       // Path assumes this code lives in the main plugin file alongside /includes/mail_templates/
       include plugin_dir_path(__FILE__) . 'mail_templates/design-request-mail.php';
       $admin_message = ob_get_clean();
       $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'Cc: ' . $customer_email,
        'Reply-To: ' . trim("$first_name $surname") . " <{$customer_email}>",
    ];
   
        // Attach uploaded design files to the email
        $design_attachments = [];
        foreach ($uploaded_file_ids as $fid) {
            $file_path = get_attached_file($fid);
            if ($file_path && file_exists($file_path)) {
                $design_attachments[] = $file_path;
            }
        }

        wp_mail(
            $admin_email,
            $admin_subject,
            $admin_message,
            $headers,
            $design_attachments
        );
    }

    if(!empty($designReqDesc)){
        update_post_meta($quote_post_id, '_design_request_description', $designReqDesc);
    }
    update_post_meta($quote_post_id, '_status', "open");



    // Save meta fields
    update_post_meta($quote_post_id, '_quote_products', $quote_products);
    update_post_meta($quote_post_id, '_quote_user_id', $user_id);
    update_post_meta($quote_post_id, '_quote_amount', $quotation_amount);
    update_post_meta($quote_post_id, '_quote_subtotal', $quotation_amount_without_tax);
    update_post_meta($quote_post_id, '_tax_percentage', $tax_percentage);
    update_post_meta($quote_post_id, '_quote_created_date', $created_date);
    update_post_meta($quote_post_id, '_customer_type', $customer_type);
    update_post_meta($quote_post_id, '_customer_first_name', $first_name);
    update_post_meta($quote_post_id, '_customer_surname', $surname);
    update_post_meta($quote_post_id, '_customer_email', $customer_email);
    update_post_meta($quote_post_id, '_customer_phone', $customer_phone);
    // update_post_meta($quote_post_id, '_customer_comments', $customer_comments);


    if ($customer_type === 'company') {
        update_post_meta($quote_post_id, '_company_name', $company_name);
        update_post_meta($quote_post_id, '_vat_number', $vat_number);
    }
    if ($customer_type === 'association') {
        update_post_meta($quote_post_id, '_association_name', $association_name);
        update_post_meta($quote_post_id, '_vat_number', $vat_number);
    }

    if ($postal_code || $house_number || $street || $city) {
        update_post_meta($quote_post_id, '_postal_code', $postal_code);
        update_post_meta($quote_post_id, '_house_number', $house_number);
        update_post_meta($quote_post_id, '_street', $street);
        update_post_meta($quote_post_id, '_city', $city);
        update_post_meta($quote_post_id, '_full_address', $customer_address);
    }

    // send an email to the user

    $pdf_result = pearl_create_quote_pdf_file($quote_post_id);
    $attachments = [];
    if (! is_wp_error($pdf_result) && ! empty($pdf_result['path']) && file_exists($pdf_result['path'])) {
        $attachments[] = $pdf_result['path'];
    }


    ob_start();
    include plugin_dir_path(__FILE__) . 'mail_templates/email-quote-template.php';
    $email_body = ob_get_clean();

    wp_mail(
        $customer_email,
        'Here’s Your Quote #' . $quote_post_id .', '. $first_name,
        $email_body,
        array('Content-Type: text/html; charset=UTF-8', 'Cc: info@hercules-merchandise.co.uk'),
        $attachments
    );

    WC()->cart->empty_cart();
    wp_send_json_success([
        'message'       => 'Quote request submitted successfully.',
        'user_id'       => $user_id,
        'username'      => $username,
        'quote_post_id' => $quote_post_id,
        'first_name'  => $first_name,
        // Lead value for the generate_lead event, ex-tax, the same basis the purchase event uses.
        // The client must take the value from here and never recompute it.
        'quote_subtotal' => (float) $quotation_amount_without_tax,
        'currency'       => function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'GBP'
    ]);
}


/**
 * Update a product inside a quote.
 * This endpoint receives a quote ID, product index, new quantity, and attributes,
 * then updates the corresponding quote product.
 */
add_action('wp_ajax_update_quote_product', 'pearl_wc_update_quote_product');
add_action('wp_ajax_nopriv_update_quote_product', 'pearl_wc_update_quote_product');

function pearl_wc_update_quote_product()
{
    // Verify nonce.
    check_ajax_referer('pearl_wc_nonce', 'nonce');

    $quote_id      = isset($_POST['quote_id']) ? absint($_POST['quote_id']) : 0;
    $product_index = isset($_POST['product_index']) ? absint($_POST['product_index']) : 0;
    $new_quantity  = isset($_POST['new_quantity']) ? absint($_POST['new_quantity']) : 1;
    $attributes    = isset($_POST['attributes']) ? (array) $_POST['attributes'] : array();

    if (! $quote_id) {
        wp_send_json_error('Invalid quote ID.');
    }

    $user_id = get_current_user_id();
    if (! $user_id) {
        wp_send_json_error('You must be logged in.');
    }
    $quote_owner = get_post_meta($quote_id, '_quote_user_id', true);
    if ($quote_owner != $user_id) {
        wp_send_json_error('You do not have permission to update this quote.');
    }

    $quote_products = get_post_meta($quote_id, '_quote_products', true);
    if (! is_array($quote_products) || ! isset($quote_products[$product_index])) {
        wp_send_json_error('Product not found in quote.');
    }

    $quote_products[$product_index]['quantity'] = $new_quantity;
    $quote_products[$product_index]['attributes'] = $attributes;

    update_post_meta($quote_id, '_quote_products', $quote_products);

    wp_send_json_success('Product updated successfully.');
}

/**
 * Override cart item price if custom_price is set.
 */
add_action('woocommerce_before_calculate_totals', 'pearl_apply_custom_price', 20, 1);
function pearl_apply_custom_price($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['custom_price']) && floatval($cart_item['custom_price']) > 0) {
            $cart_item['data']->set_price(floatval($cart_item['custom_price']));
        }
    }
}

/**
 * (Optional) Handle reorder action placeholder.
 */
add_action('wp_ajax_reorder_order', 'pearl_wc_reorder_order');
add_action('wp_ajax_nopriv_reorder_order', 'pearl_wc_reorder_order');

function pearl_wc_reorder_order()
{
    if (isset($_GET['order_id']) && ! empty($_GET['order_id'])) {
        $order_id = absint($_GET['order_id']);
        wp_send_json_error('Reorder functionality is not implemented yet.');
    }
}

/**
 * Handles deletion of a quote.
 */
add_action('init', 'pearl_wc_delete_quote');
function pearl_wc_delete_quote()
{
    if (isset($_GET['action']) && 'delete_quote' === $_GET['action'] && ! empty($_GET['quote_id'])) {
        $quote_id = absint($_GET['quote_id']);
        $user_id  = get_current_user_id();
        $quote_owner = get_post_meta($quote_id, '_quote_user_id', true);
        if ($quote_owner != $user_id) {
            wp_die('You do not have permission to delete this quote.');
        }
        wp_delete_post($quote_id, true);
        wp_safe_redirect(wc_get_account_endpoint_url('offers'));
        exit;
    }
}
