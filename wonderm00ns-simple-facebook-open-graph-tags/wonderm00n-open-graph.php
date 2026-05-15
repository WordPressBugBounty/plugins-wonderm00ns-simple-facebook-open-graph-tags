<?php
/**
 * Plugin Name: Open Graph and Twitter Card Tags
 * Plugin URI:
 * Description: Improve social media sharing by inserting Facebook Open Graph, Twitter Card and SEO Meta Tags on your WordPress website pages, posts, WooCommerce products, or any other custom post type.
 * Version: 3.4.0
 * Author: WPExperts
 * Author URI: https://wpexperts.io/
 * Text Domain: wonderm00ns-simple-facebook-open-graph-tags
 * Domain Path: /lang
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * WC requires at least: 3.0
 * WC tested up to: 8.7.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 **/

defined( 'ABSPATH' ) || exit;

define( 'WEBDADOS_FB_VERSION', '3.4.0' );
define( 'WEBDADOS_FB_PLUGIN_FILE', __FILE__ );
define( 'WEBDADOS_FB_PLUGIN_NAME', 'Open Graph and Twitter Card Tags' );
define( 'WEBDADOS_FB_W', 1200 );
define( 'WEBDADOS_FB_H', 630 );

/* Include core class */
require plugin_dir_path( __FILE__ ) . 'includes/class-webdados-fb-open-graph.php';

/* Uninstall hook */
register_uninstall_hook( __FILE__, 'webdados_fb_uninstall' );
if ( ! function_exists( 'webdados_fb_uninstall' ) ) :
	/**
	 * Uninstall plugin and clean up data.
	 *
	 * Removes all plugin options, post meta, and transients from the database
	 * when the plugin is uninstalled, unless the user has chosen to keep data.
	 *
	 * @since 1.0.0
	 */
	function webdados_fb_uninstall() {
		$options = get_option( 'wonderm00n_open_graph_settings' );
		if ( intval( $options['fb_keep_data_uninstall'] ) == 0 ) {
			// Settings.
			delete_option( 'wonderm00n_open_graph_settings' );
			delete_option( 'wonderm00n_open_graph_version' );
			delete_option( 'wonderm00n_open_graph_admin_notice' );
			global $wpdb;
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			// Post meta.
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_webdados_fb_open_graph%'" );
			// Transients - Image size cache.
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%webdados_og_image_size_%'" );
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		}
	}
endif;

/* Run it - This function name is used by the PRO add-on to detect this plugin */
if ( ! function_exists( 'webdados_fb_run' ) ) :
	/**
	 * Initialize and run the plugin.
	 *
	 * Creates and returns the main Webdados_FB class instance. This function
	 * is used by the PRO add-on to detect if this plugin is active.
	 *
	 * @since 1.0.0
	 * @return Webdados_FB|false Plugin instance on success, false on failure.
	 */
	function webdados_fb_run() {
		$webdados_fb = new Webdados_FB( WEBDADOS_FB_VERSION );

		if ( $webdados_fb ) {
			return $webdados_fb;
		} else {
			return false;
		}
	}
endif;

$webdados_fb = webdados_fb_run();
