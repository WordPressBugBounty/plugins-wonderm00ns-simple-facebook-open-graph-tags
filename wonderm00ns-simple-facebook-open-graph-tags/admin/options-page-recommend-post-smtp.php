<?php
/**
 * Post SMTP recommendation settings page template.
 *
 * This file contains the HTML and form fields for the SMTP recommendation tab
 * in the plugin options page, which promotes the Post SMTP plugin for email delivery.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 * @since    1.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

// Include the post-smtp-notice functionality.
require_once plugin_dir_path( WEBDADOS_FB_PLUGIN_FILE ) . 'vendor/post-smtp-notice/recommend-post-smtp-base.php';

// Initialize the Post SMTP recommendation system.
$post_smtp_recommendation = new \RecommendPostSMTP\Base\Recommend_Post_SMTP_Base( 'wonderm00ns-simple-facebook-open-graph-tags', false, false, 'gif' );
$post_smtp_recommendation->admin_enqueue_scripts();
$post_smtp_recommendation->admin_head();
?>
<div id="tabs-8" class="tab-content">
	<div class="postbox">
		<div class="inside">
			<?php
			// Display the Post SMTP recommendation content.
			$post_smtp_recommendation->recommend_post_smtp_submenu();
			?>
		</div>
	</div>
</div>
