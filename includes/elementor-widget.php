<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register the shared frontend handles early so they're available in Elementor preview.
 * The same handles are used by the shortcode, so every file loads only once.
 */
add_action( 'wp_enqueue_scripts', 'urisp_elementor_register_assets', 5 );
function urisp_elementor_register_assets() {
	if ( function_exists( 'uris_register_frontend_assets' ) ) {
		uris_register_frontend_assets();
	}
}

/**
 * Force-enqueue slider assets on Elementor preview pages.
 */
add_action( 'elementor/preview/enqueue_styles', 'urisp_elementor_enqueue_preview_styles' );
function urisp_elementor_enqueue_preview_styles() {
	wp_enqueue_style( 'wpfrank-uris-slider-pro-min-css' );
	wp_enqueue_style( 'wpfrank-uris-frontend-css' );
	wp_enqueue_style( 'wpfrank-uris-skins-css' );
}

add_action( 'elementor/preview/enqueue_scripts', 'urisp_elementor_enqueue_preview_scripts' );
function urisp_elementor_enqueue_preview_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wpfrank-uris-jquery-sliderPro-js' );
}

/**
 * Register Elementor Widget for Ultimate Responsive Image Slider
 */
add_action( 'elementor/widgets/register', 'urisp_register_elementor_widget' );
function urisp_register_elementor_widget( $widgets_manager ) {
	if ( class_exists( '\Elementor\Widget_Base' ) ) {

		class URISP_Elementor_Slider_Widget extends \Elementor\Widget_Base {

			public function get_name() {
				return 'urisp_slider';
			}

			public function get_title() {
				return esc_html__( 'Ultimate Responsive Image Slider', 'ultimate-responsive-image-slider' );
			}

			public function get_icon() {
				return 'eicon-slides';
			}

			public function get_categories() {
				return array( 'general' );
			}

			public function get_keywords() {
				return array( 'slider', 'image slider', 'gallery', 'responsive', 'slideshow', 'carousel' );
			}

			public function get_style_depends() {
				return array( 'wpfrank-uris-slider-pro-min-css', 'wpfrank-uris-frontend-css', 'wpfrank-uris-skins-css', 'wpfrank-uris-modern-css' );
			}

			public function get_script_depends() {
				return array( 'jquery', 'wpfrank-uris-jquery-sliderPro-js' );
			}

			protected function register_controls() {
				$this->start_controls_section(
					'content_section',
					array(
						'label' => esc_html__( 'Slider Settings', 'ultimate-responsive-image-slider' ),
						'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
					)
				);

				// Fetch Sliders List
				$all_sliders = get_posts( array(
					'post_type'      => 'ris_gallery',
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'orderby'        => 'title',
					'order'          => 'ASC',
				) );

				$options = array( '' => esc_html__( '-- Select Slider --', 'ultimate-responsive-image-slider' ) );
				if ( ! empty( $all_sliders ) ) {
					foreach ( $all_sliders as $s ) {
						$options[ $s->ID ] = $s->post_title ? $s->post_title . ' (ID: ' . $s->ID . ')' : esc_html__( '(no title)', 'ultimate-responsive-image-slider' ) . ' (ID: ' . $s->ID . ')';
					}
				}

				$this->add_control(
					'slider_id',
					array(
						'label'   => esc_html__( 'Choose Slider', 'ultimate-responsive-image-slider' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'options' => $options,
						'default' => '',
					)
				);

				$this->end_controls_section();
			}

			protected function render() {
				$settings = $this->get_settings_for_display();

				if ( empty( $settings['slider_id'] ) ) {
					echo '<div style="padding: 30px 20px; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; text-align: center; color: #64748b; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif;">';
					echo '<span class="dashicons dashicons-images-alt2" style="font-size: 32px; width: 32px; height: 32px; color: #6366f1; margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto;"></span>';
					echo '<strong style="color: #0f172a; font-size: 15px; display: block; margin-bottom: 4px;">' . esc_html__( 'Ultimate Responsive Image Slider', 'ultimate-responsive-image-slider' ) . '</strong>';
					echo '<p style="margin: 0; font-size: 13px;">' . esc_html__( 'Please select a slider from the widget settings panel on the left.', 'ultimate-responsive-image-slider' ) . '</p>';
					echo '</div>';
					return;
				}

				$slider_id = (int) $settings['slider_id'];

				// Detect Elementor editor/preview context
				$is_elementor_editor = false;
				if ( class_exists( '\Elementor\Plugin' ) ) {
					if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
						$is_elementor_editor = true;
					}
				}

				if ( $is_elementor_editor ) {
					wp_enqueue_style( 'wpfrank-uris-slider-pro-min-css' );
					wp_enqueue_style( 'wpfrank-uris-frontend-css' );
					wp_enqueue_style( 'wpfrank-uris-modern-css' );
					wp_enqueue_style( 'wpfrank-uris-skins-css' );
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'wpfrank-uris-jquery-sliderPro-js' );
				}

				// Render the slider shortcode
				echo do_shortcode( '[URISP id=' . absint( $slider_id ) . ']' );
			}
		}

		if ( ! class_exists( 'Elementor_URISP_Slider_Widget' ) ) {
			class_alias( 'URISP_Elementor_Slider_Widget', 'Elementor_URISP_Slider_Widget' );
		}

		$widgets_manager->register( new URISP_Elementor_Slider_Widget() );
	}
}
