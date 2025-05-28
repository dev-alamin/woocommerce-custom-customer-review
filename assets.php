<?php
defined( 'ABSPATH' ) || exit;

add_action('admin_enqueue_scripts', function($hook) {
    if (isset($_GET['page']) && $_GET['page'] === 'vh-wccr-customer-reviews') {
        wp_enqueue_script('twcdn', WCCR_PLUGIN_URL . 'assets/tailwind.js', [], null, false );
        wp_enqueue_script('alpine', WCCR_PLUGIN_URL . 'assets/alpine.min.js', [], null, true);

        wp_enqueue_style( 'select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
        wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );

        wp_enqueue_style( 'wccr_admin_style', WCCR_PLUGIN_URL . 'assets/admin.css', [], WCCR_VERSION, true );
        wp_enqueue_script( 'wccr_admin_script', WCCR_PLUGIN_URL . 'assets/admin.js', [], WCCR_VERSION, true );
    }

    wp_localize_script('wccr_admin_script', 'WCCR_SCRIPTS', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('generate_review_nonce')
    ]);
});
