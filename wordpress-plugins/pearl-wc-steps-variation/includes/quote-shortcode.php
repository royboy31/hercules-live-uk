<?php

/**
 * Class Pearl_Quote_Shortcode
 *
 * Registers a shortcode [pearl_quote_request] that displays a custom quote generator page.
 * The page uses a two-column layout:
 *   - Left column: displays current cart items with product thumbnail, product name,
 *     variation attributes, editable quantity, and a remove button.
 *   - Right column: displays a customer information form.
 *
 * @package Pearl_WC_Steps_Variation
 */

add_action( 'wp_enqueue_scripts', function () {
    global $post;

    if ( $post && has_shortcode( $post->post_content, 'pearl_quote_request' ) ) {
        // Enqueue
        wp_enqueue_script(
            'pearl-quote-form',
            MY_PLUGIN_URL . 'assets/js/quote-form.js',
            [], // add ['jquery'] if you rely on it
            // Bumped 2026-09-20 with the generate_lead push in quote-form.js. The version is
            // hardcoded, so an edit to the file alone would keep serving the cached 1.0 bundle.
            '1.1',
            true
        );
        wp_enqueue_style(
            'pearl-quote-form',
            MY_PLUGIN_URL . 'assets/css/form.css',
            [],
            '1.0'
        );

        // Build login URL (WooCommerce My Account if available, else wp-login.php)
        $login_url = function_exists( 'wc_get_page_permalink' )
            ? wc_get_page_permalink( 'myaccount' )
            : wp_login_url();

        wp_localize_script( 'pearl-quote-form', 'pearlWCData', [
            'ajax_url'            => admin_url( 'admin-ajax.php' ),
            'nonce'               => wp_create_nonce( 'pearl_quote_nonce' ),
            'login_url'           => $login_url,
            // Optional: if you want this available in JS too
            'thank_you_page_url' => get_permalink(get_option('pearl_thankyou_page'))
        ] );
    }
} );


if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Pearl_Quote_Shortcode
{
	public function __construct()
	{
		add_shortcode('pearl_quote_request', array($this, 'render_quote_request'));
	}

	public function render_quote_request($atts)
	{
		ob_start();

		// VAT calculation setup
		$tax_rates = WC_Tax::get_rates('');
		$first_rate = array_shift($tax_rates);
		$tax_percent = $first_rate ? floatval($first_rate['rate']) : 0;
		$total_price = 0;

		// Distributor discount
		$dist_discount = 0;
		$_uid = get_current_user_id();
		if ($_uid) {
			$_udata = get_userdata($_uid);
			if ($_udata && in_array('distributor', (array) $_udata->roles, true)) {
				$dist_discount = floatval(get_user_meta($_uid, 'distributor_discount', true) ?: 0);
			}
		}

		$current_user = is_user_logged_in() ? wp_get_current_user() : null;

		$first_name     = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_first_name', true)) : '';
		$surname        = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_last_name', true)) : '';
		$customer_email = $current_user ? esc_attr($current_user->user_email) : '';
		$customer_phone = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_phone', true)) : '';
		$house_number   = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_address_2', true)) : '';
		$street         = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_address_1', true)) : '';
		$city           = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_city', true)) : '';
		$postal_code    = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_postcode', true)) : '';
		$billing_country = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_country', true)) : '';

		// Customer type auto-fill from usermeta
		$ct = $current_user ? get_user_meta($current_user->ID, 'customer_type', true) : '';
		$billing_company_val = $current_user ? esc_attr(get_user_meta($current_user->ID, 'billing_company', true)) : '';
		if (!$ct) $ct = 'individual';

		$quote_title = sprintf('%s Quote - %s', $first_name, date('Y-m-d'));

?>
		<div class="pearl-quote-page-container" style="display: flex; gap: 30px; margin-top: 20px;">

			<!-- Left Column: Cart Items -->
			<div class="pearl-cart-info" style="flex: 1;  padding-right: 20px;">
				<?php
				$cart = WC()->cart;
				if ($cart && !$cart->is_empty()) :
					foreach ($cart->get_cart() as $cart_item_key => $cart_item) :
						$product = $cart_item['data'];
						if (!$product) {
							continue;
						}
						// Use parent product name for variations to avoid showing
					// the " - attr1, attr2" suffix WooCommerce appends.
					// Variation attributes are displayed separately below.
					if ($product->is_type('variation')) {
						$parent = wc_get_product($product->get_parent_id());
						$product_name = $parent ? $parent->get_name() : $product->get_name();
					} else {
						$product_name = $product->get_name();
					}
						$is_visible   = $product->is_visible();
						$product_link = $is_visible ? $product->get_permalink() : '';
						$thumbnail = $product->get_image('thumbnail');
						$variation_attrs = array();
						if ($product->is_type('variation')) {
							$variation_attrs = $product->get_variation_attributes();
						}
						$remove_url = wc_get_cart_remove_url($cart_item_key);

						// Get min qty from cart item data
						$min_qty = isset($cart_item['min_qty']) ? intval($cart_item['min_qty']) : 1;
						// Get custom price per piece
						$custom_price = isset($cart_item['custom_price']) && floatval($cart_item['custom_price']) > 0
							? floatval($cart_item['custom_price'])
							: $product->get_price();
						$line_total = $custom_price * $cart_item['quantity'];
				?>
						<div class="pearl-cart-item" data-cart-key="<?php echo esc_attr($cart_item_key); ?>" style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
							<div class="pearl-cart-item-image" style="width: 60px;">
								<?php echo $thumbnail; ?>
							</div>
							<div class="pearl-cart-item-details" style="flex: 1;">
								<div class="pearl-cart-item-name" style="font-weight: bold;">
									<?php if ($product_link) : ?>
										<a href="<?php echo esc_url($product_link); ?>">
											<?php echo esc_html($product_name); ?>
										</a>
									<?php else : ?>
										<?php echo esc_html($product_name); ?>
									<?php endif; ?>
								</div>
								<?php if (!empty($variation_attrs)) : ?>
									<ul class="pearl-variation-attrs" style="margin: 5px 0 0; padding: 0; list-style: none; font-size: 0.9em;">
										<?php foreach ($variation_attrs as $attr_key => $attr_value) :
											$clean_key = str_replace('attribute_', '', $attr_key);
											$label = wc_attribute_label($clean_key);

											if (!$label) {
												$label = ucwords(str_replace('_', ' ', $clean_key));
											}

											if (strtolower($label) === 'options') {
												continue; // Skip this item
											}

											$human_value = $attr_value;

											// If it's a taxonomy-based attribute (pa_size, pa_color, etc.)
											if (taxonomy_exists($clean_key)) {
												$term = get_term_by('slug', $attr_value, $clean_key);
												if ($term && !is_wp_error($term)) {
													$human_value = $term->name; // This is the proper display name
												}
											} else {
												// For custom product attributes, make it readable
												$human_value = ucwords(str_replace('-', ' ', $attr_value));
											}
										?>
											<li><strong><?php echo esc_html($label); ?></strong> <?php echo esc_html($human_value); ?></li>
										<?php endforeach; ?>


										<!-- addons information -->

										<?php
										$addons = isset($cart_item['addons']) && is_array($cart_item['addons']) ? $cart_item['addons'] : [];
										if (!empty($addons)) :
										?>

											<?php foreach ($addons as $addon_id => $addon_value) :
												$addon_term = get_term($addon_id, 'product_addons');
												$addon_name = $addon_term && !is_wp_error($addon_term) ? $addon_term->name : 'Addon';
											?>
												<?php
												$is_array = is_array($addon_value);
												$display_value = $is_array ? implode(', ', array_map('esc_html', $addon_value)) : esc_html($addon_value);
												?>
												<li><strong><?php echo esc_html($addon_name); ?>:</strong> <?php echo $display_value; ?></li>

											<?php endforeach; ?>

										<?php endif; ?>


										<li class="pearl-qty-row">
											<strong>Quantity</strong>
											<span><?php echo esc_html($cart_item['quantity']); ?></span>
										</li>
										<li class="pearl-price-row"><strong>Price per piece</strong> <span class="pearl-price-per-piece" data-cart-key="<?php echo esc_attr($cart_item_key); ?>"><?php echo wc_price($custom_price); ?></span></li>
										<li><strong>Total Price</strong> <span class="pearl-line-total" data-cart-key="<?php echo esc_attr($cart_item_key); ?>"><?php echo wc_price($line_total); ?></span></li>
										<?php $total_price += $line_total; ?>
									</ul>
								<?php endif; ?>
							</div>
							<div class="pearl-cart-item-remove" style="margin-left: auto;">
								<a href="<?php echo esc_url($remove_url); ?>" class="remove" aria-label="<?php esc_attr_e('Remove this item', 'pearl-wc-steps-variation'); ?>" style="text-decoration: none; font-size: 18px;">
									<img src="<?php echo plugins_url('../assets/img/bin.svg', __FILE__); ?>" alt="Image description" />
								</a>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="kd-empty-cart-wrapper">
						<p><?php esc_html_e('Your cart is empty.', 'pearl-wc-steps-variation'); ?></p>
						<input type="hidden" value="yes" id="kd-empty-cart" />
					</div>
				<?php endif; ?>

				<?php if ($cart && !$cart->is_empty()) : ?>
				<!-- VAT Breakdown Section -->
				<div class="pearl-cart-item pearl-totals-section" style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
					<div class="pearl-cart-item-image" style="width: 60px;"></div>
					<div class="pearl-cart-item-details" style="flex: 1;">
						<ul class="pearl-variation-attrs" style="margin: 5px 0 0; padding: 0; list-style: none; font-size: 0.9em;">
							<?php if ($dist_discount > 0) :
								$discounted_total = $total_price * (1 - $dist_discount / 100);
								$discount_amount = $total_price - $discounted_total;
								$vat_on_discounted = ($discounted_total / 100) * $tax_percent;
							?>
								<li><strong>Total Excl VAT</strong> <span id="pearl-total-excl-vat"><s style="color:#999 !important; text-decoration:line-through;"><?php echo wc_price($total_price); ?></s> <span class="kd-green-text" style="color:#10C99E !important; font-weight:700;"><?php echo wc_price($discounted_total); ?></span></span></li>
								<li class="kd-green-text" style="color:#10C99E !important; font-weight:700;"><strong>Distributor discount (<?php echo $dist_discount; ?>%)</strong> <span id="pearl-distributor-discount">-<?php echo wc_price($discount_amount); ?></span></li>
								<li><strong>VAT</strong> <span id="pearl-vat-amount"><?php echo wc_price($vat_on_discounted); ?></span></li>
								<li><strong>Total Incl VAT</strong> <span id="pearl-total-incl-vat"><span class="kd-green-text" style="color:#10C99E !important; font-weight:700;"><?php echo wc_price($discounted_total + $vat_on_discounted); ?></span></span></li>
							<?php else : ?>
								<li><strong>Total Excl VAT</strong> <span id="pearl-total-excl-vat"><?php echo wc_price($total_price); ?></span></li>
								<li><strong>VAT</strong> <span id="pearl-vat-amount"><?php echo wc_price(($total_price / 100) * $tax_percent); ?></span></li>
								<li><strong>Total Incl VAT</strong> <span id="pearl-total-incl-vat"><?php echo wc_price($total_price + (($total_price / 100) * $tax_percent)); ?></span></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<?php endif; ?>

				<a href="/" class="btn rouinded green kd-add-more-btn">Add more products</a>
				<div class="kd-design-upload-section" style="border: 1px solid #DCDCDC; padding: 20px; margin-top: 30px; border-radius : 20px; background : white">
					<p style="margin: 0 0 10px; font-weight: 500;" class="design-upload-title">
						<input type="checkbox" name="design-requested" id="design-request-radio-btn" /> I would like to receive a <strong>free digital design</strong><br />
						<small class="resign-request-checkbox-desc">Give us as much information as possible, and we'll prepare a few designs for you</small>
					</p>

					<div class="design-upload-description">
						<label>Message</label>
						<textarea id="design-request-description"></textarea>
					</div>

					<div class="design-upload-btn">
						<label for="design_upload">
							Upload files
						</label>
						<input type="file" id="design_upload" name="design_upload[]" accept=".jpg,.jpeg,.png,.pdf" multiple style="font-family: Jost; color: #253461; " onchange="handleFileSelect(event)">
						<small>You can upload a maximum of 10 files, with a total size not exceeding 20MB. If you need to send more files, please contact us via email.</small>
					</div>


					<div id="selected-files" style="margin-top: 10px; font-size: 0.9em;"></div>


				</div>
			</div>

			<!-- Right Column: Customer Info Form -->
			<div class="pearl-quote-form">
				<div class="quote-form-header">
					<h2><?php esc_html_e('Get a free, no-obligation quote within 1 minute.', 'pearl-wc-steps-variation'); ?></h2>
				</div>

				<form method="post" action="" class="form-quote-form-form">

					<div class="kd-row">
						<div class="kd-col-100">
							<label><?php esc_html_e('I am ordering as', 'pearl-wc-steps-variation'); ?> *</label>
							<div>
								<label class="kd-radio-wrapper">
									<input value="individual" type="radio" name="customer-type" id="type-individual" <?php checked($ct, 'individual'); ?> />
									<span class="checkmark"></span>
									<?php esc_html_e('Individual', 'pearl-wc-steps-variation'); ?>
								</label>

								<label class="kd-radio-wrapper">
									<input value="company" type="radio" name="customer-type" id="type-company" <?php checked($ct, 'company'); ?> />
									<span class="checkmark"></span>
									<?php esc_html_e('Company', 'pearl-wc-steps-variation'); ?>
								</label>

								<label class="kd-radio-wrapper">
									<input value="association" type="radio" name="customer-type" id="type-association" <?php checked($ct, 'association'); ?> />
									<span class="checkmark"></span>
									<?php esc_html_e('Association', 'pearl-wc-steps-variation'); ?>
								</label>

							</div>
						</div>
					</div>

					<div class="kd-row">
						<div class="kd-col-50 kd-country-div">
							<label><?php esc_html_e('Country', 'pearl-wc-steps-variation'); ?> <span class="kd-country-tooltip"> ? </span></label>
							<div class="kd-country-tooltip-desc">
								Contact us if you want a delivery outside of the UK.
							</div>
							<select name="country" disabled>
								<option value="United Kingdom" <?php selected($billing_country, 'United Kingdom'); ?>>United Kingdom</option>
							</select>

						</div>
					</div>

					<div id="company-details" style="display: none;">
						<div class="kd-row">
							<div class="kd-col-100" id="company-name-wrapper" style="display: none;">
								<label><?php esc_html_e('Company Name', 'pearl-wc-steps-variation'); ?> *</label>
								<input type="text" name="company_name" value="<?php echo ($ct === 'company') ? esc_attr($billing_company_val) : ''; ?>" />
							</div>
							<div class="kd-col-100" id="association-name-wrapper" style="display: none;">
								<label><?php esc_html_e('Association Name', 'pearl-wc-steps-variation'); ?> *</label>
								<input type="text" name="association_name" value="<?php echo ($ct === 'association') ? esc_attr($billing_company_val) : ''; ?>" />
							</div>
						</div>
					</div>

					<div class="kd-row">
						<div class="kd-col-50">
							<label><?php esc_html_e('First Name', 'pearl-wc-steps-variation'); ?> *</label>
							<input type="text" name="first-name" value="<?php echo esc_attr($first_name); ?>" required />
						</div>
						<div class="kd-col-50">
							<label><?php esc_html_e('Surname', 'pearl-wc-steps-variation'); ?> *</label>
							<input type="text" name="surname" value="<?php echo esc_attr($surname); ?>" />
						</div>
					</div>

					<div class="kd-row">
						<div class="kd-col-100">
							<label><?php esc_html_e('Telephone', 'pearl-wc-steps-variation'); ?> </label>
							<input type="text" id="customer_phone" name="customer_phone" value="<?php echo esc_attr($customer_phone); ?>" style="width: 100%;" />
						</div>

					</div>

					<div class="kd-row">
						<div class="kd-col-100">
							<label><?php esc_html_e('Email', 'pearl-wc-steps-variation'); ?> *</label>
							<input type="email" id="customer_email" name="customer_email" value="<?php echo esc_attr($customer_email); ?>" style="width: 100%;" required />
						</div>

					</div>

					<div class="kd-row">
						<div class="kd-col-100">
							<label><?php esc_html_e('Quotation name', 'pearl-wc-steps-variation'); ?></label>
							<input type="text" name="quotation_name" id="quotation_name" value="<?php echo esc_attr($quote_title); ?>" />
						</div>
					</div>


					<div class="kd-row text-center row-col">
						<a href="#" id="address-field-toggle"><?php esc_html_e('Show Address Fields', 'pearl-wc-steps-variation'); ?> &#9660;</a>
						<small>(You can add your address details later) </small>
					</div>
					<div id="address-fields" style="display : none">

						<div class="kd-row">

							<div class="kd-col-50">
								<label><?php esc_html_e('Street (no PO boxes)', 'pearl-wc-steps-variation'); ?></label>
								<input type="text" name="street" value="<?php echo esc_attr($street); ?>" />
							</div>

							<div class="kd-col-50">
								<label><?php esc_html_e('House number', 'pearl-wc-steps-variation'); ?></label>
								<input type="text" name="house-number" value="<?php echo esc_attr($house_number); ?>" />
							</div>

						</div>

						<div class="kd-row">

							<div class="kd-col-50">
								<label><?php esc_html_e('Postal Code', 'pearl-wc-steps-variation'); ?></label>
								<input type="text" name="postal-code" value="<?php echo esc_attr($postal_code); ?>" />
							</div>

							<div class="kd-col-50">
								<label><?php esc_html_e('City', 'pearl-wc-steps-variation'); ?></label>
								<input type="text" name="city" value="<?php echo esc_attr($city); ?>" />
							</div>

						</div>

					</div>


					<div bis_skin_checked="1" class="kd-row" style="">
						<div class="kd-col-100" bis_skin_checked="1">
							<input type="submit" value="Send as a PDF to your inbox" class="kd-submit-btn">
						</div>
					</div>

					<div>
						<ul class="feature-list-icons">
							<li>
								<img src="<?php echo plugins_url('../assets/img/check.png', __FILE__); ?>" alt="Free design" />
								<p>Free and without any obligation</p>
							</li>
						</ul>
					</div>

					<!-- Toggle Script -->
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							var radios = document.querySelectorAll('input[name="customer-type"]');
							var details = document.getElementById('company-details');
							var cw = document.getElementById('company-name-wrapper');
							var aw = document.getElementById('association-name-wrapper');

							function toggle() {
								var v = document.querySelector('input[name="customer-type"]:checked');
								v = v ? v.value : 'individual';
								if (v === 'company') { details.style.display='block'; cw.style.display=''; aw.style.display='none'; }
								else if (v === 'association') { details.style.display='block'; cw.style.display='none'; aw.style.display=''; }
								else { details.style.display='none'; cw.style.display='none'; aw.style.display='none'; }
							}

							radios.forEach(function(r){ r.addEventListener('change', toggle); });
							toggle();
						});
					</script>
				</form>
			</div>
		</div>

		<style>
		</style>

<?php
		return ob_get_clean();
	}
}

new Pearl_Quote_Shortcode();
