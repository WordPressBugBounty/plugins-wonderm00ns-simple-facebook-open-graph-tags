<?php
/**
 * Twitter Cards settings options page template.
 *
 * This file contains the HTML and form fields for the Twitter Cards settings tab
 * in the plugin options page, including Twitter card type selection and Twitter
 * tag visibility options.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

?>
<div class="menu_containt_div" id="tabs-3">
	<p><?php esc_html_e( 'Tags used by Twitter to render their Cards.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>

	<?php do_action( 'fb_og_admin_settings_twitter_before' ); ?>

	<div class="postbox">
		<h3 class="hndle"><i class="dashicons-before dashicons-twitter"></i> <?php esc_html_e( 'Twitter Card Tags', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
		<div class="inside">
			<table class="form-table">
				<tbody>
					
					<tr>
						<th><?php esc_html_e( 'Include Title', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_title_show_twitter]" id="fb_title_show_twitter" value="1" <?php echo ( intval( $options['fb_title_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:title" content=..."/&gt;</i>
							<br/>
							- 
							<?php
								printf(
									// translators: %1$s is the filter name.
									wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
									'fb_og_title'
								);
								?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include URL', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_url_show_twitter]" id="fb_url_show_twitter" value="1" <?php echo ( intval( $options['fb_url_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:url" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
								// translators: %1$s is the filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_url'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Description', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_desc_show_twitter]" id="fb_desc_show_twitter" value="1" <?php echo ( intval( $options['fb_desc_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:description" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
								// translators: %1$s is the filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_desc'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Image', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_image_show_twitter]" id="fb_image_show_twitter" value="1" <?php echo ( intval( $options['fb_image_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:image" content="..."/&gt;</i>
							<br/>
							- 
							<?php
							printf(
								// translators: %1$s is the filter name.
								wp_kses_post( __( 'You can change this value using the <i>%1$s</i> filter', 'wonderm00ns-simple-facebook-open-graph-tags' ) ),
								'fb_og_image'
							);
							?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Post/Page Author', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_author_show_twitter]" id="fb_author_show_twitter" value="1" <?php echo ( intval( $options['fb_author_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:creator" content="@..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The user\'s Twitter Username must be filled in on his profile', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Include Publisher', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="wonderm00n_open_graph_settings[fb_publisher_show_twitter]" id="fb_publisher_show_twitter" value="1" <?php echo ( intval( $options['fb_publisher_show_twitter'] ) == 1 ? ' checked="checked"' : '' ); ?>/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:site" content="@..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The website\'s Twitter Username', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr class="fb_publisher_twitter_options">
						<th><?php esc_html_e( 'Website\'s Twitter Username', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="text" name="wonderm00n_open_graph_settings[fb_publisher_twitteruser]" id="fb_publisher_twitteruser" size="20" value="<?php echo esc_attr( trim( $options['fb_publisher_twitteruser'] ) ); ?>"/>
						</td>
					</tr>
					<tr class="fb_publisher_twitter_options">
						<td colspan="2" class="info">
							- <?php esc_html_e( 'Twitter username (without @)', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>
					
					<tr>
						<th><?php esc_html_e( 'Card Type', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<select name="wonderm00n_open_graph_settings[fb_twitter_card_type]" id="fb_twitter_card_type">
								<option value="summary"
								<?php
								if ( trim( $options['fb_twitter_card_type'] ) == 'summary' ) {
									echo ' selected="selected"';}
								?>
								><?php esc_html_e( 'Summary Card', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></option>
								<option value="summary_large_image"
								<?php
								if ( trim( $options['fb_twitter_card_type'] ) == 'summary_large_image' ) {
									echo ' selected="selected"';}
								?>
								><?php esc_html_e( 'Summary Card with Large Image', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							<i>&lt;meta name="twitter:card" content="..."/&gt;</i>
							<br/>
							- <?php esc_html_e( 'The type of Twitter Card shown on the timeline', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<?php do_action( 'fb_og_admin_settings_twitter_after' ); ?>

</div>
