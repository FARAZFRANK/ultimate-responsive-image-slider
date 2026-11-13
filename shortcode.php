<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register multiple shortcode aliases for seamless backward compatibility
add_shortcode( 'URIS', 'uris_shortcode' );
add_shortcode( 'uris', 'uris_shortcode' );
add_shortcode( 'ultimate-responsive-image-slider', 'uris_shortcode' );
add_shortcode( 'URISP', 'uris_shortcode' );

// Legacy PHP template function aliases
if ( ! function_exists( 'Wpfrank_URIS_Shortcode' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Maintained for backward compatibility with legacy themes.
	function Wpfrank_URIS_Shortcode( $atts ) {
		return uris_shortcode( $atts );
	}
}
if ( ! function_exists( 'ResponsiveRISPProShortCode' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Maintained for backward compatibility with legacy themes.
	function ResponsiveRISPProShortCode( $atts ) {
		return uris_shortcode( $atts );
	}
}

function uris_shortcode( $Id ) {
	ob_start();
	
	// Enqueue core slider assets (without heavy lightbox overhead)
	wp_enqueue_style( 'wpfrank-uris-slider-pro-min-css' );
	wp_enqueue_style( 'wpfrank-uris-frontend-css' );
	wp_enqueue_style( 'wpfrank-uris-modern-css' );
	wp_enqueue_style( 'wpfrank-uris-skins-css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wpfrank-uris-jquery-sliderPro-js' );

	if ( ! isset( $Id['id'] ) || empty( $Id['id'] ) ) {
		esc_html_e( 'Shortcode not added properly, Slider ID is missing into shortcode.', 'ultimate-responsive-image-slider' );
		return ob_get_clean();
	}

	$URISP_Id = absint( $Id['id'] );
	$post_id  = $URISP_Id;

	// Load settings with automatic legacy migration
	require_once( URIS_PLUGIN_DIR_PATH . 'includes/defaults.php' );
	require_once( URIS_PLUGIN_DIR_PATH . 'includes/migration.php' );
	require_once( URIS_PLUGIN_DIR_PATH . 'includes/slide-loader.php' );

	$settings = uris_get_slider_settings( $post_id );

	// Free version strictly operates on Layout 3 (Thumbnail Slider)
	$RISP_Slider_Layout             = 3;
	$RISP_Slide_Title               = isset( $settings['slide_title'] ) ? (int) $settings['slide_title'] : 0; // Slider header title display
	$RISP_Show_Slide_Title          = isset( $settings['show_slide_title'] ) ? (int) $settings['show_slide_title'] : 0; // Individual slide title
	$RISP_Show_Slide_Desc           = isset( $settings['show_slide_desc'] ) ? (int) $settings['show_slide_desc'] : 0; // Individual slide description
	$RISP_lightbox_type             = 0;
	$RISP_Lightbox_Preview          = 0; // Lightbox disabled in Free
	$RISP_FullScreen                = isset( $settings['fullscreen'] ) ? (int) $settings['fullscreen'] : ( isset( $settings['fullscreen_button'] ) ? (int) $settings['fullscreen_button'] : 0 );
	$RISP_Image_Size                = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'full';
	$RISP_mouse_wheel_slideshow     = 0; // Pro feature
	$RISP_Slider_Scale_Mode         = ! empty( $settings['scale_mode'] ) ? $settings['scale_mode'] : 'cover';
	$RISP_Slider_Auto_Scale         = isset( $settings['auto_scale_slider'] ) ? (int) $settings['auto_scale_slider'] : ( isset( $settings['auto_scale'] ) ? (int) $settings['auto_scale'] : 1 );
	$RISP_Slide_In_Title_Color      = ! empty( $settings['slide_in_title_color'] ) ? $settings['slide_in_title_color'] : ( ! empty( $settings['slide_title_color'] ) ? $settings['slide_title_color'] : '#FFFFFF' );
	$RISP_Slide_In_Title_BG_Color   = ! empty( $settings['slide_in_title_bg_color'] ) ? $settings['slide_in_title_bg_color'] : ( ! empty( $settings['slide_title_bg_color'] ) ? $settings['slide_title_bg_color'] : '#000000' );
	$RISP_Slide_In_Title_Size       = ! empty( $settings['slide_in_title_size'] ) ? intval( $settings['slide_in_title_size'] ) : ( ! empty( $settings['title_font_size'] ) ? intval( $settings['title_font_size'] ) : 16 );
	$RISP_Slide_In_Desc_Color       = ! empty( $settings['slide_in_desc_color'] ) ? $settings['slide_in_desc_color'] : ( ! empty( $settings['slide_desc_color'] ) ? $settings['slide_desc_color'] : '#FFFFFF' );
	$RISP_Slide_In_Desc_Size        = ! empty( $settings['slide_in_desc_size'] ) ? intval( $settings['slide_in_desc_size'] ) : ( ! empty( $settings['desc_font_size'] ) ? intval( $settings['desc_font_size'] ) : 13 );
	$RISP_Thumbnail_BG_Color        = ! empty( $settings['thumbnail_bg_color'] ) ? $settings['thumbnail_bg_color'] : '#0080FF';
	$RISP_Thumbnail_Title_Color     = ! empty( $settings['thumbnail_title_color'] ) ? $settings['thumbnail_title_color'] : '#FFFFFF';
	$RISP_Thumbnail_Desc_Color      = ! empty( $settings['thumbnail_desc_color'] ) ? $settings['thumbnail_desc_color'] : '#FFFFFF';
	$RISP_BG_Color_Opacity          = isset( $settings['bg_color_opacity'] ) ? (float) $settings['bg_color_opacity'] : ( isset( $settings['title_bg_opacity'] ) ? ( (float) $settings['title_bg_opacity'] > 1 ? (float) $settings['title_bg_opacity'] / 100 : (float) $settings['title_bg_opacity'] ) : 0.7 );
	$RISP_Read_More_Link_ON         = isset( $settings['read_more_link_on'] ) ? $settings['read_more_link_on'] : 'slide';
	$RISP_Read_More_Link_Text       = ! empty( $settings['read_more_link_text'] ) ? $settings['read_more_link_text'] : __( 'Read More', 'ultimate-responsive-image-slider' );
	$RISP_Read_More_Link_Open       = ( ! empty( $settings['read_more_link_open'] ) && in_array( $settings['read_more_link_open'], array( '_blank', '_new' ), true ) ) ? '_blank' : '_self';
	$RISP_Read_More_Link_Color      = ! empty( $settings['read_more_link_color'] ) ? $settings['read_more_link_color'] : ( ! empty( $settings['cta_button_bg_color'] ) ? $settings['cta_button_bg_color'] : '#ffffff' );

	$RISP_CTA_Button_Style          = ! empty( $settings['cta_button_style'] ) ? $settings['cta_button_style'] : 'filled';
	$RISP_CTA_Button_BG_Color       = ! empty( $settings['cta_button_bg_color'] ) ? $settings['cta_button_bg_color'] : $RISP_Read_More_Link_Color;
	$RISP_CTA_Button_Text_Color     = ! empty( $settings['cta_button_text_color'] ) ? $settings['cta_button_text_color'] : '#000000';
	$RISP_CTA_Button_Hover_Color    = ! empty( $settings['cta_button_hover_color'] ) ? $settings['cta_button_hover_color'] : '#000000';
	$RISP_CTA_Button_Hover_Text_Color = ! empty( $settings['cta_button_hover_text_color'] ) ? $settings['cta_button_hover_text_color'] : '#ffffff';
	$RISP_Naviagtion_Button_Color   = ! empty( $settings['navigation_button_color'] ) ? $settings['navigation_button_color'] : ( ! empty( $settings['navigation_bullet_color'] ) ? $settings['navigation_bullet_color'] : '#0080FF' );
	$RISP_Naviagtion_Button_Size    = ! empty( $settings['navigation_button_size'] ) ? intval( $settings['navigation_button_size'] ) : ( ! empty( $settings['navigation_bullet_size'] ) ? intval( $settings['navigation_bullet_size'] ) : 10 );
	$RISP_Naviagtion_Arrow_Color    = ! empty( $settings['navigation_arrow_color'] ) ? $settings['navigation_arrow_color'] : '#FFFFFF';
	$RISP_Naviagtion_Arrow_BG_Color = ! empty( $settings['navigation_arrow_bg_color'] ) ? $settings['navigation_arrow_bg_color'] : 'rgba(15, 23, 42, 0.45)';
	$RISP_Naviagtion_Arrow_Radius   = ! empty( $settings['navigation_arrow_radius'] ) ? $settings['navigation_arrow_radius'] : 'circle';
	if ( $RISP_Naviagtion_Arrow_Radius === 'curved' ) {
		$RISP_Arrow_Border_Radius = '8px';
	} elseif ( $RISP_Naviagtion_Arrow_Radius === 'sharp' ) {
		$RISP_Arrow_Border_Radius = '0px';
	} else {
		$RISP_Arrow_Border_Radius = '50%';
	}
	$RISP_Arrow_Style               = ! empty( $settings['arrow_style'] ) ? $settings['arrow_style'] : 'chevron';
	$RISP_Font_Style                = ! empty( $settings['font_style'] ) ? $settings['font_style'] : ( ! empty( $settings['font_family'] ) ? $settings['font_family'] : 'Arial, Helvetica, sans-serif' );
	$RISP_Touch_Swipe_Threshold     = ! empty( $settings['touch_swipe_threshold'] ) ? absint( $settings['touch_swipe_threshold'] ) : 50;
	$RISP_L1_Thumbnail_Style_Color  = ! empty( $settings['thumbnail_style_color'] ) ? $settings['thumbnail_style_color'] : '#000000';
	$RISP_L3_Thumbnail_Style_Color  = ! empty( $settings['thumbnail_style_color'] ) ? $settings['thumbnail_style_color'] : '#000000';

	// Layout 1 settings
	$RISP_L1_Auto_Slideshow         = isset( $settings['auto_slideshow'] ) ? (int) $settings['auto_slideshow'] : 1;
	$RISP_L1_Slider_Transition_Speed = ! empty( $settings['transition_speed'] ) ? $settings['transition_speed'] : 5000;
	$RISP_L1_Slide_Distance         = isset( $settings['slide_distance'] ) ? $settings['slide_distance'] : 5;
	$RISP_L1_Slider_Transition_Effect = isset( $settings['transition_effect'] ) ? (int) $settings['transition_effect'] : 1;
	$RISP_L1_Transition             = $RISP_L1_Slider_Transition_Effect;
	$RISP_L1_Width                  = ! empty( $settings['width_mode'] ) ? $settings['width_mode'] : 'custom';
	$RISP_L1_Height                 = ! empty( $settings['height_mode'] ) ? $settings['height_mode'] : 'custom';
	$RISP_L1_Slider_Width           = ! empty( $settings['slider_width'] ) ? $settings['slider_width'] : 1280;
	$RISP_L1_Slider_Height          = ! empty( $settings['slider_height'] ) ? $settings['slider_height'] : 720;
	$RISP_L1_Detailed_Navigation    = isset( $settings['detailed_navigation'] ) ? (int) $settings['detailed_navigation'] : 1;
	$RISP_L1_Slider_Navigation      = $RISP_L1_Detailed_Navigation;
	$RISP_L1_Sliding_Arrow          = isset( $settings['sliding_arrow'] ) ? (int) $settings['sliding_arrow'] : 1;
	$RISP_L1_Navigation_Button      = isset( $settings['navigation_button'] ) ? (int) $settings['navigation_button'] : 0;
	$RISP_L1_Navigation_Button_Style = isset( $settings['navigation_button_style'] ) ? (int) $settings['navigation_button_style'] : 1;
	$RISP_L1_Slide_Order            = ! empty( $settings['slide_order'] ) ? $settings['slide_order'] : 'ASC';
	$RISP_L1_Text_Alignment         = ! empty( $settings['text_alignment'] ) ? $settings['text_alignment'] : 'bottomCenter';
	$RISP_L1_Title_Transition       = ! empty( $settings['title_transition'] ) ? $settings['title_transition'] : 'up';

	// Layout 3 settings
	$RISP_L3_Slider_Width           = $RISP_L1_Slider_Width;
	$RISP_L3_Slider_Height          = $RISP_L1_Slider_Height;
	$RISP_L3_Width                  = $RISP_L1_Width;
	$RISP_L3_Height                 = $RISP_L1_Height;
	$RISP_L3_Auto_Slideshow         = $RISP_L1_Auto_Slideshow;
	$RISP_L3_Slider_Transition_Speed = $RISP_L1_Slider_Transition_Speed;
	$RISP_L3_Slide_Distance         = $RISP_L1_Slide_Distance;
	$RISP_L3_Sliding_Arrow          = $RISP_L1_Sliding_Arrow;
	$RISP_L3_Slider_Navigation      = $RISP_L1_Slider_Navigation;
	$RISP_L3_Navigation_Button      = $RISP_L1_Navigation_Button;
	$RISP_L3_Navigation_Button_Style = $RISP_L1_Navigation_Button_Style;
	$RISP_L3_Detailed_Navigation    = $RISP_L1_Detailed_Navigation;
	$RISP_L3_Text_Alignment         = $RISP_L1_Text_Alignment;
	$RISP_L3_Title_Transition       = $RISP_L1_Title_Transition;
	$RISP_L3_Slider_Transition_Effect = $RISP_L1_Slider_Transition_Effect;
	$RISP_L3_Transition             = $RISP_L1_Slider_Transition_Effect;
	$RISP_L3_Navigation_Position    = ! empty( $settings['navigation_position'] ) ? $settings['navigation_position'] : 'bottom';
	$RISP_L3_Thumbnail_Style        = ! empty( $settings['thumbnail_style'] ) ? $settings['thumbnail_style'] : 'border';
	$RISP_L3_Thumbnail_Width        = ! empty( $settings['thumbnail_width'] ) ? $settings['thumbnail_width'] : 120;
	$RISP_L3_Thumbnail_Height       = ! empty( $settings['thumbnail_height'] ) ? $settings['thumbnail_height'] : 100;
	$RISP_L3_Slide_Order            = $RISP_L1_Slide_Order;

	// Render Layout 3 (Thumbnail Slider)
	require( URIS_PLUGIN_DIR_PATH . 'layouts/layout-3.php' );

	return ob_get_clean();
}