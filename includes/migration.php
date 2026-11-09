<?php
/**
 * Ultimate Responsive Image Slider - Migration & Backward Compatibility Layer
 * Seamlessly upgrades legacy settings from Free v3.5.19 (WRIS_L3_*) and Pro formats.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( URIS_PLUGIN_DIR_PATH . 'includes/defaults.php' );

/**
 * Checks if migration to 4.0.0 format is needed
 * 
 * @return bool
 */
function uris_needs_migration() {
	$migrated_version = get_option( 'uris_migrated_version', false );
	return version_compare( (string) $migrated_version, '4.0.0', '<' );
}

/**
 * Legacy Free (v3.5.19) WRIS_L3_* to Modern Setting Key Map
 */
function uris_get_legacy_free_key_map() {
	return array(
		'WRIS_L3_Slide_Title'                   => 'slide_title',
		'WRIS_L3_Show_Slide_Title'              => 'show_slide_title',
		'WRIS_L3_Show_Slide_Desc'               => 'show_slide_desc',
		'WRIS_L3_Auto_Slideshow'                => 'auto_slideshow',
		'WRIS_L3_Transition'                    => 'transition_effect',
		'WRIS_L3_Transition_Speed'              => 'transition_speed',
		'WRIS_L3_Slide_Order'                   => 'slide_order',
		'WRIS_L3_Slide_Distance'                => 'slide_distance',
		'WRIS_L3_Sliding_Arrow'                 => 'sliding_arrow',
		'WRIS_L3_Slider_Navigation'             => 'slider_navigation',
		'WRIS_L3_Navigation_Button'             => 'navigation_button',
		'WRIS_L3_Navigation_Position'           => 'navigation_position',
		'WRIS_L3_Thumbnail_Style'               => 'thumbnail_style',
		'WRIS_L3_Thumbnail_Width'               => 'thumbnail_width',
		'WRIS_L3_Thumbnail_Height'              => 'thumbnail_height',
		'WRIS_L3_Slider_Width'                  => 'slider_width',
		'WRIS_L3_Slider_Height'                 => 'slider_height',
		'WRIS_L3_Width'                         => 'width_mode',
		'WRIS_L3_Height'                        => 'height_mode',
		'WRIS_L3_Font_Style'                    => 'font_family',
		'WRIS_L3_Title_Color'                   => 'slide_title_color',
		'WRIS_L3_Title_BgColor'                 => 'slide_title_bg_color',
		'WRIS_L3_Desc_Color'                    => 'slide_desc_color',
		'WRIS_L3_Desc_BgColor'                  => 'slide_desc_bg_color',
		'WRIS_L3_Slider_Scale_Mode'             => 'scale_mode',
		'WRIS_L3_Navigation_Button_Color'       => 'navigation_bullet_color',
		'WRIS_L3_Navigation_Button_Active_Color'=> 'navigation_bullet_active_color',
	);
}

/**
 * Universal Legacy Converter: handles both Free v3.5.19 and Pro formats
 * 
 * @param array $legacy_settings
 * @return array
 */
function uris_convert_legacy_to_unified( $legacy_settings ) {
	if ( empty( $legacy_settings ) || ! is_array( $legacy_settings ) ) {
		return uris_get_default_settings();
	}

	$defaults = uris_get_default_settings();
	$unified  = $defaults;

	// Check if this is a Free v3.5.19 settings array
	if ( isset( $legacy_settings['WRIS_L3_Slider_Width'] ) || isset( $legacy_settings['WRIS_L3_Auto_Slideshow'] ) ) {
		$free_map = uris_get_legacy_free_key_map();
		$unified['slider_layout'] = 3; // Legacy free was Layout 3 (Thumbnail slider)

		foreach ( $free_map as $old_k => $new_k ) {
			if ( isset( $legacy_settings[ $old_k ] ) ) {
				$val = $legacy_settings[ $old_k ];

				// Normalize specific legacy formats
				if ( $old_k === 'WRIS_L3_Width' ) {
					$val = ( $val === '100%' ) ? 'full' : 'custom';
				} elseif ( $old_k === 'WRIS_L3_Height' ) {
					$val = ( $val === 'auto' ) ? 'auto' : 'custom';
				}

				$unified[ $new_k ] = $val;
			}
		}
	} else {
		// General / Pro legacy format fallback
		$unified['slider_layout'] = 3; // Free version operates exclusively on Layout 3 (Thumbnail slider)

		foreach ( $legacy_settings as $k => $v ) {
			$unified[ $k ] = $v;
		}
	}

	// Mirror legacy and modern key aliases
	if ( isset( $unified['font_family'] ) && ! isset( $unified['font_style'] ) ) {
		$unified['font_style'] = $unified['font_family'];
	}
	if ( isset( $unified['font_style'] ) && ! isset( $unified['font_family'] ) ) {
		$unified['font_family'] = $unified['font_style'];
	}
	if ( isset( $unified['slide_title_color'] ) && ! isset( $unified['slide_in_title_color'] ) ) {
		$unified['slide_in_title_color'] = $unified['slide_title_color'];
	}
	if ( isset( $unified['slide_in_title_color'] ) && ! isset( $unified['slide_title_color'] ) ) {
		$unified['slide_title_color'] = $unified['slide_in_title_color'];
	}
	if ( isset( $unified['slide_title_bg_color'] ) && ! isset( $unified['slide_in_title_bg_color'] ) ) {
		$unified['slide_in_title_bg_color'] = $unified['slide_title_bg_color'];
	}
	if ( isset( $unified['slide_in_title_bg_color'] ) && ! isset( $unified['slide_title_bg_color'] ) ) {
		$unified['slide_title_bg_color'] = $unified['slide_in_title_bg_color'];
	}
	if ( isset( $unified['slide_desc_color'] ) && ! isset( $unified['slide_in_desc_color'] ) ) {
		$unified['slide_in_desc_color'] = $unified['slide_desc_color'];
	}
	if ( isset( $unified['slide_in_desc_color'] ) && ! isset( $unified['slide_desc_color'] ) ) {
		$unified['slide_desc_color'] = $unified['slide_in_desc_color'];
	}
	if ( isset( $unified['slide_desc_bg_color'] ) && ! isset( $unified['slide_in_desc_bg_color'] ) ) {
		$unified['slide_in_desc_bg_color'] = $unified['slide_desc_bg_color'];
	}
	if ( isset( $unified['slide_in_desc_bg_color'] ) && ! isset( $unified['slide_desc_bg_color'] ) ) {
		$unified['slide_desc_bg_color'] = $unified['slide_in_desc_bg_color'];
	}
	if ( isset( $unified['auto_scale_slider'] ) && ! isset( $unified['auto_scale'] ) ) {
		$unified['auto_scale'] = $unified['auto_scale_slider'];
	}
	if ( isset( $unified['auto_scale'] ) && ! isset( $unified['auto_scale_slider'] ) ) {
		$unified['auto_scale_slider'] = $unified['auto_scale'];
	}
	if ( isset( $unified['fullscreen_button'] ) && ! isset( $unified['fullscreen'] ) ) {
		$unified['fullscreen'] = $unified['fullscreen_button'];
	}
	if ( isset( $unified['fullscreen'] ) && ! isset( $unified['fullscreen_button'] ) ) {
		$unified['fullscreen_button'] = $unified['fullscreen'];
	}
	if ( isset( $unified['title_font_size'] ) && ! isset( $unified['slide_in_title_size'] ) ) {
		$unified['slide_in_title_size'] = $unified['title_font_size'];
	}
	if ( isset( $unified['desc_font_size'] ) && ! isset( $unified['slide_in_desc_size'] ) ) {
		$unified['slide_in_desc_size'] = $unified['desc_font_size'];
	}
	if ( isset( $unified['navigation_bullet_color'] ) && ! isset( $unified['navigation_button_color'] ) ) {
		$unified['navigation_button_color'] = $unified['navigation_bullet_color'];
	}
	if ( isset( $unified['navigation_bullet_size'] ) && ! isset( $unified['navigation_button_size'] ) ) {
		$unified['navigation_button_size'] = $unified['navigation_bullet_size'];
	}

	// Always ensure Fullscreen is enabled in Free and Lightbox disabled
	$unified['fullscreen_button'] = 1;
	$unified['enable_lightbox']   = 0;

	// A converted slider fills the renderer keys, so mirror them onto the keys
	// the editor edits. Without this both families diverge on the next save.
	foreach ( uris_get_editor_alias_pairs() as $editor_key => $render_key ) {
		if ( isset( $unified[ $render_key ] ) ) {
			$unified[ $editor_key ] = $unified[ $render_key ];
		}
	}

	return $unified;
}

/**
 * Get slider settings with automatic legacy fallback & migration
 * 
 * @param int $post_id
 * @return array
 */
function uris_get_slider_settings( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return uris_get_default_settings();
	}

	$defaults = uris_get_default_settings();

	// 1. Canonical 4.x key: this is what the slider editor saves to.
	$settings = get_post_meta( $post_id, 'urisp_settings_' . $post_id, true );
	if ( ! empty( $settings ) && is_array( $settings ) ) {
		return wp_parse_args( uris_sync_editor_alias_keys( uris_clean_loaded_settings( $settings ) ), $defaults );
	}

	// 2. Snapshot written by an earlier version of the migration routine. Its
	// renderer keys are the authoritative ones, so mirror them onto the editor keys.
	$settings = get_post_meta( $post_id, 'uris_settings', true );
	if ( ! empty( $settings ) && is_array( $settings ) ) {
		return wp_parse_args( uris_sync_renderer_alias_keys( uris_clean_loaded_settings( $settings ) ), $defaults );
	}

	// 3. Unprefixed Pro key (only present when a site was downgraded from Pro).
	$pro_settings = get_post_meta( $post_id, 'urisp_settings', true );
	if ( ! empty( $pro_settings ) && is_array( $pro_settings ) ) {
		return wp_parse_args( uris_sync_renderer_alias_keys( uris_clean_loaded_settings( $pro_settings ) ), $defaults );
	}

	// 4./5. Legacy Free keys. Converted on read only: a getter must never write,
	// otherwise a stale copy can shadow later editor saves.
	foreach ( array( 'WRIS_Gallery_Settings_', 'ris_gallery_settings_' ) as $legacy_prefix ) {
		$legacy_settings = get_post_meta( $post_id, $legacy_prefix . $post_id, true );
		if ( ! empty( $legacy_settings ) && is_array( $legacy_settings ) ) {
			return wp_parse_args( uris_clean_loaded_settings( uris_convert_legacy_to_unified( $legacy_settings ) ), $defaults );
		}
	}

	return $defaults;
}

/**
 * Returns the IDs of every saved slider.
 *
 * Falls back to a direct query when the ris_gallery post type is not
 * registered yet (activation, cron, WP-CLI), where get_posts() returns nothing.
 *
 * @return int[]
 */
function uris_get_slider_post_ids() {
	if ( post_type_exists( 'ris_gallery' ) ) {
		$ids = get_posts( array(
			'post_type'      => 'ris_gallery',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		) );

		return array_map( 'absint', (array) $ids );
	}

	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Fallback query if called early before post type registration.
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ( 'auto-draft', 'trash' )", 'ris_gallery' ) );

	return array_map( 'absint', (array) $ids );
}

/**
 * Global batch migration for all existing ris_gallery posts.
 *
 * Writes the canonical `urisp_settings_{id}` key so the renderer and the
 * editor share one source of truth. Never creates the old unprefixed
 * `uris_settings` snapshot.
 *
 * @return bool True when the batch completed.
 */
function uris_run_migration() {
	$post_ids = uris_get_slider_post_ids();

	try {
		foreach ( $post_ids as $post_id ) {
			$post_id = absint( $post_id );
			if ( ! $post_id ) {
				continue;
			}

			// The editor owns the canonical key: never overwrite it.
			$canonical = get_post_meta( $post_id, 'urisp_settings_' . $post_id, true );
			if ( ! empty( $canonical ) && is_array( $canonical ) ) {
				continue;
			}

			$converted = null;

			// Already-unified snapshots: merge them as they are.
			foreach ( array( 'uris_settings', 'urisp_settings' ) as $snapshot_key ) {
				$snapshot = get_post_meta( $post_id, $snapshot_key, true );
				if ( ! empty( $snapshot ) && is_array( $snapshot ) ) {
					$converted = wp_parse_args( uris_sync_renderer_alias_keys( uris_clean_loaded_settings( $snapshot ) ), uris_get_default_settings() );
					break;
				}
			}

			// Legacy Free / Pro keys need a real conversion.
			if ( null === $converted ) {
				foreach ( array( 'WRIS_Gallery_Settings_', 'ris_gallery_settings_' ) as $legacy_prefix ) {
					$legacy = get_post_meta( $post_id, $legacy_prefix . $post_id, true );
					if ( ! empty( $legacy ) && is_array( $legacy ) ) {
						$converted = uris_convert_legacy_to_unified( $legacy );
						break;
					}
				}
			}

			if ( empty( $converted ) || ! is_array( $converted ) ) {
				continue;
			}

			update_post_meta( $post_id, 'urisp_settings_' . $post_id, $converted );
		}
	} catch ( \Throwable $e ) {
		// Leave the version option untouched so the batch is retried.
		return false;
	}

	update_option( 'uris_migrated_version', '4.0.0' );

	return true;
}

// Aliases for compatibility
if ( ! function_exists( 'urisp_convert_legacy_to_unified' ) ) {
	function urisp_convert_legacy_to_unified( $legacy_settings ) {
		return uris_convert_legacy_to_unified( $legacy_settings );
	}
}

if ( ! function_exists( 'urisp_get_slider_settings' ) ) {
	function urisp_get_slider_settings( $post_id ) {
		return uris_get_slider_settings( $post_id );
	}
}
if ( ! function_exists( 'urisp_needs_migration' ) ) {
	function urisp_needs_migration() {
		return uris_needs_migration();
	}
}
if ( ! function_exists( 'urisp_run_migration' ) ) {
	function urisp_run_migration() {
		return uris_run_migration();
	}
}