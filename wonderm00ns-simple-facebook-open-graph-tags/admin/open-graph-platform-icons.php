<?php
/**
 * SVG icons for Open Graph settings platform pills.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allowed HTML for inline platform SVG icons (admin only).
 *
 * @return array
 */
function webdados_fb_og_platform_icon_kses() {
	return array(
		'span' => array(
			'class'       => true,
			'aria-hidden' => true,
		),
		'svg'  => array(
			'xmlns'   => true,
			'viewbox' => true,
			'fill'    => true,
			'class'   => true,
			'aria-hidden' => true,
			'focusable'   => true,
			'role'    => true,
		),
		'path' => array(
			'd'    => true,
			'fill' => true,
		),
	);
}

/**
 * Platform definitions: label + inline SVG markup.
 *
 * @return array<string, array{label: string, icon: string}>
 */
function webdados_fb_og_get_platforms() {
	$platforms = array(
		'facebook' => array(
			'label' => __( 'Facebook', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1877F2" aria-hidden="true" focusable="false"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.41 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.23 2.68.23v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z"/></svg>',
		),
		'linkedin' => array(
			'label' => __( 'LinkedIn', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#0A66C2" aria-hidden="true" focusable="false"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.95v5.66H9.34V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0z"/></svg>',
		),
		'reddit'   => array(
			'label' => __( 'Reddit', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FF4500" aria-hidden="true" focusable="false"><path d="M12 0C5.37 0 0 5.37 0 12c0 6.62 5.37 12 12 12s12-5.38 12-12c0-6.63-5.37-12-12-12zm6.17 13.93c.03.2.04.41.04.62 0 3.16-3.68 5.72-8.21 5.72s-8.21-2.56-8.21-5.72c0-.21.01-.42.04-.62-.55-.25-.94-.8-.94-1.45 0-.88.71-1.59 1.59-1.59.42 0 .81.17 1.09.44 1.34-.94 3.16-1.55 5.18-1.62l1.13-3.36 2.93.62a1.13 1.13 0 1 1-.21.8l-2.4-.51-.86 2.55c1.97.09 3.74.7 5.05 1.62.29-.27.68-.44 1.1-.44.88 0 1.59.71 1.59 1.59 0 .65-.39 1.2-.95 1.45zM8.79 13.4c-.7 0-1.27.57-1.27 1.27s.57 1.27 1.27 1.27 1.27-.57 1.27-1.27-.57-1.27-1.27-1.27zm6.42 0c-.7 0-1.27.57-1.27 1.27s.57 1.27 1.27 1.27 1.27-.57 1.27-1.27-.57-1.27-1.27-1.27zm-.45 3.41c-.65.55-1.7.82-2.76.82s-2.11-.27-2.76-.82a.3.3 0 0 0-.4.45c.79.67 1.96 1 3.16 1s2.37-.33 3.16-1a.3.3 0 0 0-.4-.45z"/></svg>',
		),
		'whatsapp' => array(
			'label' => __( 'WhatsApp', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true" focusable="false"><path d="M17.47 14.38c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.21-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.06 2.88 1.21 3.08.15.2 2.09 3.2 5.07 4.48.71.31 1.26.49 1.69.63.71.22 1.35.19 1.86.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35M12.04 21.78h-.01c-1.79 0-3.55-.48-5.09-1.39l-.36-.22-3.79.99 1.01-3.69-.24-.38a9.85 9.85 0 0 1-1.51-5.26c0-5.45 4.44-9.89 9.9-9.89 2.64 0 5.13 1.03 7 2.9 1.87 1.87 2.9 4.36 2.9 7 0 5.45-4.44 9.89-9.91 9.89M20.52 3.45A11.81 11.81 0 0 0 12.04 0C5.46 0 .1 5.35.1 11.92c0 2.1.55 4.15 1.59 5.96L0 24l6.27-1.65a11.9 11.9 0 0 0 5.77 1.47h.01c6.58 0 11.93-5.35 11.93-11.92 0-3.18-1.24-6.18-3.49-8.44"/></svg>',
		),
		'telegram' => array(
			'label' => __( 'Telegram', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#26A5E4" aria-hidden="true" focusable="false"><path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.27-1.86 8.74c-.14.62-.51.77-1.03.48l-2.85-2.1-1.37 1.32c-.15.15-.28.28-.58.28l.2-2.91 5.28-4.77c.23-.2-.05-.32-.36-.12L8.46 12.7l-2.81-.88c-.61-.19-.62-.61.13-.91l10.99-4.24c.51-.18.95.12.79.91z"/></svg>',
		),
		'slack'    => array(
			'label' => __( 'Slack', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="#E01E5A" d="M5.04 15.16a2.52 2.52 0 0 1-2.52 2.52A2.52 2.52 0 0 1 0 15.16a2.52 2.52 0 0 1 2.52-2.52h2.52v2.52zm1.27 0a2.52 2.52 0 0 1 2.52-2.52 2.52 2.52 0 0 1 2.52 2.52v6.32A2.52 2.52 0 0 1 8.83 24a2.52 2.52 0 0 1-2.52-2.52v-6.32z"/><path fill="#36C5F0" d="M8.83 5.04a2.52 2.52 0 0 1-2.52-2.52A2.52 2.52 0 0 1 8.83 0a2.52 2.52 0 0 1 2.52 2.52v2.52H8.83zm0 1.27a2.52 2.52 0 0 1 2.52 2.52 2.52 2.52 0 0 1-2.52 2.52H2.52A2.52 2.52 0 0 1 0 8.83a2.52 2.52 0 0 1 2.52-2.52h6.31z"/><path fill="#2EB67D" d="M18.96 8.83a2.52 2.52 0 0 1 2.52-2.52A2.52 2.52 0 0 1 24 8.83a2.52 2.52 0 0 1-2.52 2.52h-2.52V8.83zm-1.27 0a2.52 2.52 0 0 1-2.52 2.52 2.52 2.52 0 0 1-2.52-2.52V2.52A2.52 2.52 0 0 1 15.17 0a2.52 2.52 0 0 1 2.52 2.52v6.31z"/><path fill="#ECB22E" d="M15.17 18.96a2.52 2.52 0 0 1 2.52 2.52A2.52 2.52 0 0 1 15.17 24a2.52 2.52 0 0 1-2.52-2.52v-2.52h2.52zm0-1.27a2.52 2.52 0 0 1-2.52-2.52 2.52 2.52 0 0 1 2.52-2.52h6.31A2.52 2.52 0 0 1 24 15.17a2.52 2.52 0 0 1-2.52 2.52h-6.31z"/></svg>',
		),
		'discord'  => array(
			'label' => __( 'Discord', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#5865F2" aria-hidden="true" focusable="false"><path d="M20.32 4.37a19.79 19.79 0 0 0-4.89-1.52.07.07 0 0 0-.08.04c-.21.38-.44.87-.61 1.25a18.27 18.27 0 0 0-5.48 0c-.17-.39-.4-.87-.62-1.25a.08.08 0 0 0-.08-.04c-1.71.3-3.34.81-4.89 1.52a.07.07 0 0 0-.03.03C.53 9.05-.32 13.58.1 18.06c0 .02.02.04.04.05a19.93 19.93 0 0 0 6 3.03.08.08 0 0 0 .08-.03c.46-.63.87-1.3 1.23-2 .02-.04 0-.09-.04-.1a13.1 13.1 0 0 1-1.87-.89.08.08 0 0 1-.01-.13c.13-.09.25-.2.37-.3a.08.08 0 0 1 .08-.01c3.93 1.79 8.18 1.79 12.06 0a.08.08 0 0 1 .08.01c.12.1.24.21.37.3a.08.08 0 0 1-.01.13c-.6.35-1.22.65-1.87.89a.08.08 0 0 0-.04.1c.36.7.77 1.37 1.23 2 .02.03.05.04.08.03a19.84 19.84 0 0 0 6-3.03.08.08 0 0 0 .03-.05c.5-5.18-.83-9.68-3.55-13.66a.06.06 0 0 0-.03-.03zM8.02 15.33c-1.18 0-2.16-1.09-2.16-2.42 0-1.33.96-2.42 2.16-2.42 1.21 0 2.18 1.1 2.16 2.42 0 1.33-.96 2.42-2.16 2.42zm7.97 0c-1.18 0-2.16-1.09-2.16-2.42 0-1.33.96-2.42 2.16-2.42 1.21 0 2.18 1.1 2.16 2.42 0 1.33-.95 2.42-2.16 2.42z"/></svg>',
		),
		'pinterest' => array(
			'label' => __( 'Pinterest', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#E60023" aria-hidden="true" focusable="false"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.16 9.42 7.62 11.17-.11-.95-.2-2.41.04-3.44.22-.94 1.4-5.97 1.4-5.97s-.36-.72-.36-1.78c0-1.66.97-2.91 2.17-2.91 1.02 0 1.52.77 1.52 1.69 0 1.03-.65 2.57-1 4-.28 1.19.6 2.16 1.78 2.16 2.13 0 3.77-2.25 3.77-5.5 0-2.88-2.06-4.88-5.01-4.88-3.41 0-5.42 2.56-5.42 5.21 0 1.03.39 2.13.89 2.73.1.12.11.22.08.34l-.34 1.36c-.05.22-.17.27-.4.16-1.5-.7-2.43-2.88-2.43-4.64 0-3.77 2.74-7.25 7.9-7.25 4.15 0 7.37 2.96 7.37 6.9 0 4.12-2.6 7.44-6.21 7.44-1.21 0-2.35-.63-2.74-1.38l-.75 2.84c-.27 1.04-1 2.35-1.49 3.15.92.28 1.89.43 2.91.43 6.62 0 12-5.37 12-12S18.63 0 12 0z"/></svg>',
		),
		'imessage' => array(
			'label' => __( 'iMessage', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#34DA50" aria-hidden="true" focusable="false"><path d="M12 0C5.37 0 0 4.6 0 10.27c0 3.22 1.72 6.1 4.42 7.99-.16 1.4-.85 3.16-1.83 4.4-.18.23 0 .56.29.5 2.93-.55 5.39-1.83 6.74-2.65.78.12 1.57.18 2.38.18 6.63 0 12-4.6 12-10.27S18.63 0 12 0z"/></svg>',
		),
		'teams'    => array(
			'label' => __( 'Microsoft Teams', 'wonderm00ns-simple-facebook-open-graph-tags' ),
			'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4B53BC" aria-hidden="true" focusable="false"><path d="M20.625 8.127h-5.292V6.376c0-.7.567-1.267 1.266-1.267h4.026c.699 0 1.267.567 1.267 1.267v.485a1.266 1.266 0 0 1-1.267 1.266zm-1.625 1.5h-3.667v8.082c0 1.5-1.217 2.717-2.716 2.717H10.4a4.5 4.5 0 0 0 4.5-4.5V12.5h4.1a1 1 0 0 0 1-1V10.6a.973.973 0 0 0-1-.973zM9.5 4.5a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm-7 7h11a1 1 0 0 1 1 1v6a4 4 0 0 1-4 4h-5a4 4 0 0 1-4-4v-6a1 1 0 0 1 1-1zm5.74 3.5H5.26v.984h1.139v3.516h1.21V15.98h1.13V15z"/></svg>',
		),
	);

	return $platforms;
}

/**
 * Inline X (Twitter) logo for admin tabs and section headings.
 *
 * @return string
 */
function webdados_fb_og_x_twitter_icon() {
	return '<span class="fb-x-twitter-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></span>';
}

/**
 * Allowed HTML for admin tab icons (dashicons and inline SVG).
 *
 * @return array
 */
function webdados_fb_og_admin_tab_icon_kses() {
	return array_merge(
		array(
			'i' => array(
				'class' => true,
			),
		),
		webdados_fb_og_platform_icon_kses()
	);
}
