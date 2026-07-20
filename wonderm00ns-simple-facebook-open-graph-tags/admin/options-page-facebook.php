<?php
/**
 * Facebook Open Graph settings options page template.
 *
 * This file contains the HTML and form fields for the Open Graph settings tab
 * in the plugin options page, including Facebook App ID, Admin ID, locale settings,
 * and Open Graph tag visibility options.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

require_once __DIR__ . '/open-graph-platform-icons.php';

$og_platform_icon_kses = webdados_fb_og_platform_icon_kses();
$og_platforms          = webdados_fb_og_get_platforms();

?>
<div class="menu_containt_div" id="tabs-2">

	<div class="og-tab-intro">
		<h2 class="og-tab-intro__title">
			<?php esc_html_e( 'Social Sharing', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
		</h2>
		<p class="og-tab-intro__desc">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s: example Open Graph meta tag names. */
					__( 'Control how your links look when they\'re shared on social apps and chat tools. This tab sets <strong>Open Graph meta tags</strong> (%s) — the standard format hundreds of apps use to build link previews.', 'wonderm00ns-simple-facebook-open-graph-tags' ),
					'<code>og:title</code>, <code>og:description</code>, <code>og:image</code>, ' . esc_html__( 'and more', 'wonderm00ns-simple-facebook-open-graph-tags' )
				)
			);
			?>
		</p>
	</div>

	<div class="og-platforms-wrap">
		<div class="og-platforms-box">
			<p class="og-platforms-label"><?php esc_html_e( 'Used for social sharing on:', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>
			<ul class="og-platforms" aria-label="<?php esc_attr_e( 'Social platforms that use Open Graph', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>">
				<?php
				foreach ( $og_platforms as $slug => $platform ) {
					printf(
						'<li><span class="og-platform og-platform--%1$s"><span class="og-platform__icon">%2$s</span><span class="og-platform__name">%3$s</span></span></li>',
						esc_attr( $slug ),
						wp_kses( $platform['icon'], $og_platform_icon_kses ),
						esc_html( $platform['label'] )
					);
				}
				?>
				<li><span class="og-platform og-platform--more"><span class="og-platform__name"><?php esc_html_e( '+ more', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></span></span></li>
			</ul>
			<p class="og-platform-note">
				<?php esc_html_e( 'Instagram and others use Open Graph for link shares; in-app features (Stories, Reels) may use different rules.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
			</p>
		</div>
	</div>

	<div class="og-notice og-notice-warning" role="note">
		<strong><?php esc_html_e( 'Heads up:', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></strong>
		<?php esc_html_e( 'You configure tags once here; each platform decides how to display them. We don\'t connect to Facebook, LinkedIn, or Reddit APIs — your site publishes the tags, and their crawlers read them.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
	</div>

	<?php do_action( 'fb_og_admin_settings_facebook_before' ); ?>

	<div class="postbox">
		<div class="fb-postbox-header">
			<h3 class="hndle"><i class="dashicons-before dashicons-admin-links"></i> <?php esc_html_e( 'Open Graph tags (shared across platforms)', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
			<p class="og-section-desc"><?php esc_html_e( 'One set of tags → many networks. Enable the fields below to improve previews wherever Open Graph is supported.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>
		</div>
		<div class="inside">
			<table class="form-table">
				<tbody>
					
					<tr>
						<th><?php esc_html_e( 'Include Post/Page Title', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_title_show]" id="fb_title_show" value="1" <?php echo ( intval( $options['fb_title_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:title" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
								// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_title'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Site Name', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_sitename_show]" id="fb_sitename_show" value="1" <?php echo ( intval( $options['fb_sitename_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:site_name" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'From Settings &gt; General &gt; Site Title', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include URL', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_url_show]" id="fb_url_show" value="1" <?php echo ( intval( $options['fb_url_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:url" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
									// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_url'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Description', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_desc_show]" id="fb_desc_show" value="1" <?php echo ( intval( $options['fb_desc_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:description" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
									// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_desc'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Image', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_image_show]" id="fb_image_show" value="1" <?php echo ( intval( $options['fb_image_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:image" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
								// translators: 1: image width in pixels, 2: image height in pixels.
								esc_attr__( 'All images must have at least 200px on both dimensions in order to Facebook to load them at all. %1$dx%2$dpx for optimal results. Minimum of 600x315px is recommended.', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								esc_attr( $webdados_fb->img_w ),
								esc_attr( $webdados_fb->img_h )
							);
							?>
							<br/>
							- 
							<?php
							printf(
								// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_image'
							);
							?>
						</td>
					</tr>
					
					<tr class="fb_image_options">
						<th><?php esc_html_e( 'Include Image Dimensions', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_image_size_show]" id="fb_image_size_show" value="1" <?php echo ( intval( $options['fb_image_size_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr class="fb_image_options">
						<td colspan="2" class="info">
							<i>&lt;meta property="og:image:width" content="..."/&gt;</i> <?php esc_html_e( 'and', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?> <i>&lt;meta property="og:image:height" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'Recommended only if Facebook is having problems loading the image when the post is shared for the first time, or else it adds extra unnecessary processing time', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Type', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_type_show]" id="fb_type_show" value="1" <?php echo ( intval( $options['fb_type_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="og:type" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
									// translators: 1: type for posts and pages, 2: type for homepage, 3: alternative type for homepage.
								esc_attr__( 'Will be "%1$s" for posts and pages and "%2$s" or "%3$s" for the homepage', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'article',
								'website',
								'blog'
							);
							?>
							<br/>
							- <?php esc_html_e( 'Additional types may be used depending on 3rd party integrations', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
							<br/>
							- 
							<?php
							printf(
								// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_type'
							);
							?>
						</td>
					</tr>
					
					<tr class="fb_type_options">
						<th><?php esc_html_e( 'Homepage Type', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							website
							<input type="hidden" name="wonderm00n_open_graph_settings[fb_type_homepage]" value="website"/>
							<!--<select name="wonderm00n_open_graph_settings[fb_type_homepage]" id="fb_type_homepage">
								<option value="website"
								<?php
								if ( trim( $options['fb_type_homepage'] ) == '' || trim( $options['fb_type_homepage'] ) == 'website' ) {
									echo ' selected="selected"';}
								?>
								>website</option>
								<option value="blog"
								<?php
								if ( trim( $options['fb_type_homepage'] ) == 'blog' ) {
									echo ' selected="selected"';}
								?>
								>blog</option>
							</select>-->
						</td>
					</tr>
					<tr class="fb_type_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Facebook does not support <i>blog</i> anymore, so we have to default to <i>website</i>', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Post/Page Author', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_author_show]" id="fb_author_show" value="1" <?php echo ( intval( $options['fb_author_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="article:author" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The user\'s Facebook URL must be filled in on his profile', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Published/Modified Dates', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_article_dates_show]" id="fb_article_dates_show" value="1" <?php echo ( intval( $options['fb_article_dates_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="article:published_time" content="..."/&gt;</i>, <i>&lt;meta property="article:modified_time" content="..."/&gt;</i> <?php esc_html_e( 'and', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?> <i>&lt;meta property="og:updated_time" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'For posts only', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Article Sections', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_article_sections_show]" id="fb_article_sections_show" value="1" <?php echo ( intval( $options['fb_article_sections_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="article:section" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'For posts only', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>, <?php esc_html_e( 'from the categories names', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><a name="fblocale"></a><?php esc_html_e( 'Include Locale', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_locale_show]" id="fb_locale_show" value="1" <?php echo ( intval( $options['fb_locale_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="fb:locale" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The website\'s Facebook Page', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr class="fb_locale_options">
						<th><?php esc_html_e( 'Locale', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<?php
							$listLocales   = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
							$loadedOnline  = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
							$loadedOffline = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
							// phpcs:disable WordPress.Security.NonceVerification.Recommended
							// Online.
							if ( ! empty( $_GET['localeOnline'] ) ) {
								if ( intval( $_GET['localeOnline'] ) == 1 ) {

									$response = wp_remote_get( 'https://www.facebook.com/translations/FacebookLocales.xml' );

									if ( is_wp_error( $response ) ) {
										$my_secure_variable = true;
										// Handle error: $response->get_error_message().
									} else {
										$http_code = wp_remote_retrieve_response_code( $response );
										if ( intval( $http_code ) === 200 ) {
											// Save the file locally.
											$fb_locales = wp_remote_retrieve_body( $response );

											// Initialize WordPress filesystem API.
											global $wp_filesystem;
											if ( empty( $wp_filesystem ) ) {
												require_once ABSPATH . '/wp-admin/includes/file.php';
												WP_Filesystem();
											}

											if ( $wp_filesystem ) {
												$file_path = WP_PLUGIN_DIR . '/wonderm00ns-simple-facebook-open-graph-tags/includes/FacebookLocales.xml';
												if ( $wp_filesystem->put_contents( $file_path, $fb_locales, FS_CHMOD_FILE ) ) {
													$listLocales  = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
													$loadedOnline = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
												}
											}
										}
									}
								}
							}
							// Offline.
							if ( ! $listLocales ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

								$fb_locales = file_get_contents( WP_PLUGIN_DIR . '/wonderm00ns-simple-facebook-open-graph-tags/includes/FacebookLocales.xml' );
								if ( $fb_locales ) {
									$listLocales   = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
									$loadedOffline = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
								}
							}
							$locale     = get_locale(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							$fb_locale  = $webdados_fb->get_locale();
							$locale_txt = $locale;
							if ( $fb_locale != $locale ) {
								$locale_txt .= ' -&gt; ' . $fb_locale;
							}
							?>
							<select name="wonderm00n_open_graph_settings[fb_locale]" id="fb_locale">
								<option value=""
								<?php
								if ( trim( $options['fb_locale'] ) == '' ) {
									echo ' selected="selected"';}
								?>
								><?php esc_html_e( 'WordPress current locale/language', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?> (<?php echo esc_html( $locale_txt ); ?>)</option>
								<?php
								// OK.
								if ( $listLocales ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
									$xml     = simplexml_load_string( $fb_locales );
									$json    = json_encode( $xml );
									$locales = json_decode( $json, true );
									if ( is_array( $locales['locale'] ) ) {
										foreach ( $locales['locale'] as $locale ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
											?>
											<option value="<?php echo esc_attr( $locale['codes']['code']['standard']['representation'] ); ?>"
											<?php
											if ( trim( $options['fb_locale'] ) == trim( $locale['codes']['code']['standard']['representation'] ) ) {
												echo ' selected="selected"';
											}
											?>
											><?php echo esc_html( $locale['englishName'] ); ?> (<?php echo esc_html( $locale['codes']['code']['standard']['representation'] ); ?>)</option>
											<?php
										}
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr class="fb_locale_options">
						<td colspan="2" class="info">
							- 
							<?php
							if ( $loadedOnline ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
								esc_html_e( 'List loaded from Facebook (online)', 'wonderm00ns-simple-facebook-open-graph-tags' );
							} elseif ( $loadedOffline ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
									esc_html_e( 'List loaded from local cache (offline)', 'wonderm00ns-simple-facebook-open-graph-tags' );
								?>
									<!-- - <a href="?page=class-webdados-fb-open-graph-admin.php&amp;localeOnline=1" onClick="return(confirm('<?php esc_html_e( 'You\\\'l lose any changes you haven\\\'t saved. Are you sure?', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>'));"><?php esc_html_e( 'Reload from Facebook', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></a>-->
									<?php
							} else {
								esc_html_e( 'List not loaded', 'wonderm00ns-simple-facebook-open-graph-tags' );
							}
							// phpcs:enable WordPress.Security.NonceVerification.Recommended
							?>
							<br/>
							- 
							<?php
							printf(
								// translators: 1: filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_locale'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Declaration Method', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<select name="wonderm00n_open_graph_settings[fb_declaration_method]" id="fb_declaration_method">
								<option value="xmlns"
								<?php
								if ( trim( $options['fb_declaration_method'] ) == '' || trim( $options['fb_declaration_method'] ) == 'xmlns' ) {
									echo ' selected="selected"';}
								?>
								>xmlns</option>
								<option value="prefix"
								<?php
								if ( trim( $options['fb_declaration_method'] ) == 'prefix' ) {
									echo ' selected="selected"';}
								?>
								>prefix</option>
							</select>
						</td>
					</tr>
					<tr class="fb_type_options">
						<td colspan="2" class="info">
							<i>&lt;html xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#"&gt;</i> <?php esc_html_e( 'or', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?> <i>&lt;html prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"&gt;</i>
							<br/>
							- <?php esc_html_e( 'Prefix is recommended because it validates properly with the W3C validator, xmlns is the legacy method', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<div class="postbox">
		<div class="fb-postbox-header">
			<h3 class="hndle"><i class="dashicons-before dashicons-facebook-alt"></i> <?php esc_html_e( 'Facebook-only options', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
			<p class="og-section-desc"><?php esc_html_e( 'Add-on settings and properties — from publisher to referral — for snippets on Facebook.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>
		</div>
		<div class="inside">
			<table class="form-table">
				<tbody>

					<tr>
						<th><?php esc_html_e( 'Include Publisher', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_publisher_show]" id="fb_publisher_show" value="1" <?php echo ( intval( $options['fb_publisher_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="article:publisher" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The website\'s Facebook Page', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

					<tr class="fb_publisher_options">
						<th><?php esc_html_e( 'Website\'s Facebook Page', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_publisher]" id="fb_publisher" size="50" value="<?php echo esc_attr( trim( $options['fb_publisher'] ) ); ?>"/>
						</td>
					</tr>
					<tr class="fb_publisher_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Facebook Page URL (with https://)', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Include Facebook Admin(s) ID', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_admin_id_show]" id="fb_admin_id_show" value="1" <?php echo ( intval( $options['fb_admin_id_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="fb:admins" content="..."/&gt;</i>
						</td>
					</tr>

					<tr class="fb_admin_id_options">
						<th><?php esc_html_e( 'Facebook Admin(s) ID', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_admin_id]" id="fb_admin_id" size="50" value="<?php echo esc_attr( trim( $options['fb_admin_id'] ) ); ?>"/>
						</td>
					</tr>
					<tr class="fb_admin_id_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Comma separated if more than one', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Include Facebook Platform App ID', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_app_id_show]" id="fb_app_id_show" value="1" <?php echo ( intval( $options['fb_app_id_show'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta property="fb:app_id" content="..."/&gt;</i>
						</td>
					</tr>

					<tr class="fb_app_id_options">
						<th><?php esc_html_e( 'Facebook Platform App ID', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_app_id]" id="fb_app_id" size="50" value="<?php echo esc_attr( trim( $options['fb_app_id'] ) ); ?>"/>
						</td>
					</tr>
					<tr class="fb_app_id_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'From your Facebook Developers dashboard', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<div class="postbox">
		<h3 class="hndle"><i class="dashicons-before dashicons-update"></i> <?php esc_html_e( 'Facebook Open Graph Tags cache', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
		<div class="inside">
			<table class="form-table">
				<tbody>
					
					<tr>
						<th><?php esc_html_e( 'Clear cache', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_adv_notify_fb]" id="fb_adv_notify_fb" value="1" <?php echo ( intval( $options['fb_adv_notify_fb'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Try to clear the Facebook Open Graph Tags cache when saving a post or page, so the link preview on Facebook is immediately updated', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr class="fb_adv_notify_fb_options">
						<th><?php esc_html_e( 'App ID', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_adv_notify_fb_app_id]" id="fb_adv_notify_fb_app_id" size="20" value="<?php echo esc_attr( trim( $options['fb_adv_notify_fb_app_id'] ) ); ?>"/>
						</td>
					</tr>
					
					<tr class="fb_adv_notify_fb_options">
						<th><?php esc_html_e( 'App Secret', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_adv_notify_fb_app_secret]" id="fb_adv_notify_fb_app_secret" size="39" value="<?php echo esc_attr( trim( $options['fb_adv_notify_fb_app_secret'] ) ); ?>"/>
						</td>
					</tr>
					<tr class="fb_adv_notify_fb_options">
						<td colspan="2" class="info">
							- 
							<?php
							printf(
									// translators: 1: link.
								wp_kses_post( __( 'Facebook no longer allows updating the cache anonymously, so you have to use a App ID and Secret to do it. <a href="%s" target="_blank">Read here</a> how to do it.', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								esc_attr( 'https://www.webdados.pt/2017/12/successfully-update-facebook-cache-using-our-facebook-open-graph-plugin/' . $out_link_utm )
							);
							?>
							<br/>
							- <?php esc_html_e( 'If you are using the (now deprecated) <i>fb_og_update_cache_url</i> filter, this ID and Secret will NOT be used. You should stop using the filter and use these settings.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
							<br/>
							- <?php esc_html_e( 'Please do not ask for support regarding this feature. Everything is explained in the blog post linked above.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr class="fb_adv_notify_fb_options">
						<th><?php esc_html_e( 'Suppress cache notices', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_adv_supress_fb_notice]" id="fb_adv_supress_fb_notice" value="1" <?php echo ( intval( $options['fb_adv_supress_fb_notice'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr class="fb_adv_notify_fb_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Sometimes we aren\'t able to update the cache and the post author will see a notice if this option is not checked', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<?php do_action( 'fb_og_admin_settings_facebook_after' ); ?>

</div>
