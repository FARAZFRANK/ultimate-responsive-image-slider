<?php
/*
Plugin Name: Ultimate Responsive Image Slider
Plugin URI:  https://wpfrank.com/
Description: Ultimate Responsive Image Slider is a fully responsive WordPress image slider plugin with clean touch navigation, thumbnail carousel, and full backward compatibility.
Version:     4.0.0
Author:      WP Frank
Author URI:  https://wpfrank.com/
Text Domain: ultimate-responsive-image-slider
Domain Path: /languages
License:     GPL2
*/
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * Constant Variables
 */
define( "URIS_TD", "ultimate-responsive-image-slider" );
define( "URIS_PLUGIN_URL", plugin_dir_url(__FILE__) );
define( "URIS_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__) );
define( "URIS_PLUGIN_VER", "4.0.0" );

// Compatibility aliases for shared components
if ( ! defined( 'RISP_PLUGIN_URL' ) ) {
	define( 'RISP_PLUGIN_URL', URIS_PLUGIN_URL );
}
if ( ! defined( 'RISP_PLUGIN_DIR_PATH' ) ) {
	define( 'RISP_PLUGIN_DIR_PATH', URIS_PLUGIN_DIR_PATH );
}
if ( ! defined( 'RISP_PLUGIN_VER' ) ) {
	define( 'RISP_PLUGIN_VER', URIS_PLUGIN_VER );
}
if ( ! defined( 'RIS_PLUGIN_URL' ) ) {
	define( 'RIS_PLUGIN_URL', URIS_PLUGIN_URL );
}
if ( ! defined( 'RIS_PLUGIN_DIR_PATH' ) ) {
	define( 'RIS_PLUGIN_DIR_PATH', URIS_PLUGIN_DIR_PATH );
}

// Add or update plugin version & run migration
register_activation_hook( __FILE__, 'wpfrank_uris_activate' );
function wpfrank_uris_activate(){
	$uris_new_version = URIS_PLUGIN_VER;
	// Default to '0' so installs that never stored a version still migrate.
	$uris_previous_version = get_option("wpfrank_uris_current_version", '0');

	update_option("wpfrank_uris_previous_version", $uris_previous_version);

	// Make the dedicated capabilities real capabilities of the administrator role.
	if ( function_exists( 'uris_grant_admin_capabilities' ) ) {
		uris_grant_admin_capabilities();
	}

	// Run migration from legacy v3.5.19 format if needed
	if ( version_compare( (string)$uris_previous_version, '4.0.0', '<' ) ) {
		require_once URIS_PLUGIN_DIR_PATH . 'includes/migration.php';
		if ( function_exists( 'uris_run_migration' ) ) {
			uris_run_migration();
		}
	}

	// Stored only after the migration finished: on failure the next request retries.
	update_option("wpfrank_uris_current_version", $uris_new_version);
	update_option( 'uris_migration_attempts', 0 );
}

// Automatic check on plugin update
add_action( 'admin_init', 'wpfrank_uris_check_version_update' );
function wpfrank_uris_check_version_update() {
	$current_version = get_option( 'wpfrank_uris_current_version', '1.0.0' );

	if ( ! version_compare( (string) $current_version, URIS_PLUGIN_VER, '<' ) ) {
		return;
	}

	// Bounded retries: one deterministically broken slider must not run the
	// whole batch on every admin request.
	$attempts = (int) get_option( 'uris_migration_attempts', 0 );
	if ( $attempts >= 3 ) {
		return;
	}

	update_option( 'wpfrank_uris_previous_version', $current_version );

	require_once URIS_PLUGIN_DIR_PATH . 'includes/migration.php';
	$migrated = function_exists( 'uris_run_migration' ) ? uris_run_migration() : true;

	if ( ! $migrated ) {
		update_option( 'uris_migration_attempts', $attempts + 1 );
		return;
	}

	update_option( 'uris_migration_attempts', 0 );
	update_option( 'wpfrank_uris_current_version', URIS_PLUGIN_VER );
}

// Core requires
require_once( URIS_PLUGIN_DIR_PATH . 'includes/defaults.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'includes/class-urisp-fonts.php' );
if ( function_exists( 'urisp_fonts' ) ) {
	urisp_fonts();
}
require_once( URIS_PLUGIN_DIR_PATH . 'includes/dynamic-source.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'includes/slide-loader.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'includes/elementor-widget.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'includes/gutenberg-block.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'init.php' );
require_once( URIS_PLUGIN_DIR_PATH . 'shortcode.php' );

/**
 * Registers (once) the single set of frontend handles used by the shortcode,
 * the Gutenberg block and the Elementor widget.
 */
function uris_register_frontend_assets() {
	$ver = defined( 'URIS_PLUGIN_VER' ) ? URIS_PLUGIN_VER : '4.0.0';
	$js_file = URIS_PLUGIN_DIR_PATH . 'assets/js/jquery.sliderPro.js';
	$js_ver  = file_exists( $js_file ) ? filemtime( $js_file ) : $ver;
	$fe_css  = URIS_PLUGIN_DIR_PATH . 'assets/css/urisp-frontend.css';
	$fe_ver  = file_exists( $fe_css ) ? filemtime( $fe_css ) : $ver;
	$mod_css = URIS_PLUGIN_DIR_PATH . 'assets/css/urisp-modern.css';
	$mod_ver = file_exists( $mod_css ) ? filemtime( $mod_css ) : $ver;

	if ( ! wp_script_is( 'wpfrank-uris-jquery-sliderPro-js', 'registered' ) ) {
		wp_register_script('wpfrank-uris-jquery-sliderPro-js', URIS_PLUGIN_URL . 'assets/js/jquery.sliderPro.js', array('jquery'), $js_ver, true);
	}
	if ( ! wp_style_is( 'wpfrank-uris-slider-pro-min-css', 'registered' ) ) {
		wp_register_style('wpfrank-uris-slider-pro-min-css', URIS_PLUGIN_URL . 'assets/css/slider-pro.min.css', array(), $ver);
	}
	if ( ! wp_style_is( 'wpfrank-uris-frontend-css', 'registered' ) ) {
		wp_register_style('wpfrank-uris-frontend-css', URIS_PLUGIN_URL . 'assets/css/urisp-frontend.css', array(), $fe_ver);
	}
	if ( ! wp_style_is( 'wpfrank-uris-modern-css', 'registered' ) ) {
		wp_register_style('wpfrank-uris-modern-css', URIS_PLUGIN_URL . 'assets/css/urisp-modern.css', array(), $mod_ver);
	}
	if ( ! wp_style_is( 'wpfrank-uris-skins-css', 'registered' ) ) {
		wp_register_style('wpfrank-uris-skins-css', URIS_PLUGIN_URL . 'assets/css/urisp-skins.css', array(), $ver);
	}
}

/**
 * CSS and JS scripts for Free URIS Frontend
 */
function uris_frontend_scripts() {
	wp_enqueue_script('jquery');
	uris_register_frontend_assets();
}
add_action( 'wp_enqueue_scripts', 'uris_frontend_scripts');