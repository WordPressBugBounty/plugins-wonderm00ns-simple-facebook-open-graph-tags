<?php
/**
 * Core plugin class file.
 *
 * This file contains the main Webdados_FB class which handles plugin initialization,
 * option management, dependency loading, and hook registration for both admin and public areas.
 * It also provides methods for checking third-party plugin compatibility and managing
 * plugin updates and database migrations.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Includes
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

if ( ! class_exists( 'Webdados_FB' ) ) :
	/**
	 * Main plugin class.
	 *
	 * Handles plugin initialization, option loading, dependency management, and hook registration.
	 * This class serves as the core of the plugin, managing all aspects of plugin functionality
	 * including admin and public hooks, third-party plugin compatibility checks, and database updates.
	 *
	 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
	 * @subpackage Includes
	 * @since    1.0.0
	 */
	class Webdados_FB {
		/**
		 * Version.
		 *
		 * @since 1.0.0
		 * @var string $version Version.
		 */
		protected $version;

		/**
		 * Database options.
		 *
		 * @since 1.0.0
		 * @var array $options Database options.
		 */
		public $options;

		/**
		 * Image sizes.
		 *
		 * @since 1.0.0
		 * @var int $img_w Image sizes.
		 */
		public $img_w = WEBDADOS_FB_W;

		/**
		 * Image sizes.
		 *
		 * @since 1.0.0
		 * @var int $img_h Image sizes.
		 */
		public $img_h = WEBDADOS_FB_H;

		/**
		 * Constructor.
		 *
		 * Initializes the plugin by setting version, loading options, dependencies,
		 * and registering hooks for admin and public areas.
		 *
		 * @since 1.0.0
		 * @param string $version Plugin version.
		 */
		public function __construct( $version ) {
			// $this->plugin_slug = 'wonderm00ns-simple-facebook-open-graph-tags';
			$this->version = $version;
			$this->options = $this->load_options();
			$this->load_dependencies();
			$this->init_smtp_recommendation();
			// $this->set_locale();
			$this->call_global_hooks();
			if ( is_admin() ) {
				$this->call_admin_hooks();
			}
			if ( ! is_admin() ) {
				$this->call_public_hooks();
			}

			add_action( 'before_woocommerce_init', array( $this, 'hpos_incompatibility' ) );
		}

		/**
		 * WooCommerce HPOS compatibility declaration.
		 *
		 * Declares compatibility with WooCommerce High-Performance Order Storage (HPOS)
		 * feature to prevent compatibility warnings.
		 *
		 * @since 1.0.0
		 */
		public function hpos_incompatibility() {
			if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WEBDADOS_FB_PLUGIN_FILE, true );
			}
		}

		/**
		 * Initialize SMTP recommendation system.
		 *
		 * Loads and initializes the Post SMTP recommendation notice system
		 * to promote the Post SMTP plugin for email delivery.
		 *
		 * @since 1.0.0
		 */
		private function init_smtp_recommendation() {

			if ( ! class_exists( 'Recommend_Post_SMTP_Admin_Notice' ) ) {
				require_once plugin_dir_path( WEBDADOS_FB_PLUGIN_FILE ) . 'vendor/post-smtp-notice/recommend-post-smtp-admin-notice.php';
				$recommend_smtp_admin_notice = Recommend_Post_SMTP_Admin_Notice::get_instance();
				$recommend_smtp_admin_notice->set_plugin_info( 'fma', 'png' );
			}

			require_once plugin_dir_path( WEBDADOS_FB_PLUGIN_FILE ) . 'vendor/post-smtp-notice/recommend-post-smtp-loader.php';
			// Initialize universal Post SMTP recommendation system.
			$recommend_smtp = recommend_smtp_loader(
				'wonderm00n-open-graph',     // unique plugin ID.
				'wonderm00n-open-graph',   // your plugin slug.
				true,            // show admin notice.
				'wonderm00n-open-graph',           // parent menu.
				'gif'            // logo format.
			);
		}

		/**
		 * Get default plugin options.
		 *
		 * Returns an array of default option values used when no user settings exist.
		 * Values can be filtered using the 'fb_og_default_options' filter.
		 *
		 * @since 1.0.0
		 * @return array Array of default option key-value pairs.
		 */
		private function default_options() {
			return apply_filters(
				'fb_og_default_options',
				array(
					// System.
					'fb_keep_data_uninstall'       => 1,
					'fb_image_min_size'            => 200,
					// General.
					'fb_desc_chars'                => 300,
					'fb_image_use_specific'        => 1,
					'fb_image_use_featured'        => 1,
					'fb_image_use_content'         => 0,
					'fb_image_use_media'           => 0,
					'fb_image_use_default'         => 1,
					'fb_image_use_mshot'           => 0,
					'fb_adv_disable_image_size'    => 0,
					// OG.
					'fb_title_show'                => 1,
					'fb_sitename_show'             => 1,
					'fb_url_show'                  => 1,
					'fb_desc_show'                 => 1,
					'fb_image_show'                => 1,
					'fb_type_show'                 => 1,
					'fb_author_show'               => 1,
					'fb_article_dates_show'        => 1,
					'fb_article_sections_show'     => 1,
					'fb_publisher_show'            => 1,
					'fb_locale_show'               => 1,
					'fb_declaration_method'        => 'prefix',
					'fb_adv_notify_fb'             => 1,
					// Twitter.
					'fb_title_show_twitter'        => 1,
					'fb_url_show_twitter'          => 1,
					'fb_desc_show_twitter'         => 1,
					'fb_image_show_twitter'        => 1,
					'fb_author_show_twitter'       => 1,
					'fb_publisher_show_twitter'    => 1,
					'fb_twitter_card_type'         => 'summary_large_image',
					// Schema.
					'fb_title_show_schema'         => 1,
					'fb_desc_show_schema'          => 1,
					'fb_image_show_schema'         => 1,
					'fb_author_show_schema'        => 1,
					'fb_article_dates_show_schema' => 1,
					'fb_publisher_show_schema'     => 1,
					// SEO.
					// ...
					// 3rd party.
					'fb_show_wpseoyoast'           => 1,
					'fb_show_aioseop'              => 0,
					'fb_wc_useproductgallery'      => 1,
					'fb_subheading_position'       => 'after',
				)
			);
		}

		/**
		 * Get all plugin options with their sanitization methods.
		 *
		 * Returns an array of all available options with their corresponding
		 * sanitization function names (e.g., 'intval', 'trim'). Values can be
		 * filtered using the 'fb_og_all_options' filter.
		 *
		 * @since 1.0.0
		 * @return array Array of option keys and their sanitization methods.
		 */
		public function all_options() {
			return apply_filters(
				'fb_og_all_options',
				array(
					'fb_app_id_show'                     => 'intval',
					'fb_app_id'                          => 'trim',
					'fb_admin_id_show'                   => 'intval',
					'fb_admin_id'                        => 'trim',
					'fb_locale_show'                     => 'intval',
					'fb_locale'                          => 'trim',
					'fb_sitename_show'                   => 'intval',
					'fb_title_show'                      => 'intval',
					'fb_title_show_schema'               => 'intval',
					'fb_title_show_twitter'              => 'intval',
					'fb_url_show'                        => 'intval',
					'fb_url_show_twitter'                => 'intval',
					'fb_url_canonical'                   => 'intval',
					'fb_url_add_trailing'                => 'intval',
					'fb_type_show'                       => 'intval',
					'fb_type_show_schema'                => 'intval',
					'fb_type_homepage'                   => 'trim',
					'fb_type_schema_homepage'            => 'trim',
					'fb_type_schema_post'                => 'trim',
					'fb_article_dates_show'              => 'intval',
					'fb_article_dates_show_schema'       => 'intval',
					'fb_article_sections_show'           => 'intval',
					'fb_publisher_show'                  => 'intval',
					'fb_publisher'                       => 'trim',
					'fb_publisher_show_schema'           => 'intval',
					'fb_publisher_schema'                => 'trim',
					'fb_publisher_show_twitter'          => 'intval',
					'fb_publisher_twitteruser'           => 'trim',
					'fb_author_show'                     => 'intval',
					'fb_author_show_schema'              => 'intval',
					'fb_author_show_meta'                => 'intval',
					'fb_author_show_linkrelgp'           => 'intval',
					'fb_author_show_twitter'             => 'intval',
					'fb_author_hide_on_pages'            => 'intval',
					'fb_desc_show'                       => 'intval',
					'fb_desc_show_meta'                  => 'intval',
					'fb_desc_show_schema'                => 'intval',
					'fb_desc_show_twitter'               => 'intval',
					'fb_desc_chars'                      => 'intval',
					'fb_desc_homepage'                   => 'trim',
					'fb_desc_homepage_customtext'        => 'trim',
					'fb_desc_default_option'             => 'trim',
					'fb_desc_default'                    => 'trim',
					'fb_image_show'                      => 'intval',
					'fb_image_size_show'                 => 'intval',
					'fb_image_show_schema'               => 'intval',
					'fb_image_show_twitter'              => 'intval',
					'fb_image'                           => 'trim',
					'fb_image_rss'                       => 'intval',
					'fb_image_use_specific'              => 'intval',
					'fb_image_use_featured'              => 'intval',
					'fb_image_use_content'               => 'intval',
					'fb_image_use_media'                 => 'intval',
					'fb_image_use_default'               => 'intval',
					'fb_image_use_mshot'                 => 'intval',
					'fb_adv_disable_image_size'          => 'intval',
					'fb_image_min_size'                  => 'intval',
					'fb_show_wpseoyoast'                 => 'intval',
					'fb_show_aioseop'                    => 'intval',
					'fb_show_subheading'                 => 'intval',
					'fb_subheading_position'             => 'trim',
					'fb_show_businessdirectoryplugin'    => 'intval',
					'fb_keep_data_uninstall'             => 'intval',
					'fb_adv_force_local'                 => 'intval',
					'fb_adv_notify_fb'                   => 'intval',
					'fb_adv_notify_fb_app_id'            => 'trim',
					'fb_adv_notify_fb_app_secret'        => 'trim',
					'fb_adv_supress_fb_notice'           => 'intval',
					'fb_twitter_card_type'               => 'trim',
					'fb_wc_usecategthumb'                => 'intval',
					'fb_wc_useproductgallery'            => 'intval',
					'fb_wc_usepg_png_overlay'            => 'intval',
					'fb_image_overlay'                   => 'intval',
					'fb_image_overlay_not_for_default'   => 'intval',
					'fb_image_overlay_image'             => 'trim',
					'fb_image_overlay_original_behavior' => 'trim',
					'fb_publisher_show_meta'             => 'intval',
					'fb_declaration_method'              => 'trim',
					'settings_last_tab'                  => 'intval',
				)
			);
		}

		/**
		 * Load and merge plugin options.
		 *
		 * Loads user options from database, merges with default options,
		 * and ensures all options are set to avoid PHP notices.
		 *
		 * @since 1.0.0
		 * @return array Merged array of user and default options.
		 */
		private function load_options() {
			$user_options = get_option( 'wonderm00n_open_graph_settings' );
			if ( ! is_array( $user_options ) ) {
				$user_options = array();
			}
			$all_options     = $this->all_options();
			$default_options = $this->default_options();
			if ( is_array( $all_options ) ) {
				// Merge the settings "all together now" (yes, it's a Beatles reference).
				foreach ( $all_options as $key => $sanitize ) {
					// We have it on the user settings ?
					if ( isset( $user_options[ $key ] ) ) {
						// Is it empty?
						if ( mb_strlen( trim( $user_options[ $key ] ) ) == 0 ) {
							// Should we get it from defaults, then?
							if ( ! empty( $default_options[ $key ] ) ) {
								$user_options[ $key ] = $default_options[ $key ];
							}
						}
					} elseif ( ! empty( $default_options[ $key ] ) ) {
							// Get it from defaults.
							$user_options[ $key ] = $default_options[ $key ];
					} else {
						// Or just set it as an empty strings to avoid php notices and having to test isset() all the time.
						$user_options[ $key ] = '';
					}
				}
			}
			// Some defaults...
			// Default type to 'website' - https://wordpress.org/support/topic/the-ogtype-blog-is-not-valid-anymore/.
			$user_options['fb_type_homepage'] = 'website';
			// No GD? No overlay.
			if ( ! extension_loaded( 'gd' ) ) {
				$user_options['fb_image_overlay'] = 0;
			}
			return $user_options;
		}

		/**
		 * Load plugin dependencies.
		 *
		 * Conditionally loads admin or public class files based on current context.
		 *
		 * @since 1.0.0
		 */
		private function load_dependencies() {
			if ( is_admin() ) {
				require_once plugin_dir_path( __DIR__ ) . 'admin/class-webdados-fb-open-graph-admin.php';
			}
			if ( ! is_admin() ) {
				require_once plugin_dir_path( __DIR__ ) . 'public/class-webdados-fb-open-graph-public.php';
			}
		}

		/**
		 * Set plugin locale for translations.
		 *
		 * Loads plugin text domain for internationalization support.
		 *
		 * @since 1.0.0
		 */
		private function set_locale() {
		}

		/**
		 * Register global WordPress hooks.
		 *
		 * Registers hooks that run in both admin and public contexts,
		 * including database update checks, image size settings, and excerpt support.
		 *
		 * @since 1.0.0
		 */
		private function call_global_hooks() {
			// Update.
			add_action( 'plugins_loaded', array( $this, 'update_db_check' ) );
			// Image sizes - After PRO is loaded.
			add_action( 'plugins_loaded', array( $this, 'set_image_sizes' ), 12 );
			// Add excerpts to pages.
			add_action( 'init', array( $this, 'add_excerpts_to_pages' ) );
		}

		/**
		 * Register admin-specific WordPress hooks.
		 *
		 * Registers all hooks related to admin functionality including menu creation,
		 * settings registration, meta boxes, and admin notices.
		 *
		 * @since 1.0.0
		 */
		private function call_admin_hooks() {
			$plugin_admin = new Webdados_FB_Admin( $this->options, $this->version );
			// Menu.
			add_action( 'admin_menu', array( $plugin_admin, 'create_admin_menu' ) );
			// Register settings.
			add_action( 'admin_init', array( $plugin_admin, 'options_init' ) );
			// WPML - Translate options.
			add_action( 'update_option_wonderm00n_open_graph_settings', array( $plugin_admin, 'options_wpml' ), 10, 3 );
			// Settings link on the Plugins list.
			add_filter( 'plugin_action_links_' . plugin_basename( WEBDADOS_FB_PLUGIN_FILE ), array( $plugin_admin, 'place_settings_link' ) );
			// User Facebook, Google+ and Twitter profiles.
			add_action( 'user_contactmethods', array( $plugin_admin, 'user_contactmethods' ) );
			// Add metabox to posts.
			add_action( 'add_meta_boxes', array( $plugin_admin, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $plugin_admin, 'save_meta_boxes' ) );
			// Admin notices.
			add_action( 'admin_notices', array( $plugin_admin, 'admin_notices' ) );
			// Admin link to manually update cache.
			add_action( 'post_updated_messages', array( $plugin_admin, 'post_updated_messages' ) );
			// Session start so we can know if the cache was cleared on Facebook.
			// if(!session_id())
			// @session_start(); //We use @ because some other plugin could previously sent something to the browser.
		}

		/**
		 * Register public/frontend WordPress hooks.
		 *
		 * Registers all hooks related to frontend functionality including meta tag
		 * insertion, namespace modifications, and RSS feed enhancements.
		 *
		 * @since 1.0.0
		 */
		private function call_public_hooks() {
			// Create public object.
			$plugin_public = new Webdados_FB_Public( $this->options, $this->version );
			// Get Post as soon as he's set, because some plugins, like BDP usally mess with it.
			add_action( 'the_post', array( $plugin_public, 'get_post' ), 0 );
			// hook to upate plugin db/options based on version.
			add_action( 'wp_head', array( $plugin_public, 'insert_meta_tags' ), 99999 );
			// hook to add Open Graph Namespace.
			add_filter( 'language_attributes', array( $plugin_public, 'add_open_graph_namespace' ), 99999 );
			// Add Schema.org itemtype.
			add_filter( 'language_attributes', array( $plugin_public, 'add_schema_itemtype' ), 99999 );
			// RSS.
			add_action( 'rss2_ns', array( $plugin_public, 'images_on_feed_yahoo_media_tag' ) );
			add_action( 'rss_item', array( $plugin_public, 'images_on_feed_image' ) );
			add_action( 'rss2_item', array( $plugin_public, 'images_on_feed_image' ) );
		}

		/**
		 * Check and perform database updates.
		 *
		 * Checks plugin version and performs necessary database migrations
		 * when upgrading from older versions.
		 *
		 * @since 1.0.0
		 */
		public function update_db_check() {
			$upgrade = false;
			// Upgrade from 0.5.4 - Last version with individual settings.
			$v = get_option( 'wonderm00n_open_graph_version' );
			if ( ! $v ) {
				// No version because it's a new install or because it's 0.5.4 or less?
				if ( $this->version <= '0.5.4' ) {
					$my_secure_variable = true;
				} else {
					// A new install - set the default data on the database.
					$upgrade = true;
					update_option( 'wonderm00n_open_graph_settings', $this->options );
				}
			} elseif ( $v < $this->version ) {
					// Any version upgrade.
					$upgrade = true;
					// We should do any upgrade we need, right here.
				if ( $v < '2.0.8' ) {
					$this->options['fb_declaration_method'] = 'xmlns';
					update_option( 'wonderm00n_open_graph_settings', $this->options );
				}
			}
			// Set version on database.
			if ( $upgrade ) {
				update_option( 'wonderm00n_open_graph_version', $this->version );
			}
		}

		/**
		 * Set Open Graph image dimensions.
		 *
		 * Sets the default image width and height for Open Graph images.
		 * Dimensions can be filtered using the 'fb_og_image_size' filter.
		 *
		 * @since 1.0.0
		 */
		public function set_image_sizes() {
			$size        = apply_filters( 'fb_og_image_size', array( $this->img_w, $this->img_h ) );
			$this->img_w = $size[0];
			$this->img_h = $size[1];
		}

		/**
		 * Add excerpt support to pages post type.
		 *
		 * Enables excerpt functionality for pages to allow better description
		 * generation for Open Graph tags.
		 *
		 * @since 1.0.0
		 */
		public function add_excerpts_to_pages() {
			add_post_type_support( 'page', 'excerpt' );
		}

		/**
		 * Check if WPML plugin is active.
		 *
		 * Determines if WPML (WordPress Multilingual Plugin) is installed and active.
		 *
		 * @since 1.0.0
		 * @return bool True if WPML is active, false otherwise.
		 */
		public function is_wpml_active() {
			if ( function_exists( 'icl_object_id' ) && function_exists( 'icl_register_string' ) ) {
				global $sitepress;
				if ( is_object( $sitepress ) ) {
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

		/**
		 * Get WordPress locale mapped to Facebook locale format.
		 *
		 * Retrieves the current WordPress locale and maps it to a Facebook-compatible
		 * locale format, as Facebook doesn't support all WordPress locales.
		 *
		 * @since 1.0.0
		 * @return string Facebook-compatible locale string.
		 */
		public function get_locale() {
			$locale = get_locale();
			// Facebook doesn't has all the WordPress locales.
			$locale_mappings = array(
				'af'             => 'af_ZA',
				'ar'             => 'ar_AR',
				'ary'            => 'ar_AR',
				'as'             => 'as_IN',
				'az'             => 'az_AZ',
				'azb'            => 'az_AZ',
				'bel'            => 'be_BY',
				'bn_BD'          => 'bn_IN',
				'bo'             => 'bp_IN',
				'ca'             => 'ca_ES',
				'ceb'            => 'cx_PH',
				'ckb'            => 'cb_IQ',
				'cy'             => 'cy_GB',
				'de_CH'          => 'de_DE',
				'de_CH_informal' => 'de_DE',
				'de_DE_formal'   => 'de_DE',
				'el'             => 'el_GR',
				'en_AU'          => 'en_GB',
				'en_CA'          => 'en_US',
				'en_NZ'          => 'en_GB',
				'en_ZA'          => 'en_GB',
				'eo'             => 'eo_EO',
				'es_AR'          => 'es_ES',
				'es_CL'          => 'es_ES',
				'es_CO'          => 'es_ES',
				'es_GT'          => 'es_ES',
				'es_PE'          => 'es_ES',
				'es_VE'          => 'es_ES',
				'et'             => 'et_EE',
				'eu'             => 'eu_ES',
				'fi'             => 'fi_FI',
				'fr_BE'          => 'fr_FR',
				'gd'             => 'ga_IE',
				'gu'             => 'gu_IN',
				'hr'             => 'hr_HR',
				'hy'             => 'hy_AM',
				'ja'             => 'ja_JP',
				'km'             => 'km_KH',
				'lo'             => 'lo_LA',
				'lv'             => 'lv_LV',
				'mn'             => 'mn_MN',
				'mr'             => 'mr_IN',
				'nl_NL_formal'   => 'nl_NL',
				'ps'             => 'ps_AF',
				'pt_PT_ao90'     => 'pt_PT',
				'sah'            => 'ky_KG',
				'sq'             => 'sq_AL',
				'te'             => 'te_IN',
				'th'             => 'th_TH',
				'tl'             => 'tl_PH',
				'uk'             => 'uk_UA',
				'ur'             => 'ur_PK',
				'vi'             => 'vi_VN',
			);
			if ( isset( $locale_mappings[ $locale ] ) ) {
				$locale = $locale_mappings[ $locale ];
			}
			return trim( $locale );
		}

		/**
		 * Check if Yoast SEO plugin is active.
		 *
		 * Determines if Yoast SEO plugin is installed and active by checking
		 * for the WPSEO_VERSION constant.
		 *
		 * @since 1.0.0
		 * @return bool True if Yoast SEO is active, false otherwise.
		 */
		public function is_yoast_seo_active() {
			if ( defined( 'WPSEO_VERSION' ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if All in One SEO Pack plugin is active.
		 *
		 * Determines if All in One SEO Pack plugin is installed and active by checking
		 * for the AIOSEOP_VERSION constant.
		 *
		 * @since 1.0.0
		 * @return bool True if All in One SEO Pack is active, false otherwise.
		 */
		public function is_aioseop_active() {
			if ( defined( 'AIOSEOP_VERSION' ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if WooCommerce plugin is active.
		 *
		 * Determines if WooCommerce plugin is installed and active by checking
		 * the active plugins list.
		 *
		 * @since 1.0.0
		 * @return bool True if WooCommerce is active, false otherwise.
		 */
		public function is_woocommerce_active() {
			return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}

		/**
		 * Check if Subheading plugin is active.
		 *
		 * Determines if the Subheading plugin is installed and active by checking
		 * for the SubHeading class and get_the_subheading function.
		 *
		 * @since 1.0.0
		 * @return bool True if Subheading plugin is active, false otherwise.
		 */
		public function is_subheading_plugin_active() {
			if ( class_exists( 'SubHeading' ) && function_exists( 'get_the_subheading' ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Check if Business Directory Plugin is active.
		 *
		 * Determines if Business Directory Plugin is installed and active by checking
		 * the active plugins list.
		 *
		 * @since 1.0.0
		 * @return bool True if Business Directory Plugin is active, false otherwise.
		 */
		public function is_business_directory_active() {
			@include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'business-directory-plugin/business-directory-plugin.php' ) ) {
				return true;
			}
			return false;
		}
	}
endif;
