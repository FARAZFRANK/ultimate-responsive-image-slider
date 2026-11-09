<?php
/**
 * Ultimate Responsive Image Slider - Typography Helper (Free Version)
 * Custom font file uploads (.woff2, .ttf) and font file management are reserved for Pro.
 *
 * @package Ultimate_Responsive_Image_Slider
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class URISP_Fonts {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_grouped_fonts_for_select() {
		return array(
			'custom'        => array(),
			'accessibility' => array(
				'OpenDyslexic'          => array( 'name' => 'OpenDyslexic', 'label' => 'OpenDyslexic (Pro)' ),
				'Atkinson Hyperlegible' => array( 'name' => 'Atkinson Hyperlegible', 'label' => 'Atkinson Hyperlegible (Pro)' ),
				'Lexend'                => array( 'name' => 'Lexend', 'label' => 'Lexend (Pro)' ),
				'Comic Neue'            => array( 'name' => 'Comic Neue', 'label' => 'Comic Neue (Pro)' ),
			),
			'system'        => array(
				'Arial, Helvetica, sans-serif'                     => 'Arial',
				"'Comic Sans MS', cursive, sans-serif"             => 'Comic Sans MS',
				"'Courier New', Courier, monospace"                => 'Courier New',
				'Georgia, serif'                                   => 'Georgia',
				'Impact, Charcoal, sans-serif'                     => 'Impact',
				"'Lucida Console', Monaco, monospace"              => 'Lucida Console',
				"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => 'Lucida Sans',
				'Tahoma, Geneva, sans-serif'                       => 'Tahoma',
				"'Times New Roman', Times, serif"                  => 'Times New Roman',
				"'Trebuchet MS', Helvetica, sans-serif"            => 'Trebuchet MS',
				'Verdana, Geneva, sans-serif'                      => 'Verdana',
			),
		);
	}

	public function get_system_fonts() {
		return array(
			'Arial, Helvetica, sans-serif'                     => 'Arial',
			"'Comic Sans MS', cursive, sans-serif"             => 'Comic Sans MS',
			"'Courier New', Courier, monospace"                => 'Courier New',
			'Georgia, serif'                                   => 'Georgia',
			'Impact, Charcoal, sans-serif'                     => 'Impact',
			"'Lucida Console', Monaco, monospace"              => 'Lucida Console',
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => 'Lucida Sans',
			'Tahoma, Geneva, sans-serif'                       => 'Tahoma',
			"'Times New Roman', Times, serif"                  => 'Times New Roman',
			"'Trebuchet MS', Helvetica, sans-serif"            => 'Trebuchet MS',
			'Verdana, Geneva, sans-serif'                      => 'Verdana',
		);
	}

	public function get_accessibility_presets() {
		return array(
			'OpenDyslexic'          => array( 'name' => 'OpenDyslexic', 'label' => 'OpenDyslexic (Pro)' ),
			'Atkinson Hyperlegible' => array( 'name' => 'Atkinson Hyperlegible', 'label' => 'Atkinson Hyperlegible (Pro)' ),
			'Lexend'                => array( 'name' => 'Lexend', 'label' => 'Lexend (Pro)' ),
			'Comic Neue'            => array( 'name' => 'Comic Neue', 'label' => 'Comic Neue (Pro)' ),
		);
	}

	public function get_custom_fonts() {
		return array();
	}

	public function get_all_custom_fonts_css() {
		return '';
	}

	public function get_font_embed_code( $font_name ) {
		return '';
	}
}

function urisp_fonts() {
	return URISP_Fonts::instance();
}