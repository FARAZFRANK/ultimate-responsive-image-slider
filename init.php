<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
require_once( RISP_PLUGIN_DIR_PATH . 'includes/global-settings.php' );
require_once( RISP_PLUGIN_DIR_PATH . 'includes/docs.php' );
URISP_Docs::init();
// Role sync removed in free

class URIS_Free {
	private static $instance;
	private $admin_thumbnail_size = 150;
	private $thumbnail_size_w = 150;
	private $thumbnail_size_h = 150;
	var $counter;

	public static function forge() {
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->counter = 0;
		add_image_size('rpg_gallery_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true);
		add_image_size('rpg_gallery_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true);
		add_shortcode('rpggallery', array(&$this, 'shortcode'));
		add_filter('widget_text','do_shortcode'); // text widget support
		// plugin translation (loaded at init action with priority 0 for WordPress 6.7+ compatibility)
		add_action( 'init', array( &$this, 'URIS_Translate' ), 0 );

		// The slider post type has to exist in every context (frontend, REST,
		// cron, WP-CLI), not only inside wp-admin.
		add_action( 'init', array( &$this, 'UltimateResponsiveImageSliderFree' ), 1 );

		if (is_admin()) {
			// CPT meta box generate
			add_action('add_meta_boxes', array(&$this, 'risp_add_all_meta_boxes'));
			add_action('admin_init', array(&$this, 'risp_add_all_meta_boxes'), 1);
			add_filter('get_user_option_meta-box-order_ris_gallery', array(&$this, 'urisp_meta_box_order'));
			add_action('admin_footer', array(&$this, 'urisp_render_preview_elements'));
			// CPT meta box save
			add_action('save_post', array(&$this, 'risp_add_image_meta_box_save'), 9, 1);
			add_action('save_post', array(&$this, 'risp_settings_meta_save'), 9, 1);
			add_action('wp_ajax_uris_get_thumbnail', array(&$this, 'risp_ajax_get_thumbnail'));
			
			// only for admin dashboard clone slider ajax JS
			add_action( 'admin_enqueue_scripts', array(&$this, 'uris_scripts'));
			
				add_action('wp_ajax_urisp_render_preview', array(&$this, 'urisp_render_preview'));
			add_action('wp_ajax_urisp_ajax_save_settings', array(&$this, 'urisp_ajax_save_settings'));
			add_action('wp_ajax_urisp_ajax_upload_file', array(&$this, 'urisp_ajax_upload_file'));
			add_action('wp_ajax_urisp_fetch_video_thumbnail', array(&$this, 'urisp_ajax_fetch_video_thumbnail'));
		}
	}
	
		public function uris_render_upgrade_page() {
		require_once( URIS_PLUGIN_DIR_PATH . 'admin/upgrade-to-pro.php' );
	}

	/**
	 * Scripts and styles should not be registered or enqueued until the wp_enqueue_scripts, admin_enqueue_scripts, or login_enqueue_scripts hooks.
	 */
	public function uris_scripts() {
		global $post;
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen ) {
			$is_urisp_screen = (
				( isset( $screen->post_type ) && 'ris_gallery' === $screen->post_type ) ||
				( isset( $screen->id ) && ( false !== strpos( $screen->id, 'ris_gallery' ) || false !== strpos( $screen->id, 'urisp' ) ) )
			);
			if ( ! $is_urisp_screen ) {
				return;
			}
		}

		$post_id = ( isset( $post ) && isset( $post->ID ) ) ? $post->ID : 0;
		$ver = defined( 'RISP_PLUGIN_VER' ) ? RISP_PLUGIN_VER : '4.0.0';
		$css_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/css/urisp-modern.css' : '';
		$modern_ver = ( $css_file && file_exists( $css_file ) ) ? filemtime( $css_file ) : $ver;
		wp_enqueue_style( 'wpfrank-urisp-admin-css', RISP_PLUGIN_URL . 'assets/css/urisp-admin.css', array(), $ver );
		wp_enqueue_style( 'wpfrank-urisp-modern-css', RISP_PLUGIN_URL . 'assets/css/urisp-modern.css', array(), $modern_ver );

		// Inline critical styles for post list table alignment
		$table_inline_css = "
			.post-type-ris_gallery .wp-list-table {
				table-layout: fixed !important;
				width: 100% !important;
			}
			.post-type-ris_gallery .wp-list-table th,
			.post-type-ris_gallery .wp-list-table td {
				vertical-align: middle !important;
			}
			.post-type-ris_gallery .wp-list-table th.check-column,
			.post-type-ris_gallery .wp-list-table td.check-column {
				width: 44px !important;
				max-width: 44px !important;
				padding: 10px 0 10px 10px !important;
				box-sizing: border-box !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-preview,
			.post-type-ris_gallery .wp-list-table td.column-preview {
				width: 80px !important;
				max-width: 80px !important;
				text-align: center !important;
				padding: 8px 6px !important;
				box-sizing: border-box !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-title,
			.post-type-ris_gallery .wp-list-table td.column-title {
				width: 26% !important;
				min-width: 180px !important;
				padding: 10px 16px !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-shortcode,
			.post-type-ris_gallery .wp-list-table td.column-shortcode {
				width: 24% !important;
				min-width: 200px !important;
				padding: 10px 16px !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-layout,
			.post-type-ris_gallery .wp-list-table td.column-layout {
				width: 18% !important;
				min-width: 150px !important;
				padding: 10px 16px !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-slides,
			.post-type-ris_gallery .wp-list-table td.column-slides {
				width: 14% !important;
				min-width: 100px !important;
				padding: 10px 16px !important;
			}
			.post-type-ris_gallery .wp-list-table th.column-date,
			.post-type-ris_gallery .wp-list-table td.column-date {
				width: 18% !important;
				min-width: 130px !important;
				padding: 10px 16px !important;
			}
		";
		wp_add_inline_style( 'wpfrank-urisp-modern-css', $table_inline_css );

		// Core WordPress Color Picker CSS & JS
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'wpfrank-urisp-color-picker-script', RISP_PLUGIN_URL . 'assets/js/wl-color-picker.js', array( 'jquery', 'wp-color-picker' ), $ver, true );

		$modal_js_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/js/urisp-modal.js' : '';
		$modal_ver = ( $modal_js_file && file_exists( $modal_js_file ) ) ? filemtime( $modal_js_file ) : $ver;
		wp_enqueue_script( 'wpfrank-urisp-modal-js', RISP_PLUGIN_URL . 'assets/js/urisp-modal.js', array(), $modal_ver, true );

		wp_enqueue_script( 'wpfrank-urisp-ajax-script', RISP_PLUGIN_URL . 'assets/js/uris-ajax-script.js', array( 'jquery', 'wpfrank-urisp-modal-js' ), $ver, true );
		wp_localize_script( 'wpfrank-urisp-ajax-script', 'uris_ajax_object', array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'urisp_ajax_nonce' ),
			'preview_nonce' => wp_create_nonce( 'urisp_preview_nonce' ),
			'post_id'       => $post_id,
			'i18n'          => array(
				'config_error'  => __( 'Configuration error: Slider AJAX parameters are not loaded.', 'ultimate-responsive-image-slider' ),
				'preview_error' => __( 'Failed to update live preview. Please update the post manually.', 'ultimate-responsive-image-slider' ),
			),
		) );
	}
	

	/**
	 * Translate Plugin
	 */
	public function URIS_Translate() {
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound, WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain( 'ultimate-responsive-image-slider', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	// Register Custom Post Type
	public function UltimateResponsiveImageSliderFree() {
		$labels = array(
			'name' => __( 'Ultimate Responsive Image Slider', 'ultimate-responsive-image-slider' ),
			'singular_name' => __( 'Ultimate Responsive Image Slider', 'ultimate-responsive-image-slider' ),
			'add_new' => __( 'Add New Image Slider', 'ultimate-responsive-image-slider' ),
			'add_new_item' => __( 'Add New Image Slider', 'ultimate-responsive-image-slider' ),
			'edit_item' => __( 'Edit Image Slider', 'ultimate-responsive-image-slider' ),
			'new_item' => __( 'New Image Slider', 'ultimate-responsive-image-slider' ),
			'view_item' => __( 'View Image Slider', 'ultimate-responsive-image-slider' ),
			'search_items' => __( 'Search Image Slider', 'ultimate-responsive-image-slider' ),
			'not_found' => __( 'No Image Slider found', 'ultimate-responsive-image-slider' ),
			'not_found_in_trash' => __( 'No Image Slider found in Trash', 'ultimate-responsive-image-slider' ),
			'parent_item_colon' => __( 'Parent Image Slider:', 'ultimate-responsive-image-slider' ),
			'all_items' => __( 'All Image Sliders', 'ultimate-responsive-image-slider' ),
			'menu_name' => __( 'UR Image Slider', 'ultimate-responsive-image-slider' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title' ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 10,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_nav_menus' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => false,
			'capability_type' => 'post',
			'capabilities' => array(
				'edit_post'              => 'edit_post',
				'read_post'              => 'read_post',
				'delete_post'            => 'delete_post',
				'edit_posts'             => 'manage_urisp_sliders',
				'edit_others_posts'      => 'manage_urisp_sliders',
				'publish_posts'          => 'manage_urisp_sliders',
				'read_private_posts'     => 'manage_urisp_sliders',
				'delete_posts'           => 'manage_urisp_sliders',
				'delete_private_posts'   => 'manage_urisp_sliders',
				'delete_published_posts' => 'manage_urisp_sliders',
				'delete_others_posts'    => 'manage_urisp_sliders',
				'edit_private_posts'     => 'manage_urisp_sliders',
				'edit_published_posts'   => 'manage_urisp_sliders',
				'create_posts'           => 'manage_urisp_sliders',
			),
			'map_meta_cap' => true
		);

		register_post_type( 'ris_gallery', $args );

		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'manage_edit-ris_gallery_columns', array(&$this, 'ris_gallery_columns' )) ;
		add_action( 'manage_ris_gallery_posts_custom_column', array(&$this, 'ris_gallery_manage_columns' ), 10, 2 );
		add_filter( 'manage_edit-ris_gallery_sortable_columns', array(&$this, 'ris_gallery_sortable_columns' ) );
		add_filter( 'post_row_actions', array(&$this, 'ris_gallery_row_actions' ), 10, 2 );
	}
	
	function ris_gallery_columns( $columns ){
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'preview'   => __( 'Preview', 'ultimate-responsive-image-slider' ),
			'title'     => __( 'Slider Title', 'ultimate-responsive-image-slider' ),
			'shortcode' => __( 'Shortcode', 'ultimate-responsive-image-slider' ),
			'layout'    => __( 'Layout', 'ultimate-responsive-image-slider' ),
			'slides'    => __( 'Slides', 'ultimate-responsive-image-slider' ),
			'date'      => __( 'Date', 'ultimate-responsive-image-slider' ),
		);
		return $columns;
	}

	function ris_gallery_sortable_columns( $columns ) {
		$columns['title'] = 'title';
		$columns['date']  = 'date';
		return $columns;
	}

	function ris_gallery_row_actions( $actions, $post ) {
		if ( $post->post_type !== 'ris_gallery' ) {
			return $actions;
		}

		$preview_btn = '<a href="#" class="urisp-row-preview-link" data-post-id="' . $post->ID . '">' . __( 'Live Preview', 'ultimate-responsive-image-slider' ) . '</a>';

		$new_actions = array();
		if ( isset( $actions['edit'] ) ) $new_actions['edit'] = $actions['edit'];
		if ( isset( $actions['inline hide-if-no-js'] ) ) $new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];
		$new_actions['urisp_preview'] = $preview_btn;
		if ( isset( $actions['trash'] ) ) $new_actions['trash'] = $actions['trash'];

		return $new_actions;
	}

	function ris_gallery_manage_columns( $column, $post_id ){
		global $post;
		switch( $column ) {
			case 'preview' :
				$slides = get_post_meta( $post_id, 'ris_all_photos_details', true );
				$thumb_url = '';
				if ( is_array( $slides ) && ! empty( $slides ) ) {
					$first_slide = reset( $slides );
					if ( isset( $first_slide['rpgp_image_id'] ) ) {
						$img = wp_get_attachment_image_src( absint( $first_slide['rpgp_image_id'] ), 'thumbnail' );
						if ( is_array( $img ) && ! empty( $img[0] ) ) {
							$thumb_url = $img[0];
						}
					}
				}

				if ( $thumb_url ) {
					echo '<a href="#" class="urisp-table-thumb-wrap urisp-row-preview-link" data-post-id="' . absint( $post_id ) . '" title="' . esc_attr__( 'Click to Live Preview', 'ultimate-responsive-image-slider' ) . '" style="position: relative; width: 64px; height: 48px; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.06); background: #f8fafc; display: block; cursor: pointer;">';
					echo '<img src="' . esc_url( $thumb_url ) . '" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="' . esc_attr( get_the_title( $post_id ) ) . '">';
					echo '</a>';
				} else {
					echo '<a href="#" class="urisp-row-preview-link" data-post-id="' . absint( $post_id ) . '" title="' . esc_attr__( 'Click to Live Preview', 'ultimate-responsive-image-slider' ) . '" style="width: 64px; height: 48px; border-radius: 8px; border: 1px dashed #cbd5e1; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; text-decoration: none; cursor: pointer;">';
					echo '<span class="dashicons dashicons-format-image" style="font-size: 22px; width: 22px; height: 22px; line-height: 1;"></span>';
					echo '</a>';
				}
				break;

			case 'shortcode' :
				?>
				<div class="urisp-shortcode-cell" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
					<code class="urisp-code-badge" style="background: #f8fafc; color: #1e293b; padding: 6px 10px; border-radius: 6px; font-family: 'Consolas', 'Monaco', monospace; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0; letter-spacing: 0.2px; user-select: all;">[URISP id=<?php echo absint( $post_id ); ?>]</code>
					<button type="button" class="button button-small urisp-copy-btn" data-code="[URISP id=<?php echo absint( $post_id ); ?>]" title="<?php esc_attr_e( 'Copy shortcode to clipboard', 'ultimate-responsive-image-slider' ); ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 5px; height: 28px; padding: 0 10px; border-radius: 6px; font-weight: 500; font-size: 11px; border-color: #cbd5e1; background: #ffffff; color: #334155; transition: all 0.2s;"><span class="dashicons dashicons-admin-page" style="font-size: 14px; width: 14px; height: 14px; line-height: 1; color: #6366f1; margin: 0;"></span><span class="urisp-copy-label"><?php esc_html_e( 'Copy', 'ultimate-responsive-image-slider' ); ?></span></button>
				</div>
				<?php
				break;

			case 'layout' :
				$settings = get_post_meta( $post_id, 'urisp_settings_' . $post_id, true );
				if ( empty( $settings ) || ! is_array( $settings ) ) {
					$legacy = get_post_meta( $post_id, 'WRIS_Gallery_Settings_' . $post_id, true );
					$layout = isset( $legacy['RISP_Slider_Layout'] ) ? absint( $legacy['RISP_Slider_Layout'] ) : 3;
				} else {
					$layout = isset( $settings['slider_layout'] ) ? absint( $settings['slider_layout'] ) : 3;
				}

				$layout_configs = array(
					1 => array( 'name' => 'Layout 1', 'desc' => 'Standard', 'bg' => '#e0e7ff', 'color' => '#4338ca', 'icon' => 'dashicons-slides' ),
					2 => array( 'name' => 'Layout 2', 'desc' => 'Grid Lightbox', 'bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'dashicons-grid-view' ),
					3 => array( 'name' => 'Layout 3', 'desc' => 'Thumbnails', 'bg' => '#d1fae5', 'color' => '#047857', 'icon' => 'dashicons-format-gallery' ),
					4 => array( 'name' => 'Layout 4', 'desc' => 'Filmstrip', 'bg' => '#e0f2fe', 'color' => '#0369a1', 'icon' => 'dashicons-images-alt' ),
					5 => array( 'name' => 'Layout 5', 'desc' => 'Carousel', 'bg' => '#f3e8ff', 'color' => '#7e22ce', 'icon' => 'dashicons-image-rotate' ),
				);

				$cfg = isset( $layout_configs[$layout] ) ? $layout_configs[$layout] : $layout_configs[3];
				?>
				<span class="urisp-badge" style="display: inline-flex; align-items: center; gap: 5px; background: <?php echo esc_attr( $cfg['bg'] ); ?>; color: <?php echo esc_attr( $cfg['color'] ); ?>; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 11px;">
					<span class="dashicons <?php echo esc_attr( $cfg['icon'] ); ?>" style="font-size: 13px; width: 13px; height: 13px; line-height: 1;"></span>
					<?php echo esc_html( $cfg['name'] . ' (' . $cfg['desc'] . ')' ); ?>
				</span>
				<?php
				break;

			case 'slides' :
				$slides = get_post_meta( $post_id, 'ris_all_photos_details', true );
				$count  = ( is_array( $slides ) ) ? count( $slides ) : 0;
				?>
				<span class="urisp-badge" style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; border: 1px solid #e2e8f0;">
					<span class="dashicons dashicons-format-gallery" style="font-size: 14px; width: 14px; height: 14px; line-height: 1; color: #6366f1;"></span>
					<?php
					/* translators: %s: Number of images in slider */
					echo esc_html( sprintf( _n( '%s Image', '%s Images', $count, 'ultimate-responsive-image-slider' ), number_format_i18n( $count ) ) );
					?>
				</span>
				<?php
				break;

			default :
				break;
		}
	}

	public function risp_add_all_meta_boxes() {
		add_meta_box( 'urisp_settings_meta_box', __('Configure Setting', 'ultimate-responsive-image-slider'), array(&$this, 'urisp_settings_meta_box_function'), 'ris_gallery', 'normal', 'high');
		add_meta_box( 'urisp_shortcode_meta_box', __('Copy Slider Shortcode', 'ultimate-responsive-image-slider'), array(&$this, 'urisp_shotcode_meta_box_function'), 'ris_gallery', 'side', 'default');
	}

	public function urisp_meta_box_order( $order ) {
		if ( ! is_array( $order ) ) {
			$order = array();
		}

		if ( ! isset( $order['side'] ) || empty( $order['side'] ) ) {
			$order['side'] = 'submitdiv,urisp_shortcode_meta_box';
			return $order;
		}

		$side_boxes = array_filter( array_map( 'trim', explode( ',', $order['side'] ) ) );
		// Remove shortcode box from wherever it might be currently positioned
		$side_boxes = array_diff( $side_boxes, array( 'urisp_shortcode_meta_box' ) );
		
		$new_side = array();
		$inserted = false;
		foreach ( $side_boxes as $box ) {
			$new_side[] = $box;
			if ( $box === 'submitdiv' ) {
				$new_side[] = 'urisp_shortcode_meta_box';
				$inserted = true;
			}
		}
		if ( ! $inserted ) {
			array_unshift( $new_side, 'submitdiv' );
			$new_side[] = 'urisp_shortcode_meta_box';
		}
		$order['side'] = implode( ',', array_unique( $new_side ) );

		return $order;
	}

	public function urisp_render_preview_elements() {
		global $post, $current_screen;
		if ( ! $current_screen || $current_screen->post_type !== 'ris_gallery' ) {
			return;
		}

		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) {
			return;
		}

		$post_id = ( isset( $post ) && isset( $post->ID ) ) ? $post->ID : 0;
		$preview_url = $post_id ? add_query_arg(array(
			'action'  => 'urisp_render_preview',
			'post_id' => $post_id,
			'nonce'   => wp_create_nonce('urisp_preview_nonce'),
		), admin_url('admin-ajax.php')) : '';
		?>
		<div id="urisp-preview-container">
			<?php if ( $current_screen->base === 'post' && isset($post) && $post->post_status !== 'auto-draft' ) : ?>
			<!-- Floating Preview Button on Edit Screen -->
			<button type="button" id="urisp-floating-preview-btn" style="position:fixed; bottom:30px; right:30px; z-index:99999; background:linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); color:#fff; border:none; border-radius:24px; height:48px; padding:0 20px; box-shadow:0 4px 14px rgba(126, 34, 206, 0.45); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1); outline:none;" title="<?php esc_attr_e('Check Live Preview', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-visibility" style="font-size:20px; width:20px; height:20px; color:#fff; margin:0; line-height:1; position:relative; top:-0.5px;"></span><span style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:14px; font-weight:600; color:#fff; white-space:nowrap;"><?php esc_html_e('Check Live Preview', 'ultimate-responsive-image-slider'); ?></span></button>
			<?php endif; ?>

			<style>
			#urisp-floating-preview-btn:hover {
				background: linear-gradient(135deg, #9333ea 0%, #6b21a8 100%) !important;
				transform: translateY(-2px) scale(1.04) !important;
				box-shadow: 0 6px 20px rgba(126, 34, 206, 0.6) !important;
			}
			#urisp-preview-modal {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: 999999;
				display: flex;
				justify-content: center;
				align-items: center;
				opacity: 0;
				visibility: hidden;
				pointer-events: none;
				transition: opacity 0.18s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.18s cubic-bezier(0.16, 1, 0.3, 1);
			}
			#urisp-preview-modal.urisp-active {
				opacity: 1;
				visibility: visible;
				pointer-events: auto;
			}
			#urisp-preview-modal-backdrop {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(15, 23, 42, 0.65);
			}
			#urisp-preview-modal .urisp-preview-modal-box {
				position: relative;
				width: 96%;
				max-width: 1400px;
				height: 92vh;
				background: #fff;
				border-radius: 12px;
				box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2), 0 10px 10px -5px rgba(0,0,0,0.1);
				display: flex;
				flex-direction: column;
				overflow: hidden;
				z-index: 1;
				transform: scale(0.96) translateY(8px);
				transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1);
				will-change: transform;
			}
			#urisp-preview-modal.urisp-active .urisp-preview-modal-box {
				transform: scale(1) translateY(0);
			}
			</style>

			<!-- Modal Overlay Popup -->
			<div id="urisp-preview-modal">
				<!-- Backdrop -->
				<div id="urisp-preview-modal-backdrop"></div>
				
				<!-- Content Box -->
				<div class="urisp-preview-modal-box">
					<!-- Header -->
					<div style="display:flex; justify-content:space-between; align-items:center; padding:10px 18px; border-bottom:1px solid #f1f5f9; background:#f8fafc; flex-shrink:0;">
						<span style="font-size:14px; font-weight:600; color:#0f172a; display:flex; align-items:center; gap:8px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
							<span class="dashicons dashicons-visibility" style="color:#9333ea; font-size:18px; width:18px; height:18px; line-height:1;"></span>
							<span id="urisp-preview-title"><?php esc_html_e('Live Slider Preview', 'ultimate-responsive-image-slider'); ?></span>
						</span>
						<div style="display:flex; gap:8px; align-items:center;">
							<button type="button" id="urisp-refresh-preview" onclick="urisp_reload_preview();" style="background:none; border:none; color:#64748b; cursor:pointer; padding:4px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" title="<?php esc_attr_e('Refresh Preview', 'ultimate-responsive-image-slider'); ?>">
								<span class="dashicons dashicons-update" style="font-size:20px; width:20px; height:20px;"></span>
							</button>
							<button type="button" id="urisp-close-preview-modal" style="background:none; border:none; color:#64748b; cursor:pointer; padding:4px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s;">
								<span class="dashicons dashicons-no-alt" style="font-size:20px; width:20px; height:20px;"></span>
							</button>
						</div>
					</div>
					
					<!-- Body (Flex wrapper containing the iframe + loader overlay) -->
					<div style="position:relative; padding:10px; background:#f8fafc; flex:1; display:flex; flex-direction:column; overflow:hidden;">
						<div style="position:relative; width: 100%; flex: 1; min-height: 250px; resize: vertical; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; padding-bottom: 6px; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); display:flex; flex-direction:column;">
							<!-- Loading Spinner Overlay -->
							<div id="urisp-preview-overlay" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.95); z-index:10; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
								<div style="width:40px; height:40px; border:3px solid #e2e8f0; border-top-color:#9333ea; border-radius:50%; animation:urispSpin 0.8s linear infinite;"></div>
								<span style="font-size:13px; font-weight:600; color:#475569; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;"><?php esc_html_e('Loading Preview...', 'ultimate-responsive-image-slider'); ?></span>
							</div>

							<iframe id="urisp-preview-iframe"
									data-src="<?php echo esc_url($preview_url); ?>"
									src="about:blank"
									style="width: 100%; flex: 1; border: none;"
									scrolling="auto"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			// 1-Click Copy Shortcode Button
			$(document).on('click', '.urisp-copy-btn', function(e) {
				e.preventDefault();
				var $btn = $(this);
				if ($btn.data('urisp-is-copied')) {
					return;
				}
				var code = $btn.attr('data-code');
				if (!code) {
					var $input = $btn.siblings('input');
					if ($input.length) {
						code = $input.val();
					}
				}
				if (!code) return;

				var $label = $btn.find('.urisp-copy-label');
				var $icon  = $btn.find('.dashicons');
				var originalText = $label.length ? $label.text() : $btn.text();
				var isSmall = $btn.hasClass('button-small');

				function markCopied() {
					$btn.data('urisp-is-copied', true).addClass('urisp-copied');
					$btn.css({
						'border-color': '#10b981',
						'background': '#f0fdf4',
						'color': '#10b981'
					});
					if ($icon.length) {
						$icon.removeClass('dashicons-admin-page').addClass('dashicons-yes').css('color', '#10b981');
					}
					if ($label.length) {
						$label.text('<?php echo esc_js( __( 'Copied!', 'ultimate-responsive-image-slider' ) ); ?>');
					} else {
						$btn.text('<?php echo esc_js( __( 'Copied!', 'ultimate-responsive-image-slider' ) ); ?>');
					}

					if (window.urispModal && window.urispModal.toast) {
						window.urispModal.toast({
							message: '<?php echo esc_js( __( 'Shortcode copied to clipboard!', 'ultimate-responsive-image-slider' ) ); ?>',
							type: 'success',
							duration: 2500
						});
					}

					setTimeout(function() {
						$btn.removeClass('urisp-copied').removeData('urisp-is-copied');
						$btn.css({
							'border-color': '#cbd5e1',
							'background': isSmall ? '#ffffff' : '#f1f5f9',
							'color': '#334155'
						});
						if ($icon.length) {
							$icon.removeClass('dashicons-yes').addClass('dashicons-admin-page').css('color', '#6366f1');
						}
						if ($label.length) {
							$label.text(originalText);
						} else {
							$btn.text(originalText);
						}
					}, 2000);
				}

				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(code).then(markCopied, function() {
						fallbackCopy(code, markCopied);
					});
				} else {
					fallbackCopy(code, markCopied);
				}

				function fallbackCopy(text, callback) {
					var temp = $('<textarea>');
					temp.css({
						position: 'fixed',
						top: '-9999px',
						left: '-9999px',
						opacity: '0'
					}).val(text);
					$('body').append(temp);
					temp.focus().select();
					try {
						document.execCommand('copy');
						if (typeof callback === 'function') {
							callback();
						}
					} catch (err) {
						console.error('Copy failed:', err);
					}
					temp.remove();
				}
			});

			// List Screen Preview Link
			$(document).on('click', '.urisp-row-preview-link', function(e) {
				e.preventDefault();
				var postId = $(this).data('post-id');
				if (!postId) return;
				var previewUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>?action=urisp_render_preview&post_id=' + postId + '&nonce=<?php echo esc_attr( wp_create_nonce( 'urisp_preview_nonce' ) ); ?>';
				$('#urisp-preview-overlay').css('display', 'flex');
				$('#urisp-preview-iframe').attr('src', previewUrl);
				$('#urisp-preview-modal').addClass('urisp-active');
				$('body').css('overflow', 'hidden');
			});

			// Close Modal
			$('#urisp-close-preview-modal, #urisp-preview-modal-backdrop').on('click', function() {
				$('#urisp-preview-modal').removeClass('urisp-active');
				$('body').css('overflow', '');
			});
		});
		</script>
		<?php
	}

	public function urisp_render_preview() {
		check_ajax_referer('urisp_preview_nonce', 'nonce');
		if (!urisp_current_user_can('manage_urisp_sliders')) wp_die('Unauthorized');
		
		$post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
		if (!$post_id) wp_die('Invalid Slider ID');

		// Pre-enqueue all URISP assets so styles load in <head> instead of <footer> to prevent layout shifting
		if ( function_exists( 'uris_register_frontend_assets' ) ) {
			uris_register_frontend_assets();
		}
		wp_enqueue_style('wpfrank-uris-slider-pro-min-css');
		wp_enqueue_style('wpfrank-uris-frontend-css');
		wp_enqueue_style('wpfrank-uris-modern-css');
		wp_enqueue_style('wpfrank-uris-skins-css');
		wp_enqueue_script('jquery');
		wp_enqueue_script('wpfrank-uris-jquery-sliderPro-js');
		
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo('charset'); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php wp_head(); ?>
			<style>
				body { 
					margin: 15px; 
					background: #ffffff; 
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; 
					-webkit-font-smoothing: antialiased;
					overflow-x: hidden;
				}
				/* Hide theme/plugin injected elements like Scroll-to-Top buttons and Admin Bar inside the preview iframe */
				#wpadminbar,
				#ast-scroll-top, .ast-scroll-top,
				#back-to-top, .back-to-top,
				#scroll-to-top, .scroll-to-top,
				#scrollTop, .scrollTop,
				#scrollUp, .scrollUp,
				.scroll-top, #scroll-top { 
					display: none !important; 
				}
				html { margin-top: 0 !important; }
				* html body { margin-top: 0 !important; }
			</style>
		</head>
		<body>
			<?php echo do_shortcode('[URISP id=' . $post_id . ']'); ?>
			<?php wp_footer(); ?>
		</body>
		</html>
		<?php
		die;
	}

	public function urisp_ajax_save_settings() {
		check_ajax_referer('urisp_ajax_nonce', 'nonce');
		if (!urisp_current_user_can('manage_urisp_sliders')) {
			wp_send_json_error('Unauthorized');
		}
		
		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid post ID' );
		}

		// Only Vimeo is supported in Free. Reject other URLs with a message
		// instead of silently downgrading the slide to an image.
		if ( ! empty( $_POST['rpgp_video_url'] ) && is_array( $_POST['rpgp_video_url'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$posted_video_urls = (array) wp_unslash( $_POST['rpgp_video_url'] );
			foreach ( $posted_video_urls as $index => $raw_video_url ) {
				$raw_video_url = esc_url_raw( $raw_video_url );
				$slide_type    = isset( $_POST['rpgp_slide_type'][ $index ] ) ? sanitize_key( wp_unslash( $_POST['rpgp_slide_type'][ $index ] ) ) : 'image';

				if ( 'video' !== $slide_type || '' === $raw_video_url ) {
					continue;
				}

				if ( ! preg_match( '/(?:vimeo\.com\/(?:video\/)?)([0-9]+)/i', $raw_video_url ) ) {
					wp_send_json_error( array(
						'message' => __( 'Only Vimeo video URLs are supported in the Free version. YouTube, Dailymotion and self-hosted videos require Ultimate Responsive Image Slider Pro.', 'ultimate-responsive-image-slider' ),
					) );
				}
			}
		}

		$this->risp_add_image_meta_box_save($post_id);
		$this->risp_settings_meta_save($post_id);
		wp_send_json_success(array('message' => 'Settings saved successfully.'));
	}


	public function urisp_ajax_upload_file() {
		check_ajax_referer('urisp_ajax_nonce', 'nonce');
		// `upload_files` alone must not grant access to a slider's slides.
		if ( ! urisp_current_user_can('manage_urisp_sliders') ) {
			wp_send_json_error('Unauthorized');
		}

		if ( ! empty( $_FILES['async-uploads'] ) && isset( $_FILES['async-uploads']['name'] ) && is_array( $_FILES['async-uploads']['name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$html_out = '';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Processed securely via media_handle_upload.
			$files = (array) $_FILES['async-uploads'];
			$file_count = count($files['name']);

			for ($i = 0; $i < $file_count; $i++) {
				$_FILES['current-upload'] = array(
					'name'     => $files['name'][$i],
					'type'     => $files['type'][$i],
					'tmp_name' => $files['tmp_name'][$i],
					'error'    => $files['error'][$i],
					'size'     => $files['size'][$i],
				);

				$attachment_id = media_handle_upload('current-upload', 0);

				if (!is_wp_error($attachment_id)) {
					ob_start();
					$this->risp_admin_thumb($attachment_id);
					$html_out .= ob_get_clean();
				}
			}

			if (!empty($html_out)) {
				wp_send_json_success(array('html' => $html_out));
			} else {
				wp_send_json_error('Failed to upload files.');
			}
		} else {
			wp_send_json_error('No files received');
		}
	}


	
	/**
	 * This function display Add New Image interface
	 * Also loads all saved slide iamge into slider
	 */
	public function urisp_settings_meta_box_function($post) {
		// admin js
		$modal_js_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/js/urisp-modal.js' : '';
		$modal_ver = ( $modal_js_file && file_exists( $modal_js_file ) ) ? filemtime( $modal_js_file ) : ( defined('RISP_PLUGIN_VER') ? RISP_PLUGIN_VER : false );
		$uploader_js_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/js/urisp-multiple-media-uploader.js' : '';
		$uploader_ver = ( $uploader_js_file && file_exists( $uploader_js_file ) ) ? filemtime( $uploader_js_file ) : ( defined('RISP_PLUGIN_VER') ? RISP_PLUGIN_VER : false );

		wp_enqueue_script( 'wpfrank-urisp-modal-js', RISP_PLUGIN_URL . 'assets/js/urisp-modal.js', array(), $modal_ver, true );
		wp_enqueue_script('media-upload');
		wp_enqueue_script('wpfrank-urisp-media-uploader-js', RISP_PLUGIN_URL . 'assets/js/urisp-multiple-media-uploader.js', array('jquery', 'jquery-ui-sortable', 'wpfrank-urisp-modal-js'), $uploader_ver, true);
		wp_localize_script( 'wpfrank-urisp-media-uploader-js', 'urisp_uploader_i18n', array(
			'delete_slide_confirm'    => __( 'Are you sure you want to delete this slide?', 'ultimate-responsive-image-slider' ),
			'delete_slide_title'      => __( 'Delete Slide', 'ultimate-responsive-image-slider' ),
			'delete_selected_confirm' => __( 'Are you sure you want to delete the selected slide(s)?', 'ultimate-responsive-image-slider' ),
			'delete_selected_title'   => __( 'Delete Selected Slides', 'ultimate-responsive-image-slider' ),
			'delete_all_confirm'      => __( 'Are you sure you want to delete all the slides?', 'ultimate-responsive-image-slider' ),
			'delete_all_title'        => __( 'Delete All Slides', 'ultimate-responsive-image-slider' ),
			'delete_btn'              => __( 'Delete', 'ultimate-responsive-image-slider' ),
			'cancel_btn'              => __( 'Cancel', 'ultimate-responsive-image-slider' ),
			'upload_failed'           => __( 'Failed to upload files: ', 'ultimate-responsive-image-slider' ),
			'upload_error'            => __( 'An error occurred while uploading files.', 'ultimate-responsive-image-slider' ),
		) );
		wp_enqueue_media();
		add_thickbox();

		//color-picker CSS n JS
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wpfrank-urisp-color-picker-script', plugins_url('assets/js/wl-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), RISP_PLUGIN_VER, true );

		// meta settings
		wp_nonce_field('urisp_save_settings', 'urisp_settings_nonce');

		if ( file_exists( RISP_PLUGIN_DIR_PATH . 'admin/settings.php' ) ) {
			require_once( RISP_PLUGIN_DIR_PATH . 'admin/settings.php' );
		} else {
			require_once( RISP_PLUGIN_DIR_PATH . 'settings.php' );
		}
	}
	
	public function urisp_shotcode_meta_box_function($post) {
		$post_id  = $post ? $post->ID : get_the_ID();
		$shortcode = '[URISP id=' . $post_id . ']';
		$php_tag   = "<?php echo do_shortcode('[URISP id=" . $post_id . "]'); ?>";
		?>
		<div class="urisp-shortcode-metabox-content" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13px; color: #334155; line-height: 1.5;">
			<!-- Standard Shortcode -->
			<div style="margin-bottom: 16px;">
				<label style="display: block; font-weight: 600; color: #0f172a; margin-bottom: 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
					<?php esc_html_e('Standard Shortcode', 'ultimate-responsive-image-slider'); ?>
				</label>
				<p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">
					<?php esc_html_e('Paste this shortcode into any Page, Post, Widget, or Page Builder:', 'ultimate-responsive-image-slider'); ?>
				</p>
				<div style="display: flex; gap: 6px; align-items: stretch;">
					<input readonly="readonly" type="text" value="<?php echo esc_attr($shortcode); ?>" onclick="this.select();" style="flex: 1; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; font-weight: 600; color: #0f172a; box-shadow: inset 0 1px 2px rgba(0,0,0,0.04); cursor: text;">
					<button type="button" class="button urisp-copy-btn" data-code="<?php echo esc_attr($shortcode); ?>" title="<?php esc_attr_e('Copy Shortcode', 'ultimate-responsive-image-slider'); ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 5px; padding: 0 12px; height: auto; border-radius: 6px; background: #f1f5f9; border-color: #cbd5e1; color: #334155; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;"><span class="dashicons dashicons-admin-page" style="font-size: 14px; width: 14px; height: 14px; line-height: 1; color: #6366f1; margin: 0;"></span><span class="urisp-copy-label"><?php esc_html_e('Copy', 'ultimate-responsive-image-slider'); ?></span></button>
				</div>
			</div>

			<!-- PHP Function Code -->
			<div style="margin-bottom: 16px; padding-top: 14px; border-top: 1px solid #e2e8f0;">
				<label style="display: block; font-weight: 600; color: #0f172a; margin-bottom: 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
					<?php esc_html_e('PHP Template Tag', 'ultimate-responsive-image-slider'); ?>
				</label>
				<p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">
					<?php esc_html_e('Use in theme template files (e.g. header.php, front-page.php):', 'ultimate-responsive-image-slider'); ?>
				</p>
				<div style="display: flex; gap: 6px; align-items: stretch;">
					<input readonly="readonly" type="text" value="<?php echo esc_attr($php_tag); ?>" onclick="this.select();" style="flex: 1; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; font-family: 'Consolas', 'Monaco', monospace; font-size: 11px; font-weight: 600; color: #0f172a; box-shadow: inset 0 1px 2px rgba(0,0,0,0.04); cursor: text;">
					<button type="button" class="button urisp-copy-btn" data-code="<?php echo esc_attr($php_tag); ?>" title="<?php esc_attr_e('Copy PHP Code', 'ultimate-responsive-image-slider'); ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 5px; padding: 0 12px; height: auto; border-radius: 6px; background: #f1f5f9; border-color: #cbd5e1; color: #334155; font-weight: 600; font-size: 12px; cursor: pointer; transition: all 0.2s;"><span class="dashicons dashicons-admin-page" style="font-size: 14px; width: 14px; height: 14px; line-height: 1; color: #6366f1; margin: 0;"></span><span class="urisp-copy-label"><?php esc_html_e('Copy', 'ultimate-responsive-image-slider'); ?></span></button>
				</div>
			</div>

			<!-- Full Documentation Link -->
			<div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center;">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery&page=urisp_docs#doc-quick-start' ) ); ?>" target="_blank" rel="noopener noreferrer" style="font-size: 11.5px; color: #6366f1; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
					<span class="dashicons dashicons-book" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span>
					<span><?php esc_html_e('View Publishing Guide in Docs &rarr;', 'ultimate-responsive-image-slider'); ?></span>
				</a>
			</div>

			<!-- Plugin Version Badge -->
			<div style="margin-top: 12px; text-align: center; font-size: 11px; color: #94a3b8; font-weight: 500;">
				<?php echo esc_html__( 'Ultimate Responsive Image Slider', 'ultimate-responsive-image-slider' ) . ' v' . esc_html( RISP_PLUGIN_VER ); ?>
			</div>
		</div>
		<?php 
	}

	public function risp_admin_thumb($id) {
		$attachment = get_post( $id );
		$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
		$slide_caption = $attachment->post_excerpt;
		$slide_description = $attachment->post_content;
		$slide_href = get_permalink( $attachment->ID );
		$slide_src = $attachment->guid;
		$slide_title = $attachment->post_title;
		$slide_medium = wp_get_attachment_image_src($id, 'medium', true);
		$slide_full  = wp_get_attachment_image_src($id, 'full', true);
		$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
		// Slides rendered through AJAX are brand new and have no stored external link yet.
		$slide_link = '';
		
		// if slide alt blank than add the slide title as alt test
		if($slide_alt == "") $slide_alt = $slide_title;
		?>
		<li id="<?php echo esc_attr( $id ); ?>" class="uris-slide" data-position="<?php echo esc_attr( $id ); ?>">
			<label class="urisp-slide-checkbox-wrap" title="<?php esc_attr_e('Select slide', 'ultimate-responsive-image-slider'); ?>">
				<input type="checkbox" class="urisp-slide-checkbox">
				<span class="urisp-checkbox-custom"></span>
			</label>
			<a class="uris-slide-delete-icon" href="#" title="<?php esc_attr_e('Delete slide', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-trash"></span></a>
			<a class="uris-slide-edit-icon" href="#" title="<?php esc_attr_e('Edit slide details', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-edit"></span></a>
			<div class="uris-slide-meta">
				<img src="<?php echo esc_url( isset($slide_medium[0]) ? $slide_medium[0] : '' ); ?>" class="uris-slide-image" alt="<?php echo esc_attr($slide_alt); ?>">
				<div class="urisp-slide-inputs" style="display: none;">
					<input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr( $UniqueString ); ?>" />
					<input type="hidden" id="rpgp_image_id[]" name="rpgp_image_id[]" value="<?php echo esc_attr( $id ); ?>">
					<input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_attr($slide_title); ?>">
					<textarea id="rpgp_image_desc[]" name="rpgp_image_desc[]" class="urisp_richeditbox_<?php echo esc_attr( $id ); ?> uris-slide-input-text urisp-input-styled"><?php echo esc_textarea($slide_description); ?></textarea>
					<input type="text" id="rpgp_external_link[]" name="rpgp_external_link[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_url( $slide_link ); ?>">
					<input type="text" id="rpgp_image_alt[]" name="rpgp_image_alt[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_attr($slide_alt); ?>">
					<input type="hidden" id="rpgp_slide_type[]" name="rpgp_slide_type[]" class="urisp-input-slide-type" value="image">
					<input type="hidden" id="rpgp_video_url[]" name="rpgp_video_url[]" class="urisp-input-video-url" value="">
					<input type="hidden" id="rpgp_orig_image_id[]" name="rpgp_orig_image_id[]" class="urisp-input-orig-image-id" value="<?php echo esc_attr( $id ); ?>">
					<input type="hidden" id="rpgp_orig_image_url[]" name="rpgp_orig_image_url[]" class="urisp-input-orig-image-url" value="<?php echo isset($slide_medium[0]) ? esc_url($slide_medium[0]) : ''; ?>">
					<input type="hidden" id="rpgp_video_thumb_url[]" name="rpgp_video_thumb_url[]" class="urisp-input-video-thumb-url" value="">
					<input type="hidden" id="rpgp_cta_text[]" name="rpgp_cta_text[]" class="urisp-input-cta-text" value="">

					<input type="hidden" id="rpgp_cta_url[]" name="rpgp_cta_url[]" class="urisp-input-cta-url" value="">
					<input type="hidden" id="rpgp_cta_style[]" name="rpgp_cta_style[]" class="urisp-input-cta-style" value="default">
					<input type="hidden" id="rpgp_cta_target[]" name="rpgp_cta_target[]" class="urisp-input-cta-target" value="_blank">
				</div>
			</div>
		</li>
		<?php
	}
	
	public function risp_ajax_get_thumbnail() {
		check_ajax_referer( 'urisp_ajax_nonce', 'nonce' );
		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) {
			wp_die( 'Unauthorized' );
		}
		if (isset($_POST['imageids']) && is_array($_POST['imageids'])) {
			$image_ids = array_map('absint', $_POST['imageids']);
			foreach ($image_ids as $id) {
				$this->risp_admin_thumb($id);
			}
		} else if (isset($_POST['imageid'])) {
			$image_id = absint( $_POST['imageid'] );
			$this->risp_admin_thumb( $image_id );
		}
		die;
	}

	public function risp_add_image_meta_box_save($PostID) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['urisp_images_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urisp_images_nonce'] ) ), 'urisp_save_images' ) ) return;
		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) return;

		if(isset($PostID) && isset($_POST['uris-save-action'])) {
			$TotalImages = 0;
			if(isset($_POST['rpgp_image_id']))
			$TotalImages = count($_POST['rpgp_image_id']);
			$ImagesArray = array();
			if($TotalImages) {
				for($i=0; $i < $TotalImages; $i++) {
					$slide_id = ( isset( $_POST['rpgp_image_id'][$i] ) ) ? absint( $_POST['rpgp_image_id'][$i] ) : 0;
					$slide_title = isset($_POST['rpgp_image_label'][$i]) ? sanitize_text_field(wp_unslash($_POST['rpgp_image_label'][$i])) : '';
					$slide_desc = isset($_POST['rpgp_image_desc'][$i]) ? wp_kses_post(wp_unslash($_POST['rpgp_image_desc'][$i])) : '';
					$slide_link = isset($_POST['rpgp_external_link'][$i]) ? esc_url_raw(wp_unslash($_POST['rpgp_external_link'][$i])) : '';
					$slide_alt = isset($_POST['rpgp_image_alt'][$i]) ? sanitize_text_field(wp_unslash($_POST['rpgp_image_alt'][$i])) : '';
					$slide_type = ( isset($_POST['rpgp_slide_type'][$i]) && in_array($_POST['rpgp_slide_type'][$i], array('image', 'video'), true) ) ? sanitize_key($_POST['rpgp_slide_type'][$i]) : 'image';
					$slide_video_url = isset($_POST['rpgp_video_url'][$i]) ? esc_url_raw(wp_unslash($_POST['rpgp_video_url'][$i])) : '';
					if ( $slide_type === 'video' ) {
						// Free version strictly supports Vimeo
						if ( ! preg_match( '/(?:vimeo\.com\/(?:video\/)?)([0-9]+)/i', $slide_video_url ) ) {
							$slide_type = 'image';
							$slide_video_url = '';
						}
					}
					$orig_image_id = isset($_POST['rpgp_orig_image_id'][$i]) ? absint($_POST['rpgp_orig_image_id'][$i]) : $slide_id;
					$orig_image_url = isset($_POST['rpgp_orig_image_url'][$i]) ? esc_url_raw(wp_unslash($_POST['rpgp_orig_image_url'][$i])) : '';
					$video_thumb_url = isset($_POST['rpgp_video_thumb_url'][$i]) ? esc_url_raw(wp_unslash($_POST['rpgp_video_thumb_url'][$i])) : '';
					$slide_cta_text = isset($_POST['rpgp_cta_text'][$i]) ? sanitize_text_field(wp_unslash($_POST['rpgp_cta_text'][$i])) : '';
					$slide_cta_url = isset($_POST['rpgp_cta_url'][$i]) ? esc_url_raw(wp_unslash($_POST['rpgp_cta_url'][$i])) : '';
					$slide_cta_style = ( isset($_POST['rpgp_cta_style'][$i]) && in_array($_POST['rpgp_cta_style'][$i], array('default', 'filled', 'outlined', 'pill', 'text'), true) ) ? sanitize_key($_POST['rpgp_cta_style'][$i]) : 'default';
					$slide_cta_target = ( isset($_POST['rpgp_cta_target'][$i]) && in_array($_POST['rpgp_cta_target'][$i], array('_self', '_blank'), true) ) ? sanitize_key($_POST['rpgp_cta_target'][$i]) : '_blank';

					$ImagesArray[] = array(
						'rpgp_image_id'      => $slide_id,
						'rpgp_external_link' => $slide_link,
						'slide_title'        => $slide_title,
						'slide_desc'         => $slide_desc,
						'slide_alt'          => $slide_alt,
						'slide_type'         => $slide_type,
						'video_url'          => $slide_video_url,
						'orig_image_id'      => $orig_image_id,
						'orig_image_url'     => $orig_image_url,
						'video_thumb_url'    => $video_thumb_url,
						'cta_text'           => $slide_cta_text,
						'cta_url'            => $slide_cta_url,
						'cta_style'          => $slide_cta_style,
						'cta_target'         => $slide_cta_target,
					);

				}
				update_post_meta($PostID, 'ris_all_photos_details', $ImagesArray);
				update_post_meta($PostID, 'ris_total_images_count', $TotalImages);
			} else {
				$TotalImages = 0;
				update_post_meta($PostID, 'ris_total_images_count', $TotalImages);
				$ImagesArray = array();
				update_post_meta($PostID, 'ris_all_photos_details', $ImagesArray);
			}
		}
	}
	
	//save settings meta box values
	public function risp_settings_meta_save($PostID) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['urisp_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urisp_settings_nonce'] ) ), 'urisp_save_settings' ) ) return;
		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) return;

		if ( isset( $_POST['urisp_active_admin_tab'] ) ) {
			update_post_meta( $PostID, '_urisp_active_admin_tab', sanitize_key( wp_unslash( $_POST['urisp_active_admin_tab'] ) ) );
		}

		if(isset($PostID) && isset($_POST['urisp_settings'])) {
			require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
			$defaults = urisp_get_default_settings();
			$sanitize_callbacks = urisp_get_sanitization_rules();
			
			$existing_settings = get_post_meta( $PostID, 'urisp_settings_' . $PostID, true );
			if ( empty( $existing_settings ) || ! is_array( $existing_settings ) ) {
				$legacy_key = 'WRIS_Gallery_Settings_' . $PostID;
				$legacy     = get_post_meta( $PostID, $legacy_key, true );
				if ( ! empty( $legacy ) && is_array( $legacy ) ) {
					require_once( RISP_PLUGIN_DIR_PATH . 'includes/migration.php' );
					$existing_settings = urisp_convert_legacy_to_unified( $legacy );
				} else {
					$existing_settings = array();
				}
			}

			$toggles = array(
				'slide_title',
				'show_slide_title',
				'show_slide_desc',
				'detailed_navigation',
				'navigation_button',
				'sliding_arrow',
				'fullscreen',
				'auto_scale',
				'skeleton_loading',
				'show_loader',
				'watermark_enabled',
				'ken_burns',
			);

			$unified_settings = array();
			
			foreach ( $defaults as $key => $default_val ) {
				if ( isset( $_POST['urisp_settings'][$key] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized dynamically below via $sanitize_callbacks[$key].
					$val = wp_unslash( $_POST['urisp_settings'][$key] );
				} elseif ( in_array( $key, $toggles ) ) {
					$val = 0;
				} else {
					$val = isset( $existing_settings[$key] ) ? $existing_settings[$key] : $default_val;
				}
				
				if ( isset( $sanitize_callbacks[$key] ) ) {
					$func = $sanitize_callbacks[$key];
					if ( function_exists( $func ) ) {
						$val = call_user_func( $func, $val );
					}
				}
				$unified_settings[$key] = $val;
			}
			
			// In Free version, only Layout 3 (Thumbnail Slider) is supported
			$unified_settings['slider_layout'] = 3;

			// The editor posts `slide_in_*`, `font_style`, `auto_scale`, ... keys while
			// the renderer reads their aliases. Keep both in sync on every save.
			$unified_settings = uris_sync_editor_alias_keys( $unified_settings );

			update_post_meta( $PostID, 'urisp_settings_' . $PostID, $unified_settings );

			// Drop the legacy migration snapshot: it must never shadow this save.
			delete_post_meta( $PostID, 'uris_settings' );
			
			// Backward compatibility: map unified keys back to WRIS_Gallery_Settings_$PostID
			$legacy_map = array_flip(urisp_get_legacy_key_map()); 
			$RISP_Settings_Array = array();
			foreach ($legacy_map as $unified_key => $legacy_key) {
				if (isset($unified_settings[$unified_key])) {
					$RISP_Settings_Array[$legacy_key] = $unified_settings[$unified_key];
				}
			}
			
			// Map unified keys to layout-specific legacy keys for layouts 1-5
			for ($i = 1; $i <= 5; $i++) {
				$RISP_Settings_Array['RISP_L'.$i.'_Auto_Slideshow'] = $unified_settings['auto_slideshow'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slide_Order'] = $unified_settings['slide_order'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slide_Distance'] = $unified_settings['slide_distance'];
				$RISP_Settings_Array['RISP_L'.$i.'_Sliding_Arrow'] = $unified_settings['sliding_arrow'];
				$RISP_Settings_Array['RISP_L'.$i.'_Navigation_Button'] = $unified_settings['navigation_button'];
				$RISP_Settings_Array['RISP_L'.$i.'_Navigation_Button_Style'] = $unified_settings['navigation_button_style'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slider_Transition_Speed'] = $unified_settings['transition_speed'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slider_Width'] = $unified_settings['slider_width'];
				$legacy_w = ( isset( $unified_settings['width_mode'] ) && in_array( $unified_settings['width_mode'], array( 'fullWidth', 'fullWindow' ), true ) ) ? $unified_settings['width_mode'] : 'custom';
				$RISP_Settings_Array['RISP_L'.$i.'_Width'] = $legacy_w;
				$RISP_Settings_Array['RISP_L'.$i.'_Slider_Height'] = $unified_settings['slider_height'];
				$RISP_Settings_Array['RISP_L'.$i.'_Height'] = $unified_settings['height_mode'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slider_Navigation'] = $unified_settings['detailed_navigation'];
				$RISP_Settings_Array['RISP_L'.$i.'_Text_Alignment'] = $unified_settings['text_alignment'];
				$RISP_Settings_Array['RISP_L'.$i.'_Title_Transition'] = $unified_settings['title_transition'];
				$RISP_Settings_Array['RISP_L'.$i.'_Description_Transition'] = $unified_settings['description_transition'];
				$RISP_Settings_Array['RISP_L'.$i.'_Thumbnail_Style_Color'] = $unified_settings['thumbnail_style_color'];
				$RISP_Settings_Array['RISP_L'.$i.'_Slider_Transition_Effect'] = $unified_settings['transition_effect'];
			}
			$RISP_Settings_Array['RISP_L3_Navigation_Position'] = $unified_settings['navigation_position'];
			$RISP_Settings_Array['RISP_L3_Thumbnail_Style'] = $unified_settings['thumbnail_style'];
			$RISP_Settings_Array['RISP_L3_Thumbnail_Width'] = $unified_settings['thumbnail_width'];
			$RISP_Settings_Array['RISP_L3_Thumbnail_Height'] = $unified_settings['thumbnail_height'];
			$RISP_Settings_Array['WRIS_L3_Navigation_Pointer_Color'] = $unified_settings['thumbnail_style_color'];
			
			$RISP_Settings_Array['RISP_L5_Sliding_Direction'] = $unified_settings['sliding_direction'];
			$RISP_Settings_Array['RISP_L5_Slider_Navigation_Content'] = $unified_settings['navigation_content'];
			$RISP_Settings_Array['RISP_L5_Slider_Navigation_Position'] = ! empty( $unified_settings['navigation_position_l5'] ) ? $unified_settings['navigation_position_l5'] : $unified_settings['navigation_position'];
			$RISP_Settings_Array['RISP_Read_More_Link_Color'] = isset( $unified_settings['read_more_link_color'] ) ? $unified_settings['read_more_link_color'] : '#ffffff';
			
			$RISP_Gallery_Settings = "WRIS_Gallery_Settings_".$PostID;
			update_post_meta($PostID, $RISP_Gallery_Settings, $RISP_Settings_Array);
		}
	}

	/**
	 * AJAX handler to fetch video thumbnail and metadata for YouTube and Vimeo
	 */
	public function urisp_ajax_fetch_video_thumbnail() {
		check_ajax_referer( 'urisp_ajax_nonce', 'nonce' );
		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized user.', 'ultimate-responsive-image-slider' ) ) );
		}

		$video_url = isset( $_POST['video_url'] ) ? esc_url_raw( wp_unslash( $_POST['video_url'] ) ) : '';
		$slider_id = isset( $_POST['slider_id'] ) ? absint( $_POST['slider_id'] ) : 0;

		if ( empty( $video_url ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a video URL first.', 'ultimate-responsive-image-slider' ) ) );
		}

		$provider    = '';
		$video_id    = '';
		$thumb_url   = '';
		$video_title = '';

		// Block YouTube and self-hosted with Pro notice
		if ( preg_match( '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/i', $video_url ) ) {
			wp_send_json_error( array(
				'is_pro_feature' => true,
				'message' => __( 'YouTube video integration is available in Ultimate Responsive Image Slider Pro. Free version supports Vimeo only.', 'ultimate-responsive-image-slider' )
			) );
		} elseif ( preg_match( '/\.(mp4|webm|ogg)($|\?)/i', $video_url ) || preg_match( '/\/wp-content\/uploads\//i', $video_url ) ) {
			wp_send_json_error( array(
				'is_pro_feature' => true,
				'message' => __( 'Self-Hosted MP4 / WebM video integration is available in Ultimate Responsive Image Slider Pro. Free version supports Vimeo only.', 'ultimate-responsive-image-slider' )
			) );
		}

		// Vimeo Match (Active in Free)
		if ( preg_match( '/(?:vimeo\.com\/(?:video\/)?)([0-9]+)/i', $video_url, $vimeo_matches ) ) {
			$provider = 'Vimeo';
			$video_id = $vimeo_matches[1];

			$vimeo_api = 'https://vimeo.com/api/oembed.json?url=' . urlencode( 'https://vimeo.com/' . $video_id );
			$response  = wp_remote_get( $vimeo_api, array( 'timeout' => 8 ) );
			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$vimeo_data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! empty( $vimeo_data['thumbnail_url'] ) ) {
					$thumb_url = $vimeo_data['thumbnail_url'];
					// Upgrade resolution from 640 to 1280 if applicable
					$thumb_url = str_replace( '_640', '_1280', $thumb_url );
				}
				if ( ! empty( $vimeo_data['title'] ) ) {
					$video_title = sanitize_text_field( $vimeo_data['title'] );
				}
			} else {
				// Fallback to direct URL in oEmbed
				$vimeo_api2 = 'https://vimeo.com/api/oembed.json?url=' . urlencode( $video_url );
				$response2  = wp_remote_get( $vimeo_api2, array( 'timeout' => 8 ) );
				if ( ! is_wp_error( $response2 ) && wp_remote_retrieve_response_code( $response2 ) === 200 ) {
					$vimeo_data2 = json_decode( wp_remote_retrieve_body( $response2 ), true );
					if ( ! empty( $vimeo_data2['thumbnail_url'] ) ) {
						$thumb_url = $vimeo_data2['thumbnail_url'];
					}
					if ( ! empty( $vimeo_data2['title'] ) ) {
						$video_title = sanitize_text_field( $vimeo_data2['title'] );
					}
				}
			}
		}

		if ( empty( $thumb_url ) ) {
			wp_send_json_error( array(
				'message' => __( 'Vimeo is active in the Free version. YouTube, Dailymotion, and Self-Hosted MP4 video sliders are available in Ultimate Responsive Image Slider Pro.', 'ultimate-responsive-image-slider' )
			) );
		}

		// Check if we already have an attachment in Media Library for this video to avoid duplicates
		$attachment_id = 0;
		$preview_url   = $thumb_url;

		// 1. Check by custom meta tags
		$existing_query = new WP_Query( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Necessary to find existing attachment by unique video metadata.
			'meta_query'     => array(
				array(
					'key'     => '_urisp_video_id',
					'value'   => $video_id,
					'compare' => '=',
				),
				array(
					'key'     => '_urisp_video_provider',
					'value'   => $provider,
					'compare' => '=',
				),
			),
		) );

		if ( ! empty( $existing_query->posts ) ) {
			$attachment_id = absint( $existing_query->posts[0] );
		} else {
			// 2. Fallback check: find existing attachment by filename pattern (e.g., previously uploaded)
			global $wpdb;
			$pattern  = '%' . $wpdb->esc_like( strtolower( $provider ) . '-' . $video_id ) . '%';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct lookup by attachment file name pattern fallback.
			$found_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id ASC LIMIT 1",
				$pattern
			) );
			if ( $found_id ) {
				$attachment_id = absint( $found_id );
				update_post_meta( $attachment_id, '_urisp_video_id', $video_id );
				update_post_meta( $attachment_id, '_urisp_video_provider', $provider );
			}
		}

		$is_reused = false;

		// If existing attachment was found, use its URLs
		if ( $attachment_id ) {
			$is_reused = true;
			$med_src  = wp_get_attachment_image_src( $attachment_id, 'medium' );
			$full_src = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( ! empty( $full_src[0] ) ) {
				$thumb_url = $full_src[0];
			}
			if ( ! empty( $med_src[0] ) ) {
				$preview_url = $med_src[0];
			} else {
				$preview_url = $thumb_url;
			}
		} else {
			// No existing attachment found: download once and save to WordPress Media Library
			if ( ! function_exists( 'download_url' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			if ( ! function_exists( 'media_handle_sideload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}

			if ( function_exists( 'download_url' ) && function_exists( 'media_handle_sideload' ) ) {
				$tmp = download_url( $thumb_url, 10 );
				if ( ! is_wp_error( $tmp ) ) {
					$file_name   = sanitize_file_name( strtolower( $provider ) . '-' . $video_id . '.jpg' );
					$file_array  = array(
						'name'     => $file_name,
						'tmp_name' => $tmp,
					);
					$sideload_id = media_handle_sideload( $file_array, $slider_id, ! empty( $video_title ) ? $video_title : $provider . ' Video Cover' );
					if ( ! is_wp_error( $sideload_id ) ) {
						$attachment_id = $sideload_id;
						update_post_meta( $attachment_id, '_urisp_video_id', $video_id );
						update_post_meta( $attachment_id, '_urisp_video_provider', $provider );

						$med_src  = wp_get_attachment_image_src( $attachment_id, 'medium' );
						$full_src = wp_get_attachment_image_src( $attachment_id, 'full' );
						if ( ! empty( $med_src[0] ) ) {
							$preview_url = $med_src[0];
						}
						if ( ! empty( $full_src[0] ) ) {
							$thumb_url = $full_src[0];
						}
					} else {
						wp_delete_file( $tmp );
					}
				}
			}
		}

		/* translators: %s: Video provider name */
		$msg_reused = sprintf( __( 'Loaded existing %s cover from Media Library!', 'ultimate-responsive-image-slider' ), $provider );
		/* translators: %s: Video provider name */
		$msg_fetched = sprintf( __( 'Cover thumbnail fetched from %s!', 'ultimate-responsive-image-slider' ), $provider );
		$success_msg = $is_reused ? $msg_reused : $msg_fetched;

		wp_send_json_success( array(
			'provider'      => $provider,
			'video_id'      => $video_id,
			'title'         => $video_title,
			'thumbnail_url' => $thumb_url,
			'preview_url'   => $preview_url,
			'attachment_id' => $attachment_id,
			'is_reused'     => $is_reused,
			'message'       => $success_msg,
		) );
	}
}

global $URISP;
$URISP = URIS_Free::forge();

/**
 * Hex Color code to RGB Color Code converter function
 */
if(!function_exists('URISPhex2rgb')) {
	function URISPhex2rgb($hex) {
		$hex = str_replace("#", "", $hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb; // returns an array with the rgb values
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

/**
 * URISP Short Code [URISP]
 */
require_once("shortcode.php");

/**
 * URISP Shortcode Button On Page/Post
 */

/**
 * True on admin screens that render the classic editor toolbar.
 *
 * @return bool
 */
function urisp_is_editor_screen() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen ) {
		return false;
	}

	if ( in_array( (string) $screen->base, array( 'post', 'post-new' ), true ) ) {
		return true;
	}

	return ! empty( $screen->post_type ) && post_type_supports( (string) $screen->post_type, 'editor' );
}

/**
 * Thickbox is needed by the shortcode popup on every screen that shows the button.
 */
add_action( 'admin_enqueue_scripts', 'urisp_maybe_add_thickbox' );
function urisp_maybe_add_thickbox() {
	if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) || ! urisp_is_editor_screen() ) {
		return;
	}

	add_thickbox();
}

/**
 * Adds the slider shortcode button above the classic editor.
 *
 * Hooked on `media_buttons`, the current action. The old
 * `media_buttons_context` filter is deprecated and no longer applied.
 */
add_action( 'media_buttons', 'urisp_add_custom_button', 20 );
if ( ! function_exists( 'add_urisp_custom_button' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Backward compatibility wrapper.
	function add_urisp_custom_button( $editor_id = 'content' ) {
		urisp_add_custom_button( $editor_id );
	}
}
function urisp_add_custom_button( $editor_id = 'content' ) {
	if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) {
		return;
	}

	$img          = plugins_url( 'assets/img/slideshow_icon.png', __FILE__ );
	$title        = __( 'Select Ultimate Responsive Image Slider Shortcode to insert into post', 'ultimate-responsive-image-slider' );
	$container_id = 'URISP';
	$href         = '#TB_inline?width=400&height=200&inlineId=' . $container_id;
	?>
	<a class="button button-primary thickbox" title="<?php echo esc_attr( $title ); ?>" href="<?php echo esc_attr( $href ); ?>">
		<span class="wp-media-buttons-icon" style="background: url(<?php echo esc_url( $img ); ?>); background-repeat: no-repeat; background-position: left bottom;"></span>
		<?php esc_html_e( 'Ultimate Responsive Image Slider Shortcode', 'ultimate-responsive-image-slider' ); ?>
	</a>
	<?php
}

add_action( 'admin_footer', 'urisp_add_inline_popup_content' );
if ( ! function_exists( 'add_urisp_inline_popup_content' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Backward compatibility wrapper.
	function add_urisp_inline_popup_content() {
		urisp_add_inline_popup_content();
	}
}
function urisp_add_inline_popup_content() {
	if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) || ! urisp_is_editor_screen() ) {
		return;
	}

	$sliders = get_posts( array(
		'post_type'      => 'ris_gallery',
		'posts_per_page' => 100,
		'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private' ),
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#urisp-insert').on('click', function() {
			var id = jQuery('#urisp-select option:selected').val();
			if (id) {
				window.send_to_editor('<p>[URISP id=' + id + ']</p>');
			}
			tb_remove();
		});
	});
	</script>

	<div id="URISP" style="display:none;">
		<h3><?php esc_html_e( 'Select Ultimate Responsive Image Slider Shortcode To Insert Into Content', 'ultimate-responsive-image-slider' ); ?></h3>
		<?php if ( ! empty( $sliders ) ) : ?>
			<select id="urisp-select">
				<?php foreach ( $sliders as $slider ) : ?>
					<option value="<?php echo esc_attr( absint( $slider->ID ) ); ?>"><?php echo esc_html( get_the_title( $slider->ID ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="button primary" id="urisp-insert"><?php esc_html_e( 'Insert Selected Shortcode', 'ultimate-responsive-image-slider' ); ?></button>
		<?php else : ?>
			<p><?php esc_html_e( 'No Slider Found', 'ultimate-responsive-image-slider' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
?>