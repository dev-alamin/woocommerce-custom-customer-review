<?php
/**
 * Plugin Name: WooCommerce User Review
 * Plugin URI:  https://almn.me/wc-user-review
 * Description: A plugin to add custom customer review.
 * Version:     1.0.0
 * Author:      VapeHub
 * Author URI:  https://vapehub.co.uk
 * Text Domain: wc_customer_review
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Requires at least: 5.4
 * Requires PHP: 7.0
 * Requires Plugins: WooCommerce
 *
 * @package     WC_User_review
 * @author      VapeHub
 * @copyright   2025 VapeHub
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 *
 * Prefix:      WCCR
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'WCCR_VERSION', WP_DEBUG ? time() : '1.0.0' );
define( 'WCCR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCCR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCCR_PLUGIN_FILE', __FILE__ );
define( 'WCCR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/**
 * Autoloader Main function
 *
 * @param [type] $class
 * @return void
 */
function wccr_core_autoloader( $class ) {
    $namespace = 'ADS\WCCR'; // Core namespace \ It could be ParentProject/SubProject;
    $base_dir  = __DIR__ . '/includes/';

    $class = ltrim( $class, '\\' );

    if ( strpos( $class, $namespace . '\\' ) === 0 ) {
        $relative_class = substr( $class, strlen( $namespace . '\\' ) );
        $file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
        if ( file_exists( $file ) ) {
            require $file;
        }
    }
}
spl_autoload_register( 'wccr_core_autoloader' );

/**
 * Generic functions for the plugin
 * This file contains utility functions that are used across the plugin.
 * It is included here to ensure that all necessary functions are available
 * before the plugin is fully loaded.
 */
require_once __DIR__ . '/functions.php';

// Bootstrap plugin
add_action( 'plugins_loaded', function () {
    // Include necessary files
    new ADS\WCCR\Assets();
    new ADS\WCCR\Admin();
    new ADS\WCCR\Ajax();
    
    // Load translations
    load_plugin_textdomain( 'wc_customer_review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );