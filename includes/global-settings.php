<?php
/**
 * Ultimate Responsive Image Slider - Dedicated Global Settings & Import/Export Page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class URISP_Global_Settings {

	public static function init() {
		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );

		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ), 20 );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_save_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_export' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_import' ) );
	}

	public static function add_admin_menu() {
		$page = add_submenu_page(
			'edit.php?post_type=ris_gallery',
			__( 'Global Settings', 'ultimate-responsive-image-slider' ),
			__( 'Global Settings', 'ultimate-responsive-image-slider' ),
			'manage_urisp_settings',
			'urisp_global_settings',
			array( __CLASS__, 'render_page' )
		);

		add_action( 'admin_print_styles-' . $page, array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function register_settings() {
		register_setting( 'urisp_global_settings_group', 'urisp_global_settings', array( __CLASS__, 'sanitize_settings' ) );
	}

	public static function handle_save_settings() {
		if ( ! isset( $_POST['urisp_save_global_settings_action'] ) ) {
			return;
		}

		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
		if ( ! urisp_current_user_can( 'manage_urisp_settings' ) ) {
			wp_die( esc_html__( 'Unauthorized user', 'ultimate-responsive-image-slider' ) );
		}

		check_admin_referer( 'urisp_global_settings_save', 'urisp_global_nonce' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via self::sanitize_settings() below.
		$raw_input = ( isset( $_POST['urisp_global_settings'] ) && is_array( $_POST['urisp_global_settings'] ) ) ? (array) wp_unslash( $_POST['urisp_global_settings'] ) : array();

		$sanitized = self::sanitize_settings( $raw_input );
		update_option( 'urisp_global_settings', $sanitized );


		wp_safe_redirect( add_query_arg( array(
			'post_type'        => 'ris_gallery',
			'page'             => 'urisp_global_settings',
			'urisp_tab'        => 'settings',
			'settings-updated' => 'true',
		), admin_url( 'edit.php' ) ) );
		exit;
	}

	/**
	 * Handles Selective Slider JSON Export
	 */
	public static function handle_export() {
		if ( ! isset( $_POST['urisp_export_action'] ) ) {
			return;
		}

		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
		require_once( RISP_PLUGIN_DIR_PATH . 'includes/migration.php' );

		if ( ! urisp_current_user_can( 'manage_urisp_settings' ) ) {
			wp_die( esc_html__( 'Unauthorized user', 'ultimate-responsive-image-slider' ) );
		}

		check_admin_referer( 'urisp_export_sliders_action', 'urisp_export_nonce' );

		$export_type  = isset( $_POST['export_type'] ) ? sanitize_key( wp_unslash( $_POST['export_type'] ) ) : 'selected';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw_selected = isset( $_POST['urisp_export_slider_ids'] ) && is_array( $_POST['urisp_export_slider_ids'] ) ? (array) wp_unslash( $_POST['urisp_export_slider_ids'] ) : array();
		$selected_ids = array_filter( array_map( 'absint', $raw_selected ) );

		$query_args = array(
			'post_type'      => 'ris_gallery',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		);

		if ( $export_type === 'selected' ) {
			if ( empty( $selected_ids ) ) {
				wp_safe_redirect( add_query_arg( array(
					'post_type'   => 'ris_gallery',
					'page'        => 'urisp_global_settings',
					'urisp_tab'   => 'import-export',
					'urisp_error' => 'none_selected',
				), admin_url( 'edit.php' ) ) );
				exit;
			}
			$query_args['post__in'] = $selected_ids;
		}

		$sliders = get_posts( $query_args );
		if ( empty( $sliders ) ) {
			wp_safe_redirect( add_query_arg( array(
				'post_type'   => 'ris_gallery',
				'page'        => 'urisp_global_settings',
				'urisp_tab'   => 'import-export',
				'urisp_error' => 'no_sliders',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		$export_data = array(
			'generator'   => 'Ultimate Responsive Image Slider',
			'version'     => defined( 'RISP_PLUGIN_VER' ) ? RISP_PLUGIN_VER : '4.0.0',
			'export_date' => gmdate( 'Y-m-d H:i:s' ),
			'count'       => count( $sliders ),
			'sliders'     => array(),
		);

		foreach ( $sliders as $slider ) {
			$s_id = $slider->ID;
			
			$unified_settings = uris_get_slider_settings( $s_id );
			$legacy_settings  = get_post_meta( $s_id, 'WRIS_Gallery_Settings_' . $s_id, true );
			if ( is_string( $legacy_settings ) ) {
				$legacy_settings = maybe_unserialize( $legacy_settings );
			}
			if ( ! is_array( $legacy_settings ) ) {
				$legacy_settings = array();
			}

			$slides = get_post_meta( $s_id, 'ris_all_photos_details', true );
			if ( is_string( $slides ) ) {
				$slides = maybe_unserialize( $slides );
			}
			if ( ! is_array( $slides ) ) {
				$slides = array();
			}

			$slides_data = array();
			foreach ( $slides as $slide ) {
				$img_id     = ! empty( $slide['rpgp_image_id'] ) ? absint( $slide['rpgp_image_id'] ) : 0;
				$attachment = $img_id ? get_post( $img_id ) : null;
				$img_url    = $img_id ? wp_get_attachment_url( $img_id ) : '';

				$slides_data[] = array(
					'image_id'           => $img_id,
					'image_url'          => $img_url ? $img_url : '',
					'title'              => ! empty( $slide['slide_title'] ) ? $slide['slide_title'] : ( $attachment ? $attachment->post_title : '' ),
					'caption'            => $attachment ? $attachment->post_excerpt : '',
					'description'        => ! empty( $slide['slide_desc'] ) ? $slide['slide_desc'] : ( $attachment ? $attachment->post_content : '' ),
					'alt'                => ! empty( $slide['slide_alt'] ) ? $slide['slide_alt'] : ( $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : '' ),
					'rpgp_external_link' => ! empty( $slide['rpgp_external_link'] ) ? $slide['rpgp_external_link'] : ( ! empty( $slide['rpgp_custom_url'] ) ? $slide['rpgp_custom_url'] : '' ),
					'slide_type'         => ! empty( $slide['slide_type'] ) ? $slide['slide_type'] : 'image',
					'video_url'          => ! empty( $slide['video_url'] ) ? $slide['video_url'] : '',
					'cta_text'           => ! empty( $slide['cta_text'] ) ? $slide['cta_text'] : '',
					'cta_url'            => ! empty( $slide['cta_url'] ) ? $slide['cta_url'] : '',
					'cta_style'          => ! empty( $slide['cta_style'] ) ? $slide['cta_style'] : 'default',
					'cta_target'         => ! empty( $slide['cta_target'] ) ? $slide['cta_target'] : '_blank',
				);
			}

			$export_data['sliders'][] = array(
				'title'            => $slider->post_title,
				'settings'         => $legacy_settings,
				'unified_settings' => $unified_settings,
				'slides'           => $slides_data,
			);
		}

		while ( ob_get_level() ) {
			ob_end_clean();
		}

		$filename = 'uris-sliders-export-' . gmdate( 'Y-m-d-His' ) . '.json';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		exit;
	}

	/**
	 * Handles Slider JSON Import
	 */
	public static function handle_import() {
		if ( ! isset( $_POST['urisp_import_action'] ) ) {
			return;
		}

		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
		require_once( RISP_PLUGIN_DIR_PATH . 'includes/migration.php' );

		if ( ! urisp_current_user_can( 'manage_urisp_settings' ) ) {
			wp_die( esc_html__( 'Unauthorized user', 'ultimate-responsive-image-slider' ) );
		}

		check_admin_referer( 'urisp_import_sliders_action', 'urisp_import_nonce' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated via is_uploaded_file() below.
		$uploaded_tmp = isset( $_FILES['urisp_import_file']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['urisp_import_file']['tmp_name'] ) ) : '';
		if ( empty( $uploaded_tmp ) || ! is_uploaded_file( $uploaded_tmp ) ) {
			wp_safe_redirect( add_query_arg( array(
				'post_type'   => 'ris_gallery',
				'page'        => 'urisp_global_settings',
				'urisp_tab'   => 'import-export',
				'urisp_error' => 'no_file',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		$file_name = isset( $_FILES['urisp_import_file']['name'] ) ? sanitize_file_name( wp_unslash( $_FILES['urisp_import_file']['name'] ) ) : '';
		$file_ext  = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
		if ( $file_ext !== 'json' ) {
			wp_safe_redirect( add_query_arg( array(
				'post_type'   => 'ris_gallery',
				'page'        => 'urisp_global_settings',
				'urisp_tab'   => 'import-export',
				'urisp_error' => 'invalid_extension',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw_json    = file_get_contents( $uploaded_tmp );
		$import_data = json_decode( $raw_json, true );

		if ( ! is_array( $import_data ) || empty( $import_data['sliders'] ) || ! is_array( $import_data['sliders'] ) ) {
			wp_safe_redirect( add_query_arg( array(
				'post_type'   => 'ris_gallery',
				'page'        => 'urisp_global_settings',
				'urisp_tab'   => 'import-export',
				'urisp_error' => 'invalid_format',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		$imported_count = 0;

		foreach ( $import_data['sliders'] as $s_item ) {
			$title = ! empty( $s_item['title'] ) ? sanitize_text_field( $s_item['title'] ) : __( 'Imported Slider', 'ultimate-responsive-image-slider' );
			$new_id = wp_insert_post( array(
				'post_title'  => $title,
				'post_type'   => 'ris_gallery',
				'post_status' => 'publish',
			) );

			if ( is_wp_error( $new_id ) || ! $new_id ) {
				continue;
			}

			// Settings resolution:
			$unified = array();
			if ( ! empty( $s_item['unified_settings'] ) && is_array( $s_item['unified_settings'] ) ) {
				$unified = wp_parse_args( uris_clean_loaded_settings( $s_item['unified_settings'] ), uris_get_default_settings() );
			} elseif ( ! empty( $s_item['settings'] ) && is_array( $s_item['settings'] ) ) {
				$unified = uris_convert_legacy_to_unified( $s_item['settings'] );
			} else {
				$unified = uris_get_default_settings();
			}

			$unified = uris_sync_editor_alias_keys( $unified );

			// Save 4.x canonical format:
			update_post_meta( $new_id, 'urisp_settings_' . $new_id, $unified );
			update_post_meta( $new_id, '_urisp_settings', $unified );

			// Map and save legacy WRIS_Gallery_Settings_ for backward compatibility:
			$legacy_map = array_flip( urisp_get_legacy_key_map() );
			$risp_legacy = array();
			foreach ( $legacy_map as $u_key => $l_key ) {
				if ( isset( $unified[ $u_key ] ) ) {
					$risp_legacy[ $l_key ] = $unified[ $u_key ];
				}
			}
			for ( $i = 1; $i <= 5; $i++ ) {
				$risp_legacy[ 'RISP_L' . $i . '_Auto_Slideshow' ]             = isset( $unified['auto_slideshow'] ) ? $unified['auto_slideshow'] : 1;
				$risp_legacy[ 'RISP_L' . $i . '_Slide_Order' ]                = isset( $unified['slide_order'] ) ? $unified['slide_order'] : 'ASC';
				$risp_legacy[ 'RISP_L' . $i . '_Slide_Distance' ]             = isset( $unified['slide_distance'] ) ? $unified['slide_distance'] : 5;
				$risp_legacy[ 'RISP_L' . $i . '_Sliding_Arrow' ]              = isset( $unified['sliding_arrow'] ) ? $unified['sliding_arrow'] : 1;
				$risp_legacy[ 'RISP_L' . $i . '_Navigation_Button' ]          = isset( $unified['navigation_button'] ) ? $unified['navigation_button'] : 1;
				$risp_legacy[ 'RISP_L' . $i . '_Slider_Transition_Speed' ]    = isset( $unified['transition_speed'] ) ? $unified['transition_speed'] : 5000;
				$risp_legacy[ 'RISP_L' . $i . '_Slider_Width' ]               = isset( $unified['slider_width'] ) ? $unified['slider_width'] : 1000;
				$risp_legacy[ 'RISP_L' . $i . '_Width' ]                      = ( isset( $unified['width_mode'] ) && $unified['width_mode'] === 'fullWidth' ) ? 'fullWidth' : 'custom';
				$risp_legacy[ 'RISP_L' . $i . '_Slider_Height' ]              = isset( $unified['slider_height'] ) ? $unified['slider_height'] : 500;
				$risp_legacy[ 'RISP_L' . $i . '_Height' ]                     = isset( $unified['height_mode'] ) ? $unified['height_mode'] : 'custom';
				$risp_legacy[ 'RISP_L' . $i . '_Slider_Navigation' ]          = isset( $unified['detailed_navigation'] ) ? $unified['detailed_navigation'] : 1;
				$risp_legacy[ 'RISP_L' . $i . '_Text_Alignment' ]             = isset( $unified['text_alignment'] ) ? $unified['text_alignment'] : 'bottomCenter';
				$risp_legacy[ 'RISP_L' . $i . '_Title_Transition' ]           = isset( $unified['title_transition'] ) ? $unified['title_transition'] : 'up';
				$risp_legacy[ 'RISP_L' . $i . '_Description_Transition' ]     = isset( $unified['description_transition'] ) ? $unified['description_transition'] : 'down';
				$risp_legacy[ 'RISP_L' . $i . '_Thumbnail_Style_Color' ]      = isset( $unified['thumbnail_style_color'] ) ? $unified['thumbnail_style_color'] : '#000000';
				$risp_legacy[ 'RISP_L' . $i . '_Slider_Transition_Effect' ]   = isset( $unified['transition_effect'] ) ? $unified['transition_effect'] : 1;
			}
			$risp_legacy['RISP_L3_Navigation_Position'] = isset( $unified['navigation_position'] ) ? $unified['navigation_position'] : 'bottom';
			$risp_legacy['RISP_L3_Thumbnail_Style']     = isset( $unified['thumbnail_style'] ) ? $unified['thumbnail_style'] : 'border';
			$risp_legacy['RISP_L3_Thumbnail_Width']     = isset( $unified['thumbnail_width'] ) ? $unified['thumbnail_width'] : 120;
			$risp_legacy['RISP_L3_Thumbnail_Height']    = isset( $unified['thumbnail_height'] ) ? $unified['thumbnail_height'] : 100;
			$risp_legacy['WRIS_L3_Navigation_Pointer_Color'] = isset( $unified['thumbnail_style_color'] ) ? $unified['thumbnail_style_color'] : '#000000';

			update_post_meta( $new_id, 'WRIS_Gallery_Settings_' . $new_id, $risp_legacy );

			// Slides importation
			$new_slides = array();
			if ( ! empty( $s_item['slides'] ) && is_array( $s_item['slides'] ) ) {
				foreach ( $s_item['slides'] as $sl ) {
					$img_url     = ! empty( $sl['image_url'] ) ? esc_url_raw( $sl['image_url'] ) : '';
					$sl_title    = ! empty( $sl['title'] ) ? sanitize_text_field( $sl['title'] ) : '';
					$sl_desc     = ! empty( $sl['description'] ) ? wp_kses_post( $sl['description'] ) : '';
					$sl_alt      = ! empty( $sl['alt'] ) ? sanitize_text_field( $sl['alt'] ) : '';
					$sl_ext_link = ! empty( $sl['rpgp_external_link'] ) ? esc_url_raw( $sl['rpgp_external_link'] ) : ( ! empty( $sl['custom_url'] ) ? esc_url_raw( $sl['custom_url'] ) : '' );
					$sl_type     = ! empty( $sl['slide_type'] ) ? sanitize_key( $sl['slide_type'] ) : 'image';
					$sl_video    = ! empty( $sl['video_url'] ) ? esc_url_raw( $sl['video_url'] ) : '';
					$cta_text    = ! empty( $sl['cta_text'] ) ? sanitize_text_field( $sl['cta_text'] ) : '';
					$cta_url     = ! empty( $sl['cta_url'] ) ? esc_url_raw( $sl['cta_url'] ) : '';
					$cta_style   = ! empty( $sl['cta_style'] ) ? sanitize_key( $sl['cta_style'] ) : 'default';
					$cta_target  = ! empty( $sl['cta_target'] ) ? sanitize_key( $sl['cta_target'] ) : '_blank';
					$attached_id = 0;

					// 1. Try matching existing attachment by URL
					if ( $img_url ) {
						$attached_id = attachment_url_to_postid( $img_url );

						// 2. If not matched, try sideloading image into Media Library
						if ( ! $attached_id && filter_var( $img_url, FILTER_VALIDATE_URL ) ) {
							require_once ABSPATH . 'wp-admin/includes/media.php';
							require_once ABSPATH . 'wp-admin/includes/file.php';
							require_once ABSPATH . 'wp-admin/includes/image.php';
							$sideload = media_sideload_image( $img_url, $new_id, $sl_title, 'id' );
							if ( ! is_wp_error( $sideload ) ) {
								$attached_id = absint( $sideload );
							}
						}
					}

					// 3. Fallback: match by local existing attachment ID
					if ( ! $attached_id && ! empty( $sl['image_id'] ) ) {
						$existing_post = get_post( absint( $sl['image_id'] ) );
						if ( $existing_post && $existing_post->post_type === 'attachment' ) {
							$attached_id = absint( $sl['image_id'] );
						}
					}

					if ( $attached_id ) {
						if ( $sl_title || $sl_desc ) {
							wp_update_post( array(
								'ID'           => $attached_id,
								'post_title'   => $sl_title,
								'post_content' => $sl_desc,
							) );
						}
						if ( $sl_alt ) {
							update_post_meta( $attached_id, '_wp_attachment_image_alt', $sl_alt );
						}

						$new_slides[] = array(
							'rpgp_image_id'      => $attached_id,
							'rpgp_external_link' => $sl_ext_link,
							'slide_title'        => $sl_title,
							'slide_desc'         => $sl_desc,
							'slide_alt'          => $sl_alt,
							'slide_type'         => $sl_type,
							'video_url'          => $sl_video,
							'orig_image_id'      => $attached_id,
							'orig_image_url'     => $img_url,
							'video_thumb_url'    => '',
							'cta_text'           => $cta_text,
							'cta_url'            => $cta_url,
							'cta_style'          => $cta_style,
							'cta_target'         => $cta_target,
						);
					}
				}
			}

			update_post_meta( $new_id, 'ris_all_photos_details', $new_slides );
			update_post_meta( $new_id, 'ris_total_images_count', count( $new_slides ) );
			$imported_count++;
		}

		wp_safe_redirect( add_query_arg( array(
			'post_type'    => 'ris_gallery',
			'page'         => 'urisp_global_settings',
			'urisp_tab'    => 'import-export',
			'urisp_status' => 'import_success',
			'count'        => $imported_count,
		), admin_url( 'edit.php' ) ) );
		exit;
	}

	public static function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$output = array();
		$output['lazy_load']        = isset( $input['lazy_load'] ) && $input['lazy_load'] == '1' ? 1 : 0;
		$output['skeleton_loading'] = 0; // PRO feature
		$output['show_loader']      = isset( $input['show_loader'] ) && $input['show_loader'] == '1' ? 1 : 0;
		$output['loader_style']     = 'spinner'; // PRO feature for custom animations
		$output['loader_color']     = isset( $input['loader_color'] ) ? sanitize_hex_color( $input['loader_color'] ) : '#6366f1';
		

		return $output;
	}

	public static function enqueue_assets() {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		$ver = defined( 'RISP_PLUGIN_VER' ) ? RISP_PLUGIN_VER : '4.0.0';
		$css_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/css/urisp-modern.css' : '';
		$modern_ver = ( $css_file && file_exists( $css_file ) ) ? filemtime( $css_file ) : $ver;
		wp_enqueue_style( 'wpfrank-urisp-modern-css', RISP_PLUGIN_URL . 'assets/css/urisp-modern.css', array(), $modern_ver );
		wp_enqueue_style( 'wpfrank-urisp-admin-css', RISP_PLUGIN_URL . 'assets/css/urisp-admin.css', array(), $ver );

			}

	/**
	 * Handles Selective Slider JSON Export
	 */
	public static function render_page() {
		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
		if ( ! urisp_current_user_can( 'manage_urisp_settings' ) ) {
			wp_die( esc_html__( 'Unauthorized user', 'ultimate-responsive-image-slider' ) );
		}

		require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
		$settings = urisp_get_global_settings();

		// Get all existing sliders for selective export list
		$sliders = get_posts( array(
			'post_type'      => 'ris_gallery',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe read-only UI tab query parameter.
		$active_tab = isset( $_GET['urisp_tab'] ) && in_array( sanitize_key( wp_unslash( $_GET['urisp_tab'] ) ), array( 'import-export', 'custom-fonts' ), true ) ? sanitize_key( wp_unslash( $_GET['urisp_tab'] ) ) : 'settings';
		?>
		<div class="urisp-settings-wrapper urisp-global-fullwidth">
			<!-- Settings Page Loader -->
			<div class="urisp-settings-loader">
				<div class="urisp-settings-loader-spinner"></div>
			</div>

			<div class="urisp-settings-main-content" style="display: none;">
			<style>
				/* Global Save Button Hover & Loading States */
				.urisp-global-save-btn {
					background: #4f46e5 !important;
					border-color: #4338ca !important;
					color: #ffffff !important;
					font-weight: 600 !important;
					height: 46px !important;
					padding: 0 32px !important;
					border-radius: 8px !important;
					font-size: 14px !important;
					display: inline-flex !important;
					align-items: center !important;
					justify-content: center !important;
					gap: 8px !important;
					cursor: pointer !important;
					box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35) !important;
					transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
					text-decoration: none !important;
				}
				.urisp-global-save-btn:hover {
					background: #4338ca !important;
					border-color: #3730a3 !important;
					color: #ffffff !important;
					box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45) !important;
					transform: translateY(-2px) !important;
				}
				.urisp-global-save-btn:active {
					background: #3730a3 !important;
					border-color: #312e81 !important;
					box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3) !important;
					transform: translateY(0) !important;
				}
				.urisp-global-save-btn:focus,
				.urisp-global-save-btn:focus-visible {
					outline: 2px solid #6366f1 !important;
					outline-offset: 2px !important;
					color: #ffffff !important;
				}
				.urisp-global-save-btn.is-loading {
					background: #4338ca !important;
					border-color: #3730a3 !important;
					color: #ffffff !important;
					opacity: 0.9 !important;
					cursor: wait !important;
					pointer-events: none !important;
					transform: none !important;
					box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3) !important;
				}
				.urisp-global-save-btn .urisp-btn-spinner {
					display: none !important;
					width: 16px;
					height: 16px;
					border: 2px solid rgba(255, 255, 255, 0.35);
					border-top-color: #ffffff;
					border-radius: 50%;
					animation: urispBtnSpin 0.75s linear infinite;
					vertical-align: middle;
					flex-shrink: 0;
				}
				.urisp-global-save-btn.is-loading .urisp-btn-icon {
					display: none !important;
				}
				.urisp-global-save-btn.is-loading .urisp-btn-spinner {
					display: inline-block !important;
				}
				@keyframes urispBtnSpin {
					0% { transform: rotate(0deg); }
					100% { transform: rotate(360deg); }
				}
				.urisp-header-btn {
					transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
				}
				.urisp-header-btn:hover {
					background: #4f46e5 !important;
					border-color: #4338ca !important;
					transform: translateY(-1px) !important;
					box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3) !important;
				}
				.urisp-global-tab-btn:hover {
					color: #4f46e5 !important;
				}
			</style>
			<!-- Header -->
			<div class="urisp-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
				<div>
					<h1 style="margin: 0 0 6px 0; font-size: 24px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px;">
						<span class="dashicons dashicons-admin-settings" style="font-size: 28px; width:28px; height:28px; color: #6366f1;"></span>
						<?php esc_html_e( 'Global Settings & Data Tools', 'ultimate-responsive-image-slider' ); ?>
						<span style="font-size: 12px; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 6px; letter-spacing: 0.3px;">v<?php echo esc_html( RISP_PLUGIN_VER ); ?></span>
					</h1>
					<p style="margin: 0; color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'Configure site-wide loading behaviors, spinner designs, custom CSS, accessible dyslexia fonts, and manage selective slider backups.', 'ultimate-responsive-image-slider' ); ?>
					</p>
				</div>
				<div style="display: flex; gap: 10px;">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ris_gallery' ) ); ?>" class="button button-primary" style="background: #6366f1; border-color: #4f46e5; border-radius: 6px; font-weight: 600; padding: 0 16px; height: 36px; display: inline-flex; align-items: center; justify-content: center; gap: 7px;"><span class="dashicons dashicons-plus-alt2" style="font-size: 17px; width: 17px; height: 17px; line-height: 1; margin: 0; display: inline-flex; align-items: center; justify-content: center; position: relative; top: -0.5px;"></span><span><?php esc_html_e( 'Add New Slider', 'ultimate-responsive-image-slider' ); ?></span></a>
				</div>
			</div>

			<!-- Top Nav Tabs -->
			<div class="urisp-global-nav-tabs" style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 28px; padding-bottom: 0;">
				<button type="button" class="urisp-global-tab-btn <?php echo $active_tab === 'settings' ? 'active' : ''; ?>" data-target="urisp-tab-global-settings" style="padding: 12px 20px; font-size: 14px; font-weight: 600; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; color: #64748b; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
					<span class="dashicons dashicons-admin-generic" style="font-size: 18px; width: 18px; height: 18px;"></span>
					<?php esc_html_e( 'Global Settings', 'ultimate-responsive-image-slider' ); ?>
				</button>
				<button type="button" class="urisp-global-tab-btn <?php echo $active_tab === 'custom-fonts' ? 'active' : ''; ?>" data-target="urisp-tab-global-fonts" style="padding: 12px 20px; font-size: 14px; font-weight: 600; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; color: #64748b; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
					<span class="dashicons dashicons-editor-textcolor" style="font-size: 18px; width: 18px; height: 18px;"></span>
					<?php esc_html_e( 'Custom & Dyslexia Fonts', 'ultimate-responsive-image-slider' ); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span>
				</button>
				<button type="button" class="urisp-global-tab-btn <?php echo $active_tab === 'import-export' ? 'active' : ''; ?>" data-target="urisp-tab-global-import-export" style="padding: 12px 20px; font-size: 14px; font-weight: 600; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; color: #64748b; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
					<span class="dashicons dashicons-database-import" style="font-size: 18px; width: 18px; height: 18px;"></span>
					<?php esc_html_e( 'Import / Export', 'ultimate-responsive-image-slider' ); ?>
				</button>
			</div>

			<!-- Alerts / Notices -->
			<?php
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Safe read-only UI alert display based on query parameters.
			if ( isset( $_GET['urisp_status'] ) && 'import_success' === sanitize_key( wp_unslash( $_GET['urisp_status'] ) ) ) :
				$imported_cnt = isset( $_GET['count'] ) ? absint( $_GET['count'] ) : 0;
			?>
				<div class="notice notice-success is-dismissible" style="margin: 0 0 24px 0; border-left-color: #10b981; border-radius: 8px; padding: 12px 16px;">
					<p style="font-weight: 600; color: #065f46; margin: 0; display: flex; align-items: center; gap: 8px;">
						<span class="dashicons dashicons-yes-alt" style="color: #10b981;"></span>
						<?php
						/* translators: %d: count of imported sliders */
						printf( esc_html__( 'Import Successful! Successfully imported %d slider(s) into your library.', 'ultimate-responsive-image-slider' ), esc_html( $imported_cnt ) );
						?>
					</p>
				</div>
			<?php endif; ?>

			<?php
			if ( isset( $_GET['urisp_error'] ) ) :
				$err_code = sanitize_key( wp_unslash( $_GET['urisp_error'] ) );
				$err_msg  = __( 'An error occurred. Please try again.', 'ultimate-responsive-image-slider' );
				if ( 'none_selected' === $err_code ) {
					$err_msg = __( 'Please select at least one slider to export.', 'ultimate-responsive-image-slider' );
				} elseif ( 'no_sliders' === $err_code ) {
					$err_msg = __( 'No sliders found in the database to export.', 'ultimate-responsive-image-slider' );
				} elseif ( 'no_file' === $err_code ) {
					$err_msg = __( 'Please select a valid JSON backup file to import.', 'ultimate-responsive-image-slider' );
				} elseif ( 'invalid_extension' === $err_code ) {
					$err_msg = __( 'Invalid file extension. Please upload a .json file.', 'ultimate-responsive-image-slider' );
				} elseif ( 'invalid_format' === $err_code ) {
					$err_msg = __( 'Invalid file format. The uploaded file is not a valid slider backup.', 'ultimate-responsive-image-slider' );
				}
			?>
				<div class="notice notice-error is-dismissible" style="margin: 0 0 24px 0; border-left-color: #ef4444; border-radius: 8px; padding: 12px 16px;">
					<p style="font-weight: 600; color: #991b1b; margin: 0; display: flex; align-items: center; gap: 8px;">
						<span class="dashicons dashicons-warning" style="color: #ef4444;"></span>
						<?php echo esc_html( $err_msg ); ?>
					</p>
				</div>
			<?php 
			endif; 
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			?>

			<!-- Alerts / Notices -->
			<?php
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe status flag from redirect after save.
			if ( isset( $_GET['settings-updated'] ) && 'true' === sanitize_key( wp_unslash( $_GET['settings-updated'] ) ) ) : ?>
				<div class="notice notice-success is-dismissible" style="margin: 0 0 24px 0; border-left-color: #10b981; border-radius: 8px; padding: 12px 16px;">
					<p style="font-weight: 600; color: #065f46; margin: 0; display: flex; align-items: center; gap: 8px;">
						<span class="dashicons dashicons-yes-alt" style="color: #10b981;"></span>
						<?php esc_html_e( 'Global Settings saved successfully.', 'ultimate-responsive-image-slider' ); ?>
					</p>
				</div>
			<?php endif; ?>

			<!-- ============================================== -->
			<!-- TAB 1: GLOBAL SETTINGS PANEL                   -->
			<!-- ============================================== -->
			<div id="urisp-tab-global-settings" class="urisp-global-tab-panel" style="<?php echo $active_tab === 'settings' ? 'display: block;' : 'display: none;'; ?>">
				<form method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery&page=urisp_global_settings' ) ); ?>">
					<?php wp_nonce_field( 'urisp_global_settings_save', 'urisp_global_nonce' ); ?>
					<input type="hidden" name="urisp_save_global_settings_action" value="1">

					<!-- ACTIVE FREE PERFORMANCE & LOADING CARD -->
					<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
						<h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
							<span class="dashicons dashicons-performance" style="color: #6366f1;"></span>
							<?php esc_html_e( 'Performance & Loading Features', 'ultimate-responsive-image-slider' ); ?>
						</h3>

						<!-- Lazy Load -->
						<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
							<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
								<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #0f172a;"><?php esc_html_e( 'Lazy Loading', 'ultimate-responsive-image-slider' ); ?></h5>
								<p style="margin: 0; font-size: 12px; color: #64748b;"><?php esc_html_e( 'Defer image downloads until slides scroll into view to increase initial page speed.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>
							<div class="urisp-setting-field">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
								echo urisp_toggle_html( 'lazy_load', $settings['lazy_load'], 'urisp_global_settings' );
								?>
							</div>
						</div>

						<!-- Show Loader -->
						<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
							<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
								<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #0f172a;"><?php esc_html_e( 'Frontend Loading Indicator', 'ultimate-responsive-image-slider' ); ?></h5>
								<p style="margin: 0; font-size: 12px; color: #64748b;"><?php esc_html_e( 'Show an animated spinner while the slider initializes on the frontend.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>
							<div class="urisp-setting-field">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
								echo urisp_toggle_html( 'show_loader', $settings['show_loader'], 'urisp_global_settings' );
								?>
							</div>
						</div>

						<!-- Loader Color -->
						<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
							<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
								<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #0f172a;"><?php esc_html_e( 'Loading Icon Color', 'ultimate-responsive-image-slider' ); ?></h5>
								<p style="margin: 0; font-size: 12px; color: #64748b;"><?php esc_html_e( 'Accent color for the frontend loading animation.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>
							<div class="urisp-setting-field">
								<input type="text" name="urisp_global_settings[loader_color]" value="<?php echo esc_attr( $settings['loader_color'] ); ?>" class="wp-color-picker" data-default-color="#6366f1">
							</div>
						</div>
					</div>

					<!-- Save Button for Active Settings -->
					<div style="margin-top: 24px; margin-bottom: 36px; text-align: left;">
						<button type="submit" id="urisp-global-save-btn" class="button button-primary button-hero urisp-global-save-btn">
							<span class="dashicons dashicons-saved urisp-btn-icon" style="font-size: 18px; width: 18px; height: 18px; line-height: 1; margin: 0; display: inline-flex; align-items: center; justify-content: center; position: relative; top: -0.5px;"></span>
							<span class="urisp-btn-spinner" style="display: none;"></span>
							<span class="urisp-btn-text"><?php esc_html_e( 'Save Global Settings', 'ultimate-responsive-image-slider' ); ?></span>
						</button>
					</div>
				</form>

				<!-- ============================================== -->
				<!-- EXCLUSIVE PRO FEATURES SHOWCASE (SEPARATE CARD AT LAST) -->
				<!-- ============================================== -->
				<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-top: 10px;">
					
					<!-- Pro Header Banner -->
					<div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 12px; padding: 22px 26px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
						<div style="display: flex; gap: 16px; align-items: center; max-width: 680px;">
							<div style="background: #f59e0b; color: #ffffff; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(245,158,11,0.3);">
								<span class="dashicons dashicons-star-filled" style="font-size: 24px; width: 24px; height: 24px;"></span>
							</div>
							<div>
								<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
									<h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #92400e;">
										<?php esc_html_e( 'Ultimate Responsive Image Slider Pro Features', 'ultimate-responsive-image-slider' ); ?>
									</h3>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">PRO ONLY</span>
								</div>
								<p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #b45309;">
									<?php esc_html_e( 'The following advanced animations, analytics tracking, user permissions, and custom styling features are available exclusively in the Pro version.', 'ultimate-responsive-image-slider' ); ?>
								</p>
							</div>
						</div>
						<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #f59e0b; border-color: #d97706; color: #fff; font-weight: 800; font-size: 14px; padding: 9px 24px; height: auto; border-radius: 8px; text-decoration: none; box-shadow: 0 3px 10px rgba(245, 158, 11, 0.3); white-space: nowrap;">
							<?php esc_html_e( 'Upgrade to Pro', 'ultimate-responsive-image-slider' ); ?> &rarr;
						</a>
					</div>

					<div style="display: flex; flex-direction: column; gap: 24px;">

						<!-- 1. Skeleton Shimmer Loading (Pro) -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
							<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 0;">
								<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
									<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-image-filter" style="color: #6366f1;"></span>
										<?php esc_html_e( 'Skeleton Shimmer Loading', 'ultimate-responsive-image-slider' ); ?>
										<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
									</h5>
									<p style="margin: 0; font-size: 12.5px; color: #64748b;">
										<?php esc_html_e( 'Display modern animated shimmer placeholders while slide images are downloading.', 'ultimate-responsive-image-slider' ); ?>
										<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="color: #b45309; font-weight: 600; text-decoration: none; margin-left: 4px;"><?php esc_html_e( 'Unlock in Pro &rarr;', 'ultimate-responsive-image-slider' ); ?></a>
									</p>
								</div>
								<div class="urisp-setting-field" style="opacity: 0.6; pointer-events: none; user-select: none;">
									<label class="urisp-toggle" style="cursor: not-allowed;">
										<input type="checkbox" disabled>
										<span class="urisp-toggle-slider" style="background-color: #cbd5e1;"></span>
									</label>
								</div>
							</div>
						</div>

						<!-- 2. Loading Icon Animation Styles (Pro) -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
							<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 0;">
								<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
									<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-update" style="color: #6366f1;"></span>
										<?php esc_html_e( 'Loading Icon Style Presets', 'ultimate-responsive-image-slider' ); ?>
										<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
									</h5>
									<p style="margin: 0; font-size: 12.5px; color: #64748b;">
										<?php esc_html_e( 'Free version includes standard Pulse Spinner. Alternative animations (Dual Ring, Dots Wave, Bouncing Cubes) are available in Pro.', 'ultimate-responsive-image-slider' ); ?>
										<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="color: #b45309; font-weight: 600; text-decoration: none; margin-left: 4px;"><?php esc_html_e( 'Unlock in Pro &rarr;', 'ultimate-responsive-image-slider' ); ?></a>
									</p>
								</div>
								<div class="urisp-setting-field">
									<select disabled style="padding: 8px 30px 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; color: #64748b; background: #f1f5f9; cursor: not-allowed;">
										<option value="spinner" selected><?php esc_html_e( 'Pulse Spinner (Default)', 'ultimate-responsive-image-slider' ); ?></option>
										<option value="dual-ring" disabled><?php esc_html_e( 'Dual Ring (PRO)', 'ultimate-responsive-image-slider' ); ?></option>
										<option value="dots-wave" disabled><?php esc_html_e( 'Dots Wave (PRO)', 'ultimate-responsive-image-slider' ); ?></option>
										<option value="bouncing-cubes" disabled><?php esc_html_e( 'Bouncing Cubes (PRO)', 'ultimate-responsive-image-slider' ); ?></option>
									</select>
								</div>
							</div>
						</div>

						<!-- 3. Analytics & Event Tracking (Pro) -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
							<div class="urisp-setting-row" style="display: flex; justify-content: space-between; align-items: center; padding: 0;">
								<div class="urisp-setting-label" style="flex: 1; padding-right: 20px;">
									<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-chart-bar" style="color: #6366f1;"></span>
										<?php esc_html_e( 'Analytics & Event Tracking Engine', 'ultimate-responsive-image-slider' ); ?>
										<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
									</h5>
									<p style="margin: 0; font-size: 12.5px; color: #64748b;">
										<?php esc_html_e( 'Track slider impressions, individual slide views, and CTA button clicks with GDPR-compliant IP anonymization.', 'ultimate-responsive-image-slider' ); ?>
										<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="color: #b45309; font-weight: 600; text-decoration: none; margin-left: 4px;"><?php esc_html_e( 'Unlock in Pro &rarr;', 'ultimate-responsive-image-slider' ); ?></a>
									</p>
								</div>
								<div class="urisp-setting-field" style="opacity: 0.6; pointer-events: none; user-select: none;">
									<label class="urisp-toggle" style="cursor: not-allowed;">
										<input type="checkbox" disabled>
										<span class="urisp-toggle-slider" style="background-color: #cbd5e1;"></span>
									</label>
								</div>
							</div>
						</div>

						<!-- 4. Access Control & Role Permissions (Pro) -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
							<div style="margin-bottom: 14px;">
								<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
									<span class="dashicons dashicons-shield" style="color: #6366f1;"></span>
									<?php esc_html_e( 'Access Control & Role Permissions Delegation', 'ultimate-responsive-image-slider' ); ?>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
								</h5>
								<p style="margin: 0; font-size: 12.5px; color: #64748b;">
									<?php esc_html_e( 'Grant dedicated gallery management, analytics viewing, and settings permissions to non-administrator user roles.', 'ultimate-responsive-image-slider' ); ?>
									<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="color: #b45309; font-weight: 600; text-decoration: none; margin-left: 4px;"><?php esc_html_e( 'Unlock in Pro &rarr;', 'ultimate-responsive-image-slider' ); ?></a>
								</p>
							</div>

							<!-- Role Matrix Preview (Dummy) -->
							<div class="urisp-responsive-table-wrap" style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; opacity: 0.75; pointer-events: none; user-select: none;">
								<table class="wp-list-table widefat striped" style="border: none; box-shadow: none; width: 100%; min-width: 540px; margin: 0;">
									<thead>
										<tr style="background: #f1f5f9;">
											<th scope="col" style="font-weight: 700; color: #334155; padding: 10px 14px; font-size: 12.5px;"><?php esc_html_e( 'User Role', 'ultimate-responsive-image-slider' ); ?></th>
											<th scope="col" style="font-weight: 700; color: #334155; padding: 10px 14px; font-size: 12.5px; text-align: center;"><?php esc_html_e( 'Manage Sliders', 'ultimate-responsive-image-slider' ); ?></th>
											<th scope="col" style="font-weight: 700; color: #334155; padding: 10px 14px; font-size: 12.5px; text-align: center;"><?php esc_html_e( 'View Analytics', 'ultimate-responsive-image-slider' ); ?></th>
											<th scope="col" style="font-weight: 700; color: #334155; padding: 10px 14px; font-size: 12.5px; text-align: center;"><?php esc_html_e( 'Global Settings', 'ultimate-responsive-image-slider' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="padding: 10px 14px; font-weight: 600; color: #0f172a; font-size: 12.5px;">Editor</td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" checked disabled style="width: 16px; height: 16px;"></td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" checked disabled style="width: 16px; height: 16px;"></td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" disabled style="width: 16px; height: 16px;"></td>
										</tr>
										<tr>
											<td style="padding: 10px 14px; font-weight: 600; color: #0f172a; font-size: 12.5px;">Author</td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" checked disabled style="width: 16px; height: 16px;"></td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" disabled style="width: 16px; height: 16px;"></td>
											<td style="text-align: center; padding: 10px;"><input type="checkbox" disabled style="width: 16px; height: 16px;"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<!-- 5. Global Custom CSS (Pro) -->
						<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
							<div style="margin-bottom: 14px;">
								<h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
									<span class="dashicons dashicons-editor-code" style="color: #6366f1;"></span>
									<?php esc_html_e( 'Global Custom CSS Overrides', 'ultimate-responsive-image-slider' ); ?>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
								</h5>
								<p style="margin: 0; font-size: 12.5px; color: #64748b;">
									<?php esc_html_e( 'Site-wide custom CSS rules injected across all slider instances.', 'ultimate-responsive-image-slider' ); ?>
									<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="color: #b45309; font-weight: 600; text-decoration: none; margin-left: 4px;"><?php esc_html_e( 'Unlock in Pro &rarr;', 'ultimate-responsive-image-slider' ); ?></a>
								</p>
							</div>

							<textarea readonly disabled placeholder="<?php esc_attr_e( '/* Global Custom CSS rules are available in Pro version. */&#10;.urisp-slider { margin-bottom: 30px; }', 'ultimate-responsive-image-slider' ); ?>" style="width: 100%; min-height: 120px; background: #1e293b; color: #94a3b8; font-family: 'Consolas', monospace; font-size: 12.5px; line-height: 1.6; padding: 14px; border-radius: 8px; border: 1px solid #334155; resize: none; box-sizing: border-box; display: block; cursor: not-allowed; opacity: 0.85;"></textarea>
						</div>

					</div>

					<!-- Card Footer CTA -->
					<div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
						<span style="font-size: 13px; color: #64748b; font-weight: 600;">
							<?php esc_html_e( 'Ready to unlock unlimited sliders, layouts 2-5, Ken Burns effects, watermarking, custom fonts, and all Pro features?', 'ultimate-responsive-image-slider' ); ?>
						</span>
						<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button" style="background: #1e293b; color: #ffffff; border-color: #0f172a; font-weight: 700; height: 38px; padding: 0 20px; border-radius: 6px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
							<span><?php esc_html_e( 'Get Ultimate Responsive Image Slider Pro', 'ultimate-responsive-image-slider' ); ?></span> &rarr;
						</a>
					</div>

				</div>
			</div>

			<!-- ============================================== -->
			<!-- TAB 2: CUSTOM & DYSLEXIA FONTS PANEL          -->
			<!-- ============================================== -->
			<?php
			$uploaded_fonts        = urisp_fonts()->get_custom_fonts();
			$accessibility_presets = urisp_fonts()->get_accessibility_presets();
			?>
			<!-- Preload Google Fonts & Custom Font-Faces for Live Preview -->
			<?php wp_enqueue_style( 'urisp-google-preview-fonts', 'https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&family=Comic+Neue:wght@400;700&family=Lexend:wght@400;600&display=swap', array(), RISP_PLUGIN_VER ); ?>
			<style id="urisp-global-preview-fonts">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-sanitized custom font CSS rules.
				echo wp_strip_all_tags( urisp_fonts()->get_all_custom_fonts_css() ); ?>
				.urisp-font-badge {
					display: inline-block;
					padding: 2px 8px;
					border-radius: 4px;
					font-size: 11px;
					font-weight: 700;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}
				.urisp-badge-accessibility { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
				.urisp-badge-custom { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
				.urisp-badge-format { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
			</style>

			<div id="urisp-tab-global-fonts" class="urisp-global-tab-panel" style="<?php echo $active_tab === 'custom-fonts' ? 'display: block;' : 'display: none;'; ?>">
				
				<div style="display: grid; grid-template-columns: 1fr; gap: 28px;">

					<!-- Intro Banner -->
					<div class="urisp-card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 12px; padding: 24px 28px; box-shadow: 0 2px 10px rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;">
						<div style="display: flex; gap: 18px; align-items: center; max-width: 720px;">
							<div style="background: #f59e0b; color: #ffffff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(245,158,11,0.3);">
								<span class="dashicons dashicons-editor-textcolor" style="font-size: 28px; width: 28px; height: 28px;"></span>
							</div>
							<div>
								<span style="background: rgba(245, 158, 11, 0.2); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.4); padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; display: inline-block;">
									<?php esc_html_e( 'Exclusive Pro Feature', 'ultimate-responsive-image-slider' ); ?>
								</span>
								<h3 style="margin: 0 0 6px 0; font-size: 19px; font-weight: 800; color: #92400e;">
									<?php esc_html_e( 'Custom Brand Font Uploads & Dyslexia Accessibility Library', 'ultimate-responsive-image-slider' ); ?>
								</h3>
								<p style="margin: 0; font-size: 14px; line-height: 1.6; color: #b45309;">
									<?php esc_html_e( 'Upload proprietary .woff2, .woff, and .ttf web font files or leverage pre-configured dyslexia and accessibility typography (OpenDyslexic, Atkinson Hyperlegible, Lexend). Seamlessly apply your brand typography to slide titles, descriptions, and button links.', 'ultimate-responsive-image-slider' ); ?>
								</p>
							</div>
						</div>
						<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #f59e0b; border-color: #d97706; color: #fff; font-weight: 800; font-size: 15px; padding: 8px 24px; height: auto; border-radius: 8px; text-decoration: none; box-shadow: 0 3px 10px rgba(245, 158, 11, 0.3);">
							<?php esc_html_e( 'Upgrade to Pro', 'ultimate-responsive-image-slider' ); ?> &rarr;
						</a>
					</div>

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; opacity: 0.85;">
						
						<!-- DUMMY CARD A: UPLOAD CUSTOM FONT -->
						<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
							<div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 20px;">
								<div style="display: flex; align-items: center; gap: 10px;">
									<span class="dashicons dashicons-upload" style="font-size: 22px; width: 22px; height: 22px; color: #94a3b8;"></span>
									<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;"><?php esc_html_e( 'Upload New Custom Font', 'ultimate-responsive-image-slider' ); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></h3>
								</div>
								<span class="urisp-font-badge urisp-badge-custom"><?php esc_html_e( 'Brand Typography', 'ultimate-responsive-image-slider' ); ?></span>
							</div>

							<!-- Font Family Name -->
							<div style="margin-bottom: 16px;">
								<label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
									<?php esc_html_e( 'Font Family Name', 'ultimate-responsive-image-slider' ); ?>
								</label>
								<input type="text" disabled placeholder="e.g. MyBrand Sans (Pro Only)" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 13.5px; background: #f8fafc; cursor: not-allowed;">
							</div>

							<!-- Font File URL -->
							<div style="margin-bottom: 20px;">
								<label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
									<?php esc_html_e( 'Font File (.woff2, .woff, .ttf, .otf)', 'ultimate-responsive-image-slider' ); ?>
								</label>
								<div style="display: flex; gap: 8px;">
									<input type="text" disabled placeholder="https://yoursite.com/fonts/mybrand.woff2" style="flex: 1; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0 12px; font-size: 13px; background: #f8fafc; cursor: not-allowed;">
									<button type="button" disabled class="button" style="height: 38px; padding: 0 14px; font-weight: 600; cursor: not-allowed; opacity: 0.7;">
										<?php esc_html_e( 'Browse', 'ultimate-responsive-image-slider' ); ?>
									</button>
								</div>
							</div>

							<button type="button" disabled class="button button-primary" style="height: 38px; padding: 0 20px; font-weight: 600; cursor: not-allowed; opacity: 0.7;">
								<span class="dashicons dashicons-yes" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span>
								<?php esc_html_e( 'Save & Register Font (Pro)', 'ultimate-responsive-image-slider' ); ?>
							</button>
						</div>

						<!-- DUMMY CARD B: PRESET ACCESSIBILITY FONTS -->
						<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
							<div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px;">
								<div style="display: flex; align-items: center; gap: 10px;">
									<span class="dashicons dashicons-universal-access-alt" style="font-size: 22px; width: 22px; height: 22px; color: #94a3b8;"></span>
									<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;"><?php esc_html_e( 'Accessibility & Dyslexia Presets', 'ultimate-responsive-image-slider' ); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></h3>
								</div>
								<span class="urisp-font-badge urisp-badge-accessibility"><?php esc_html_e( 'WCAG AAA', 'ultimate-responsive-image-slider' ); ?></span>
							</div>

							<p style="margin: 0 0 16px 0; font-size: 13px; color: #64748b; line-height: 1.5;">
								<?php esc_html_e( 'Included out-of-the-box in Pro with pre-loaded Google / open-source CDN font definitions:', 'ultimate-responsive-image-slider' ); ?>
							</p>

							<div style="display: flex; flex-direction: column; gap: 10px;">
								<div style="padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
									<div>
										<strong style="color: #1e293b; font-size: 13.5px;">OpenDyslexic</strong>
										<span style="font-size: 11.5px; color: #64748b; display: block;"><?php esc_html_e( 'Weighted bottoms prevent letter rotation and confusion for readers with dyslexia.', 'ultimate-responsive-image-slider' ); ?></span>
									</div>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
								</div>
								<div style="padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
									<div>
										<strong style="color: #1e293b; font-size: 13.5px;">Atkinson Hyperlegible</strong>
										<span style="font-size: 11.5px; color: #64748b; display: block;"><?php esc_html_e( 'Developed for Braille Institute to dramatically maximize character distinguishability.', 'ultimate-responsive-image-slider' ); ?></span>
									</div>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
								</div>
								<div style="padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
									<div>
										<strong style="color: #1e293b; font-size: 13.5px;">Lexend</strong>
										<span style="font-size: 11.5px; color: #64748b; display: block;"><?php esc_html_e( 'Scientifically proven to reduce visual stress and increase reading fluency.', 'ultimate-responsive-image-slider' ); ?></span>
									</div>
									<span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>
								</div>
							</div>
						</div>

					</div>

				</div>

			</div>

			<!-- Tab 3: Selective Slider Backup & Export/Import -->
			<div id="urisp-tab-global-import-export" class="urisp-global-tab-panel" style="<?php echo $active_tab === 'import-export' ? 'display: block;' : 'display: none;'; ?>">
				
				<div style="display: grid; grid-template-columns: 1fr; gap: 28px;">
					
					<!-- Intro Banner -->
					<div class="urisp-card" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border: 1px solid #e0e7ff; border-radius: 12px; padding: 24px 28px; box-shadow: 0 2px 10px rgba(99, 102, 241, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;">
						<div style="display: flex; gap: 18px; align-items: center; max-width: 850px;">
							<div style="background: #4f46e5; color: #ffffff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
								<span class="dashicons dashicons-database-import" style="font-size: 28px; width: 28px; height: 28px;"></span>
							</div>
							<div>
								<span style="background: rgba(99, 102, 241, 0.15); color: #4338ca; border: 1px solid rgba(99, 102, 241, 0.3); padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; display: inline-block;">
									<?php esc_html_e( 'Data Migration & Backup', 'ultimate-responsive-image-slider' ); ?>
								</span>
								<h3 style="margin: 0 0 6px 0; font-size: 19px; font-weight: 800; color: #1e1b4b;">
									<?php esc_html_e( 'Slider Backup, Migration & JSON Import/Export', 'ultimate-responsive-image-slider' ); ?>
								</h3>
								<p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">
									<?php esc_html_e( 'Export single, multiple, or all sliders with full image details, captions, URLs, and customized settings into portable JSON backup files. Effortlessly migrate sliders to any WordPress site or restore backups with 1-click.', 'ultimate-responsive-image-slider' ); ?>
								</p>
							</div>
						</div>
					</div>

					<!-- CARDS CONTAINER -->
					<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
						
						<!-- EXPORT CARD -->
						<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
							<form method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery&page=urisp_global_settings' ) ); ?>" id="urisp-export-form">
								<?php wp_nonce_field( 'urisp_export_sliders_action', 'urisp_export_nonce' ); ?>
								<input type="hidden" name="urisp_export_action" value="1">

								<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
									<div style="display: flex; align-items: center; gap: 10px;">
										<span class="dashicons dashicons-download" style="font-size: 22px; width: 22px; height: 22px; color: #4f46e5;"></span>
										<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">
											<?php esc_html_e( 'Export Image Sliders', 'ultimate-responsive-image-slider' ); ?>
										</h3>
									</div>
									<span style="background: #f1f5f9; color: #64748b; font-weight: 600; font-size: 12px; padding: 4px 12px; border-radius: 20px;">
										<?php
										/* translators: %d: Total number of sliders */
										printf( esc_html__( 'Total Sliders: %d', 'ultimate-responsive-image-slider' ), count( $sliders ) );
										?>
									</span>
								</div>

								<p style="margin: 0 0 16px 0; color: #64748b; font-size: 13px;">
									<?php esc_html_e( 'Select the sliders you want to export into a portable JSON backup file.', 'ultimate-responsive-image-slider' ); ?>
								</p>

								<!-- Table Toolbar -->
								<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
									<label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 13px; color: #334155; cursor: pointer;">
										<input type="checkbox" id="urisp-export-select-all" style="cursor: pointer;">
										<?php esc_html_e( 'Select / Deselect All Sliders', 'ultimate-responsive-image-slider' ); ?>
									</label>
									<span id="urisp-export-selected-count" style="font-size: 12px; color: #6366f1; font-weight: 700;">
										0 <?php esc_html_e( 'selected', 'ultimate-responsive-image-slider' ); ?>
									</span>
								</div>

								<!-- Slider Selection Table -->
								<div class="urisp-responsive-table-wrap" style="max-height: 320px; overflow-y: auto; overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; margin-bottom: 20px;">
									<table class="wp-list-table widefat striped" style="border: none; box-shadow: none; width: 100%; min-width: 580px;">
										<thead>
											<tr style="background: #f1f5f9;">
												<th scope="col" style="width: 44px; text-align: center; padding: 12px 8px;"></th>
												<th scope="col" style="width: 90px; font-weight: 700; color: #475569; padding: 12px 10px;"><?php esc_html_e( 'Preview', 'ultimate-responsive-image-slider' ); ?></th>
												<th scope="col" style="font-weight: 700; color: #475569; padding: 12px 12px;"><?php esc_html_e( 'Slider Title', 'ultimate-responsive-image-slider' ); ?></th>
												<th scope="col" style="width: 220px; font-weight: 700; color: #475569; padding: 12px 12px;"><?php esc_html_e( 'Shortcode', 'ultimate-responsive-image-slider' ); ?></th>
												<th scope="col" style="width: 90px; text-align: center; font-weight: 700; color: #475569; padding: 12px 8px;"><?php esc_html_e( 'Slides', 'ultimate-responsive-image-slider' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php if ( ! empty( $sliders ) ) : ?>
												<?php foreach ( $sliders as $slider_post ) : 
													$s_slides = get_post_meta( $slider_post->ID, 'ris_all_photos_details', true );
													$thumb_url = '';
													if ( is_array( $s_slides ) && ! empty( $s_slides ) ) {
														$first_sl = reset( $s_slides );
														if ( ! empty( $first_sl['rpgp_image_id'] ) ) {
															$thumb_url = wp_get_attachment_image_url( absint( $first_sl['rpgp_image_id'] ), array( 60, 50 ) );
														}
													}
												?>
													<tr class="urisp-export-row" style="cursor: pointer;">
														<td style="text-align: center; padding: 12px 8px;">
															<input type="checkbox" name="urisp_export_slider_ids[]" value="<?php echo esc_attr( $slider_post->ID ); ?>" class="urisp-export-checkbox" style="cursor: pointer;">
														</td>
														<td style="padding: 10px 10px;">
															<?php if ( $thumb_url ) : ?>
																<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" style="width: 44px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; display: block;" />
															<?php else : ?>
																<span class="dashicons dashicons-format-gallery" style="color: #94a3b8; font-size: 24px;"></span>
															<?php endif; ?>
														</td>
														<td style="padding: 12px 12px; font-weight: 600; color: #334155;">
															<a href="<?php echo esc_url( get_edit_post_link( $slider_post->ID ) ); ?>" target="_blank" style="color: #1e293b; text-decoration: none; font-weight: 600;">
																<?php echo esc_html( $slider_post->post_title ? $slider_post->post_title : __( '(no title)', 'ultimate-responsive-image-slider' ) ); ?>
															</a>
														</td>
														<td style="padding: 12px 12px;">
															<code>[URISP id=<?php echo esc_attr( $slider_post->ID ); ?>]</code>
														</td>
														<td style="text-align: center; padding: 12px 8px; color: #64748b; font-weight: 600;">
															<?php echo is_array( $s_slides ) ? count( $s_slides ) : 0; ?>
														</td>
													</tr>
												<?php endforeach; ?>
											<?php else : ?>
												<tr>
													<td colspan="5" style="text-align: center; padding: 24px; color: #94a3b8;">
														<?php esc_html_e( 'No sliders available to export.', 'ultimate-responsive-image-slider' ); ?>
													</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>

								<!-- Export Buttons -->
								<div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
									<button type="submit" name="export_type" value="selected" class="button button-primary" id="urisp-btn-export-selected" style="height: 40px; padding: 0 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
										<span class="dashicons dashicons-download" style="font-size: 17px; width: 17px; height: 17px; line-height: 1; margin: 0;"></span>
										<span><?php esc_html_e( 'Export Selected Sliders (.JSON)', 'ultimate-responsive-image-slider' ); ?></span>
									</button>
									<button type="submit" name="export_type" value="all" class="button" id="urisp-btn-export-all" style="height: 40px; padding: 0 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border-color: #cbd5e1; color: #334155;">
										<span class="dashicons dashicons-download" style="font-size: 17px; width: 17px; height: 17px; line-height: 1; margin: 0;"></span>
										<span><?php esc_html_e( 'Export All Sliders (.JSON)', 'ultimate-responsive-image-slider' ); ?></span>
									</button>
								</div>
							</form>
						</div>

						<!-- IMPORT CARD -->
						<div class="urisp-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
							<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery&page=urisp_global_settings' ) ); ?>" id="urisp-import-form">
								<?php wp_nonce_field( 'urisp_import_sliders_action', 'urisp_import_nonce' ); ?>
								<input type="hidden" name="urisp_import_action" value="1">

								<div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px;">
									<div style="display: flex; align-items: center; gap: 10px;">
										<span class="dashicons dashicons-upload" style="font-size: 22px; width: 22px; height: 22px; color: #4f46e5;"></span>
										<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">
											<?php esc_html_e( 'Import Image Sliders', 'ultimate-responsive-image-slider' ); ?>
										</h3>
									</div>
									<span class="urisp-font-badge urisp-badge-custom"><?php esc_html_e( 'JSON Migration', 'ultimate-responsive-image-slider' ); ?></span>
								</div>

								<p style="margin: 0 0 16px 0; color: #64748b; font-size: 13px;">
									<?php esc_html_e( 'Upload a previously exported JSON backup file to restore sliders and configurations.', 'ultimate-responsive-image-slider' ); ?>
								</p>

								<!-- File Upload Dropzone -->
								<div class="urisp-import-dropzone" id="urisp-import-dropzone" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 32px 20px; text-align: center; background: #f8fafc; margin-bottom: 20px; cursor: pointer; transition: all 0.2s ease;">
									<span class="dashicons dashicons-media-document" style="font-size: 42px; width: 42px; height: 42px; color: #6366f1; margin-bottom: 10px;"></span>
									<h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: #0f172a;"><?php esc_html_e( 'Choose a JSON file or drag it here', 'ultimate-responsive-image-slider' ); ?></h4>
									<p style="margin: 0 0 16px 0; font-size: 12px; color: #94a3b8;"><?php esc_html_e( 'Supports .json files generated by Ultimate Responsive Image Slider (v3.5.x or v4.x)', 'ultimate-responsive-image-slider' ); ?></p>
									
									<input type="file" id="urisp_import_file" name="urisp_import_file" accept=".json" style="display: none;" required>

									<button type="button" id="urisp-btn-browse-file" class="button" style="height: 36px; padding: 0 20px; font-weight: 600; color: #4f46e5; border-color: #6366f1; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 7px;">
										<span class="dashicons dashicons-cloud-upload" style="font-size: 17px; width: 17px; height: 17px; line-height: 1; margin: 0;"></span>
										<span><?php esc_html_e( 'Browse File', 'ultimate-responsive-image-slider' ); ?></span>
									</button>

									<div id="urisp-file-selected-name" style="display: none; margin-top: 14px; font-size: 13px; font-weight: 600; color: #065f46; background: #ecfdf5; padding: 8px 16px; border-radius: 6px; border: 1px solid #a7f3d0; max-width: 400px; margin-left: auto; margin-right: auto;"></div>
								</div>

								<!-- Import Submit Button -->
								<div>
									<button type="submit" class="button button-primary" id="urisp-btn-import-submit" style="height: 40px; padding: 0 24px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
										<span class="dashicons dashicons-upload" style="font-size: 17px; width: 17px; height: 17px; line-height: 1; margin: 0;"></span>
										<span><?php esc_html_e( 'Import Sliders Now', 'ultimate-responsive-image-slider' ); ?></span>
									</button>
								</div>
							</form>
						</div>

					</div>

				</div>
			</div>

				</div>
			</div>
			</div><!-- /.urisp-settings-main-content -->
		</div><!-- /.urisp-settings-wrapper -->

		<script>
		jQuery(document).ready(function($) {
			// Settings Page Loader Transition
			function revealSettingsPage() {
				$('.urisp-settings-wrapper .urisp-settings-loader').fadeOut(250, function () {
					$(this).remove();
					$('.urisp-settings-main-content').fadeIn(250, function() {
						if (typeof globalCssEditor !== 'undefined' && globalCssEditor) {
							globalCssEditor.refresh();
						}
					});
				});
			}
			if (document.readyState === 'complete') {
				revealSettingsPage();
			} else {
				$(window).on('load', revealSettingsPage);
				setTimeout(revealSettingsPage, 1000);
			}

			// Initialize Color Picker
			if ($.fn.wpColorPicker) {
				$('.wp-color-picker').wpColorPicker();
			}

			// Global Settings Form Submit Loading State
			$('#urisp-tab-global-settings form').on('submit', function() {
				var $btn = $('#urisp-global-save-btn');
				if ($btn.length) {
					$btn.addClass('is-loading');
					$btn.find('.urisp-btn-icon').hide();
					$btn.find('.urisp-btn-spinner').show();
					$btn.find('.urisp-btn-text').text('<?php esc_attr_e( "Saving Settings...", "ultimate-responsive-image-slider" ); ?>');
				}
			});

			// Tab Switching Logic
			$('.urisp-global-tab-btn').on('click', function(e) {
				e.preventDefault();
				var targetId = $(this).data('target');

				$('.urisp-global-tab-btn').removeClass('active').css({
					'color': '#64748b',
					'border-bottom-color': 'transparent'
				});
				$(this).addClass('active').css({
					'color': '#6366f1',
					'border-bottom-color': '#6366f1'
				});

				$('.urisp-global-tab-panel').hide();
				$('#' + targetId).fadeIn(150);

				// Update URL param without full reload
				var tabName = 'settings';
				if (targetId === 'urisp-tab-global-import-export') {
					tabName = 'import-export';
				} else if (targetId === 'urisp-tab-global-fonts') {
					tabName = 'custom-fonts';
				}
				if (history.pushState) {
					var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?post_type=ris_gallery&page=urisp_global_settings&urisp_tab=' + tabName;
					window.history.pushState({path:newurl},'',newurl);
				}
			});

			// Set active tab styling on load
			$('.urisp-global-tab-btn.active').css({
				'color': '#6366f1',
				'border-bottom-color': '#6366f1'
			});

			// Select / Deselect All Export Checkboxes
			$('#urisp-export-select-all').on('change', function() {
				var isChecked = $(this).is(':checked');
				$('.urisp-export-checkbox').prop('checked', isChecked).trigger('change');
			});

			// Row Click toggle checkbox
			$('.urisp-export-row').on('click', function(e) {
				if (!$(e.target).is('input[type="checkbox"]') && !$(e.target).is('a')) {
					var $cb = $(this).find('.urisp-export-checkbox');
					$cb.prop('checked', !$cb.is(':checked')).trigger('change');
				}
			});

			// Counter update
			$('.urisp-export-checkbox').on('change', function() {
				var total = $('.urisp-export-checkbox').length;
				var checked = $('.urisp-export-checkbox:checked').length;
				$('#urisp-export-selected-count').text(checked + ' <?php esc_html_e( "selected", 'ultimate-responsive-image-slider' ); ?>');
				$('#urisp-export-select-all').prop('checked', total > 0 && checked === total);
			});

			// File Upload Indicator & Browse Click
			$('#urisp-btn-browse-file').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$('#urisp_import_file').trigger('click');
			});

			$('#urisp-import-dropzone').on('click', function(e) {
				if (!$(e.target).is('#urisp-btn-browse-file') && !$(e.target).closest('#urisp-btn-browse-file').length) {
					$('#urisp_import_file').trigger('click');
				}
			});

			// Drag & drop support
			$('#urisp-import-dropzone').on('dragover dragenter', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).css({ 'background': '#eef2ff', 'border-color': '#6366f1' });
			}).on('dragleave dragend drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).css({ 'background': '#f8fafc', 'border-color': '#cbd5e1' });
				if (e.type === 'drop' && e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files.length) {
					var file = e.originalEvent.dataTransfer.files[0];
					if (file.name.toLowerCase().endsWith('.json')) {
						var dataTransfer = new DataTransfer();
						dataTransfer.items.add(file);
						document.getElementById('urisp_import_file').files = dataTransfer.files;
						$('#urisp_import_file').trigger('change');
					} else {
						if (window.urispModal && window.urispModal.toast) {
							window.urispModal.toast('<?php esc_html_e( "Please select a valid .json file.", "ultimate-responsive-image-slider" ); ?>', 'error');
						} else {
							alert('<?php esc_html_e( "Please select a valid .json file.", "ultimate-responsive-image-slider" ); ?>');
						}
					}
				}
			});

			$('#urisp_import_file').on('change', function() {
				var fileName = $(this).val().split('\\').pop();
				if (fileName) {
					$('#urisp-file-selected-name').html('<span class="dashicons dashicons-yes" style="vertical-align: middle;"></span> ' + fileName).css('display', 'inline-flex');
				} else {
					$('#urisp-file-selected-name').hide();
				}
			});

			// Export Validation
			$('#urisp-btn-export-selected').on('click', function(e) {
				var checked = $('.urisp-export-checkbox:checked').length;
				if (checked === 0) {
					e.preventDefault();
					if (window.urispModal && window.urispModal.toast) {
						window.urispModal.toast('<?php esc_html_e( "Please select at least one slider to export.", "ultimate-responsive-image-slider" ); ?>', 'warning');
					} else {
						alert('<?php esc_html_e( "Please select at least one slider to export.", "ultimate-responsive-image-slider" ); ?>');
					}
				}
			});

			// Import Validation
			$('#urisp-import-form').on('submit', function(e) {
				var fileInput = document.getElementById('urisp_import_file');
				if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
					e.preventDefault();
					if (window.urispModal && window.urispModal.toast) {
						window.urispModal.toast('<?php esc_html_e( "Please choose a JSON file to import.", "ultimate-responsive-image-slider" ); ?>', 'warning');
					} else {
						alert('<?php esc_html_e( "Please choose a JSON file to import.", "ultimate-responsive-image-slider" ); ?>');
					}
				}
			});

						// ==========================================
			// CUSTOM FONTS MANAGEMENT (TAB 2)
			// ==========================================
			var urispFontNonce = '<?php echo esc_attr( wp_create_nonce( "urisp_ajax_nonce" ) ); ?>';
			var fontMediaFrame = null;

			// WP Media Library Uploader for Font Files
			$('#urisp-btn-choose-font').on('click', function(e) {
				e.preventDefault();
				if (fontMediaFrame) {
					fontMediaFrame.open();
					return;
				}

				fontMediaFrame = wp.media({
					title: '<?php esc_attr_e( "Select or Upload Font File (.woff2, .woff, .ttf, .otf)", "ultimate-responsive-image-slider" ); ?>',
					button: { text: '<?php esc_attr_e( "Use Selected Font", "ultimate-responsive-image-slider" ); ?>' },
					multiple: false
				});

				fontMediaFrame.on('select', function() {
					var attachment = fontMediaFrame.state().get('selection').first().toJSON();
					$('#urisp-font-url').val(attachment.url);
					$('#urisp-font-attachment-id').val(attachment.id);

					if (!$('#urisp-font-name').val().trim()) {
						var rawName = attachment.title || attachment.filename.replace(/\.[^/.]+$/, "");
						rawName = rawName.replace(/[-_]/g, ' ');
						rawName = rawName.replace(/\b\w/g, function(l){ return l.toUpperCase(); });
						$('#urisp-font-name').val(rawName);
					}
				});

				fontMediaFrame.open();
			});

			// Save Custom Font via AJAX
			$('#urisp-btn-save-font').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var $notice = $('#urisp-font-upload-notice');
				var $spinner = $('#urisp-font-save-spinner');

				var fontName = $('#urisp-font-name').val().trim();
				var fontUrl  = $('#urisp-font-url').val().trim();
				var attachId = $('#urisp-font-attachment-id').val() || 0;

				$notice.hide().removeClass('notice-error notice-success').css({'background': '', 'color': '', 'border': ''});

				if (!fontName) {
					$notice.html('<strong style="color: #b91c1c;"><?php esc_attr_e( "Error:", "ultimate-responsive-image-slider" ); ?></strong> <?php esc_attr_e( "Please specify a Font Family Name.", "ultimate-responsive-image-slider" ); ?>')
						.css({'background': '#fef2f2', 'border': '1px solid #fecaca', 'color': '#991b1b'}).fadeIn(150);
					$('#urisp-font-name').focus();
					return;
				}

				if (!fontUrl) {
					$notice.html('<strong style="color: #b91c1c;"><?php esc_attr_e( "Error:", "ultimate-responsive-image-slider" ); ?></strong> <?php esc_attr_e( "Please provide or upload a font file URL.", "ultimate-responsive-image-slider" ); ?>')
						.css({'background': '#fef2f2', 'border': '1px solid #fecaca', 'color': '#991b1b'}).fadeIn(150);
					$('#urisp-font-url').focus();
					return;
				}

				$btn.prop('disabled', true);
				$spinner.addClass('is-active');

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'urisp_save_custom_font',
						nonce: urispFontNonce,
						font_name: fontName,
						font_url: fontUrl,
						attachment_id: attachId
					},
					success: function(res) {
						$spinner.removeClass('is-active');
						$btn.prop('disabled', false);

						if (res.success) {
							$notice.html('<strong style="color: #15803d;"><?php esc_attr_e( "Success:", "ultimate-responsive-image-slider" ); ?></strong> ' + (res.data.message || '<?php esc_attr_e( "Font saved successfully!", "ultimate-responsive-image-slider" ); ?>'))
								.css({'background': '#f0fdf4', 'border': '1px solid #bbf7d0', 'color': '#166534'}).fadeIn(150);

							// Clear form fields
							$('#urisp-font-name').val('');
							$('#urisp-font-url').val('');
							$('#urisp-font-attachment-id').val('0');

							// Reload tab smoothly to refresh table and @font-face rules
							setTimeout(function() {
								window.location.href = window.location.protocol + "//" + window.location.host + window.location.pathname + '?post_type=ris_gallery&page=urisp_global_settings&urisp_tab=custom-fonts';
							}, 800);
						} else {
							var errMsg = (res.data && res.data.message) ? res.data.message : '<?php esc_attr_e( "Failed to save font.", "ultimate-responsive-image-slider" ); ?>';
							$notice.html('<strong style="color: #b91c1c;"><?php esc_attr_e( "Error:", "ultimate-responsive-image-slider" ); ?></strong> ' + errMsg)
								.css({'background': '#fef2f2', 'border': '1px solid #fecaca', 'color': '#991b1b'}).fadeIn(150);
						}
					},
					error: function() {
						$spinner.removeClass('is-active');
						$btn.prop('disabled', false);
						$notice.html('<strong style="color: #b91c1c;"><?php esc_attr_e( "Error:", "ultimate-responsive-image-slider" ); ?></strong> <?php esc_attr_e( "A server error occurred while saving the font.", "ultimate-responsive-image-slider" ); ?>')
							.css({'background': '#fef2f2', 'border': '1px solid #fecaca', 'color': '#991b1b'}).fadeIn(150);
					}
				});
			});

			// Delete Custom Font via AJAX
			$(document).on('click', '.urisp-delete-font-btn', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var fontId = $btn.data('font-id');
				var fontName = $btn.data('font-name') || fontId;

				var confirmMsg = '<?php esc_attr_e( "Are you sure you want to remove font: ", "ultimate-responsive-image-slider" ); ?>' + '"' + fontName + '"?';
				var doDeleteFont = function() {
					$btn.prop('disabled', true).css('opacity', 0.5);

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'urisp_delete_custom_font',
							nonce: urispFontNonce,
							font_id: fontId
						},
						success: function(res) {
							if (res.success) {
								var $row = $('#urisp-font-row-' + fontId);
								$row.fadeOut(250, function() {
									$row.remove();
									var remaining = $('#urisp-custom-fonts-tbody tr').length;
									$('#urisp-custom-fonts-count').text(remaining);

									if (remaining === 0) {
										$('#urisp-custom-fonts-table-container').html(
											'<div id="urisp-fonts-empty-state" style="text-align: center; padding: 36px 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">' +
											'<span class="dashicons dashicons-editor-textcolor" style="font-size: 40px; width: 40px; height: 40px; color: #94a3b8; margin-bottom: 8px;"></span>' +
											'<h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: #334155;"><?php esc_attr_e( "No custom fonts uploaded yet", "ultimate-responsive-image-slider" ); ?></h4>' +
											'<p style="margin: 0; font-size: 13px; color: #94a3b8;"><?php esc_attr_e( "Use the uploader above to add your first custom font file.", "ultimate-responsive-image-slider" ); ?></p>' +
											'</div>'
										);
									}
								});
							} else {
								$btn.prop('disabled', false).css('opacity', 1);
								var errMsg = (res.data && res.data.message) ? res.data.message : '<?php esc_attr_e( "Failed to delete font.", "ultimate-responsive-image-slider" ); ?>';
								if (window.urispModal && window.urispModal.alert) {
									urispModal.alert({
										title: '<?php esc_attr_e( "Error", "ultimate-responsive-image-slider" ); ?>',
										message: errMsg,
										type: 'danger'
									});
								} else {
									alert(errMsg);
								}
							}
						},
						error: function() {
							$btn.prop('disabled', false).css('opacity', 1);
							var errServerMsg = '<?php esc_attr_e( "Server error while deleting font.", "ultimate-responsive-image-slider" ); ?>';
							if (window.urispModal && window.urispModal.alert) {
								urispModal.alert({
									title: '<?php esc_attr_e( "Server Error", "ultimate-responsive-image-slider" ); ?>',
									message: errServerMsg,
									type: 'danger'
								});
							} else {
								alert(errServerMsg);
							}
						}
					});
				};

				if (window.urispModal && window.urispModal.confirm) {
					urispModal.confirm({
						title: '<?php esc_attr_e( "Remove Font", "ultimate-responsive-image-slider" ); ?>',
						message: confirmMsg,
						confirmText: '<?php esc_attr_e( "Remove", "ultimate-responsive-image-slider" ); ?>',
						cancelText: '<?php esc_attr_e( "Cancel", "ultimate-responsive-image-slider" ); ?>',
						type: 'danger'
					}).then(function(confirmed) {
						if (confirmed) {
							doDeleteFont();
						}
					});
				} else if (confirm(confirmMsg)) {
					doDeleteFont();
				}
			});
		});
		</script>
		<?php
	}
}

URISP_Global_Settings::init();
