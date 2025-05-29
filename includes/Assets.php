<?php
namespace ADS\WCCR;

defined( 'ABSPATH' ) || exit;

class Assets {
    /**
     * Constructor to initialize the class and register hooks.
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ]);
    }

    /**
     * Enqueue admin assets for the customer reviews page.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_assets($hook) {
        if (isset($_GET['page']) && $_GET['page'] === 'vh-wccr-customer-reviews') {
            wp_enqueue_script('twcdn', WCCR_PLUGIN_URL . 'assets/tailwind.js', [], null, false );
            wp_enqueue_script('alpine', WCCR_PLUGIN_URL . 'assets/alpine.min.js', [], null, true);
            
            wp_enqueue_style( 'wccr_admin_style', WCCR_PLUGIN_URL . 'assets/admin.css', [], WCCR_VERSION, 'all' );
            wp_enqueue_script( 'wccr_admin_script', WCCR_PLUGIN_URL . 'assets/admin.js', [], WCCR_VERSION, true );

            wp_localize_script('wccr_admin_script', 'WCCR_SCRIPTS', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('generate_review_nonce')
            ]);
        }
    }
}
