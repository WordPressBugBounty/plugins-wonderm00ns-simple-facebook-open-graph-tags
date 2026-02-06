<?php
/**
 * Tools settings options page template.
 *
 * This file contains the HTML and form fields for the Tools settings tab
 * in the plugin options page, including utility tools like clearing transients
 * and other maintenance functions.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

?>
<div class="menu_containt_div" id="tabs-7">
	<p><?php esc_html_e( 'Just some random tools', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>

	<?php do_action( 'fb_og_admin_settings_tools_before' ); ?>

	<div class="postbox">
		<h3 class="hndle"><i class="dashicons-before dashicons-format-image"></i> <?php esc_html_e( 'Image tools', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></h3>
		<div class="inside">
			<table class="form-table">
				<tbody>
					
					<tr>
						<th><?php esc_html_e( 'Clear all transients', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>:</th>
						<td>
							<input type="checkbox" name="tools[]" value="clear_transients"/>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="info">
							- <strong><?php esc_html_e( 'This is an advanced tool: Don\'t mess with this unless you know what you\'re doing', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></strong>
							<br/>
							- <?php esc_html_e( 'We use transients to cache the image sizes, so that we only have to calculate them once (a week). Because of some server issues it may happen that we cannot correctly get the image size and we\'ll cache that, meaning that we\'ll never try it again (for a week). This tool will delete ALL the transients and force the image size calculation to be done again for all images, as they\'re nedded.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?>
						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>

	<?php do_action( 'fb_og_admin_settings_tools_after' ); ?>

</div>
