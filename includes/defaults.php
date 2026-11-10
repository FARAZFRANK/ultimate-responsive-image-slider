<?php
/**
 * Ultimate Responsive Image Slider - Default Settings & Definitions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns default settings array for URIS Free
 * 
 * @return array
 */
function uris_get_default_settings() {
	return array(
		// Layout selection (3 = Classic Thumbs; 1, 2, 4, 5 are Pro)
		'slider_layout'            => 3,
		'slide_title'              => 0,

		// Shared settings
		'auto_slideshow'           => 1,
		'slide_order'              => 'ASC',
		'slide_distance'           => 5,
		'sliding_arrow'            => 1,
		'navigation_button'        => 0,
		'navigation_button_style'  => 1,
		'transition_speed'         => '5000',
		'transition_effect'        => 1,
		'slider_width'             => 1280,
		'width_mode'               => 'custom',
		'slider_height'            => 720,
		'height_mode'              => 'custom',

		// Layout 1 & 3 specific
		'text_alignment'           => 'bottomCenter',
		'title_transition'         => 'up',
		'description_transition'   => 'down',
		'detailed_navigation'      => 1,

		// Layout 3 specific
		'navigation_position'      => 'bottom',
		'thumbnail_style'          => 'border',
		'thumbnail_style_color'    => '#000000',
		'thumbnail_width'          => 120,
		'thumbnail_height'         => 100,

		// Layout 5 fallback
		'sliding_direction'        => 'horizontal',
		'navigation_content'       => 'both',
		'navigation_position_l5'   => 'right',

		// Common styling & Content Display
		'show_slide_title'         => 0,
		'show_slide_desc'          => 0,
		'slide_in_title_color'     => '#FFFFFF',
		'slide_in_title_bg_color'  => '#000000',
		'slide_in_title_size'      => 16,
		'slide_in_desc_color'      => '#FFFFFF',
		'slide_in_desc_bg_color'   => '#000000',
		'slide_in_desc_size'       => 14,
		'thumbnail_bg_color'       => '#0080FF',
		'thumbnail_title_color'    => '#ffffff',
		'thumbnail_desc_color'     => '#ffffff',
		'bg_color_opacity'         => '0.7',
		'read_more_link_on'        => 'text',
		'read_more_link_text'      => 'Read More',
		'read_more_link_color'     => '#0080FF',
		'read_more_link_open'      => '_new',
		'navigation_button_color'  => '#0080FF',
		'navigation_button_size'   => 10,
		'navigation_arrow_color'   => '#FFFFFF',
		'navigation_arrow_bg_color'=> '#0f172a',
		'navigation_arrow_radius'  => 'circle',
		'arrow_style'              => 'chevron',
		'lightbox_preview'         => 0,
		'lightbox_type'            => 0,
		'mouse_wheel_slideshow'    => 0,
		'scale_mode'               => 'cover',
		'image_size'               => 'full',
		'auto_scale'               => 1,
		'fullscreen'               => 0,
		'font_style'               => 'Arial',

		// Interaction & Responsive
		'touch_swipe'              => 1,
		'mouse_wheel'              => 0,
		'keyboard_navigation'      => 1,
		'loop'                     => 1,
		'pause_on_hover'           => 1,
		'slider_order_by'          => 'menu_order',
		'reverse_order'            => 0,
		'touch_swipe_threshold'    => 50,

		// Visual Skin & Preloader
		'skin_preset'              => 'default',
		'slider_skin'              => 'default',
		'ken_burns'                => 0,
		'ken_burns_intensity'      => 'subtle',
		'ken_burns_speed'          => 8000,
		'preloader_spinner'        => 'pulse',
		'spinner_color'            => '#6366f1',
		'lazy_load'                => '1',
		'lazy_loading'             => 1,
		'skeleton_loading'         => 0,
		'show_loader'              => '1',
		'loader_style'             => 'spinner',
		'loader_color'             => '#6366f1',

		// CTA Button Settings
		'cta_button_style'         => 'filled',
		'cta_button_bg_color'      => '#6366f1',
		'cta_button_text_color'    => '#ffffff',
		'cta_button_hover_color'   => '#4f46e5',
		'cta_button_hover_text_color' => '#ffffff',

		// Fullscreen & Lightbox
		'fullscreen_button'        => 0,
		'enable_lightbox'          => 0,
		'lightbox_theme'           => 'default',

		// Video Settings (Vimeo is Active in Free; YouTube, Dailymotion & MP4 are Pro)
		'video_type'               => 'vimeo',
		'video_autoplay'           => 0,

		// Dynamic Source (Pro Teaser in Free)
		'slider_source'            => 'manual',
		'dynamic_source_type'      => 'manual',
		'dynamic_post_type'        => 'post',
		'dynamic_taxonomy'         => '',
		'dynamic_term'             => '',
		'dynamic_posts_count'      => 10,
		'dynamic_orderby'          => 'date',
		'dynamic_order'            => 'DESC',

		// Watermark Settings (Pro Teaser in Free)
		'watermark_enabled'        => 0,
		'enable_watermark'         => 0,
		'watermark_type'           => 'text',
		'watermark_text'           => '',
		'watermark_image'          => '',
		'watermark_image_id'       => 0,
		'watermark_position'       => 'bottom-right',
		'watermark_opacity'        => 50,
		'watermark_size'           => 24,
		'watermark_image_size'     => 15,

		// Backward compatibility key aliases
		'auto_scale_slider'        => 1,
		'font_family'              => 'Arial, Helvetica, sans-serif',
		'slide_title_color'        => '#FFFFFF',
		'slide_title_bg_color'     => '#000000',
		'slide_desc_color'         => '#FFFFFF',
		'slide_desc_bg_color'      => '#000000',
		'title_font_size'          => 16,
		'desc_font_size'           => 14,
		'title_bg_opacity'         => 70,
		'desc_bg_opacity'          => 70,
		'navigation_bullet_color'  => '#0080FF',
		'navigation_bullet_active_color' => '#0080FF',
		'navigation_bullet_size'   => 10,
		'slider_navigation'        => 1,
	);
}

// Alias function for backward compatibility
if ( ! function_exists( 'urisp_get_default_settings' ) ) {
	function urisp_get_default_settings() {
		return uris_get_default_settings();
	}
}

/**
 * Returns key map between old legacy setting keys and unified new setting keys
 * 
 * @return array
 */
function uris_get_legacy_key_map() {
	return array(
		// Global / Common Settings
		'RISP_Slider_Layout'            => 'slider_layout',
		'RISP_Slide_Title'              => 'slide_title',
		'RISP_Show_Slide_Title'         => 'show_slide_title',
		'RISP_Show_Slide_Desc'          => 'show_slide_desc',
		'RISP_lightbox_type'            => 'lightbox_type',
		'RISP_mouse_wheel_slideshow'    => 'mouse_wheel_slideshow',
		'RISP_Slider_Scale_Mode'        => 'scale_mode',
		'RISP_Slider_Auto_Scale'        => 'auto_scale',
		'RISP_Slide_In_Title_Color'     => 'slide_in_title_color',
		'RISP_Slide_In_Title_BG_Color'   => 'slide_in_title_bg_color',
		'RISP_Slide_In_Title_Size'      => 'slide_in_title_size',
		'RISP_Slide_In_Desc_Color'      => 'slide_in_desc_color',
		'RISP_Slide_In_Desc_BG_Color'    => 'slide_in_desc_bg_color',
		'RISP_Slide_In_Desc_Size'       => 'slide_in_desc_size',
		'RISP_Thumbnail_BG_Color'       => 'thumbnail_bg_color',
		'RISP_Thumbnail_Title_Color'    => 'thumbnail_title_color',
		'RISP_Thumbnail_Desc_Color'     => 'thumbnail_desc_color',
		'RISP_BG_Color_Opacity'         => 'bg_color_opacity',
		'RISP_Read_More_Link_ON'        => 'read_more_link_on',
		'RISP_Read_More_Link_Text'      => 'read_more_link_text',
		'RISP_Read_More_Link_Color'     => 'read_more_link_color',
		'RISP_Read_More_Link_Open'      => 'read_more_link_open',
		'RISP_Naviagtion_Button_Color'  => 'navigation_button_color',
		'RISP_Naviagtion_Button_Size'   => 'navigation_button_size',
		'RISP_Naviagtion_Arrow_Color'   => 'navigation_arrow_color',
		'RISP_Naviagtion_Arrow_BG_Color'=> 'navigation_arrow_bg_color',
		'RISP_Naviagtion_Arrow_Radius'  => 'navigation_arrow_radius',
		'RISP_Arrow_Style'              => 'arrow_style',
		'RISP_Lightbox_Preview'         => 'lightbox_preview',
		'RISP_Fullscreeen'              => 'fullscreen',
		'RISP_Font_Style'              => 'font_style',
		'RISP_Woo_Show_Price'          => 'woo_show_price',
		'RISP_Woo_Show_Cart_Btn'       => 'woo_show_cart_btn',
		'RISP_Woo_Show_Sale_Badge'     => 'woo_show_sale_badge',
		'RISP_Ken_Burns'               => 'ken_burns',
		'RISP_Ken_Burns_Intensity'     => 'ken_burns_intensity',
		'RISP_Pause_On_Hover'          => 'pause_on_hover',
		'RISP_CTA_Button_Style'        => 'cta_button_style',
		'RISP_CTA_Button_BG_Color'     => 'cta_button_bg_color',
		'RISP_CTA_Button_Text_Color'   => 'cta_button_text_color',
		'RISP_CTA_Button_Hover_Color'  => 'cta_button_hover_color',
		'RISP_CTA_Button_Hover_Text_Color' => 'cta_button_hover_text_color',
		'RISP_Skin_Preset'             => 'skin_preset',
		'RISP_Touch_Swipe_Threshold'   => 'touch_swipe_threshold',

		// Layout 1
		'RISP_L1_Text_Alignment'        => 'text_alignment',
		'RISP_L1_Title_Transition'      => 'title_transition',
		'RISP_L1_Description_Transition'=> 'description_transition',
		'RISP_L1_Auto_Slideshow'        => 'auto_slideshow',
		'RISP_L1_Slide_Order'           => 'slide_order',
		'RISP_L1_Slide_Distance'        => 'slide_distance',
		'RISP_L1_Thumbnail_Style_Color' => 'thumbnail_style_color',
		'RISP_L1_Sliding_Arrow'         => 'sliding_arrow',
		'RISP_L1_Slider_Navigation'     => 'detailed_navigation',
		'RISP_L1_Navigation_Button'     => 'navigation_button',
		'RISP_L1_Navigation_Button_Style'=> 'navigation_button_style',
		'RISP_L1_Slider_Transition_Speed'=> 'transition_speed',
		'RISP_L1_Slider_Transition_Effect'=> 'transition_effect',
		'RISP_L1_Slider_Width'          => 'slider_width',
		'RISP_L1_Width'                 => 'width_mode',
		'RISP_L1_Slider_Height'         => 'slider_height',
		'RISP_L1_Height'                => 'height_mode',

		// Layout 2
		'RISP_L2_Text_Alignment'        => 'text_alignment',
		'RISP_L2_Title_Transition'      => 'title_transition',
		'RISP_L2_Description_Transition'=> 'description_transition',
		'RISP_L2_Auto_Slideshow'        => 'auto_slideshow',
		'RISP_L2_Slide_Order'           => 'slide_order',
		'RISP_L2_Slide_Distance'        => 'slide_distance',
		'RISP_L2_Sliding_Arrow'         => 'sliding_arrow',
		'RISP_L2_Navigation_Button'     => 'navigation_button',
		'RISP_L2_Navigation_Button_Style'=> 'navigation_button_style',
		'RISP_L2_Slider_Transition_Speed'=> 'transition_speed',
		'RISP_L2_Slider_Width'          => 'slider_width',
		'RISP_L2_Width'                 => 'width_mode',
		'RISP_L2_Slider_Height'         => 'slider_height',
		'RISP_L2_Height'                => 'height_mode',

		// Layout 3
		'RISP_L3_Text_Alignment'        => 'text_alignment',
		'RISP_L3_Title_Transition'      => 'title_transition',
		'RISP_L3_Description_Transition'=> 'description_transition',
		'RISP_L3_Auto_Slideshow'        => 'auto_slideshow',
		'RISP_L3_Slide_Order'           => 'slide_order',
		'RISP_L3_Slide_Distance'        => 'slide_distance',
		'RISP_L3_Navigation_Position'    => 'navigation_position',
		'RISP_L3_Thumbnail_Style'        => 'thumbnail_style',
		'RISP_L3_Thumbnail_Style_Color' => 'thumbnail_style_color',
		'RISP_L3_Thumbnail_Width'       => 'thumbnail_width',
		'RISP_L3_Thumbnail_Height'      => 'thumbnail_height',
		'RISP_L3_Sliding_Arrow'         => 'sliding_arrow',
		'RISP_L3_Slider_Navigation'     => 'detailed_navigation',
		'RISP_L3_Navigation_Button'     => 'navigation_button',
		'RISP_L3_Navigation_Button_Style'=> 'navigation_button_style',
		'RISP_L3_Slider_Transition_Speed'=> 'transition_speed',
		'RISP_L3_Slider_Transition_Effect'=> 'transition_effect',
		'RISP_L3_Slider_Width'          => 'slider_width',
		'RISP_L3_Width'                 => 'width_mode',
		'RISP_L3_Slider_Height'         => 'slider_height',
		'RISP_L3_Height'                => 'height_mode',

		// Layout 4
		'RISP_L4_Text_Alignment'        => 'text_alignment',
		'RISP_L4_Title_Transition'      => 'title_transition',
		'RISP_L4_Description_Transition'=> 'description_transition',
		'RISP_L4_Auto_Slideshow'        => 'auto_slideshow',
		'RISP_L4_Slide_Order'           => 'slide_order',
		'RISP_L4_Slide_Distance'        => 'slide_distance',
		'RISP_L4_Sliding_Arrow'         => 'sliding_arrow',
		'RISP_L4_Navigation_Button'     => 'navigation_button',
		'RISP_L4_Navigation_Button_Style'=> 'navigation_button_style',
		'RISP_L4_Slider_Transition_Speed'=> 'transition_speed',
		'RISP_L4_Slider_Transition_Effect'=> 'transition_effect',
		'RISP_L4_Slider_Width'          => 'slider_width',
		'RISP_L4_Width'                 => 'width_mode',
		'RISP_L4_Slider_Height'         => 'slider_height',
		'RISP_L4_Height'                => 'height_mode',

		// Layout 5
		'RISP_L5_Text_Alignment'        => 'text_alignment',
		'RISP_L5_Title_Transition'      => 'title_transition',
		'RISP_L5_Description_Transition'=> 'description_transition',
		'RISP_L5_Auto_Slideshow'        => 'auto_slideshow',
		'RISP_L5_Slide_Order'           => 'slide_order',
		'RISP_L5_Slide_Distance'        => 'slide_distance',
		'RISP_L5_Thumbnail_Style_Color' => 'thumbnail_style_color',
		'RISP_L5_Sliding_Arrow'         => 'sliding_arrow',
		'RISP_L5_Sliding_Direction'     => 'sliding_direction',
		'RISP_L5_Navigation_Button'     => 'navigation_button',
		'RISP_L5_Navigation_Button_Style'=> 'navigation_button_style',
		'RISP_L5_Slider_Navigation'     => 'detailed_navigation',
		'RISP_L5_Slider_Navigation_Content'=> 'navigation_content',
		'RISP_L5_Slider_Navigation_Position'=> 'navigation_position_l5',
		'RISP_L5_Slider_Transition_Speed'=> 'transition_speed',
		'RISP_L5_Slider_Transition_Effect'=> 'transition_effect',
		'RISP_L5_Slider_Width'          => 'slider_width',
		'RISP_L5_Width'                 => 'width_mode',
		'RISP_L5_Slider_Height'         => 'slider_height',
		'RISP_L5_Height'                => 'height_mode',
	);
}

if ( ! function_exists( 'urisp_get_legacy_key_map' ) ) {
	function urisp_get_legacy_key_map() {
		return uris_get_legacy_key_map();
	}
}

/**
 * Returns sanitization rule mapping for unified keys
 * 
 * @return array
 */
function uris_get_sanitization_rules() {
	return array(
		'slider_layout'            => 'absint',
		'slide_title'              => 'absint',
		'auto_slideshow'           => 'absint',
		'slide_order'              => 'sanitize_text_field',
		'slide_distance'           => 'absint',
		'sliding_arrow'            => 'absint',
		'navigation_button'        => 'absint',
		'navigation_button_style'  => 'absint',
		'transition_speed'         => 'absint',
		'transition_effect'        => 'absint',
		'slider_width'             => 'sanitize_text_field',
		'width_mode'               => 'sanitize_text_field',
		'slider_height'            => 'sanitize_text_field',
		'height_mode'              => 'sanitize_text_field',
		'text_alignment'           => 'sanitize_text_field',
		'title_transition'         => 'sanitize_text_field',
		'description_transition'   => 'sanitize_text_field',
		'detailed_navigation'      => 'absint',
		'navigation_position'      => 'sanitize_text_field',
		'thumbnail_style'          => 'sanitize_text_field',
		'thumbnail_style_color'    => 'sanitize_hex_color',
		'thumbnail_width'          => 'absint',
		'thumbnail_height'         => 'absint',
		'sliding_direction'        => 'sanitize_text_field',
		'navigation_content'       => 'sanitize_text_field',
		'navigation_position_l5'   => 'sanitize_text_field',
		'show_slide_title'         => 'absint',
		'show_slide_desc'          => 'absint',
		'slide_in_title_color'     => 'sanitize_hex_color',
		'slide_in_title_bg_color'  => 'sanitize_text_field',
		'slide_in_title_size'      => 'absint',
		'slide_in_desc_color'      => 'sanitize_hex_color',
		'slide_in_desc_bg_color'   => 'sanitize_text_field',
		'slide_in_desc_size'       => 'absint',
		'thumbnail_bg_color'       => 'sanitize_text_field',
		'thumbnail_title_color'    => 'sanitize_hex_color',
		'thumbnail_desc_color'     => 'sanitize_hex_color',
		'bg_color_opacity'         => 'sanitize_text_field',
		'read_more_link_on'        => 'sanitize_text_field',
		'read_more_link_text'      => 'sanitize_text_field',
		'read_more_link_color'     => 'sanitize_hex_color',
		'read_more_link_open'      => 'sanitize_text_field',
		'navigation_button_color'  => 'sanitize_hex_color',
		'navigation_button_size'   => 'absint',
		'navigation_arrow_color'   => 'sanitize_hex_color',
		'navigation_arrow_bg_color'=> 'sanitize_text_field',
		'navigation_arrow_radius'  => 'sanitize_text_field',
		'arrow_style'              => 'sanitize_key',
		'lightbox_preview'         => 'absint',
		'lightbox_type'            => 'absint',
		'mouse_wheel_slideshow'    => 'absint',
		'scale_mode'               => 'sanitize_text_field',
		'image_size'               => 'sanitize_key',
		'auto_scale'               => 'absint',
		'fullscreen'               => 'absint',
		'font_style'               => 'sanitize_text_field',
		'lazy_load'                => 'absint',
		'skeleton_loading'         => 'absint',
		'show_loader'              => 'absint',
		'loader_style'             => 'sanitize_text_field',
		'loader_color'             => 'sanitize_hex_color',

		// Dynamic Data Source Rules
		'slider_source'            => 'sanitize_key',
		'dynamic_post_type'        => 'sanitize_key',
		'dynamic_taxonomy'         => 'sanitize_key',
		'dynamic_term'             => 'sanitize_text_field',
		'dynamic_posts_count'      => 'absint',
		'dynamic_orderby'          => 'sanitize_key',
		'dynamic_order'            => 'sanitize_key',

		// WooCommerce Integration Rules
		'woo_show_price'           => 'absint',
		'woo_show_cart_btn'        => 'absint',
		'woo_show_sale_badge'      => 'absint',
		'ken_burns'                => 'absint',
		'ken_burns_intensity'      => 'sanitize_key',
		'pause_on_hover'           => 'absint',
		'cta_button_style'         => 'sanitize_key',
		'cta_button_bg_color'      => 'sanitize_hex_color',
		'cta_button_text_color'    => 'sanitize_hex_color',
		'cta_button_hover_color'   => 'sanitize_hex_color',
		'cta_button_hover_text_color' => 'sanitize_hex_color',
		'skin_preset'              => 'sanitize_key',
		'touch_swipe_threshold'    => 'absint',

		// Watermark Protection Rules
		'watermark_enabled'        => 'absint',
		'watermark_type'           => 'sanitize_key',
		'watermark_text'           => 'sanitize_text_field',
		'watermark_image'          => 'esc_url_raw',
		'watermark_position'       => 'sanitize_key',
		'watermark_opacity'        => 'absint',
		'watermark_size'           => 'absint',
		'watermark_image_size'     => 'absint',
	);
}

if ( ! function_exists( 'urisp_get_sanitization_rules' ) ) {
	function urisp_get_sanitization_rules() {
		return uris_get_sanitization_rules();
	}
}


/**
 * Returns available system fonts for Free version
 */
function uris_get_system_fonts() {
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

/**
 * Alias pairs where the slider editor edits the first key while the frontend
 * renderer reads the second one.
 *
 * @return array
 */
function uris_get_editor_alias_pairs() {
	return array(
		'slide_in_title_color'    => 'slide_title_color',
		'slide_in_title_bg_color' => 'slide_title_bg_color',
		'slide_in_title_size'     => 'title_font_size',
		'slide_in_desc_color'     => 'slide_desc_color',
		'slide_in_desc_bg_color'  => 'slide_desc_bg_color',
		'slide_in_desc_size'      => 'desc_font_size',
		'font_style'              => 'font_family',
		'auto_scale'              => 'auto_scale_slider',
		'fullscreen'              => 'fullscreen_button',
		'navigation_button_color' => 'navigation_bullet_color',
		'navigation_button_size'  => 'navigation_bullet_size',
	);
}

if ( ! function_exists( 'urisp_get_editor_alias_pairs' ) ) {
	function urisp_get_editor_alias_pairs() {
		return uris_get_editor_alias_pairs();
	}
}

/**
 * Copies the editor keys onto the alias keys the renderer reads.
 *
 * @param array $settings
 * @return array
 */
function uris_sync_editor_alias_keys( $settings ) {
	if ( ! is_array( $settings ) ) {
		return $settings;
	}

	foreach ( uris_get_editor_alias_pairs() as $editor_key => $render_key ) {
		if ( isset( $settings[ $editor_key ] ) ) {
			$settings[ $render_key ] = $settings[ $editor_key ];
		}
	}

	return $settings;
}

if ( ! function_exists( 'urisp_sync_editor_alias_keys' ) ) {
	function urisp_sync_editor_alias_keys( $settings ) {
		return uris_sync_editor_alias_keys( $settings );
	}
}

/**
 * Copies the renderer keys onto the editor keys.
 *
 * Used for data that was produced outside the editor (legacy conversion and
 * migration snapshots), where the renderer keys are the authoritative ones.
 *
 * @param array $settings
 * @return array
 */
function uris_sync_renderer_alias_keys( $settings ) {
	if ( ! is_array( $settings ) ) {
		return $settings;
	}

	foreach ( uris_get_editor_alias_pairs() as $editor_key => $render_key ) {
		if ( isset( $settings[ $render_key ] ) ) {
			$settings[ $editor_key ] = $settings[ $render_key ];
		}
	}

	return $settings;
}

if ( ! function_exists( 'urisp_sync_renderer_alias_keys' ) ) {
	function urisp_sync_renderer_alias_keys( $settings ) {
		return uris_sync_renderer_alias_keys( $settings );
	}
}

/**
 * Cleans up loaded settings to ensure fields that were incorrectly saved as 0 or empty
 * are filtered out so that they correctly fallback to their default values.
 * 
 * @param array $settings
 * @return array
 */
function uris_clean_loaded_settings( $settings ) {
	if ( ! is_array( $settings ) ) {
		return array();
	}

	$non_zero_keys = array(
		'height_mode',
		'title_transition',
		'description_transition',
		'navigation_position',
		'thumbnail_style',
		'thumbnail_style_color',
		'thumbnail_width',
		'thumbnail_height',
		'sliding_direction',
		'navigation_content',
		'navigation_position_l5',
		'read_more_link_color',
		'navigation_button_size',
	);

	foreach ( $non_zero_keys as $key ) {
		if ( isset( $settings[$key] ) && ( $settings[$key] === 0 || $settings[$key] === '0' || $settings[$key] === '' ) ) {
			unset( $settings[$key] );
		}
	}

	// Normalize width_mode if loaded from legacy format
	if ( isset( $settings['width_mode'] ) && ( $settings['width_mode'] === '100%' || $settings['width_mode'] === 'percentage' || $settings['width_mode'] === '%' ) ) {
		$settings['width_mode'] = 'custom';
		if ( empty( $settings['slider_width'] ) || ( is_numeric( str_replace( '%', '', $settings['slider_width'] ) ) && (int) str_replace( '%', '', $settings['slider_width'] ) <= 100 ) ) {
			$settings['slider_width'] = 1280;
		}
	}

	if ( isset( $settings['image_size'] ) && ! in_array( $settings['image_size'], array( 'full', 'large', 'medium_large', 'medium' ), true ) ) {
		unset( $settings['image_size'] );
	}

	if ( isset( $settings['arrow_style'] ) && ! in_array( $settings['arrow_style'], array( 'chevron', 'caret', 'thin', 'bold', 'minimal' ), true ) ) {
		unset( $settings['arrow_style'] );
	}

	if ( isset( $settings['ken_burns_intensity'] ) && ! in_array( $settings['ken_burns_intensity'], array( 'subtle', 'medium', 'dramatic' ), true ) ) {
		unset( $settings['ken_burns_intensity'] );
	}

	if ( isset( $settings['cta_button_style'] ) && ! in_array( $settings['cta_button_style'], array( 'filled', 'outlined', 'pill', 'text' ), true ) ) {
		unset( $settings['cta_button_style'] );
	}

	if ( isset( $settings['skin_preset'] ) && ! in_array( $settings['skin_preset'], array( 'default', 'glassmorphism', 'minimalist', 'vibrant', 'neumorphic' ), true ) ) {
		unset( $settings['skin_preset'] );
	}

	if ( isset( $settings['touch_swipe_threshold'] ) && ( ! is_numeric( $settings['touch_swipe_threshold'] ) || (int) $settings['touch_swipe_threshold'] <= 0 ) ) {
		unset( $settings['touch_swipe_threshold'] );
	}

		// Normalize legacy and modern key aliases
	if ( isset( $settings['font_family'] ) && ! isset( $settings['font_style'] ) ) {
		$settings['font_style'] = $settings['font_family'];
	}
	if ( isset( $settings['font_style'] ) && ! isset( $settings['font_family'] ) ) {
		$settings['font_family'] = $settings['font_style'];
	}
	if ( isset( $settings['slide_title_color'] ) && ! isset( $settings['slide_in_title_color'] ) ) {
		$settings['slide_in_title_color'] = $settings['slide_title_color'];
	}
	if ( isset( $settings['slide_in_title_color'] ) && ! isset( $settings['slide_title_color'] ) ) {
		$settings['slide_title_color'] = $settings['slide_in_title_color'];
	}
	if ( isset( $settings['slide_title_bg_color'] ) && ! isset( $settings['slide_in_title_bg_color'] ) ) {
		$settings['slide_in_title_bg_color'] = $settings['slide_title_bg_color'];
	}
	if ( isset( $settings['slide_in_title_bg_color'] ) && ! isset( $settings['slide_title_bg_color'] ) ) {
		$settings['slide_title_bg_color'] = $settings['slide_in_title_bg_color'];
	}
	if ( isset( $settings['slide_desc_color'] ) && ! isset( $settings['slide_in_desc_color'] ) ) {
		$settings['slide_in_desc_color'] = $settings['slide_desc_color'];
	}
	if ( isset( $settings['slide_in_desc_color'] ) && ! isset( $settings['slide_desc_color'] ) ) {
		$settings['slide_desc_color'] = $settings['slide_in_desc_color'];
	}
	if ( isset( $settings['slide_desc_bg_color'] ) && ! isset( $settings['slide_in_desc_bg_color'] ) ) {
		$settings['slide_in_desc_bg_color'] = $settings['slide_desc_bg_color'];
	}
	if ( isset( $settings['slide_in_desc_bg_color'] ) && ! isset( $settings['slide_desc_bg_color'] ) ) {
		$settings['slide_desc_bg_color'] = $settings['slide_in_desc_bg_color'];
	}
	if ( isset( $settings['auto_scale_slider'] ) && ! isset( $settings['auto_scale'] ) ) {
		$settings['auto_scale'] = $settings['auto_scale_slider'];
	}
	if ( isset( $settings['auto_scale'] ) && ! isset( $settings['auto_scale_slider'] ) ) {
		$settings['auto_scale_slider'] = $settings['auto_scale'];
	}
	if ( isset( $settings['fullscreen_button'] ) && ! isset( $settings['fullscreen'] ) ) {
		$settings['fullscreen'] = $settings['fullscreen_button'];
	}
	if ( isset( $settings['fullscreen'] ) && ! isset( $settings['fullscreen_button'] ) ) {
		$settings['fullscreen_button'] = $settings['fullscreen'];
	}
	if ( isset( $settings['title_font_size'] ) && ! isset( $settings['slide_in_title_size'] ) ) {
		$settings['slide_in_title_size'] = $settings['title_font_size'];
	}
	if ( isset( $settings['desc_font_size'] ) && ! isset( $settings['slide_in_desc_size'] ) ) {
		$settings['slide_in_desc_size'] = $settings['desc_font_size'];
	}
	if ( isset( $settings['navigation_bullet_color'] ) && ! isset( $settings['navigation_button_color'] ) ) {
		$settings['navigation_button_color'] = $settings['navigation_bullet_color'];
	}
	if ( isset( $settings['navigation_bullet_size'] ) && ! isset( $settings['navigation_button_size'] ) ) {
		$settings['navigation_button_size'] = $settings['navigation_bullet_size'];
	}

	return $settings;
}

if ( ! function_exists( 'urisp_clean_loaded_settings' ) ) {
	function urisp_clean_loaded_settings( $settings ) {
		return uris_clean_loaded_settings( $settings );
	}
}

/**
 * Returns merged global settings option
 * 
 * @return array
 */
function uris_get_global_settings() {
	$defaults = array(
		'lazy_load'        => 1,
		'skeleton_loading' => 1,
		'show_loader'      => 1,
		'loader_style'     => 'spinner',
		'loader_color'     => '#6366f1',
		'enable_analytics' => 0,
	);

	$saved = get_option( 'urisp_global_settings' );
	if ( ! is_array( $saved ) ) {
		return $defaults;
	}

	return wp_parse_args( $saved, $defaults );
}

if ( ! function_exists( 'urisp_get_global_settings' ) ) {
	function urisp_get_global_settings() {
		return uris_get_global_settings();
	}
}

/**
 * Returns array of dedicated custom capabilities for the plugin
 */
function uris_get_plugin_capabilities() {
	return array(
		'manage_urisp_sliders'  => array(
			'label' => __( 'Manage Sliders', 'ultimate-responsive-image-slider' ),
			'desc'  => __( 'Create, edit, clone, and delete image sliders.', 'ultimate-responsive-image-slider' ),
		),
		'view_urisp_analytics'  => array(
			'label' => __( 'View Analytics', 'ultimate-responsive-image-slider' ),
			'desc'  => __( 'View visitor impressions, CTR, and export CSV reports.', 'ultimate-responsive-image-slider' ),
		),
		'manage_urisp_settings' => array(
			'label' => __( 'Manage Global Settings', 'ultimate-responsive-image-slider' ),
			'desc'  => __( 'Configure global plugin settings and perform import/export.', 'ultimate-responsive-image-slider' ),
		),
	);
}

if ( ! function_exists( 'urisp_get_plugin_capabilities' ) ) {
	function urisp_get_plugin_capabilities() {
		return uris_get_plugin_capabilities();
	}
}

function uris_get_plugin_capability_keys() {
	return array(
		'manage_urisp_sliders',
		'view_urisp_analytics',
		'manage_urisp_settings',
	);
}

if ( ! function_exists( 'urisp_get_plugin_capability_keys' ) ) {
	function urisp_get_plugin_capability_keys() {
		return uris_get_plugin_capability_keys();
	}
}

function uris_current_user_can( $capability ) {
	if ( ! is_user_logged_in() ) {
		return false;
	}
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}
	return current_user_can( $capability );
}

if ( ! function_exists( 'urisp_current_user_can' ) ) {
	function urisp_current_user_can( $capability ) {
		return uris_current_user_can( $capability );
	}
}

if ( ! function_exists( 'urisp_toggle_html' ) ) {
	function urisp_toggle_html( $name, $current_val, $array_name = 'urisp_settings' ) {
		$checked = ( $current_val == 1 ) ? 'checked="checked"' : '';
		return '<label class="urisp-toggle">
			<input type="checkbox" name="' . esc_attr( $array_name ) . '[' . esc_attr( $name ) . ']" value="1" ' . $checked . '>
			<span class="urisp-toggle-slider"></span>
		</label>';
	}
}

if ( ! function_exists( 'uris_toggle_html' ) ) {
	function uris_toggle_html( $name, $current_val, $array_name = 'urisp_settings' ) {
		return urisp_toggle_html( $name, $current_val, $array_name );
	}
}

/**
 * Grants the plugin capabilities to the administrator role.
 *
 * Called on activation so the capabilities exist on a real role instead of
 * only being injected at runtime by uris_filter_user_has_cap().
 */
function uris_grant_admin_capabilities() {
	$role = get_role( 'administrator' );
	if ( ! $role ) {
		return;
	}

	foreach ( uris_get_plugin_capability_keys() as $capability ) {
		if ( ! $role->has_cap( $capability ) ) {
			$role->add_cap( $capability );
		}
	}
}

if ( ! function_exists( 'urisp_grant_admin_capabilities' ) ) {
	function urisp_grant_admin_capabilities() {
		return uris_grant_admin_capabilities();
	}
}

if ( ! function_exists( 'urisp_hex_to_rgba' ) ) {
	function urisp_hex_to_rgba( $hex, $opacity = 1 ) {
		$hex = trim( (string) $hex );
		if ( empty( $hex ) ) {
			return 'rgba(0, 0, 0, ' . floatval( $opacity ) . ')';
		}
		if ( strpos( $hex, 'rgba' ) !== false || strpos( $hex, 'rgb' ) !== false ) {
			return $hex;
		}
		$clean_hex = ltrim( $hex, '#' );
		if ( strlen( $clean_hex ) === 3 ) {
			$r = hexdec( substr( $clean_hex, 0, 1 ) . substr( $clean_hex, 0, 1 ) );
			$g = hexdec( substr( $clean_hex, 1, 1 ) . substr( $clean_hex, 1, 1 ) );
			$b = hexdec( substr( $clean_hex, 2, 1 ) . substr( $clean_hex, 2, 1 ) );
		} elseif ( strlen( $clean_hex ) >= 6 ) {
			$r = hexdec( substr( $clean_hex, 0, 2 ) );
			$g = hexdec( substr( $clean_hex, 2, 2 ) );
			$b = hexdec( substr( $clean_hex, 4, 2 ) );
		} else {
			$r = 0;
			$g = 0;
			$b = 0;
		}
		return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . floatval( $opacity ) . ')';
	}
}

// Ensure administrators always have permissions
function uris_filter_user_has_cap( $allcaps, $caps, $args, $user ) {
	if ( ! empty( $allcaps['manage_options'] ) ) {
		$allcaps['manage_urisp_sliders']  = true;
		$allcaps['manage_urisp_settings'] = true;
		$allcaps['view_urisp_analytics']  = true;
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'uris_filter_user_has_cap', 10, 4 );