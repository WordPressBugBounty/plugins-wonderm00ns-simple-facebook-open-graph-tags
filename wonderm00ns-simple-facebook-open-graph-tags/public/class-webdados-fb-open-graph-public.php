<?php
/**
 * Public/frontend functionality class file.
 *
 * This file contains the Webdados_FB_Public class which handles all frontend functionality
 * including Open Graph meta tag generation, Twitter Cards, Schema.org markup, image processing,
 * and RSS feed enhancements. It outputs meta tags in the HTML head section.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Public
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

if ( ! class_exists( 'Webdados_FB_Public' ) ) :
	/**
	 * Public/frontend functionality class.
	 *
	 * Handles all frontend functionality including Open Graph meta tag generation, Twitter Cards,
	 * Schema.org markup, image processing and overlay, RSS feed enhancements, and HTML namespace
	 * modifications. This class outputs all meta tags in the HTML head section.
	 *
	 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
	 * @subpackage Public
	 * @since    1.0.0
	 */
	class Webdados_FB_Public {
		/**
		 * Plugin version.
		 *
		 * @since 1.0.0
		 * @var string $version Plugin version.
		 */
		private $version;

		/**
		 * Database options.
		 *
		 * @since 1.0.0
		 * @var array $options Database options.
		 */
		private $options;

		/**
		 * Image Size temporary holder.
		 *
		 * @since 1.0.0
		 * @var bool $image_size Image Size temporary holder.
		 */
		private $image_size = false;

		/**
		 * BDP Post temporary holder.
		 *
		 * @since 1.0.0
		 * @var bool $post BDP Post temporary holder.
		 */
		private $post = false;

		/**
		 * Use partial image when getting it's size?
		 *
		 * @since 1.0.0
		 * @var bool $image_size_use_partial Use partial image when getting it's size?
		 */
		private $image_size_use_partial = false;

		/**
		 * Cache / transient validity - Default: one week.
		 *
		 * @since 1.0.0
		 * @var int $transient_validity Cache / transient validity.
		 */
		private $transient_validity = WEEK_IN_SECONDS;



		/**
		 * Constructor.
		 *
		 * Initializes the public class with options and version.
		 *
		 * @since 1.0.0
		 * @param array  $options Plugin options array.
		 * @param string $version Plugin version.
		 */
		public function __construct( $options, $version ) {
			$this->options = $options;
			$this->version = $version;
		}



		/**
		 * Store post object for later use.
		 *
		 * Captures the post object on singular pages for use in meta tag generation.
		 * This is needed because some plugins (like Business Directory Plugin) modify
		 * the global $post object.
		 *
		 * @since 1.0.0
		 * @param WP_Post $post Post object.
		 * @return WP_Post Post object.
		 */
		public function get_post( $post ) {
			if ( is_singular() ) {
				$this->post = $post;
			}
			return $post;
		}



		/**
		 * Insert Open Graph, Twitter Card, and Schema.org meta tags in HTML head.
		 *
		 * Generates and outputs all meta tags for Open Graph, Twitter Cards,
		 * Schema.org markup, and SEO meta tags based on current page context.
		 *
		 * @since 1.0.0
		 */
		public function insert_meta_tags() {
			global $webdados_fb, $wp_query;

			$debug = array();

			if ( ! apply_filters( 'fb_og_disable', false ) ) {

				// Open tag.
				$html = '
    <!-- START - ' . WEBDADOS_FB_PLUGIN_NAME . ' ' . WEBDADOS_FB_VERSION . ' -->
    ';

				if ( apply_filters( 'fb_og_enabled', true ) ) {

					// Partial image - Since 2.2 we do NOT get partial by default anymore - Advanced users can use this filter to use it again.
					$this->image_size_use_partial = apply_filters( 'fb_og_image_size_use_partial', false );
					// If we're using partial image, we lower the transient validity to one day.
					if ( $this->image_size_use_partial ) {
						$this->transient_validity = DAY_IN_SECONDS;
					}

					// Also set Title Tag? - Needed??
					$fb_set_title_tag = 0;

					// Init values.
					$fb_locale                = '';
					$fb_title                 = '';
					$fb_url                   = '';
					$fb_desc                  = '';
					$fb_image                 = '';
					$fb_type                  = 'article';
					$fb_author                = '';
					$fb_author_meta           = '';
					$fb_author_linkrelgp      = '';
					$fb_author_twitter        = '';
					$fb_article_pub_date      = '';
					$fb_article_mod_date      = '';
					$fb_image_additional      = array();
					$fb_additional_tags       = array(
						'name'     => array(),
						'property' => array(),
					);
					$fb_publisher             = trim( $this->options['fb_publisher'] );
					$fb_publisher_schema      = trim( $this->options['fb_publisher_schema'] );
					$fb_publisher_twitteruser = trim( $this->options['fb_publisher_twitteruser'] );

					// Homepage Description.
					switch ( $this->options['fb_desc_homepage'] ) {
						case 'custom':
							$fb_desc_homepage = $this->options['fb_desc_homepage_customtext'];
							// WPML?
							if ( $webdados_fb->is_wpml_active() ) {
								global $sitepress;
								if ( ICL_LANGUAGE_CODE != $sitepress->get_default_language() ) {
									$fb_desc_homepage = icl_t( 'wd-fb-og', 'wd_fb_og_desc_homepage_customtext', $fb_desc_homepage );
								}
							}
							break;
						default:
							$fb_desc_homepage = get_bloginfo( 'description' );
							break;
					}

					if ( is_singular() ) { // Including homepage if set as static page.

						$debug[] = 'is_singular';

						global $post;
						// Title.

						$_webdados_fb_open_graph_specific_title = get_post_meta( $post->ID, '_webdados_fb_open_graph_specific_title', true );

						if ( empty( $_webdados_fb_open_graph_specific_title ) ) {
							// It's a Post or a Page or an attachment page - It can also be the homepage if it's set as a page.
							$fb_title = wp_strip_all_tags( stripslashes( $post->post_title ), true );
							// SubHeading.
							if ( isset( $this->options['fb_show_subheading'] ) && ( 1 === intval( $this->options['fb_show_subheading'] ) ) && $webdados_fb->is_subheading_plugin_active() ) {
								if ( isset( $this->options['fb_subheading_position'] ) && 'before' === $this->options['fb_subheading_position'] ) {
									$fb_title = trim( trim( get_the_subheading() ) . ' - ' . trim( $fb_title ), ' -' );
								} else {
									$fb_title = trim( trim( $fb_title ) . ' - ' . trim( get_the_subheading() ), ' -' );
								}
							}
						} else {
							$fb_title = $_webdados_fb_open_graph_specific_title;
						}

						// URL.
							$fb_url = get_permalink();
						// Type if it's a homepage page.
						if ( is_front_page() ) {
							/* Fix homepage type when it's a static page. */
							$fb_url  = get_option( 'home' ) . ( 1 == intval( $this->options['fb_url_add_trailing'] ) ? '/' : '' );
							$fb_type = trim( '' === $this->options['fb_type_homepage'] ? 'website' : $this->options['fb_type_homepage'] );
						}
						// Description.
						$fb_desc = trim( get_post_meta( $post->ID, '_webdados_fb_open_graph_specific_description', true ) );
						if ( $fb_desc ) {

							$my_secure_variable = true;

						} elseif ( '' !== trim( $post->post_excerpt ) ) {
								// If there's an excerpt that's what we'll use.
								$fb_desc = trim( $post->post_excerpt );
						} else {
							// If not we grab it from the content.
							$fb_desc = trim( $post->post_content );
						}
						// Image.
						if ( 1 == intval( $this->options['fb_image_show'] ) || 1 == intval( $this->options['fb_image_show_schema'] ) || 1 == intval( $this->options['fb_image_show_twitter'] ) ) {
							$fb_image = $this->get_post_image();
						}
						// Author.
							$author_id = $post->post_author;
						if ( $author_id > 0 && ! ( is_page() && 1 == intval( $this->options['fb_author_hide_on_pages'] ) ) ) {
							$fb_author           = get_the_author_meta( 'facebook', $author_id );
							$fb_author_meta      = get_the_author_meta( 'display_name', $author_id );
							$fb_author_linkrelgp = get_the_author_meta( 'googleplus', $author_id );
							$fb_author_twitter   = get_the_author_meta( 'twitter', $author_id );
						}

						// Published and Modified time - We should check this out and maybe have it for any kind of post...
						if ( is_singular( 'post' ) ) {
							$fb_article_pub_date = get_the_date( 'c' );
							$fb_article_mod_date = get_the_modified_date( 'c' );
						} else {
							// Reset dates show because we're not on posts.
							$this->options['fb_article_dates_show'] = 0;
						}
						// Sections.
						if ( is_singular( 'post' ) ) {
							$cats = get_the_category();
							if ( ! is_wp_error( $cats ) && ( is_array( $cats ) && count( $cats ) > 0 ) ) {
								$fb_sections = array();
								foreach ( $cats as $cat ) {
									$fb_sections[] = $cat->name;
								}
							}
						} else {
							$this->options['fb_article_sections_show'] = 0;
						}
						// Business Directory Plugin.
						if ( isset( $this->options['fb_show_businessdirectoryplugin'] ) && $webdados_fb->is_business_directory_active() ) {
							global $wpbdp;
							$bdp_action          = wpbdp_current_action();
							$bdp_disable_cpt     = wpbdp_get_option( 'disable-cpt' );
							$current_view_object = $wpbdp->dispatcher->current_view_object();
							switch ( $bdp_action ) {
								case 'show_listing':
									$fb_title         = trim( wp_strip_all_tags( stripslashes( $this->post->post_title ), true ) . ' - ' . $fb_title, ' -' );
									$fb_set_title_tag = 1;
									$fb_url           = get_permalink( $this->post->ID );
									if ( '' != trim( $this->post->post_excerpt ) ) {
										// If there's an excerpt that's what we'll use.
										$fb_desc = trim( $this->post->post_excerpt );
									} else {
										// If not we grab it from the content.
										$fb_desc = trim( $this->post->post_content );
									}
									if ( 1 == intval( $this->options['fb_image_show'] ) || 1 == intval( $this->options['fb_image_show_schema'] ) || 1 == intval( $this->options['fb_image_show_twitter'] ) ) {
										$thumbdone = false;
										if ( 1 == intval( $this->options['fb_image_use_featured'] ) ) {
											// Featured.
											$id_attachment = get_post_thumbnail_id( $this->post->ID );
											if ( $id_attachment ) {
												// There's a featured/thumbnail image for this listing.
												$fb_image  = wp_get_attachment_url( $id_attachment, false );
												$thumbdone = true;
											}
										}
										if ( ! $thumbdone ) {
											// Main image loaded.
											$thumbnail_id = wpbdp_listings_api()->get_thumbnail_id( $this->post->ID );
											if ( $thumbnail_id ) {
												$fb_image  = wp_get_attachment_url( $thumbnail_id, false );
												$thumbdone = true;
											}
										}
									}
							}
						}
						// WooCommerce.
						if ( $webdados_fb->is_woocommerce_active() && is_product() ) {
							$debug[] = 'is_product';
							$fb_type = 'product';
							$product = new WC_Product( $post->ID );
							// Price.
							$price    = version_compare( WC_VERSION, '3.0', '>=' ) ? wc_get_price_including_tax( $product ) : $product->get_price_including_tax();
							$currency = get_woocommerce_currency();
							$fb_additional_tags['property']['product_price_amount'] = array(
								$price,
							);
							if ( function_exists( 'get_woocommerce_currency' ) ) {
								$fb_additional_tags['property']['product_price_currency'] = array(
									$currency,
								);
							}
							$fb_additional_tags['name']['twitter_label1'] = array(
								__( 'Price', 'wonderm00ns-simple-facebook-open-graph-tags' ),
							);
							if ( function_exists( 'get_woocommerce_currency' ) ) {
								$fb_additional_tags['name']['twitter_data1'] = array(
									$price . ' ' . get_woocommerce_currency(),
								);
							}
							// Stock.
							if ( $product->is_in_stock() ) {
								$fb_additional_tags['property']['product_availability'] = array(
									'instock',
								);
							} else {
								$fb_additional_tags['property']['product_availability'] = array(
									'oos',
								);
							}
							// Additional product images?
							if ( 1 == intval( $this->options['fb_image_show'] ) && 1 == $this->options['fb_wc_useproductgallery'] ) {
								$attachment_ids = version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
								if ( $attachment_ids ) {
									foreach ( $attachment_ids as $attachment_id ) {
										$image_link = wp_get_attachment_url( $attachment_id );
										if ( $image_link ) {
											if ( '' != trim( $image_link ) ) {
												$fb_image_additional[] = array(
													'fb_image' => trim( $image_link ),
													'png_overlay' => ( intval( $this->options['fb_wc_usepg_png_overlay'] ) ? true : false ),
												);
											}
										}
									}
								}
							}
						}
					} else {

						// Other pages - Defaults.
						$fb_title = wp_strip_all_tags( stripslashes( get_bloginfo( 'name' ) ), true );
						$fb_url   = ( ( ! empty( $_SERVER['HTTPS'] ) && isset( $_SERVER['HTTP_HOST'] ) && 'on' == sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) ? 'https://' : 'http://' ) . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );  // Not really canonical but will work for now.
						if ( isset( $_SERVER['REQUEST_URI'] ) ) {
							$fb_url = $fb_url . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
						}
						$fb_image = 1 == intval( $this->options['fb_image_use_default'] ) ? trim( $this->options['fb_image'] ) : '';

						$this->options['fb_article_sections_show'] = 0;
						$this->options['fb_article_dates_show']    = 0;
						$this->options['fb_author_show']           = 0;
						$this->options['fb_author_show_meta']      = 0;
						$this->options['fb_author_show_linkrelgp'] = 0;
						$this->options['fb_author_show_twitter']   = 0;

						// Category.
						if ( is_category() ) {
							$debug[]  = 'is_category';
							$fb_title = wp_strip_all_tags( stripslashes( single_cat_title( '', false ) ), true );
							$term     = $wp_query->get_queried_object();
							if ( $term ) {
								$fb_url = get_term_link( $term, $term->taxonomy );
							}
							$cat_desc = trim( wp_strip_all_tags( stripslashes( category_description() ), true ) );
							if ( '' != trim( $cat_desc ) ) {
								$fb_desc = $cat_desc;
							}
						} elseif ( is_tag() ) {
							$debug[]  = 'is_tag';
							$fb_title = wp_strip_all_tags( stripslashes( single_tag_title( '', false ) ), true );
							$term     = $wp_query->get_queried_object();
							$fb_url   = get_term_link( $term, $term->taxonomy );
							$tag_desc = trim( wp_strip_all_tags( stripslashes( tag_description() ), true ) );
							if ( '' != trim( $tag_desc ) ) {
								$fb_desc = $tag_desc;
							}
						} elseif ( is_tax() ) {
							$fb_title = wp_strip_all_tags( stripslashes( single_term_title( '', false ) ), true );
							$term     = $wp_query->get_queried_object();
							$fb_url   = get_term_link( $term, $term->taxonomy );
							$debug[]  = 'is_tax: ' . $term->taxonomy;
							$tax_desc = trim( wp_strip_all_tags( stripslashes( term_description() ), true ) );
							if ( '' != trim( $tax_desc ) ) {
											$fb_desc = $tax_desc;
							}
							// WooCommerce.
							if ( $webdados_fb->is_woocommerce_active() && ( 1 == intval( $this->options['fb_wc_usecategthumb'] ) ) && ( is_product_category() || is_tax( 'product_brand' ) ) ) {
								if ( is_product_category() ) {
									$debug[] = 'is_product_category';
								}
								if ( 1 == intval( $this->options['fb_image_show'] ) || 1 == intval( $this->options['fb_image_show_schema'] ) || 1 == intval( $this->options['fb_image_show_twitter'] ) ) {
									$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
									if ( $thumbnail_id ) {
										$image = wp_get_attachment_url( $thumbnail_id );
										if ( $image ) {
											$fb_image = $image;
										}
									}
								}
							}
						} elseif ( is_search() ) {
							$debug[]  = 'is_search';
							$fb_title = wp_strip_all_tags( stripslashes( __( 'Search for', 'wonderm00ns-simple-facebook-open-graph-tags' ) . ' "' . get_search_query() . '"' ), true );
							$fb_url   = get_search_link();
						} elseif ( is_author() ) {
											$debug[]  = 'is_author';
											$fb_title = wp_strip_all_tags( stripslashes( get_the_author_meta( 'display_name', get_query_var( 'author' ) ) ), true );
											$fb_url   = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
						} elseif ( is_archive() ) {
								$debug[] = 'is_archive';
							if ( is_day() ) {
											$debug[]  = 'is_day';
											$fb_title = wp_strip_all_tags( stripslashes( get_query_var( 'day' ) . ' ' . single_month_title( ' ', false ) . ' ' . __( 'Archives', 'wonderm00ns-simple-facebook-open-graph-tags' ) ), true );
											$fb_url   = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
							} elseif ( is_month() ) {
													$debug[]  = 'is_month';
													$fb_title = wp_strip_all_tags( stripslashes( single_month_title( ' ', false ) . ' ' . __( 'Archives', 'wonderm00ns-simple-facebook-open-graph-tags' ) ), true );
													$fb_url   = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
							} elseif ( is_year() ) {
																			$debug[]  = 'is_year';
																			$fb_title = wp_strip_all_tags( stripslashes( get_query_var( 'year' ) . ' ' . __( 'Archives', 'wonderm00ns-simple-facebook-open-graph-tags' ) ), true );
																			$fb_url   = get_year_link( get_query_var( 'year' ) );
							}
						} elseif ( is_front_page() ) {
							// Regular homepage.
								$debug[] = 'is_front_page';
								$fb_url  = get_option( 'home' ) . ( 1 == intval( $this->options['fb_url_add_trailing'] ) ? '/' : '' );
								$fb_type = trim( '' == $this->options['fb_type_homepage'] ? 'website' : $this->options['fb_type_homepage'] );
								$fb_desc = $fb_desc_homepage;
						} elseif ( is_home() ) {
							// Blog page (set as page).
								$debug[]    = 'is_home';
							$page_for_posts = get_option( 'page_for_posts' );
							if ( 'page' === get_option( 'show_on_front' ) && $page_for_posts ) {
								// $post = get_post( $page_for_posts ); //This is NOT the global $post and it's actually not needed because we'll use the post ID = $page_for_posts.
								// Blog page.
								$fb_type = trim( '' == $this->options['fb_type_homepage'] ? 'website' : $this->options['fb_type_homepage'] );
								$fb_desc = trim( get_post_meta( $page_for_posts, '_webdados_fb_open_graph_specific_description', true ) );
								if ( $fb_desc ) {
									$my_secure_variable = true;
								} else {
									// Use default.
									$fb_desc = $fb_desc_homepage;
								}
								if ( 1 == intval( $this->options['fb_image_show'] ) || 1 == intval( $this->options['fb_image_show_schema'] ) || 1 == intval( $this->options['fb_image_show_twitter'] ) ) {
									$fb_image = $this->get_post_image( $page_for_posts );
								}
							}
						} else {
							$my_secure_variable = true;
						}
					}

					// og:type for WPML root page?
					if ( $webdados_fb->is_wpml_active() ) {
						if ( class_exists( 'WPML_Root_Page' ) ) {
							if ( WPML_Root_Page::is_current_request_root() ) {
								$fb_type = trim( '' == $this->options['fb_type_homepage'] ? 'website' : $this->options['fb_type_homepage'] );
							}
						}
					}

					// Default description, if empty until now.
					if ( '' == trim( $fb_desc ) ) {
						switch ( $this->options['fb_desc_default_option'] ) {
							case 'custom':
								$fb_desc = $this->options['fb_desc_default'];
								// WPML?
								if ( $webdados_fb->is_wpml_active() ) {
									global $sitepress;
									if ( ICL_LANGUAGE_CODE != $sitepress->get_default_language() ) {
										$fb_desc = icl_t( 'wd-fb-og', 'wd_fb_og_fb_desc_default', $fb_desc );
									}
								}
								break;
							default:
								$fb_desc = $fb_desc_homepage;
								break;
						}
					}

					// Trim description.
					$fb_desc = trim( str_replace( '&nbsp;', ' ', $fb_desc ) ); // Non-breaking spaces are usefull on a meta description. We'll just convert them to normal spaces to really trim it.
					$fb_desc = trim(
						intval( $this->options['fb_desc_chars'] ) > 0
								?
								mb_substr( wp_strip_all_tags( strip_shortcodes( stripslashes( $fb_desc ), true ) ), 0, intval( $this->options['fb_desc_chars'] ) )
								:
								wp_strip_all_tags( strip_shortcodes( stripslashes( $fb_desc ), true ) )
					);

					// YOAST SEO?
					if ( 1 == $this->options['fb_show_wpseoyoast'] ) {
						if ( $webdados_fb->is_yoast_seo_active() ) {
							$debug[] = 'yoast_seo';
							if ( version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
								// https://developer.yoast.com/blog/yoast-seo-14-0-using-yoast-seo-surfaces/.
								$fb_title_temp = YoastSEO()->meta->for_current_page()->title;
								$fb_url_temp   = YoastSEO()->meta->for_current_page()->canonical;
								$fb_desc_temp  = YoastSEO()->meta->for_current_page()->description;

								/**
								 * If we don't get it, we try the old way.
								 * NO WE DON'T
								 *
								 * @see https://wordpress.org/support/topic/error-class-wpseo_frontend-not-found-after-updating-yoast-seo-to-17-8/.
								 * if (
								 * (
								 * trim( $fb_title_temp ) == ''
								 * ||
								 * trim( $fb_url_temp ) == ''
								 * ||
								 * trim( $fb_desc_temp ) == ''
								 * )
								 * &&
								 * class_exists( 'WPSEO_Frontend' )
								 * ) {
								 * $wpseo = WPSEO_Frontend::get_instance();
								 * if ( trim( $fb_title_temp ) == '' ) $fb_title_temp = @$wpseo->title( false );
								 * if ( trim( $fb_url_temp ) == '' )   $fb_url_temp   = @$wpseo->canonical( false );
								 * if ( trim( $fb_desc_temp ) == '' )  $fb_desc_temp  = @$wpseo->metadesc( false );
								 * }
								 */

							} else {
								$wpseo         = WPSEO_Frontend::get_instance();
								$fb_title_temp = $wpseo->title( false );
								$fb_url_temp   = $wpseo->canonical( false );
								$fb_desc_temp  = $wpseo->metadesc( false );
							}
							// Title.
							$fb_title = wp_strip_all_tags( '' != trim( $fb_title_temp ) ? trim( $fb_title_temp ) : $fb_title, true );
							// Title - SubHeading plugin.
							if ( '' != $fb_title_temp && 1 == $this->options['fb_show_subheading'] ) {
								if ( $webdados_fb->is_subheading_plugin_active() ) {
									if ( isset( $this->options['fb_subheading_position'] ) && 'before' == $this->options['fb_subheading_position'] ) {
										$fb_title = trim( trim( get_the_subheading() ) . ' - ' . trim( $fb_title ), ' -' );
									} else {
										$fb_title = trim( trim( $fb_title ) . ' - ' . trim( get_the_subheading() ), ' -' );
									}
								}
							}
							// URL.
							$fb_url = wp_strip_all_tags( '' != trim( $fb_url_temp ) ? trim( $fb_url_temp ) : $fb_url, true );
							// Description.
							$fb_desc = wp_strip_all_tags( '' != trim( $fb_desc_temp ) ? trim( $fb_desc_temp ) : $fb_desc, true );
						}
					}

					// All in One SEO Pack?
					if ( 1 == $this->options['fb_show_aioseop'] ) {
						if ( $webdados_fb->is_aioseop_active() ) {
							$debug[] = 'aio_seo';
							global $aiosp;
							// Title - Why are we getting the first post title on archives and homepage...?!?
							$fb_title_temp = $aiosp->orig_title;
							$fb_title      = wp_strip_all_tags( '' != trim( $fb_title_temp ) ? trim( $fb_title_temp ) : $fb_title, true );
							// Title - SubHeading plugin.
							if ( '' != $fb_title_temp && 1 == $this->options['fb_show_subheading'] ) {
								if ( $webdados_fb->is_subheading_plugin_active() ) {
									if ( isset( $this->options['fb_subheading_position'] ) && 'before' == $this->options['fb_subheading_position'] ) {
										$fb_title = trim( trim( get_the_subheading() ) . ' - ' . trim( $fb_title ), ' -' );
									} else {
										$fb_title = trim( trim( $fb_title ) . ' - ' . trim( get_the_subheading() ), ' -' );
									}
								}
							}

							/**
							 * URL - See aioseop_class.php 3898 - We have a problem because wp_query is not the same right now.
							 *
							 * $fb_url_temp = '';
							 * $aioseop_options = get_option( 'aioseop_options' );
							 * $opts = $aiosp->meta_opts;
							 * $show_page = true;
							 * if ( ! empty( $aioseop_options['aiosp_no_paged_canonical_links'] ) ) {
							 * $show_page = false;
							 * }
							 * if ( $aioseop_options['aiosp_can'] ) {
							 * if ( ! empty( $aioseop_options['aiosp_customize_canonical_links'] ) && ! empty( $opts['aiosp_custom_link'] ) ) {
							 * $fb_url_temp = $opts['aiosp_custom_link'];
							 * }
							 * if ( empty( $url ) ) {
							 * $fb_url_temp = $aiosp->aiosp_mrt_get_url( $wp_query, $show_page );
							 * }
							 *
							 * $fb_url_temp = $aiosp->validate_url_scheme( $fb_url_temp );
							 *
							 * $fb_url_temp = apply_filters( 'aioseop_canonical_url', $fb_url_temp );
							 * }
							 * $fb_url = wp_strip_all_tags( trim($fb_url_temp)!='' ? trim($fb_url_temp) : $fb_url, true);
							 */

							// Description - Why are we getting the first post description on archives and homepage...?!?
							if ( is_home() && ! is_front_page() ) {
                                // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								$post = aiosp_common::get_blog_page();
							} else {
                                // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								$post = $aiosp->get_queried_object();
							}
							$fb_desc_temp = apply_filters( 'aioseop_description', $aiosp->get_main_description( $post ) );
							$fb_desc      = wp_strip_all_tags( '' != trim( $fb_desc_temp ) ? trim( $fb_desc_temp ) : $fb_desc, true );

						}
					}

					// Private post or password protected? (Thanks Benoît).
					if ( is_singular() && ( 'private' == get_post_status( $post->ID ) || ! empty( $post->post_password ) ) ) {
						$fb_desc = '';
					} elseif ( '' == $fb_image && 1 == intval( $this->options['fb_image_use_mshot'] ) && ! empty( $fb_url ) ) {
						// No size and no overlay.
						$this->options['fb_image_size_show'] = 0;
						$this->options['fb_image_overlay']   = 0;
						$fb_image                            = 'https://s0.wordpress.com/mshots/v1/' . urlencode( $fb_url ) . '?w=1200&h=630';
					}

					// Apply Filters.
					$fb_app_id           = apply_filters( 'fb_og_app_id', $this->options['fb_app_id'] );
					$fb_locale           = apply_filters( 'fb_og_locale', $fb_locale );
					$fb_title            = apply_filters( 'fb_og_title', $fb_title );
					$fb_url              = apply_filters( 'fb_og_url', $fb_url );
					$fb_type             = apply_filters( 'fb_og_type', $fb_type );
					$fb_desc             = apply_filters( 'fb_og_desc', $fb_desc );
					$fb_image            = apply_filters( 'fb_og_image', $fb_image );
					$fb_image_additional = apply_filters( 'fb_og_image_additional', $fb_image_additional );

					// Image size.
					$fb_image_size = false;
					if ( 1 == intval( $this->options['fb_image_show'] ) && '' != trim( $fb_image ) ) {
						if ( 1 == intval( $this->options['fb_image_size_show'] ) && 0 == intval( $this->options['fb_adv_disable_image_size'] ) ) {
							if ( isset( $this->image_size ) && is_array( $this->image_size ) ) { // Already fetched.
								$fb_image_size = $this->image_size;
							} else {
								$fb_image_size = $this->get_open_graph_image_size( $fb_image );
							}
						}
					} else {
						$this->options['fb_image_show'] = 0;
					}

					// Image overlay.
					// Object queried.
					$img_overlay_params = array(
						'is_home'       => is_home(),
						'is_front_page' => is_front_page(),
						'object_type'   => '',
						'post_id'       => '', // Legacy support - do not remove.
					);

					$object = get_queried_object();
					if ( $object ) {
						switch ( get_class( $object ) ) {
							case 'WP_Post':
								// $this->is_posts_page || $this->is_singular.
								$img_overlay_params['object_type'] = 'post';
								$img_overlay_params['object_id']   = $object->ID;
								$img_overlay_params['post_id']     = $object->ID;
								break;
							case 'WP_Post_Type':
								// $this->is_post_type_archive.
								$img_overlay_params['object_type'] = 'post_type';
								$img_overlay_params['object_id']   = $object->name;
								break;
							case 'WP_Term':
								// $this->is_category || $this->is_tag || $this->is_tax.
								$img_overlay_params['object_type'] = 'term';
								$img_overlay_params['taxonomy']    = $object->taxonomy;
								$img_overlay_params['object_id']   = $object->term_id;
								break;
							case 'WP_User':
								// $this->is_author.
								$img_overlay_params['object_type'] = 'user';
								$img_overlay_params['object_id']   = $object->ID;
								break;
							default:
								// We should be looking into other types of objects?
								break;
						}
					}
					// Should we do it?
					if (
						( 1 == intval( $this->options['fb_image_show'] ) )
						&&
						( 1 == intval( $this->options['fb_image_overlay'] ) )
						&&
						( ! (
							( 1 == intval( $this->options['fb_image_overlay_not_for_default'] ) )
							&&
							( trim( $fb_image ) == trim( $this->options['fb_image_overlay_image'] ) )
							)
						)
						&&
						apply_filters( 'fb_og_image_overlay', true, $fb_image, $img_overlay_params )
					) {
						$debug[] = 'image overlay';

						// The main one.
						$temp_fb_image_overlay = $this->get_image_with_overlay( $fb_image, $img_overlay_params, false );
						if ( $temp_fb_image_overlay['overlay'] ) {
							$fb_image = $temp_fb_image_overlay['fb_image'];
							// We know the exact size now. We better just show it, right?
							$this->options['fb_image_size_show'] = 1;
							$fb_image_size                       = array( $webdados_fb->img_w, $webdados_fb->img_h );
						}
						// Additional.
						if ( isset( $fb_image_additional ) && is_array( $fb_image_additional ) && count( $fb_image_additional ) > 0 ) {
							foreach ( $fb_image_additional as $key => $value ) {
								if ( isset( $value['png_overlay'] ) && $value['png_overlay'] ) {
									$temp_fb_image_overlay = $this->get_image_with_overlay( $value['fb_image'], $img_overlay_params, true );
									if ( $temp_fb_image_overlay['overlay'] ) {
										$fb_image_additional[ $key ]['fb_image'] = $temp_fb_image_overlay['fb_image'];
									}
								}
							}
						}
					}

					// No spaces on URLs.
					if ( isset( $fb_url ) && '' != trim( $fb_url ) ) {
						$fb_url = str_replace( ' ', '%20', trim( $fb_url ) );
					}
					if ( isset( $fb_publisher ) && '' != trim( $fb_publisher ) ) {
						$fb_publisher = str_replace( ' ', '%20', trim( $fb_publisher ) );
					}
					if ( isset( $fb_publisher_schema ) && '' != trim( $fb_publisher_schema ) ) {
						$fb_publisher_schema = str_replace( ' ', '%20', trim( $fb_publisher_schema ) );
					}
					if ( isset( $fb_author ) && '' != trim( $fb_author ) ) {
						$fb_author = str_replace( ' ', '%20', trim( $fb_author ) );
					}
					if ( isset( $fb_author_linkrelgp ) && '' != trim( $fb_author_linkrelgp ) ) {
						$fb_author_linkrelgp = str_replace( ' ', '%20', trim( $fb_author_linkrelgp ) );
					}
					if ( isset( $fb_image ) && '' != trim( $fb_image ) ) {
						$fb_image = str_replace( ' ', '%20', trim( $fb_image ) );
					}
					if ( isset( $fb_image_additional ) && is_array( $fb_image_additional ) && count( $fb_image_additional ) ) {
						foreach ( $fb_image_additional as $key => $value ) {
							if ( isset( $value['fb_image'] ) ) {
								$fb_image_additional[ $key ]['fb_image'] = str_replace( ' ', '%20', trim( $value['fb_image'] ) );
							}
						}
					}

					// If there's still no description let's just add the title as a last resort.
					if ( '' == trim( $fb_desc ) ) {
						$fb_desc = $fb_title;
					}

					// Print tags.
						// Facebook.
					$html .= ' <!-- Facebook Open Graph -->
    ';
						// Locale.
					if ( 1 == intval( $this->options['fb_locale_show'] ) ) {
						$html .= '  <meta property="og:locale" content="' . esc_attr( trim( '' != trim( $this->options['fb_locale'] ) ? trim( $this->options['fb_locale'] ) : $webdados_fb->get_locale() ) ) . '"/>
    ';
					}
						// Site name.
					if ( 1 == intval( $this->options['fb_sitename_show'] ) ) {
						$html .= '  <meta property="og:site_name" content="' . esc_attr( trim( get_bloginfo( 'name' ) ) ) . '"/>
    ';
					}
						// Title.
					if ( 1 == intval( $this->options['fb_title_show'] ) && '' != trim( $fb_title ) ) {
						$html .= '  <meta property="og:title" content="' . esc_attr( trim( $fb_title ) ) . '"/>
    ';
					}
						// URL.
					if ( 1 == intval( $this->options['fb_url_show'] ) && '' != trim( $fb_url ) ) {
						$html .= '  <meta property="og:url" content="' . esc_attr( trim( $fb_url ) ) . '"/>
    ';
					}
						// Type.
					if ( 1 == intval( $this->options['fb_type_show'] ) && '' != trim( $fb_type ) ) {
						$html .= '  <meta property="og:type" content="' . esc_attr( trim( $fb_type ) ) . '"/>
    ';
					}
						// Description.
					if ( 1 == intval( $this->options['fb_desc_show'] ) && '' != trim( $fb_desc ) ) {
						$html .= '  <meta property="og:description" content="' . esc_attr( trim( $fb_desc ) ) . '"/>
    ';
					}
						// Image.
					if ( 1 == intval( $this->options['fb_image_show'] ) && '' != trim( $fb_image ) ) {
						$html .= '  <meta property="og:image" content="' . esc_attr( trim( $fb_image ) ) . '"/>
      <meta property="og:image:url" content="' . esc_attr( trim( $fb_image ) ) . '"/>
    ';
					}
					if ( 0 === strpos( trim( $fb_image ), 'https://' ) ) {
						$html .= '  <meta property="og:image:secure_url" content="' . esc_attr( trim( $fb_image ) ) . '"/>
    ';
					}
						// Additional Images.
					if ( 1 == intval( $this->options['fb_image_show'] ) && isset( $fb_image_additional ) && is_array( $fb_image_additional ) && count( $fb_image_additional ) > 0 ) {
						foreach ( $fb_image_additional as $fb_image_additional_temp ) {
							if ( isset( $fb_image_additional_temp['fb_image'] ) && '' != trim( $fb_image_additional_temp['fb_image'] ) ) {
								$html .= '  <meta property="og:image" content="' . esc_attr( trim( $fb_image_additional_temp['fb_image'] ) ) . '"/>
      <meta property="og:image:url" content="' . esc_attr( trim( $fb_image_additional_temp['fb_image'] ) ) . '"/>
    ';
								if ( 0 === strpos( trim( $fb_image_additional_temp['fb_image'] ), 'https://' ) ) {
									$html .= '  <meta property="og:image:secure_url" content="' . esc_attr( trim( $fb_image_additional_temp['fb_image'] ) ) . '"/>
    ';
								}
							}
						}
					} else {
						// Image Size - We only show the image size if we only have one image
						if ( 1 == intval( $this->options['fb_image_size_show'] ) && isset( $fb_image_size ) && is_array( $fb_image_size ) ) {
							$html .= '  <meta property="og:image:width" content="' . esc_attr( intval( $fb_image_size[0] ) ) . '"/>
  <meta property="og:image:height" content="' . esc_attr( intval( $fb_image_size[1] ) ) . '"/>
';
						}
					}
						// Dates.
					if ( 1 == intval( $this->options['fb_article_dates_show'] ) && '' != trim( $fb_article_pub_date ) ) {
						$html .= '  <meta property="article:published_time" content="' . esc_attr( trim( $fb_article_pub_date ) ) . '"/>
    ';
					}
					if ( 1 == intval( $this->options['fb_article_dates_show'] ) && '' != trim( $fb_article_mod_date ) ) {
						$html .= '  <meta property="article:modified_time" content="' . esc_attr( trim( $fb_article_mod_date ) ) . '" />
      <meta property="og:updated_time" content="' . esc_attr( trim( $fb_article_mod_date ) ) . '" />
    ';
					}
						// Sections.
					if ( 1 == intval( $this->options['fb_article_sections_show'] ) && isset( $fb_sections ) && is_array( $fb_sections ) && count( $fb_sections ) > 0 ) {
						foreach ( $fb_sections as $fb_section ) {
							$html .= '  <meta property="article:section" content="' . esc_attr( trim( $fb_section ) ) . '"/>
    ';
						}
					}
						// Author.
					if ( 1 == intval( $this->options['fb_author_show'] ) && '' != $fb_author ) {
						$html .= '  <meta property="article:author" content="' . esc_attr( trim( $fb_author ) ) . '"/>
    ';
					}
						// Publisher.
					if ( 1 == intval( $this->options['fb_publisher_show'] ) && '' != trim( $fb_publisher ) ) {
						$html .= '  <meta property="article:publisher" content="' . esc_attr( trim( $fb_publisher ) ) . '"/>
    ';
					}
						// App ID.
					if ( 1 == intval( $this->options['fb_app_id_show'] ) && '' != trim( $fb_app_id ) ) {
						$html .= '  <meta property="fb:app_id" content="' . esc_attr( trim( $fb_app_id ) ) . '"/>
    ';
					}
						// Admins.
					if ( 1 == intval( $this->options['fb_admin_id_show'] ) && '' != trim( $this->options['fb_admin_id'] ) ) {
						$html .= '  <meta property="fb:admins" content="' . esc_attr( trim( $this->options['fb_admin_id'] ) ) . '"/>
    ';
					}
					// Schema.
					$html .= ' <!-- Google+ / Schema.org -->
    ';
						// Title.
					if ( 1 == intval( $this->options['fb_title_show_schema'] ) && '' != trim( $fb_title ) ) {
						$html .= '  <meta itemprop="name" content="' . esc_attr( trim( $fb_title ) ) . '"/>
      <meta itemprop="headline" content="' . esc_attr( trim( $fb_title ) ) . '"/>
    ';
					}
						// Description.
					if ( 1 == intval( $this->options['fb_desc_show_schema'] ) && '' != trim( $fb_desc ) ) {
						$html .= '  <meta itemprop="description" content="' . esc_attr( trim( $fb_desc ) ) . '"/>
    ';
					}
						// Image.
					if ( 1 == intval( $this->options['fb_image_show_schema'] ) && '' != trim( $fb_image ) ) {
						$html .= '  <meta itemprop="image" content="' . esc_attr( trim( $fb_image ) ) . '"/>
    ';
					}
						// Dates.
					if ( 1 == intval( $this->options['fb_article_dates_show_schema'] ) && '' != trim( $fb_article_pub_date ) ) {
						$html .= '  <meta itemprop="datePublished" content="' . substr( esc_attr( trim( $fb_article_pub_date ) ), 0, 10 ) . '"/>
    ';
					}
					if ( 1 == intval( $this->options['fb_article_dates_show_schema'] ) && '' != trim( $fb_article_mod_date ) ) {
						$html .= '  <meta itemprop="dateModified" content="' . esc_attr( trim( $fb_article_mod_date ) ) . '" />
    ';
					}
						// Author - Link (no longer used).
					if ( 1 == intval( $this->options['fb_author_show_linkrelgp'] ) && '' != trim( $fb_author_linkrelgp ) ) {
						$html .= '  <link rel="author" href="' . esc_attr( trim( $fb_author_linkrelgp ) ) . '"/>
    ';
					}
						// Author - Name.
					if ( 1 == intval( $this->options['fb_author_show_schema'] ) && '' != $fb_author_meta ) {
						$html .= '  <meta itemprop="author" content="' . esc_attr( trim( $fb_author_meta ) ) . '"/>
    ';
					}
						// Publisher - Link.
					if ( 1 == intval( $this->options['fb_publisher_show_schema'] ) && '' != trim( $fb_publisher_schema ) ) {
						$html .= '  <link rel="publisher" href="' . esc_attr( trim( $fb_publisher_schema ) ) . '"/>
    ';
					}
						// Publisher- Name - The attribute publisher.itemtype has an invalid value.
					if ( 1 == intval( $this->options['fb_publisher_show_schema'] ) ) {
						$html .= '  <!--<meta itemprop="publisher" content="' . esc_attr( trim( get_bloginfo( 'name' ) ) ) . '"/>--> <!-- To solve: The attribute publisher.itemtype has an invalid value. -->
    ';
					}
					// Twitter.
					$html .= ' <!-- Twitter Cards -->
    ';
					$twitter_cards_enabled = (
						1 == intval( $this->options['fb_title_show_twitter'] )
						|| 1 == intval( $this->options['fb_url_show_twitter'] )
						|| 1 == intval( $this->options['fb_desc_show_twitter'] )
						|| 1 == intval( $this->options['fb_publisher_show_twitter'] )
						|| 1 == intval( $this->options['fb_image_show_twitter'] )
					);
						// Title.
					if ( 1 == intval( $this->options['fb_title_show_twitter'] ) && '' != trim( $fb_title ) ) {
						$html .= '  <meta name="twitter:title" content="' . esc_attr( trim( $fb_title ) ) . '"/>
    ';
					} elseif ( ! intval( $this->options['fb_title_show_twitter'] ) && $twitter_cards_enabled ) {
						// Prevent X/Twitter and preview tools from falling back to og:title when this tag is omitted.
						$html .= '  <meta name="twitter:title" content="' . esc_attr( apply_filters( 'fb_og_twitter_title_disabled_content', "\u{200B}" ) ) . '"/>
    ';
					}
						// URL.
					if ( 1 == intval( $this->options['fb_url_show_twitter'] ) && '' != trim( $fb_url ) ) {
						$html .= '  <meta name="twitter:url" content="' . esc_attr( trim( $fb_url ) ) . '"/>
    ';
					}
						// Description.
					if ( 1 == intval( $this->options['fb_desc_show_twitter'] ) && '' != trim( $fb_desc ) ) {
						$html .= '  <meta name="twitter:description" content="' . esc_attr( trim( $fb_desc ) ) . '"/>
    ';
					} elseif ( ! intval( $this->options['fb_desc_show_twitter'] ) && $twitter_cards_enabled ) {
						// Prevent X/Twitter and preview tools from falling back to og:description when this tag is omitted.
						$html .= '  <meta name="twitter:description" content="' . esc_attr( apply_filters( 'fb_og_twitter_description_disabled_content', "\u{200B}" ) ) . '"/>
    ';
					}
						// Image.
					if ( 1 == intval( $this->options['fb_image_show_twitter'] ) && '' != trim( $fb_image ) ) {
						$html .= '  <meta name="twitter:image" content="' . esc_attr( trim( $fb_image ) ) . '"/>
    ';
					}
						// Twitter Card.
					if ( 1 == intval( $this->options['fb_title_show_twitter'] ) || 1 == intval( $this->options['fb_url_show_twitter'] ) || 1 == intval( $this->options['fb_desc_show_twitter'] ) || 1 == intval( $this->options['fb_publisher_show_twitter'] ) || 1 == intval( $this->options['fb_image_show_twitter'] ) ) {
						$html .= '  <meta name="twitter:card" content="' . esc_attr( trim( $this->options['fb_twitter_card_type'] ) ) . '"/>
    ';
					}
						// Author.
					if ( 1 == intval( $this->options['fb_author_show_twitter'] ) && '' != trim( $fb_author_twitter ) ) {
						$html .= '  <meta name="twitter:creator" content="@' . esc_attr( trim( $fb_author_twitter ) ) . '"/>
    ';
					}
						// Publisher.
					if ( 1 == intval( $this->options['fb_publisher_show_twitter'] ) && '' != trim( $fb_publisher_twitteruser ) ) {
						$html .= '  <meta name="twitter:site" content="@' . esc_attr( trim( $fb_publisher_twitteruser ) ) . '"/>
    ';
					}
					// SEO.
					$html .= ' <!-- SEO -->
    ';
						// Title.
					if ( 1 == intval( $fb_set_title_tag ) && '' != trim( $fb_title ) ) {
						$my_secure_variable = true;
						// Does nothing so far. We try to create the <title> tag but it's too late now.
						// We should use wp_title(), but do we want to? This is only because Business Directory Plugin and they seem to have it covered by now...
					}
						// URL.
					if ( 1 == intval( $this->options['fb_url_canonical'] ) ) {
						$html .= '  <link rel="canonical" href="' . esc_attr( trim( $fb_url ) ) . '"/>
    ';
					}
						// Description.
					if ( 1 == intval( $this->options['fb_desc_show_meta'] ) && '' != trim( $fb_desc ) ) {
						$html .= '  <meta name="description" content="' . esc_attr( trim( $fb_desc ) ) . '"/>
    ';
					}
						// Author.
					if ( 1 == intval( $this->options['fb_author_show_meta'] ) && '' != $fb_author_meta ) {
						$html .= '  <meta name="author" content="' . esc_attr( trim( $fb_author_meta ) ) . '"/>
    ';
					}
						// Publisher.
					if ( 1 == intval( $this->options['fb_publisher_show_meta'] ) ) {
						$html .= '  <meta name="publisher" content="' . esc_attr( trim( get_bloginfo( 'name' ) ) ) . '"/>
    ';
					}
					// SEO.
					$html .= ' <!-- Misc. tags -->
    ';
					foreach ( $fb_additional_tags as $type => $tags ) {
						foreach ( $tags as $tag => $values ) {
							foreach ( $values as $value ) {
								$html .= '  <meta ' . $type . '="' . str_replace( '_', ':', trim( $tag ) ) . '" content="' . esc_attr( trim( $value ) ) . '"/>
    ';
							}
						}
					}
				} else {

					$debug[] = 'Removed by fb_og_enabled filter';

				}

				// Close tag.
				if ( apply_filters( 'fb_og_enable_debug', true ) ) {
					$html .= ' <!-- ' . implode( ' | ', $debug ) . ' -->
    ';
				}
				$html .= '<!-- END - ' . WEBDADOS_FB_PLUGIN_NAME . ' ' . WEBDADOS_FB_VERSION . ' -->
        
    ';
			} else {
				$html = '
    <!-- START - ' . WEBDADOS_FB_PLUGIN_NAME . ' ' . WEBDADOS_FB_VERSION . ' -->
    <!-- Disabled by the "fb_og_disable" filter -->
    <!-- END - ' . WEBDADOS_FB_PLUGIN_NAME . ' ' . WEBDADOS_FB_VERSION . ' -->
    ';
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is already escaped with esc_attr() for all attributes.
			echo apply_filters( 'fb_og_output', $html );
		}



		/**
		 * Get Open Graph image for a post.
		 *
		 * Retrieves the appropriate image for Open Graph tags based on settings,
		 * checking post-specific image, featured image, content images, media library,
		 * default image, and mshot service in order of priority.
		 *
		 * @since 1.0.0
		 * @param int|null $post_id Optional. Post ID. Defaults to current post.
		 * @return string|false Image URL or false if no image found.
		 */
		private function get_post_image( $post_id = null ) {
			if ( $post_id ) {
				$current_post = false;
				// Specific post.
				$post = get_post( $post_id );
			} else {
				$current_post = true;
				// Current post.
				global $post;
			}
			if ( $post ) {
				$thumbdone = false;
				$fb_image  = '';
				$minsize   = intval( $this->options['fb_image_min_size'] );
				// Attachment page? - This overrides the other options.
				if ( ! $current_post && is_attachment() ) {
					$temp = wp_get_attachment_image_src( null, 'full' );
					if ( $temp ) {
						$fb_image = trim( $temp[0] );
						$img_size = array( intval( $temp[1] ), intval( $temp[2] ) );
						if ( '' != trim( $fb_image ) ) {
							$thumbdone = true;
						}
					}
				}
				// Specific post image.
				if ( ! $thumbdone ) {
					if ( 1 == intval( $this->options['fb_image_use_specific'] ) ) {
						$fb_image = trim( get_post_meta( $post->ID, '_webdados_fb_open_graph_specific_image', true ) );
						if ( $fb_image ) {
							if ( '' != trim( $fb_image ) ) {
								$thumbdone = true;
							}
						}
					}
				}
				// Featured image.
				if ( ! $thumbdone ) {
					if ( function_exists( 'get_post_thumbnail_id' ) ) {
						if ( 1 == intval( $this->options['fb_image_use_featured'] ) ) {
							$id_attachment = get_post_thumbnail_id( $post->ID );
							if ( $id_attachment ) {
								// There's a featured/thumbnail image for this post.
								$fb_image  = wp_get_attachment_url( $id_attachment, false );
								$thumbdone = true;
							}
						}
					}
				}
				// From post/page content.
				if ( ! $thumbdone ) {
					if ( 1 == intval( $this->options['fb_image_use_content'] ) ) {
						$imgreg = '/<img .*src=["\']([^ ^"^\']*)["\']/';
						preg_match_all( $imgreg, trim( $post->post_content ), $matches );
						if ( $matches[1] ) {
							$imagetemp = false;
							foreach ( $matches[1] as $image ) {
								// There's an image on the content.
								$pos = strpos( $image, site_url() );
								if ( false === $pos ) {
									if ( stristr( $image, 'http://' ) || stristr( $image, 'https://' ) || '//' == mb_substr( $image, 0, 2 ) ) {
										if ( '//' == mb_substr( $image, 0, 2 ) ) {
											$image = ( ( ! empty( $_SERVER['HTTPS'] ) && 'on' == $_SERVER['HTTPS'] ) ? 'https:' : 'http:' ) . $image;
										}
										// Complete URL - offsite.
										// if ( intval(ini_get('allow_url_fopen' ))==1 ) {.
											$imagetemp     = $image;
											$imagetempsize = $imagetemp;
										// } else {.
											// If it's offsite we can't getimagesize'it, so we won't use it.
											// We could save a temporary version locally and then getimagesize'it but do we want to do this every single time?
										// }.
									} else {
										// Partial URL - we guess it's onsite because no http(s)://.
										$imagetemp     = site_url() . $image;
										$imagetempsize = (
											1 == intval( ini_get( 'allow_url_fopen' ) )
											?
											(
												1 == intval( $this->options['fb_adv_force_local'] )
												?
												ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
												:
												$imagetemp
											)
											:
											ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
										);
									}
								} else {
									// Complete URL - onsite.
									$imagetemp     = $image;
									$imagetempsize = (
										1 == intval( ini_get( 'allow_url_fopen' ) )
										?
										(
											1 == intval( $this->options['fb_adv_force_local'] )
											?
											ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
											:
											$imagetemp
										)
										:
										ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
									);
								}
								if ( $imagetemp ) {
									$img_size = $this->get_open_graph_image_size( $imagetempsize );
									if ( 1 == intval( $this->options['fb_adv_disable_image_size'] ) ) {
										// If we don't check for image size, we'll just accept the first one.
										$fb_image  = $imagetemp;
										$thumbdone = true;
										break; // Break the foreach.
									} elseif ( $img_size ) {
										if ( $img_size[0] >= $minsize && $img_size[1] >= $minsize ) {
											$fb_image  = $imagetemp;
											$thumbdone = true;
											break; // Break the foreach.
										}
									}
								}
							}
						}
					}
				}
				// From media gallery.
				if ( ! $thumbdone ) {
					if ( 1 == intval( $this->options['fb_image_use_media'] ) ) {
						$images = get_posts(
							array(
								'post_type'      => 'attachment',
								'numberposts'    => -1,
								'post_status'    => null,
								'order'          => 'ASC',
								'orderby'        => 'menu_order',
								'post_mime_type' => 'image',
								'post_parent'    => $post->ID,
							)
						);
						if ( $images ) {
							foreach ( $images as $image ) {
								$imagetemp     = wp_get_attachment_url( $image->ID, false );
								$imagetempsize = (
									1 == intval( ini_get( 'allow_url_fopen' ) )
									?
									(
										1 == intval( $this->options['fb_adv_force_local'] )
										?
										ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
										:
										$imagetemp
									)
									:
									ABSPATH . str_replace( trailingslashit( site_url() ), '', $imagetemp )
								);
								$img_size      = $this->get_open_graph_image_size( $imagetempsize );
								if ( 1 == intval( $this->options['fb_adv_disable_image_size'] ) ) {
									// If we don't check for image size, we'll just accept the first one.
									$fb_image  = $imagetemp;
									$thumbdone = true;
									break; // Break the foreach.
								} elseif ( $img_size ) {
									if ( $img_size[0] >= $minsize && $img_size[1] >= $minsize ) {
										$fb_image  = $imagetemp;
										$thumbdone = true;
										break; // Break the foreach.
									}
								}
							}
						}
					}
				}
				// From default.
				if ( ! $thumbdone ) {
					if ( 1 == intval( $this->options['fb_image_use_default'] ) ) {
						// Well... We sure did try. We'll just keep the default one!
						$fb_image = $this->options['fb_image'];
					} else {
						// User chose not to use default on pages/posts.
						$fb_image = '';
					}
				}
				// Return.
				return $fb_image;
			} else {
				// No post.
				return false;
			}
		}


		/**
		 * Get image URL with overlay applied.
		 *
		 * Generates a URL to the image overlay processing script (fbimg.php) that
		 * applies a PNG overlay logo to the base image if overlay is enabled.
		 *
		 * @since 1.0.0
		 * @param string $fb_image   Base image URL.
		 * @param array  $params     URL parameters for overlay processing.
		 * @param bool   $additional Optional. Whether this is an additional image. Default false.
		 * @return array Array with 'overlay' boolean and 'fb_image' URL string.
		 */
		private function get_image_with_overlay( $fb_image, $params, $additional = false ) {
			$fb_image_parsed = wp_parse_url( $fb_image );
			// Only if the image is hosted locally.
			if ( isset( $_SERVER['HTTP_HOST'] ) && sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) == $fb_image_parsed['host'] ) {
				// Params.
				$params['img'] = urlencode( $fb_image );
				$fb_image      = apply_filters( 'fb_og_image_overlay_url', plugins_url( '/wonderm00ns-simple-facebook-open-graph-tags/fbimg.php' ) . '?' . http_build_query( $params ), http_build_query( $params ) );
				return array(
					'overlay'  => true,
					'fb_image' => $fb_image,
				);
			}
			return array(
				'overlay'  => false,
				'fb_image' => $fb_image,
			);
		}



		/**
		 * Get image dimensions using cURL.
		 *
		 * Retrieves image width and height by downloading partial image data
		 * via cURL and parsing image headers.
		 *
		 * @since 1.0.0
		 * @param string $image   Image URL.
		 * @param array  $headers HTTP response headers from initial request.
		 * @return array|false Array with 'width' and 'height' keys, or false on failure.
		 */
		private function get_open_graph_image_size_curl( $image, $headers ) {
			try {
				$args = array(
					'timeout'     => 30,
					'redirection' => 5,
					'sslverify'   => false, // Equivalent to CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER set to 0.
				);

				// Set HTTP headers if provided.
				if ( is_array( $headers ) ) {
					$args['headers'] = $headers;
				} else {
					$args['headers'] = array();
				}

				// Set HTTP REFERER and USER AGENT just in case. Some servers may have hotlinking protection.
				$referer = '';
				if ( isset( $_SERVER['HTTPS'] ) && isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
					$https       = sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) );
					$http_host   = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
					$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
					$referer     = ( ( ! empty( $https ) && 'on' === $https ) ? 'https://' : 'http://' ) . $http_host . $request_uri;
				}
				if ( ! empty( $referer ) ) {
					$args['headers']['Referer'] = $referer;
				}
				if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
					$user_agent                    = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
					$args['headers']['User-Agent'] = $user_agent;
				}

				$response = wp_remote_get( $image, $args );

				if ( is_wp_error( $response ) ) {
					return false;
				}

				$data = wp_remote_retrieve_body( $response );
				return $data;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Get Open Graph image dimensions.
		 *
		 * Retrieves image width and height, using cached transients when available.
		 * Falls back to cURL method if cache is not available.
		 *
		 * @since 1.0.0
		 * @param string $image Image URL.
		 * @return array|false Array with 'width' and 'height' keys, or false on failure.
		 */
		private function get_open_graph_image_size( $image ) {
			// Just in case we've missed it somewhere...
			if ( 1 == intval( $this->options['fb_adv_disable_image_size'] ) ) {
				return false;
			}
			// Go ahead.
			if ( apply_filters( 'fb_og_image_size_cache', true ) ) {
				$transient_key = 'webdados_og_image_size_' . md5( $image );
				$transient_val = get_transient( $transient_key );
				if ( $transient_val ) {
					return $transient_val;
				}
			}
			if ( stristr( $image, 'http://' ) || stristr( $image, 'https://' ) || '//' == mb_substr( $image, 0, 2 ) ) {
				if ( function_exists( 'curl_version' ) && function_exists( 'imagecreatefromstring' ) ) {
					// If true - We'll get just a part of the image to speed things up. From http://stackoverflow.com/questions/4635936/super-fast-getimagesize-in-php.
					if ( $this->image_size_use_partial ) {
						$headers = array(
							'Range: bytes=0-32768',
						);
					} else {
						$headers = null;
					}
					$data = $this->get_open_graph_image_size_curl( $image, $headers );
					if ( $data ) {
						$done_partial = false;
						$tried_full   = false;
						try {
							$im = @imagecreatefromstring( $data ); // Mute errors because we're not loading the all image.
							if ( $im ) {
								$done_partial = true;
							}
						} catch ( Exception $e ) {
							if ( ! $this->image_size_use_partial ) { // We already tried it full.
								// Try again with the whole image - In case of Fatal Error.
								$tried_full = true;
								$data       = $this->get_open_graph_image_size_curl( $image, null );
								$im         = @imagecreatefromstring( $data );
							}
						}
						if ( ! $this->image_size_use_partial ) { // We already tried it full.
							if ( ! $done_partial && ! $tried_full ) {
								// Try again with the whole image - In case of Warning.
								$data = $this->get_open_graph_image_size_curl( $image, null );
								if ( $data ) {
									$im = @imagecreatefromstring( $data );
								} else {
									// No way...
									$im = false;
								}
							}
						}
						if ( $im ) {
							$x = imagesx( $im );
							if ( $x ) {
								// We have to fake the image type - For RSS.
								$ext = pathinfo( $image, PATHINFO_EXTENSION );
								switch ( strtolower( $ext ) ) {
									case 'gif':
										$type = 1;
										break;
									case 'jpg':
									case 'jpeg':
										$type = 2;
										break;
									case 'png':
										$type = 3;
										break;
									default:
										$type = 2;
										break;
								}
								$img_size = array( $x, imagesy( $im ), $type, '' );
							} else {
								$img_size = false;
							}
						} else {
							$img_size = false;
						}
					} else {
						$img_size = false;
					}
				} elseif ( 1 == intval( ini_get( 'allow_url_fopen' ) ) ) {
						$img_size = getimagesize( $image );
				} else {
					// We give up!
					$img_size = false;
				}
			} else {
				// Local path.
				$img_size = getimagesize( $image );
			}
			if ( $img_size && apply_filters( 'fb_og_image_size_cache', true ) ) {
				set_transient( $transient_key, $img_size, $this->transient_validity );
			}
			$this->image_size = $img_size;
			return $img_size;
		}



		/**
		 * Add Open Graph namespace to HTML tag.
		 *
		 * Modifies the HTML tag's prefix or xmlns attribute to include Open Graph
		 * namespace declarations based on the configured declaration method.
		 *
		 * @since 1.0.0
		 * @param string $output HTML language attributes string.
		 * @return string Modified HTML attributes with Open Graph namespace.
		 */
		public function add_open_graph_namespace( $output ) {
			if ( 'prefix' == $this->options['fb_declaration_method'] ) {
				if ( preg_match( '/\bprefix=(["\'])([^"\']+)["\']/i', $output, $m ) ) {
					// prefix attribute already there, so let's look into it.
					$prefix = $m[2];
					if ( ! preg_match( '/\bog: /', $prefix ) ) {
						// og prefix missing, let's add it.
						$prefix .= ' og: http://ogp.me/ns#';
					}
					if ( ! preg_match( '/\bfb: /', $prefix ) ) {
						// fb prefix missing, let's add it.
						$prefix .= ' fb: http://ogp.me/ns/fb#';
					}
					// replace existing prefix attribute with new one.
					$output = str_replace( $m[0], 'prefix=' . $m[1] . $prefix . $m[1], $output );
				} else {
					// No prefix attribute there, let's add it.
					$output = $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
				}
			} else {
				if ( stristr( $output, 'xmlns:og' ) ) {
					$my_secure_variable = true;
				} else {
					// Let's add it.
					$output = $output . ' xmlns:og="http://ogp.me/ns#"';
				}
				if ( stristr( $output, 'xmlns:fb' ) ) {
					$my_secure_variable = true;
				} else {
					// Let's add it.
					$output = $output . ' xmlns:fb="http://ogp.me/ns/fb#"';
				}
			}
			return $output;
		}



		/**
		 * Add Schema.org itemtype to HTML tag.
		 *
		 * Modifies the HTML tag to include Schema.org itemtype attribute
		 * based on the current page type and settings.
		 *
		 * @since 1.0.0
		 * @param string $output HTML language attributes string.
		 * @return string Modified HTML attributes with Schema.org itemtype.
		 */
		public function add_schema_itemtype( $output ) {
			if ( 1 == intval( $this->options['fb_type_show_schema'] ) ) {
				$fb_type_schema = '';
				if ( is_front_page() ) {
					$fb_type_schema = '' == trim( $this->options['fb_type_schema_homepage'] ) ? 'WebSite' : trim( $this->options['fb_type_schema_homepage'] );
				} else {
					$fb_type_schema = '' == trim( $this->options['fb_type_schema_post'] ) ? 'Article' : trim( $this->options['fb_type_schema_post'] );
				}
				$fb_type_schema = apply_filters( 'fb_type_schema', $fb_type_schema );
				if ( '' != $fb_type_schema ) {
					$output = $output . ' itemscope itemtype="http://schema.org/' . esc_attr( $fb_type_schema ) . '"';
				}
			}
			return $output;
		}



		/**
		 * Add Yahoo Media RSS namespace to RSS feed.
		 *
		 * Adds the Yahoo Media RSS namespace declaration to RSS feeds
		 * when RSS image enhancement is enabled.
		 *
		 * @since 1.0.0
		 */
		public function images_on_feed_yahoo_media_tag() {
			if ( 1 == intval( $this->options['fb_image_rss'] ) ) {
				// Even if it's comments feed, not a problem.
				echo 'xmlns:media="http://search.yahoo.com/mrss/"';
			}
		}
		/**
		 * Add image to RSS feed items.
		 *
		 * Outputs image tags for each RSS feed item when RSS image enhancement is enabled.
		 *
		 * @since 1.0.0
		 */
		public function images_on_feed_image() {
			if ( 1 == intval( $this->options['fb_image_rss'] ) ) {
				// Only runs on posts feed, not comments, so cool!
				$fb_image = $this->get_post_image();
				if ( '' != $fb_image ) {
					$uploads = wp_upload_dir();
					$url     = wp_parse_url( $fb_image );
					$path    = $uploads['basedir'] . preg_replace( '/.*uploads(.*)/', '${1}', $url['path'] );
					if ( file_exists( $path ) ) {
						$filesize = filesize( $path );
						$url      = $path;
					} else {
						$header   = get_headers( $fb_image, 1 );
						$filesize = $header['Content-Length'];
						$url      = $fb_image;
					}
					if ( 0 == intval( $this->options['fb_adv_disable_image_size'] ) ) {
						if ( list( $width, $height, $type, $attr ) = $this->get_open_graph_image_size( $url ) ) {
							echo '<enclosure url="' . esc_url( $fb_image ) . '" length="' . esc_attr( $filesize ) . '" type="' . esc_attr( image_type_to_mime_type( $type ) ) . '"/>';
							echo '<media:content url="' . esc_url( $fb_image ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '" medium="image" type="' . esc_attr( image_type_to_mime_type( $type ) ) . '"/>';
						} else {
							echo '<enclosure url="' . esc_url( $fb_image ) . '" length="' . esc_attr( $filesize ) . '" type="' . esc_attr( image_type_to_mime_type( null ) ) . '"/>';
							echo '<media:content url="' . esc_url( $fb_image ) . '" width="" height="" medium="image" type="' . esc_attr( image_type_to_mime_type( null ) ) . '"/>';
						}
					} else {
						echo '<enclosure url="' . esc_url( $fb_image ) . '" length="' . esc_attr( $filesize ) . '" type="' . esc_attr( image_type_to_mime_type( null ) ) . '"/>';
						echo '<media:content url="' . esc_url( $fb_image ) . '" width="" height="" medium="image" type="' . esc_attr( image_type_to_mime_type( null ) ) . '"/>';
					}
				}
			}
		}
	}
endif;
