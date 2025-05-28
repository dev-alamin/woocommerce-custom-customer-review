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
define( 'WCCR_PLUGIN', __FILE__ );
define( 'WCCR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCCR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'plugins_loaded', 'WCCR_plugin_init' );
/**
 * Load localization files
 *
 * @return void
 */
function WCCR_plugin_init() {
    load_plugin_textdomain( 'wc_customer_review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    require_once __DIR__ . '/includes/admin.php';
    require_once __DIR__ . '/assets.php';
    require_once __DIR__ . '/functions.php';

}
