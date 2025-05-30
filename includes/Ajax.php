<?php 
namespace ADS\WCCR;
defined( 'ABSPATH' ) || exit;

/**
 * Class Ajax
 *
 * Handles AJAX requests for the WooCommerce User Review plugin.
 */
class Ajax {
    public function __construct() {
        add_action( 'wp_ajax_submit_wc_review', [ $this, 'submit_review' ] );
    }

    public function submit_review() {
        check_ajax_referer('generate_review_nonce', 'nonce');

        $name        = sanitize_text_field($_POST['name'] ?? '');
        $rating      = intval($_POST['rating'] ?? 0);
        $review      = sanitize_textarea_field($_POST['review'] ?? '');
        $product_id  = intval($_POST['selectedProduct'] ?? 0);
        $review_date = sanitize_text_field($_POST['reviewDate'] ?? '');

        if (!$name || !$review || !$product_id || $rating < 1 || $rating > 5) {
            wp_send_json_error('Invalid form data');
        }

        // Validate and format review date
        $comment_date = current_time('mysql');
        if ($review_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $review_date)) {
            $comment_date = $review_date . ' 00:00:00';
        }

        // Insert comment as WooCommerce review
        $commentdata = array(
            'comment_post_ID'      => $product_id,
            'comment_author'       => ucwords(str_replace( '_', ' ', $name )),
            'comment_author_email' => 'demo@demo.com', // Optional: use dummy or map with reviewer ID
            'comment_content'      => $review,
            'comment_type'         => 'review',
            'comment_approved'     => 1, // or 0 for pending moderation
            'comment_date'         => $comment_date,
            'comment_date_gmt'     => get_gmt_from_date($comment_date),
        );

        $comment_id = wp_insert_comment($commentdata);

        if (is_wp_error($comment_id) || !$comment_id) {
            wp_send_json_error('Could not submit review');
        }

        // Set rating meta
        add_comment_meta($comment_id, 'rating', $rating);
        add_comment_meta($comment_id, 'verified', '1');

        $submitted_product = wc_get_product($product_id);

        if (!$submitted_product) {
            wp_send_json_error('Invalid product ID');
        }
        // once successfully submitted, show that X Product's review has been submitted
        // Along with product link to visit
        $product_link = get_permalink($product_id);
        if ($product_link) {
            $product_name = sprintf('<a href="%s" target="_blank">%s</a>', esc_url($product_link), esc_html($submitted_product->get_name()));
        } else {
            $product_name = esc_html($submitted_product->get_name());
        }
        $message = sprintf('Review for %s has been submitted successfully.', $product_name);
        // Optionally, you can send a success message
        wp_send_json_success($message);

        die();
    }
}