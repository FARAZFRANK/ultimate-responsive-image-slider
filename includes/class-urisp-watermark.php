<?php
/**
 * Ultimate Responsive Image Slider - Watermark Stub (Free Version)
 * All GD image manipulation and server-side watermark processing is reserved for Pro.
 *
 * @package Ultimate_Responsive_Image_Slider
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class URISP_Watermark {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_watermarked_url( $attachment_id, $slider_id = 0, $settings = array(), $size = 'full' ) {
		// Free version returns original attachment image URL without watermark processing
		$src = wp_get_attachment_image_src( $attachment_id, $size, true );
		return is_array( $src ) ? $src[0] : '';
	}

	public function purge_cache( $slider_id = null ) {
		return true;
	}

	public function clear_slider_cache( $slider_id = null ) {
		return 0;
	}
}

function urisp_watermark() {
	return URISP_Watermark::instance();
}