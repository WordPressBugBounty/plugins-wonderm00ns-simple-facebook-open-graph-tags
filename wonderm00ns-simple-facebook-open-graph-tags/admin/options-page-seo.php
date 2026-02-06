<?php
/**
 * SEO tags settings options page template.
 *
 * This file contains the HTML and form fields for the SEO tags settings tab
 * in the plugin options page, including canonical URL, meta description,
 * and other SEO-related meta tag options.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

?>
<div class="menu_containt_div" id="tabs-5">
	<p><?php esc_html_e( 'SEO Meta Tags that are recommended ONLY if no other plugin is setting them already.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>

	<?php do_action( 'fb_og_admin_settings_seo_before' ); ?>

	<div class="postbox">
		<h3 class="hndle"><i class="dashicons-before dashicons-admin-site"></i> <?php esc_html_e( 'SEO Meta Tags', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
		<div class="inside">
			<table class="form-table">
				<tbody>
					
					<tr>
						<th><?php esc_html_e( 'Set Canonical URL', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_url_canonical]" id="fb_url_canonical" value="1" <?php echo ( intval( $options['fb_url_canonical'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;link rel="canonical" href="..."/&gt;</i>
							<?php
							if ( $webdados_fb->is_yoast_seo_active() ) {
								?>
								<br/>
								- <?php esc_html_e( 'Not recommended because you have Yoast SEO active', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
								<?php
							}
							?>
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
						<th><?php esc_html_e( 'Include Meta Description tag', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_desc_show_meta]" id="fb_desc_show_meta" value="1" <?php echo ( intval( $options['fb_desc_show_meta'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="description" content="..."/&gt;</i>
							<?php
							if ( $webdados_fb->is_yoast_seo_active() ) {
								?>
								<br/>
								- <?php esc_html_e( 'Not recommended because you have Yoast SEO active', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
								<?php
							}
							?>
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
						<th><?php esc_html_e( 'Include Post/Page Author name', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_author_show_meta]" id="fb_author_show_meta" value="1" <?php echo ( intval( $options['fb_author_show_meta'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="author" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'From the user Display name', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Publisher', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_publisher_show_meta]" id="fb_publisher_show_meta" value="1" <?php echo ( intval( $options['fb_publisher_show_meta'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="publisher" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'From Settings &gt; General &gt; Site Title', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<?php do_action( 'fb_og_admin_settings_seo_after' ); ?>

</div>
