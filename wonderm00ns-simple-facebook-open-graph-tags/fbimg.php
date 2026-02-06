<?php
/**
 * Image overlay processing utility file.
 *
 * This file handles the generation of Open Graph images with overlay logos.
 * It processes image requests via URL parameters, applies PNG overlays to images,
 * resizes/crops images to the required Open Graph dimensions, and outputs the final image.
 * This file is accessed directly via HTTP requests, not through WordPress hooks.
 *
 * @package Wonderm00ns_Simple_Facebook_Open_Graph_Tags
 * @subpackage Utilities
 * @since    1.0.0
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

define( 'WP_USE_THEMES', false );

require '../../../wp-blog-header.php';

$webdados_fb = webdados_fb_run();

if ( $webdados_fb ) {

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	// Base 64 from PRO add-on?
	if ( isset( $_GET['b64'] ) ) {
		$b64      = 'base64_decode';
		$argstemp = $b64( sanitize_text_field( wp_unslash( $_GET['b64'] ) ) );
		$args     = array();
		parse_str( $argstemp, $args );
	} else {
		$args = $_GET;
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	// Fix GET on some weird scenarios.
	foreach ( $args as $key => $value ) {
		$args[ str_replace( 'amp;', '', $key ) ] = $value;
	}

	if ( isset( $args['img'] ) && trim( $args['img'] ) != '' ) {
		$url = wp_parse_url( urldecode( trim( $args['img'] ) ) );
		if ( $url ) {
			if ( isset( $_SERVER['HTTP_HOST'] ) && $url['host'] === $_SERVER['HTTP_HOST'] ) {

				if ( isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
					$image = imagecreatefromfile( sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . $url['path'] );
					if ( $image ) {

						$size         = apply_filters( 'fb_og_image_size', array( $webdados_fb->img_w, $webdados_fb->img_h ) );
						$thumb_width  = intval( $size[0] );
						$thumb_height = intval( $size[1] );

						$width  = imagesx( $image );
						$height = imagesy( $image );

						$original_aspect = $width / $height;
						$thumb_aspect    = $thumb_width / $thumb_height;

						if ( $original_aspect >= $thumb_aspect ) {
							// If image is wider than thumbnail (in aspect ratio sense).
							$new_height = $thumb_height;
							$new_width  = $width / ( $height / $thumb_height );
						} else {
							// If the thumbnail is wider than the image.
							$new_width  = $thumb_width;
							$new_height = $height / ( $width / $thumb_width );
						}

						$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
						// Fill with white because the source image can be a transparent PNG.
						$thumb_fill_color = apply_filters( 'fb_og_thumb_fill_color', array( 255, 255, 255 ) );
						imagefill( $thumb, 0, 0, imagecolorallocate( $thumb, $thumb_fill_color[0], $thumb_fill_color[1], $thumb_fill_color[2] ) );

						$original_behavior = isset( $webdados_fb->options['fb_image_overlay_original_behavior'] ) ? trim( $webdados_fb->options['fb_image_overlay_original_behavior'] ) : '';
						switch ( $original_behavior ) {

							case 'shrinkcenter':
								// Shrink and center.
								if ( $width <= $thumb_width && $height <= $thumb_height ) {
									// Smaller image.
									imagecopyresampled(
										$thumb,
										$image,
										0 - ( $width - $thumb_width ) / 2, // Center the image horizontally.
										0 - ( $height - $thumb_height ) / 2, // Center the image vertically.
										0,
										0,
										$width,
										$height,
										$width,
										$height
									);
								} elseif ( $width > $thumb_width ) {
									$new_height = $height / ( $width / $thumb_width );
									imagecopyresampled(
										$thumb,
										$image,
										0, // Center the image horizontally.
										0 - ( $new_height - $thumb_height ) / 2, // Center the image vertically.
										0,
										0,
										$thumb_width,
										$new_height,
										$width,
										$height
									);
								} else {
									$new_width = $width / ( $height / $thumb_height );
									imagecopyresampled(
										$thumb,
										$image,
										0 - ( $new_width - $thumb_width ) / 2, // Center the image horizontally.
										0, // Center the image vertically.
										0,
										0,
										$new_width,
										$thumb_height,
										$width,
										$height
									);
								}
								break;

							default:
								// Resize and crop.
								imagecopyresampled(
									$thumb,
									$image,
									0 - ( $new_width - $thumb_width ) / 2, // Center the image horizontally.
									0 - ( $new_height - $thumb_height ) / 2, // Center the image vertically.
									0,
									0,
									$new_width,
									$new_height,
									$width,
									$height
								);
								break;
						}

						// Allow developers to change the thumb.
						$thumb = apply_filters( 'fb_og_thumb', $thumb, $args );

						// Barra.
						if ( trim( $webdados_fb->options['fb_image_overlay_image'] ) != '' ) {
							$barra_url = wp_parse_url( apply_filters( 'fb_og_thumb_image', trim( $webdados_fb->options['fb_image_overlay_image'] ), intval( $args['post_id'] ) ) );
							$barra     = imagecreatefromfile( sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . $barra_url['path'] );
							imagecopy( $thumb, $barra, 0, 0, 0, 0, intval( $thumb_width ), intval( $thumb_height ) );
						}

						if ( has_action( 'fb_og_alternate_output' ) ) {

							do_action( 'fb_og_alternate_output', $thumb, urldecode( $args['img'] ) );

						} else {

							@header( 'HTTP/1.0 200 OK' );
							switch ( apply_filters( 'fb_og_overlayed_image_format', 'jpg' ) ) {
								case 'png':
									header( 'Content-Type: image/png' );
									imagepng( $thumb );
									break;
								case 'jpg':
								default:
									header( 'Content-Type: image/jpeg' );
									imagejpeg( $thumb, null, apply_filters( 'fb_og_overlayed_image_format_jpg_quality', 100 ) );
									break;
							}
						}

						imagedestroy( $image );
						imagedestroy( $thumb );
						imagedestroy( $barra );

					}
				}
			}
		}
	}
}



if ( ! function_exists( 'imagecreatefromfile' ) ) :
	/**
	 * Create image resource from file.
	 *
	 * Creates a GD image resource from various image file formats (JPEG, PNG, GIF)
	 * by automatically detecting the file type and using the appropriate GD function.
	 *
	 * @since 1.0.0
	 * @param string $filename Path to the image file.
	 * @return resource|false GD image resource on success, false on failure.
	 * @throws InvalidArgumentException If file does not exist.
	 */
	function imagecreatefromfile( $filename ) {
		try {
			if ( ! file_exists( $filename ) ) {
				throw new InvalidArgumentException( 'File "' . htmlentities( $filename ) . '" not found.' );
			}
			switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
				case 'jpeg':
				case 'jpg':
					return imagecreatefromjpeg( $filename );
				break;

				case 'png':
					return imagecreatefrompng( $filename );
				break;

				case 'gif':
					return imagecreatefromgif( $filename );
				break;

				default:
					throw new InvalidArgumentException( 'File "' . htmlentities( $filename ) . '" is not valid jpg, png or gif image.' );
				break;
			}
		} catch ( Exception $e ) {
			die( 'No image found' );
		}
	}
	endif;
