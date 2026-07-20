<?php
/**
 * Main options page template file.
 *
 * This file contains the main settings page structure, tab navigation, and form wrapper
 * for the plugin's admin settings interface. It includes all option page tabs and handles
 * the overall layout and JavaScript functionality for the settings page.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

// Include plugin.php for is_plugin_active function.
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

wp_enqueue_media();

// Enqueue WordPress updates script first.
wp_enqueue_script( 'wp-updates' );

// Enqueue Post SMTP script for SMTP tab.
$script_url  = plugin_dir_url( __FILE__ ) . '../includes/post-smtp-notice/assets/js/admin-script.js';
$script_path = plugin_dir_path( __FILE__ ) . '../includes/post-smtp-notice/assets/js/admin-script.js';

// Debug: Check if script file exists.
if ( file_exists( $script_path ) ) {
	wp_enqueue_script( 'recommend-post-smtp-script', $script_url, array( 'wp-updates', 'jquery' ), '1.0.0', true );
	wp_localize_script(
		'recommend-post-smtp-script',
		'recommendPostSMTP',
		array(
			'redirectURL' => admin_url( 'admin-post.php?action=hide-post-smtp-recommendation-notice&nonce=' . wp_create_nonce( 'hide-post-smtp-recommendation-notice' ) ),
			'postSMTPURL' => admin_url( 'admin.php?page=postman' ),
			'XWPNonce'    => wp_create_nonce( 'wp_rest' ),
			'restURL'     => rest_url(),
		)
	);
	echo '<script>console.log("Script file found and enqueued: ' . esc_attr( $script_url ) . '");</script>';
} else {
	echo '<script>console.error("Script file NOT found: ' . esc_attr( $script_path ) . '");</script>';
}

$out_link_utm = '?utm_source=' . urlencode( home_url() ) . '&amp;utm_medium=link&amp;utm_campaign=fb_og_wp_plugin';

require_once __DIR__ . '/open-graph-platform-icons.php';

?>
<div class="wrap" id="webdados_fb_admin">


	<h1>
		<?php echo esc_html( WEBDADOS_FB_PLUGIN_NAME ); ?> (<?php echo esc_html( WEBDADOS_FB_VERSION ); ?>)
		<?php do_action( 'fb_og_admin_settings_title' ); ?>
	</h1>
	<p class="webdados-fb-page-intro"><?php esc_html_e( 'Please set some default values and which tags should, or should not, be included. It may be necessary to exclude some tags if other plugins are already including them.', 'wonderm00ns-simple-facebook-open-graph-tags' ); ?></p>

	<div class="columns-2 webdados_fb_admin_left" id="post-body">
		<div class="menu_div metabox-holder" id="tabs">
			<form id="webdados_fb_form" action="options.php" method="post">

				<?php settings_fields( 'wonderm00n_open_graph_settings' ); ?>

				<!-- Remember last active tab -->
				<input type="hidden" name="wonderm00n_open_graph_settings[settings_last_tab]" id="settings_last_tab" value="<?php echo intval( $options['settings_last_tab'] ); ?>"/>
				<!-- Minimum image size -->
				<input type="hidden" name="wonderm00n_open_graph_settings[fb_image_min_size]" value="<?php echo intval( $options['fb_image_min_size'] ); ?>"/>
				
				<h2 class="nav-tab-wrapper">
					<ul>
						<?php
						// Check if Post SMTP plugin is active.
						$is_post_smtp_active = false;
						if ( function_exists( 'is_plugin_active' ) ) {
							$is_post_smtp_active = is_plugin_active( 'post-smtp/postman-smtp.php' );
						}

						$settings_tabs = array(
							'1' => array(
								'icon'  => '<i class="dashicons-before dashicons-admin-generic"></i>',
								'title' => __( 'General', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-general.php',
							),
							'2' => array(
								'icon'  => '<i class="dashicons-before dashicons-share"></i>',
								'title' => __( 'Social Sharing', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-facebook.php',
							),
							'3' => array(
								'icon'  => webdados_fb_og_x_twitter_icon(),
								'title' => __( 'Cards', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-twitter.php',
							),

							/**
							 * I don't think we need this tab anymore.
							 * '4' => array(
							 * 'icon'  => '<i class="dashicons-before dashicons-googleplus"></i>',
							 * 'title' => __( 'Schema', 'wonderm00ns-simple-facebook-open-graph-tags' ).' ('.__( 'deprecated', 'wonderm00ns-simple-facebook-open-graph-tags' ).')',
							 * 'file'  => 'options-page-schema.php'
							 * ),
							 */

							'5' => array(
								'icon'  => '<i class="dashicons-before dashicons-admin-site"></i>',
								'title' => __( 'SEO tags', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-seo.php',
							),
							'6' => array(
								'icon'  => '<i class="dashicons-before dashicons-layout"></i>',
								'title' => __( '3rd party', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-3rdparty.php',
							),
							'7' => array(
								'icon'  => '<i class="dashicons-before dashicons-admin-tools"></i>',
								'title' => __( 'Tools', 'wonderm00ns-simple-facebook-open-graph-tags' ),
								'file'  => 'options-page-tools.php',
							),
						);

						// Only add SMTP tab if Post SMTP is not active.
						if ( ! $is_post_smtp_active ) {
							$settings_tabs['8'] = array(
								'icon'  => '<i class="dashicons-before dashicons-email"></i>',
								'title' => __( 'SMTP', 'wonderm00ns-simple-facebook-open-graph-tags' ) . ' <span class="awaiting-mod"><span class="pending-count">Free</span></span>',
								'file'  => 'options-page-recommend-post-smtp.php',
							);
						}
						// Show tabs.
						foreach ( $settings_tabs as $key => $tab ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							$index = 0;
							?>
							<li>
								<a class="nav-tab" href="#tabs-<?php echo esc_attr( $key ); ?>" data-tab-index="<?php echo intval( $index ); ?>">
									<?php echo wp_kses( $tab['icon'], webdados_fb_og_admin_tab_icon_kses() ); ?>
									<?php echo wp_kses_post( $tab['title'] ); ?>
								</a>
							</li>
							<?php
							++$index;
						}
						?>
					</ul>
				</h2>

				<div id="poststuff">

					<div class="clear"></div>

					<?php
					// Include files.
					foreach ( $settings_tabs as $key => $tab ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						require_once $tab['file'];
					}
					?>

					<div class="clear"></div>
					<?php
					// phpcs:disable WordPress.Security.NonceVerification.Recommended
					// Only show save button if not on SMTP tab.
					if ( ! isset( $_GET['tab'] ) || '8' != $_GET['tab'] ) {
						submit_button();
					}
					// phpcs:enable WordPress.Security.NonceVerification.Recommended
					?>

				</div>

			</form>
		</div>
	</div>


	<?php require 'options-page-right.php'; ?>
	

	<div class="clear">
		<p><br/>&copy; 2011-<?php echo esc_attr( gmdate( 'Y' ) ); ?></p>
	</div>


</div>
<script type="text/javascript">
jQuery(document).ready(function($) {

		//Tabs
		$( function() {
			$( "#tabs" ).tabs({
				active: <?php echo intval( $options['settings_last_tab'] ); ?>,
				activate: function( event, ui ) {
					// Hide save button on SMTP tab (tab 8).
					if( ui.newTab.index() == 7 ) { // 0-based index, so tab 8 is index 7.
						$('.submit .button-primary').hide();
					} else {
						$('.submit .button-primary').show();
					}
				}
			});
			
			// Check initial tab and hide save button if needed.
			if( $( "#tabs" ).tabs( "option", "active" ) == 7 ) {
				$('.submit .button-primary').hide();
			}
		});
		<?php
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['localeOnline'] ) && intval( $_GET['localeOnline'] ) == 1 ) {
			?>
			location.hash = "#fblocale";
			$('#fb_locale').focus();
			<?php
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		?>

});
</script>

<style type="text/css">
/* Custom styling for SMTP tab */
.nav-tab[href="#tabs-8"] {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
	color: white !important;
	border-color: #667eea !important;
}

.nav-tab[href="#tabs-8"]:hover {
	background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
	color: white !important;
}

.nav-tab[href="#tabs-8"].nav-tab-active {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
	color: white !important;
	border-bottom-color: #667eea !important;
}

/* Hide save button on SMTP tab */
#tabs-8 .submit .button-primary {
	display: none !important;
}
</style>
