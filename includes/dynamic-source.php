<?php
/**
 * Ultimate Responsive Image Slider - Dynamic Data Source Stub (Free Version)
 *
 * Querying WordPress posts, custom post types, and WooCommerce products is an exclusive Pro feature.
 * In the Free version, this module provides safe inert stubs for the admin configuration preview.
 *
 * @package Ultimate_Responsive_Image_Slider
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Inert stub: Returns empty slide array in Free version
 */
function urisp_get_dynamic_slides( $settings, $image_size = 'full', $slider_id = 0 ) {
	return array();
}

/**
 * Inert stub: Renders no WooCommerce layers in Free version
 */
function urisp_render_woocommerce_slide_layers( $slide, $settings ) {
	return;
}

/**
 * Returns dummy post types for admin settings preview
 */
function urisp_get_available_post_types() {
	return array(
		'post'    => __( 'Posts (Pro)', 'ultimate-responsive-image-slider' ),
		'page'    => __( 'Pages (Pro)', 'ultimate-responsive-image-slider' ),
		'product' => __( 'WooCommerce Products (Pro)', 'ultimate-responsive-image-slider' ),
	);
}

/**
 * Returns dummy taxonomies for admin settings preview
 */
function urisp_get_taxonomies_for_post_type( $post_type ) {
	return array(
		'category' => __( 'Categories (Pro)', 'ultimate-responsive-image-slider' ),
		'post_tag' => __( 'Tags (Pro)', 'ultimate-responsive-image-slider' ),
	);
}