<?php
/**
 * Plugin Name: Hercules Category Webhooks
 * Description: Sends webhooks to Cloudflare Product Sync Worker when WooCommerce product categories are created, updated, or deleted.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

class Hercules_Category_Webhooks {

    private $webhook_secret = 'hercules-webhook-secret-uk-2024';

    /**
     * Get the correct worker URL based on environment (staging vs production)
     */
    private function get_webhook_url() {
        $site_url = site_url();
        if (strpos($site_url, 'staging.') !== false) {
            return 'https://hercules-product-sync-uk.gilles-86d.workers.dev';
        }
        return 'https://hercules-product-sync-uk-production.gilles-86d.workers.dev';
    }

    public function __construct() {
        // Product category created
        add_action('created_product_cat', array($this, 'on_category_create'), 10, 2);

        // Product category updated
        add_action('edited_product_cat', array($this, 'on_category_update'), 10, 2);

        // Product category deleted
        add_action('delete_product_cat', array($this, 'on_category_delete'), 10, 4);
    }

    /**
     * Category created
     */
    public function on_category_create($term_id, $tt_id) {
        $this->send_webhook('/webhook/category-create', array(
            'id'     => $term_id,
            'action' => 'create',
        ));
    }

    /**
     * Category updated (name, description, image, slug, parent changed)
     */
    public function on_category_update($term_id, $tt_id) {
        $this->send_webhook('/webhook/category-update', array(
            'id'     => $term_id,
            'action' => 'update',
        ));
    }

    /**
     * Category deleted
     */
    public function on_category_delete($term_id, $tt_id, $deleted_term, $object_ids) {
        $this->send_webhook('/webhook/category-delete', array(
            'id'     => $term_id,
            'action' => 'delete',
        ));
    }

    /**
     * Send webhook with HMAC-SHA256 signature
     */
    private function send_webhook($endpoint, $data) {
        $url     = $this->get_webhook_url() . $endpoint;
        $payload = json_encode($data);

        $signature = base64_encode(hash_hmac('sha256', $payload, $this->webhook_secret, true));

        wp_remote_post($url, array(
            'timeout'  => 5,
            'blocking' => false,
            'headers'  => array(
                'Content-Type'           => 'application/json',
                'X-WC-Webhook-Signature' => $signature,
            ),
            'body' => $payload,
        ));

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Hercules Category Webhook: Sent to {$endpoint} for category {$data['id']} (action: {$data['action']})");
        }
    }
}

new Hercules_Category_Webhooks();
