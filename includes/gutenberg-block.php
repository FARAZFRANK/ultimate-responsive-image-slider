<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register Gutenberg Block for Ultimate Responsive Image Slider
 */
add_action( 'init', 'urisp_register_gutenberg_block' );
function urisp_register_gutenberg_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Use the same handles the frontend registers, so each file loads only once.
	if ( function_exists( 'uris_register_frontend_assets' ) ) {
		uris_register_frontend_assets();
	}
	wp_register_style( 'wpfrank-uris-block-editor-css', RISP_PLUGIN_URL . 'assets/css/urisp-block-editor.css', array(), RISP_PLUGIN_VER );

	wp_register_script(
		'urisp-gutenberg-block-js',
		RISP_PLUGIN_URL . 'assets/js/urisp-gutenberg-block.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'jquery', 'wpfrank-uris-jquery-sliderPro-js' ),
		RISP_PLUGIN_VER,
		true
	);

	register_block_type( 'urisp/slider-select', array(
		'api_version'     => 3,
		'editor_script'   => 'urisp-gutenberg-block-js',
		'editor_style'    => array( 'wpfrank-uris-slider-pro-min-css', 'wpfrank-uris-frontend-css', 'wpfrank-uris-skins-css', 'wpfrank-uris-block-editor-css' ),
		'render_callback' => 'uris_gutenberg_block_render',
		'attributes'      => array(
			'sliderId' => array(
				'type'    => 'string',
				'default' => '',
			),
		),
	) );
}

add_action( 'enqueue_block_editor_assets', 'urisp_gutenberg_localize' );
function urisp_gutenberg_localize() {
	wp_enqueue_script( 'wpfrank-uris-jquery-sliderPro-js' );
	wp_enqueue_style( 'wpfrank-uris-slider-pro-min-css' );
	wp_enqueue_style( 'wpfrank-uris-frontend-css' );
	wp_enqueue_style( 'wpfrank-uris-skins-css' );
	wp_enqueue_style( 'wpfrank-uris-block-editor-css' );

	$all_sliders = get_posts( array(
		'post_type'      => 'ris_gallery',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	$sliders_data = array();
	if ( ! empty( $all_sliders ) ) {
		foreach ( $all_sliders as $s ) {
			$sliders_data[] = array(
				'id'    => $s->ID,
				'title' => $s->post_title ? $s->post_title : __( '(no title)', 'ultimate-responsive-image-slider' ),
			);
		}
	}

	wp_localize_script( 'urisp-gutenberg-block-js', 'urisp_gutenberg_data', array(
		'sliders'           => $sliders_data,
		'edit_url_base'     => admin_url( 'post.php?action=edit&post=' ),
		'create_slider_url' => admin_url( 'post-new.php?post_type=ris_gallery' ),
	) );
}

/**
 * Gutenberg Block Render Callback
 * Used for both frontend output and ServerSideRender editor preview.
 */
function urisp_gutenberg_block_render( $attributes ) {
	$slider_id = isset( $attributes['sliderId'] ) ? (int) $attributes['sliderId'] : 0;
	if ( $slider_id > 0 ) {
		return do_shortcode( '[URISP id=' . $slider_id . ']' );
	}
	return '';
}


function uris_gutenberg_block_render( $attributes ) {
	if ( empty( $attributes['sliderId'] ) ) {
		return '<div class="urisp-block-placeholder" style="padding: 24px; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b;">'
			. '<p style="margin: 0; font-weight: 600;">' . esc_html__( 'Please select a slider in the block settings sidebar.', 'ultimate-responsive-image-slider' ) . '</p>'
			. '</div>';
	}
	return uris_shortcode( array( 'id' => $attributes['sliderId'] ) );
}
if ( ! function_exists( 'urisp_gutenberg_block_render' ) ) {
	function urisp_gutenberg_block_render( $attributes ) {
		return uris_gutenberg_block_render( $attributes );
	}
}