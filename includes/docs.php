<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * URISP Documentation & User Tutorial Page
 * 
 * Provides a comprehensive, interactive in-dashboard documentation and tutorial
 * covering all slider configurations, layouts, content sources, and global settings.
 *
 * @package Ultimate Responsive Image Slider
 * @version 8.3.0
 */
class URISP_Docs {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ), 25 );
	}

	public static function add_admin_menu() {
		$page = add_submenu_page(
			'edit.php?post_type=ris_gallery',
			__( 'Documentation & Guide', 'ultimate-responsive-image-slider' ),
			__( 'Documentation', 'ultimate-responsive-image-slider' ),
			'manage_urisp_sliders',
			'urisp_docs',
			array( __CLASS__, 'render_page' )
		);

		add_action( 'admin_print_styles-' . $page, array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function enqueue_assets() {
		$ver = defined( 'RISP_PLUGIN_VER' ) ? RISP_PLUGIN_VER : '4.0.0';
		$css_file = defined( 'RISP_PLUGIN_DIR_PATH' ) ? RISP_PLUGIN_DIR_PATH . 'assets/css/urisp-modern.css' : '';
		$modern_ver = ( $css_file && file_exists( $css_file ) ) ? filemtime( $css_file ) : $ver;
		wp_enqueue_style( 'wpfrank-urisp-modern-css', RISP_PLUGIN_URL . 'assets/css/urisp-modern.css', array(), $modern_ver );
		wp_enqueue_style( 'dashicons' );
	}

	public static function render_page() {
		if ( ! urisp_current_user_can( 'manage_urisp_sliders' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'ultimate-responsive-image-slider' ) );
		}
		?>
		<div class="urisp-docs-page-wrapper urisp-global-fullwidth" style="width: calc(100% - 20px); max-width: none; margin: 20px 20px 20px 0; padding: 28px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; box-sizing: border-box; overflow: visible !important; position: relative; min-height: 450px;">
			<!-- Docs Page Loader -->
			<div class="urisp-settings-loader">
				<div class="urisp-settings-loader-spinner"></div>
			</div>

			<div class="urisp-docs-main-content" style="display: none;">
			<!-- Header -->
			<div class="urisp-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
				<div>
					<h1 style="margin: 0 0 6px 0; font-size: 24px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px;">
						<span class="dashicons dashicons-book-alt" style="font-size: 28px; width:28px; height:28px; color: #6366f1;"></span>
						<?php esc_html_e( 'Ultimate Responsive Image Slider — Documentation', 'ultimate-responsive-image-slider' ); ?>
						<span style="font-size: 12px; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 6px; letter-spacing: 0.3px;">v<?php echo esc_html( RISP_PLUGIN_VER ); ?></span>
					</h1>
					<p style="margin: 0; color: #64748b; font-size: 13px;">
						<?php esc_html_e( 'Comprehensive user guide and detailed configuration reference for all slider layouts, settings, and features.', 'ultimate-responsive-image-slider' ); ?>
					</p>
				</div>
				<div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
					<a href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider/reviews/#new-post" target="_blank" rel="noopener noreferrer" class="button button-secondary" style="height: 36px; line-height: 34px; padding: 0 14px; border-radius: 6px; font-weight: 600; color: #b45309; background: #fffbeb; border-color: #fde68a; display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 16px; width: 16px; height: 16px; line-height: 1; margin: 0;"></span>
						<?php esc_html_e( 'Rate 5-Stars (Free)', 'ultimate-responsive-image-slider' ); ?>
					</a>
					<a href="https://wpfrank.com/contact/" target="_blank" rel="noopener noreferrer" class="button button-secondary" style="height: 36px; line-height: 34px; padding: 0 14px; border-radius: 6px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-email-alt" style="font-size: 16px; width: 16px; height: 16px; line-height: 1; margin: 0; color: #64748b;"></span>
						<?php esc_html_e( 'Contact Page', 'ultimate-responsive-image-slider' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ris_gallery' ) ); ?>" class="button button-primary" style="background: #6366f1; border-color: #4f46e5; height: 36px; line-height: 34px; padding: 0 16px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-plus-alt2" style="font-size: 16px; width: 16px; height: 16px; line-height: 1; margin: 0;"></span>
						<?php esc_html_e( 'Create New Slider', 'ultimate-responsive-image-slider' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery' ) ); ?>" class="button button-secondary" style="height: 36px; line-height: 34px; padding: 0 14px; border-radius: 6px; font-weight: 500;">
						<?php esc_html_e( 'All Sliders', 'ultimate-responsive-image-slider' ); ?>
					</a>
				</div>
			</div>

			<!-- Search Bar -->
			<div class="urisp-docs-search-bar" style="margin-bottom: 28px; position: relative; width: 100%;">
				<svg class="urisp-search-icon" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; pointer-events: none; z-index: 5; display: block;">
					<circle cx="11" cy="11" r="8"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
				<input type="text" id="urisp-docs-search" placeholder="<?php esc_attr_e( 'Search documentation topics, settings, or shortcodes (e.g. Ken Burns, watermark, layout 1, video, autoplay)...', 'ultimate-responsive-image-slider' ); ?>" style="box-sizing: border-box !important; width: 100% !important; height: 48px !important; padding: 0 44px 0 48px !important; border-radius: 10px !important; border: 2px solid #e2e8f0 !important; font-size: 14px !important; background: #ffffff !important; outline: none !important; transition: all 0.2s ease !important;" oninput="urispDocsFilter(this.value)">
				<button type="button" id="urisp-docs-search-clear" onclick="urispClearSearch()" style="display: none; position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: #f1f5f9; border: 1px solid #cbd5e1; cursor: pointer; color: #64748b; width: 24px; height: 24px; border-radius: 50%; padding: 0; align-items: center; justify-content: center;" title="<?php esc_attr_e( 'Clear search', 'ultimate-responsive-image-slider' ); ?>">
					<span class="dashicons dashicons-no-alt" style="font-size: 14px; width: 14px; height: 14px; line-height: 1; display: block; margin: 0 auto;"></span>
				</button>
			</div>

			<!-- Main Layout: Sidebar & Content -->
			<div class="urisp-docs-layout" style="display: flex; gap: 28px; align-items: stretch; position: relative; overflow: visible !important;">
				
				<!-- Left Column Wrapper: Full height of docs body to anchor sticky sliding -->
				<div class="urisp-docs-sidebar-col" style="width: 270px; flex-shrink: 0;">
					<div class="urisp-docs-sidebar" style="position: -webkit-sticky; position: sticky; top: 52px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 10px; max-height: calc(100vh - 75px); overflow-y: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.02); z-index: 10;">
						<div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; padding: 6px 12px; letter-spacing: 0.5px;">
							<?php esc_html_e( 'Table of Contents', 'ultimate-responsive-image-slider' ); ?>
						</div>
						<nav id="urisp-docs-nav" style="display: flex; flex-direction: column; gap: 3px;">
							<a href="#doc-quick-start" class="urisp-doc-nav-item active"><span class="dashicons dashicons-controls-play"></span> <?php esc_html_e( 'Quick Start & Embed', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-layouts" class="urisp-doc-nav-item"><span class="dashicons dashicons-layout"></span> <?php esc_html_e( 'The 5 Slider Layouts', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-content-source" class="urisp-doc-nav-item"><span class="dashicons dashicons-database"></span> <?php esc_html_e( 'Content Sources & Media', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-video-slides" class="urisp-doc-nav-item"><span class="dashicons dashicons-video-alt3"></span> <?php esc_html_e( 'Video Slide Integration', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-general-settings" class="urisp-doc-nav-item"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'General Settings', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-captions" class="urisp-doc-nav-item"><span class="dashicons dashicons-editor-textcolor"></span> <?php esc_html_e( 'Titles, Captions & CTAs', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-navigation" class="urisp-doc-nav-item"><span class="dashicons dashicons-marker"></span> <?php esc_html_e( 'Navigation & Arrows', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-colors-fonts" class="urisp-doc-nav-item"><span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Colors & Typography', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-lightbox" class="urisp-doc-nav-item"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Lightbox & Fullscreen', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-watermark" class="urisp-doc-nav-item"><span class="dashicons dashicons-shield"></span> <?php esc_html_e( 'Watermark Protection', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-global-settings" class="urisp-doc-nav-item"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Global Settings & RBAC', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-analytics" class="urisp-doc-nav-item"><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e( 'Analytics Dashboard', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-cheatsheet" class="urisp-doc-nav-item"><span class="dashicons dashicons-editor-code"></span> <?php esc_html_e( 'Shortcodes & Dev API', 'ultimate-responsive-image-slider' ); ?></a>
							<a href="#doc-faqs" class="urisp-doc-nav-item"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e( 'Troubleshooting & FAQs', 'ultimate-responsive-image-slider' ); ?></a>
						</nav>
						<div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 6px;">
							<div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; padding: 2px 10px 4px; letter-spacing: 0.5px;">
								<?php esc_html_e( 'Community & Support', 'ultimate-responsive-image-slider' ); ?>
							</div>
							<a href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider/reviews/#new-post" target="_blank" rel="noopener noreferrer" class="urisp-doc-action-item urisp-doc-rate-btn" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 6px; text-decoration: none; font-size: 12.5px; font-weight: 600; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; transition: all 0.15s ease;">
								<span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 16px; width: 16px; height: 16px; line-height: 1;"></span>
								<span><?php esc_html_e( 'Rate 5-Stars (Free)', 'ultimate-responsive-image-slider' ); ?></span>
								<span class="dashicons dashicons-external" style="font-size: 12px; width: 12px; height: 12px; line-height: 1; margin-left: auto; opacity: 0.5;"></span>
							</a>
							<a href="https://wpfrank.com/contact/" target="_blank" rel="noopener noreferrer" class="urisp-doc-action-item urisp-doc-contact-btn" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 6px; text-decoration: none; font-size: 12.5px; font-weight: 600; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; transition: all 0.15s ease;">
								<span class="dashicons dashicons-email-alt" style="color: #3b82f6; font-size: 16px; width: 16px; height: 16px; line-height: 1;"></span>
								<span><?php esc_html_e( 'Contact Page', 'ultimate-responsive-image-slider' ); ?></span>
								<span class="dashicons dashicons-external" style="font-size: 12px; width: 12px; height: 12px; line-height: 1; margin-left: auto; opacity: 0.5;"></span>
							</a>
						</div>
					</div>
				</div>

				<!-- Right Main Content -->
				<div class="urisp-docs-body" style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 32px;">

					<!-- SECTION 1: Quick Start -->
					<section id="doc-quick-start" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-controls-play" style="color:#6366f1;"></span> <?php esc_html_e( 'Quick Start Guide', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Getting started with Ultimate Responsive Image Slider takes only a few clicks:', 'ultimate-responsive-image-slider' ); ?></p>
							
							<div class="urisp-step-grid">
								<div class="urisp-step-box">
									<div class="urisp-step-num">1</div>
									<h4><?php esc_html_e( 'Create a Slider', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Click "Add New Image Slider" in the admin menu and give your slider a title.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<div class="urisp-step-num">2</div>
									<h4><?php esc_html_e( 'Add Content', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Drag & drop photos, paste YouTube/Vimeo URLs, or select Dynamic WordPress Posts / WooCommerce Products.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<div class="urisp-step-num">3</div>
									<h4><?php esc_html_e( 'Select Layout', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Go to the Layouts tab and pick 1 of the 5 layouts (Standard, Grid Lightbox, Thumbnails, Filmstrip, or Carousel).', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<div class="urisp-step-num">4</div>
									<h4><?php esc_html_e( 'Publish & Embed', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Copy the generated shortcode [URISP id=XXX] or use the Gutenberg block / Elementor widget.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
							</div>

							<!-- Detailed Publishing Instructions across all Editors -->
							<h3 style="margin-top: 28px; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
								<span class="dashicons dashicons-editor-paste-text" style="color: #6366f1;"></span>
								<?php esc_html_e( 'How to Publish & Embed Your Slider in Any Editor', 'ultimate-responsive-image-slider' ); ?>
							</h3>
							<p style="color: #64748b; font-size: 13.5px; margin-bottom: 18px;">
								<?php esc_html_e( 'UR Image Slider integrates seamlessly with modern WordPress block workflows, popular drag-and-drop page builders, and classic editors alike:', 'ultimate-responsive-image-slider' ); ?>
							</p>

							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
								<!-- Gutenberg Block Editor Card -->
								<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; display: flex; flex-direction: column; gap: 10px;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-block-default" style="color: #6366f1; font-size: 20px; width: 20px; height: 20px;"></span>
										<strong style="color: #0f172a; font-size: 14px;"><?php esc_html_e( 'Gutenberg (Block Editor)', 'ultimate-responsive-image-slider' ); ?></strong>
										<span style="margin-left: auto; font-size: 11px; background: #e0e7ff; color: #4338ca; padding: 2px 7px; border-radius: 4px; font-weight: 700;"><?php esc_html_e( 'Recommended', 'ultimate-responsive-image-slider' ); ?></span>
									</div>
									<ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #475569; line-height: 1.6;">
										<li><strong><?php esc_html_e( 'Native Block:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Click the (+) Block Inserter and search for "Ultimate Responsive Image Slider" (or type /urisp).', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Dropdown Selection:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Select any slider from the dropdown. Live ServerSideRender instantly displays the real slides and navigation inside the editor!', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Toolbar Controls:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Click the Edit button in the block toolbar or inspector to quickly modify the slider settings.', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Shortcode Block:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Alternatively, add a core Shortcode block and paste your [URISP id=XXX] code.', 'ultimate-responsive-image-slider' ); ?></li>
									</ul>
								</div>

								<!-- Elementor & Divi Card -->
								<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; display: flex; flex-direction: column; gap: 10px;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-screenoptions" style="color: #ec4899; font-size: 20px; width: 20px; height: 20px;"></span>
										<strong style="color: #0f172a; font-size: 14px;"><?php esc_html_e( 'Elementor, Divi & Page Builders', 'ultimate-responsive-image-slider' ); ?></strong>
									</div>
									<ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #475569; line-height: 1.6;">
										<li><strong><?php esc_html_e( 'Elementor Widget:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Search for the "Ultimate Responsive Image Slider" widget in Elementor panel, drag it into any section, and pick your slider.', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Divi Builder:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Insert a Code or Shortcode module and paste your [URISP id=XXX] shortcode.', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Beaver / WPBakery:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Use any HTML, Code, or Shortcode element to embed seamlessly.', 'ultimate-responsive-image-slider' ); ?></li>
									</ul>
								</div>

								<!-- Classic Editor Card -->
								<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; display: flex; flex-direction: column; gap: 10px;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-edit" style="color: #0ea5e9; font-size: 20px; width: 20px; height: 20px;"></span>
										<strong style="color: #0f172a; font-size: 14px;"><?php esc_html_e( 'Classic Editor & TinyMCE', 'ultimate-responsive-image-slider' ); ?></strong>
									</div>
									<ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #475569; line-height: 1.6;">
										<li><strong><?php esc_html_e( 'Toolbar Media Button:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Click the "Add Slider" button located directly above the visual editor toolbar.', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Modal Insert:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Choose your slider from the popup list and click "Insert Selected Shortcode".', 'ultimate-responsive-image-slider' ); ?></li>
										<li><strong><?php esc_html_e( 'Direct Paste:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Paste [URISP id=XXX] directly into Visual or Text mode at the desired cursor position.', 'ultimate-responsive-image-slider' ); ?></li>
									</ul>
								</div>

								<!-- PHP Theme Template Card -->
								<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; display: flex; flex-direction: column; gap: 10px;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<span class="dashicons dashicons-editor-code" style="color: #10b981; font-size: 20px; width: 20px; height: 20px;"></span>
										<strong style="color: #0f172a; font-size: 14px;"><?php esc_html_e( 'PHP Templates (Theme Devs)', 'ultimate-responsive-image-slider' ); ?></strong>
									</div>
									<p style="margin: 0; font-size: 12.5px; color: #475569; line-height: 1.6;">
										<?php esc_html_e( 'Embed sliders directly in template files (e.g. header.php, front-page.php) using WordPress do_shortcode function:', 'ultimate-responsive-image-slider' ); ?>
									</p>
									<code style="display: block; padding: 8px 10px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11.5px; color: #0f172a;">
										&lt;?php echo do_shortcode('[URISP id=123]'); ?&gt;
									</code>
								</div>
							</div>

							<!-- Embed Syntax Table -->
							<h3 style="margin-top:20px; font-size:15px; font-weight: 700; color: #1e293b;"><?php esc_html_e( 'Embedding Syntax Reference', 'ultimate-responsive-image-slider' ); ?></h3>
							<table class="urisp-doc-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Method', 'ultimate-responsive-image-slider' ); ?></th>
										<th><?php esc_html_e( 'Syntax / Instruction', 'ultimate-responsive-image-slider' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><strong><?php esc_html_e( 'Block Editor (Gutenberg Block)', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Insert the "Ultimate Responsive Image Slider" block, choose your slider from the dropdown, and see a live ServerSideRender preview.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Shortcode (Standard)', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><code>[URISP id=123]</code> <?php esc_html_e( '(Replace 123 with your slider ID)', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Shortcode with Layout Override', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><code>[URISP id=123 layout=3]</code> <?php esc_html_e( '(Forces Layout 3 without changing saved settings in backend)', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'PHP Template Code', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><code>&lt;?php echo do_shortcode('[URISP id=123]'); ?&gt;</code></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Elementor Page Builder', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Drag the "Ultimate Responsive Image Slider" widget into any column or section in Elementor.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</section>

					<!-- SECTION 2: The 5 Layouts -->
					<section id="doc-layouts" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-layout" style="color:#6366f1;"></span> <?php esc_html_e( 'The 5 Slider Layouts Explained', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Ultimate Responsive Image Slider includes pre-built layout architectures designed for different web presentation needs:', 'ultimate-responsive-image-slider' ); ?></p>
							
							<div class="urisp-layout-cards">
								<div class="urisp-layout-item">
									<div class="urisp-badge" style="background:#e0e7ff; color:#4338ca;"><?php esc_html_e( 'Layout 1', 'ultimate-responsive-image-slider' ); ?></div>
									<h4><?php esc_html_e( 'Standard Hero Slider with Detailed Navigation Tabs', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Full-width or custom-width hero showcase with slide titles & descriptions displayed in interactive bottom tabs with pointer indicators. Can be toggled to clean minimal hero by turning off Detailed Navigation in the Navigation tab.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-layout-item">
									<div class="urisp-badge" style="background:#fef3c7; color:#b45309;"><?php esc_html_e( 'Layout 2', 'ultimate-responsive-image-slider' ); ?></div>
									<h4><?php esc_html_e( 'Responsive Grid Lightbox Gallery', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Displays images in a dynamic masonry/grid photo album. When visitors click any image, a high-resolution Lightbox (FancyBox or LightGallery) opens with zoom, swipe, and thumbnail filmstrip preview.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-layout-item">
									<div class="urisp-badge" style="background:#d1fae5; color:#047857;"><?php esc_html_e( 'Layout 3', 'ultimate-responsive-image-slider' ); ?></div>
									<h4><?php esc_html_e( 'Classic Hero Slider with Image Thumbnails Below', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'A high-converting eCommerce and portfolio standard. Large main showcase with a smooth, synchronized thumbnail carousel underneath showing miniature previews of all slides.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-layout-item">
									<div class="urisp-badge" style="background:#e0f2fe; color:#0369a1;"><?php esc_html_e( 'Layout 4', 'ultimate-responsive-image-slider' ); ?></div>
									<h4><?php esc_html_e( 'Filmstrip Header Showcase', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Displays an immersive horizontal filmstrip at the top/bottom where multiple slides are visible simultaneously with smooth continuous glide transitions.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-layout-item">
									<div class="urisp-badge" style="background:#f3e8ff; color:#7e22ce;"><?php esc_html_e( 'Layout 5', 'ultimate-responsive-image-slider' ); ?></div>
									<h4><?php esc_html_e( 'Dual-Axis Carousel (Vertical / Horizontal Navigation)', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Premium split-layout displaying the main slide beside a vertical or horizontal scrolling thumbnail carousel. Excellent for product showcases, case studies, and editorial articles.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
							</div>
						</div>
					</section>

					<!-- SECTION 3: Content Source -->
					<section id="doc-content-source" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-database" style="color:#6366f1;"></span> <?php esc_html_e( 'Content Sources (Manual vs Dynamic)', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'The first tab in your slider editor allows switching seamlessly between manual uploads and automated WordPress dynamic feeds.', 'ultimate-responsive-image-slider' ); ?></p>

							<h3 style="font-size:15px; margin-top:16px;">1. <?php esc_html_e( 'Manual Uploads (Images & Videos)', 'ultimate-responsive-image-slider' ); ?></h3>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Drag-and-Drop Uploader:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Drag images directly into the upload area or click to open the WordPress Media Library.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Bulk Management:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Click "Bulk Select" in the toolbar to select all, invert selection, and delete multiple slides at once.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Slide Reordering:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Drag slides by their handle icon to rearrange their order.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Per-Slide Settings:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Expand any slide to customize its Title, Rich Text Description, External Link (URL & Target), Alt text, CTA Button text, and custom watermark override.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>

							<h3 style="font-size:15px; margin-top:20px;">2. <?php esc_html_e( 'Dynamic WordPress Content (Posts, Pages & WooCommerce)', 'ultimate-responsive-image-slider' ); ?></h3>
							<p><?php esc_html_e( 'Automatically populates slides from your existing WordPress database without manual uploading:', 'ultimate-responsive-image-slider' ); ?></p>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Post Types:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Choose Posts, Pages, WooCommerce Products, or custom post types (Portfolio, Events, etc.).', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Taxonomy Filtering:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Filter slides by Category, Tag, or custom taxonomy (e.g. "Featured Products").', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Slide Count & Ordering:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Fetch top N posts sorted by Publication Date, Title, Menu Order, ID, or Random.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Automatic Mapping:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'The featured image becomes the slide background, post title becomes slide title, excerpt becomes description, and the post permalink becomes the slide link.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>
						</div>
					</section>

					<!-- SECTION 4: Video Slides -->
					<section id="doc-video-slides" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-video-alt3" style="color:#6366f1;"></span> <?php esc_html_e( 'Video Slide Integration', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'You can mix image slides and video slides in the same gallery seamlessly.', 'ultimate-responsive-image-slider' ); ?></p>
							
							<div style="background: #f8fafc; border-left: 4px solid #6366f1; padding: 14px 18px; border-radius: 6px; margin: 16px 0;">
								<strong><?php esc_html_e( 'Supported Video Formats:', 'ultimate-responsive-image-slider' ); ?></strong>
								<ul style="margin: 8px 0 0 16px; padding: 0;">
									<li><strong>YouTube:</strong> <code>https://www.youtube.com/watch?v=VIDEO_ID</code> or short URLs <code>https://youtu.be/VIDEO_ID</code></li>
									<li><strong>Vimeo:</strong> <code>https://vimeo.com/VIDEO_ID</code></li>
									<li><strong>Self-Hosted MP4:</strong> Direct URL to an MP4 video in your WordPress Media Library.</li>
								</ul>
							</div>

							<h4><?php esc_html_e( 'Automatic Thumbnail Cover Fetching:', 'ultimate-responsive-image-slider' ); ?></h4>
							<p><?php esc_html_e( 'When you paste a YouTube or Vimeo link and click "Fetch Cover Thumbnail", the plugin connects to the provider API, downloads the high-definition video cover, and sets it as the slide poster image.', 'ultimate-responsive-image-slider' ); ?></p>

							<h4><?php esc_html_e( 'Inline Playback vs Lightbox:', 'ultimate-responsive-image-slider' ); ?></h4>
							<p><?php esc_html_e( 'Videos feature a play icon badge and overlay. Clicking play automatically pauses the slider autoplay and starts the video directly within the slide frame with responsive aspect ratio.', 'ultimate-responsive-image-slider' ); ?></p>
						</div>
					</section>

					<!-- SECTION 5: General Settings -->
					<section id="doc-general-settings" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-admin-settings" style="color:#6366f1;"></span> <?php esc_html_e( 'General Settings Reference', 'ultimate-responsive-image-slider' ); ?></h2>
							<table class="urisp-doc-table">
								<thead>
									<tr>
										<th style="width: 25%;"><?php esc_html_e( 'Setting Name', 'ultimate-responsive-image-slider' ); ?></th>
										<th><?php esc_html_e( 'Description & Best Practices', 'ultimate-responsive-image-slider' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><strong><?php esc_html_e( 'Auto Slideshow', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Choose between Disabled, Autoplay Always, or Autoplay with Pause on Hover (recommended for accessibility).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Transition Speed', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Delay between automatic slide advances in milliseconds (e.g. 5000 = 5 seconds).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Transition Effect', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Slide Animation (horizontal translate) or Fade Animation (smooth cross-dissolve opacity).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Width & Height Modes', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Choose 100% fluid responsive, custom pixel width (e.g. 1100px), or Full Width bleed. Height can be custom fixed pixels or auto-proportional.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Image Scale Mode', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><strong>Cover:</strong> <?php esc_html_e( 'Fills the entire slide area without letterboxing (recommended).', 'ultimate-responsive-image-slider' ); ?><br><strong>Contain:</strong> <?php esc_html_e( 'Fits the whole image inside the slide without cropping.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Slide Ordering', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Ascending (ASC), Descending (DESC), or Shuffle (randomizes slides on every page load).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Ken Burns Effect', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Adds a slow, cinematic zoom & pan effect to slide images with Subtle, Medium, or Dramatic intensity presets.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Skin Presets', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Quick visual styles: Default, Minimal, Dark Mode, Elegant, and Cyber.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Image Source Size', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Allows choosing whether WordPress loads Full (original), Large (1024px), or Medium Large (768px). Using Large dramatically speeds up page loading for heavy uploads.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</section>

					<!-- SECTION 6: Captions & CTAs -->
					<section id="doc-captions" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-editor-textcolor" style="color:#6366f1;"></span> <?php esc_html_e( 'Slide Captions, Titles & CTA Buttons', 'ultimate-responsive-image-slider' ); ?></h2>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Title & Description Toggles:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Enable or disable caption overlays globally across the slider.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Caption Alignment:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Position the card at Top Left, Top Center, Top Right, Center, Bottom Left, Bottom Center, or Bottom Right.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Layer Animation Transitions:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Choose animation direction for titles and descriptions entering the frame (Up, Down, Left, or Right).', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Call To Action (CTA) Button:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Configure button style (Filled, Outline, Minimal), button background color, text color, and hover states.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Slide Link Behavior:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Choose whether clicking the slide opens the link on the entire slide image or only when clicking the CTA button.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>
						</div>
					</section>

					<!-- SECTION 7: Navigation & Controls -->
					<section id="doc-navigation" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-marker" style="color:#6366f1;"></span> <?php esc_html_e( 'Navigation Controls & Accessibility', 'ultimate-responsive-image-slider' ); ?></h2>
							<table class="urisp-doc-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Control', 'ultimate-responsive-image-slider' ); ?></th>
										<th><?php esc_html_e( 'Options & Usage', 'ultimate-responsive-image-slider' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><strong><?php esc_html_e( 'Detailed Navigation', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'In Layout 1, toggles the bottom slide title/description navigation tabs with pointer arrows. Can be disabled for a minimal hero.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Navigation Bullets', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Enables pagination dots. Choose between Circle or Square shapes, and set bullet size from 6px to 20px.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Arrow Style & Shape', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Styles: Chevron, Angle, Caret, Arrow-Long, Triangle, and Modern Dot. Shapes: Circle, Curved (rounded rectangle), or Sharp (square).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Keyboard Accessibility', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'WCAG 2.1 AA compliant. Visitors can navigate slides using Left and Right Arrow keys, and activate buttons using Enter or Space.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e( 'Touch Swipe Sensitivity', 'ultimate-responsive-image-slider' ); ?></strong></td>
										<td><?php esc_html_e( 'Configure swipe distance threshold in pixels for mobile swipe sensitivity (default 50px).', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</section>

					<!-- SECTION 8: Colors & Typography -->
					<section id="doc-colors-fonts" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-art" style="color:#6366f1;"></span> <?php esc_html_e( 'Colors, Typography, Custom Fonts & Accessibility Presets', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Ultimate Responsive Image Slider gives you total control over typography and color styling, with native support for proprietary font file uploads and pre-configured accessibility/dyslexia-friendly typography.', 'ultimate-responsive-image-slider' ); ?></p>
							
							<div class="urisp-step-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 20px;">
								<div class="urisp-step-box">
									<h4><span class="dashicons dashicons-upload" style="color: #6366f1; vertical-align: middle;"></span> <?php esc_html_e( 'Custom Font Uploads', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Upload custom web fonts (.woff2, .woff, .ttf, .otf) directly in Global Settings or via the "+ Upload Font" shortcut in any slider. The plugin handles secure MIME whitelisting, storage, and automatic CSS @font-face generation.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<h4><span class="dashicons dashicons-universal-access" style="color: #2563eb; vertical-align: middle;"></span> <?php esc_html_e( 'Dyslexia & Accessibility Presets', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Built-in support for OpenDyslexic (weighted bottoms prevent letter flipping), Atkinson Hyperlegible (Braille Institute high-contrast font), Lexend (scientifically tuned reading fluency), and Comic Neue.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<h4><span class="dashicons dashicons-visibility" style="color: #10b981; vertical-align: middle;"></span> <?php esc_html_e( 'Live Typography Preview', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Interactive typography preview cards render sample sentences live in the admin interface as you select different fonts or upload new styles, ensuring you see the exact visual presentation before publishing.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
							</div>

							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Caption Colors:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Title text color, Description text color, Title background, Description background, and Background Opacity (from 0.0 transparent to 1.0 solid).', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Slide Overlay Font Sizes:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Fine-tune title size (10px - 32px) and description size (6px - 24px) for desktop and mobile screens.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Control Colors:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Arrow color, Arrow background color, Active/Inactive bullet colors, and Thumbnail active border colors.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>
						</div>
					</section>

					<!-- SECTION 9: Lightbox & Fullscreen -->
					<section id="doc-lightbox" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-visibility" style="color:#6366f1;"></span> <?php esc_html_e( 'Lightbox & Fullscreen Preview', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Under the Advanced Settings tab, configure fullscreen presentation options:', 'ultimate-responsive-image-slider' ); ?></p>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Lightbox Engines:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Choose between FancyBox (classic, lightweight) or LightGallery (modern, mobile-optimized with pinch-to-zoom and thumbnail strip).', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Fullscreen Button:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Enables an expand button in the corner to toggle full-browser immersive viewing.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Reset Settings:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Safely restores all slider options to official plugin defaults with a single click without deleting your uploaded images.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>
						</div>
					</section>

					<!-- SECTION 10: Watermark -->
					<section id="doc-watermark" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-shield" style="color:#6366f1;"></span> <?php esc_html_e( 'Dual-Layer Watermark Protection Engine', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Protect your photography and digital assets from unauthorized image theft with server-side watermark stamping (fully supported for both manual image uploads and dynamic WordPress / WooCommerce feeds):', 'ultimate-responsive-image-slider' ); ?></p>
							
							<div class="urisp-step-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
								<div class="urisp-step-box">
									<h4><?php esc_html_e( 'Text Watermarks', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Apply copyright notices, brand names, or URLs. Customize text font size, color, opacity (0-100%), and 9-point anchor alignment.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<h4><?php esc_html_e( 'Logo / PNG Watermarks', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Upload transparent PNG or SVG brand logos with auto-scaling to keep watermarks proportional across images.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<h4><?php esc_html_e( 'Server-Side Caching', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Watermarked versions are generated via PHP GD and cached in wp-content/uploads/urisp-watermarks/ for zero performance impact.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
								<div class="urisp-step-box">
									<h4><?php esc_html_e( 'Purge Cache', 'ultimate-responsive-image-slider' ); ?></h4>
									<p><?php esc_html_e( 'Click "Purge Watermark Cache" whenever you change your logo or font size to instantly regenerate all watermarked images.', 'ultimate-responsive-image-slider' ); ?></p>
								</div>
							</div>
						</div>
					</section>

					<!-- SECTION 11: Global Settings & RBAC -->
					<section id="doc-global-settings" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-admin-generic" style="color:#6366f1;"></span> <?php esc_html_e( 'Global Settings, Performance & Role Permissions', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Located at UR Image Slider → Global Settings:', 'ultimate-responsive-image-slider' ); ?></p>
							
							<h4>1. <?php esc_html_e( 'Performance & Optimization', 'ultimate-responsive-image-slider' ); ?></h4>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Lazy Loading:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Defers off-screen slide image loading until the slide approaches, saving mobile bandwidth and improving Google PageSpeed / Core Web Vitals.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Conditional Loading:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Prevents slider CSS and JavaScript from loading on pages that do not contain an active slider.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>

							<h4>2. <?php esc_html_e( 'Role-Based Access Control (RBAC)', 'ultimate-responsive-image-slider' ); ?></h4>
							<p><?php esc_html_e( 'Grant slider management to non-administrators (e.g. Editors, Authors) without exposing dangerous site-wide settings:', 'ultimate-responsive-image-slider' ); ?></p>
							<table class="urisp-doc-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Capability', 'ultimate-responsive-image-slider' ); ?></th>
										<th><?php esc_html_e( 'Description', 'ultimate-responsive-image-slider' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><code>manage_urisp_sliders</code></td>
										<td><?php esc_html_e( 'Permission to create, edit, duplicate, and delete sliders.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><code>view_urisp_analytics</code></td>
										<td><?php esc_html_e( 'Permission to view slider performance metrics, impressions, and CTR charts.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
									<tr>
										<td><code>manage_urisp_settings</code></td>
										<td><?php esc_html_e( 'Permission to configure global settings, role matrix, and run Import/Export backups.', 'ultimate-responsive-image-slider' ); ?></td>
									</tr>
								</tbody>
							</table>

							<h4>3. <?php esc_html_e( 'Import & Export Backup Tools', 'ultimate-responsive-image-slider' ); ?></h4>
							<p><?php esc_html_e( 'Export single, multiple, or all sliders with full image details, captions, and settings to a JSON backup file. Import JSON backups into any WordPress site running Ultimate Responsive Image Slider.', 'ultimate-responsive-image-slider' ); ?></p>
						</div>
					</section>

					<!-- SECTION 12: Analytics Dashboard -->
					<section id="doc-analytics" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-chart-bar" style="color:#6366f1;"></span> <?php esc_html_e( 'Analytics Dashboard', 'ultimate-responsive-image-slider' ); ?></h2>
							<p><?php esc_html_e( 'Located at UR Image Slider → Analytics:', 'ultimate-responsive-image-slider' ); ?></p>
							<ul class="urisp-doc-list">
								<li><strong><?php esc_html_e( 'Impressions:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Recorded whenever a slider scrolls into the visitor\'s active viewport.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'Slide Transitions:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Measures user engagement as visitors swipe or click through slides.', 'ultimate-responsive-image-slider' ); ?></li>
								<li><strong><?php esc_html_e( 'CTA Clicks & CTR:', 'ultimate-responsive-image-slider' ); ?></strong> <?php esc_html_e( 'Calculates real Click-Through Rates (CTR %) on your Read More and Call To Action buttons to help you identify your highest-converting sliders.', 'ultimate-responsive-image-slider' ); ?></li>
							</ul>
						</div>
					</section>

					<!-- SECTION 13: Cheatsheet & Developer API -->
					<section id="doc-cheatsheet" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-editor-code" style="color:#6366f1;"></span> <?php esc_html_e( 'Shortcode Cheatsheet & Developer API', 'ultimate-responsive-image-slider' ); ?></h2>
							
							<h4><?php esc_html_e( 'Shortcode Parameters', 'ultimate-responsive-image-slider' ); ?></h4>
							<pre class="urisp-code-block">[URISP id=42]
[URISP id=42 layout=3]
[URISP id=42 layout=5]</pre>

							<h4><?php esc_html_e( 'PHP Template Implementation', 'ultimate-responsive-image-slider' ); ?></h4>
							<pre class="urisp-code-block">&lt;?php
if ( shortcode_exists( 'URISP' ) ) {
    echo do_shortcode( '[URISP id=42]' );
}
?&gt;</pre>
						</div>
					</section>

					<!-- SECTION 14: Troubleshooting & FAQs -->
					<section id="doc-faqs" class="urisp-doc-section">
						<div class="urisp-doc-card">
							<h2 class="urisp-doc-title"><span class="dashicons dashicons-editor-help" style="color:#6366f1;"></span> <?php esc_html_e( 'Troubleshooting & Frequently Asked Questions', 'ultimate-responsive-image-slider' ); ?></h2>
							
							<div class="urisp-faq-item">
								<h4><?php esc_html_e( 'Q: Why is my slider not displaying on the frontend?', 'ultimate-responsive-image-slider' ); ?></h4>
								<p><?php esc_html_e( 'Ensure your slider has at least 1 image uploaded or active dynamic posts found, and that its status is Published (not Draft). Also check that your theme calls wp_footer() in footer.php so jQuery scripts can execute.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>

							<div class="urisp-faq-item">
								<h4><?php esc_html_e( 'Q: In Layout 1, how do I remove the blue title boxes at the bottom?', 'ultimate-responsive-image-slider' ); ?></h4>
								<p><?php esc_html_e( 'Go to Slider Settings → Navigation tab → toggle "Show Slider Navigation" to OFF. This hides the tabs and leaves a clean, minimal hero showcase.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>

							<div class="urisp-faq-item">
								<h4><?php esc_html_e( 'Q: Why do my images look cropped or cut off?', 'ultimate-responsive-image-slider' ); ?></h4>
								<p><?php esc_html_e( 'Go to General Settings → Image Scale Mode. If set to "Cover", images fill the container without distortion by cropping overflowing edges. Change it to "Contain" if you want the entire image visible without any cropping.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>

							<div class="urisp-faq-item">
								<h4><?php esc_html_e( 'Q: How do I duplicate an existing slider?', 'ultimate-responsive-image-slider' ); ?></h4>
								<p><?php esc_html_e( 'Go to UR Image Slider → All Image Sliders, and click the "Duplicate" button in the Actions column next to any slider. An exact replica will be created in Draft status immediately.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>

							<div class="urisp-faq-item">
								<h4><?php esc_html_e( 'Q: Can I restore my slider settings if I made a mistake?', 'ultimate-responsive-image-slider' ); ?></h4>
								<p><?php esc_html_e( 'Yes! Open the slider editor, go to the Advanced tab, and click "Reset to Default Settings". This will safely reset all configuration options back to defaults while keeping all your uploaded slides and captions untouched.', 'ultimate-responsive-image-slider' ); ?></p>
							</div>
						</div>
					</section>

				</div>
			</div>
			</div><!-- /.urisp-docs-main-content -->
		</div><!-- /.urisp-docs-page-wrapper -->

		<style type="text/css">
			.urisp-doc-card {
				background: #ffffff;
				border: 1px solid #e2e8f0;
				border-radius: 10px;
				padding: 24px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.04);
			}
			.urisp-doc-title {
				margin: 0 0 14px 0;
				font-size: 19px;
				font-weight: 700;
				color: #0f172a;
				display: flex;
				align-items: center;
				gap: 8px;
				padding-bottom: 12px;
				border-bottom: 1px solid #f1f5f9;
			}
			.urisp-doc-nav-item {
				display: flex;
				align-items: center;
				gap: 8px;
				padding: 9px 12px;
				border-radius: 6px;
				color: #475569;
				text-decoration: none;
				font-size: 13px;
				font-weight: 500;
				transition: all 0.15s ease;
			}
			.urisp-doc-nav-item:hover {
				background: #e2e8f0;
				color: #0f172a;
			}
			.urisp-doc-nav-item.active {
				background: #e0e7ff;
				color: #4338ca;
				font-weight: 600;
			}
			.urisp-doc-rate-btn:hover {
				background: #fef3c7 !important;
				border-color: #f59e0b !important;
				color: #92400e !important;
				transform: translateY(-1px);
				box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
			}
			.urisp-doc-contact-btn:hover {
				background: #dbeafe !important;
				border-color: #3b82f6 !important;
				color: #1e3a8a !important;
				transform: translateY(-1px);
				box-shadow: 0 2px 6px rgba(59, 130, 246, 0.15);
			}
			.urisp-step-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
				gap: 16px;
				margin: 18px 0;
			}
			.urisp-step-box {
				background: #f8fafc;
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px;
				position: relative;
			}
			.urisp-step-num {
				width: 24px;
				height: 24px;
				border-radius: 50%;
				background: #6366f1;
				color: #ffffff;
				font-size: 12px;
				font-weight: 700;
				display: flex;
				align-items: center;
				justify-content: center;
				margin-bottom: 8px;
			}
			.urisp-step-box h4 {
				margin: 0 0 6px 0;
				font-size: 14px;
				color: #0f172a;
			}
			.urisp-step-box p {
				margin: 0;
				font-size: 12px;
				color: #64748b;
				line-height: 1.4;
			}
			.urisp-layout-cards {
				display: flex;
				flex-direction: column;
				gap: 14px;
				margin-top: 16px;
			}
			.urisp-layout-item {
				background: #f8fafc;
				border: 1px solid #e2e8f0;
				border-radius: 8px;
				padding: 16px;
			}
			.urisp-layout-item h4 {
				margin: 8px 0 6px 0;
				font-size: 15px;
				color: #0f172a;
			}
			.urisp-layout-item p {
				margin: 0;
				font-size: 13px;
				color: #64748b;
				line-height: 1.5;
			}
			.urisp-doc-table {
				width: 100%;
				border-collapse: collapse;
				margin: 14px 0;
				font-size: 13px;
			}
			.urisp-doc-table th, .urisp-doc-table td {
				padding: 10px 14px;
				border: 1px solid #e2e8f0;
				text-align: left;
			}
			.urisp-doc-table th {
				background: #f8fafc;
				color: #334155;
				font-weight: 600;
			}
			.urisp-doc-list {
				margin: 12px 0 12px 20px;
				padding: 0;
				line-height: 1.6;
				color: #334155;
				font-size: 13px;
			}
			.urisp-code-block {
				background: #0f172a;
				color: #e2e8f0;
				padding: 14px 16px;
				border-radius: 8px;
				font-family: 'Consolas', 'Monaco', monospace;
				font-size: 12px;
				overflow-x: auto;
				margin: 10px 0 18px 0;
			}
			.urisp-faq-item {
				padding: 14px 0;
				border-bottom: 1px solid #f1f5f9;
			}
			.urisp-faq-item:last-child {
				border-bottom: none;
			}
			.urisp-faq-item h4 {
				margin: 0 0 6px 0;
				font-size: 14px;
				color: #1e293b;
			}
			.urisp-faq-item p {
				margin: 0;
				font-size: 13px;
				color: #64748b;
				line-height: 1.5;
			}
			html {
				scroll-behavior: smooth;
			}
			.urisp-docs-page-wrapper,
			.urisp-docs-layout,
			.urisp-docs-sidebar-col,
			#wpbody,
			#wpbody-content,
			#wpwrap {
				overflow: visible !important;
			}
			.urisp-docs-layout {
				display: flex !important;
				gap: 28px !important;
				align-items: stretch !important;
				position: relative !important;
			}
			.urisp-docs-sidebar-col {
				width: 270px !important;
				min-width: 270px !important;
				max-width: 270px !important;
				flex-shrink: 0 !important;
				position: relative !important;
			}
			.urisp-docs-sidebar {
				position: -webkit-sticky !important;
				position: sticky !important;
				top: 52px !important;
				background: #f8fafc !important;
				border: 1px solid #e2e8f0 !important;
				border-radius: 10px !important;
				padding: 14px 10px !important;
				max-height: calc(100vh - 80px) !important;
				overflow-y: auto !important;
				box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
				z-index: 20 !important;
				box-sizing: border-box !important;
				width: 270px !important;
			}
			.urisp-doc-section {
				scroll-margin-top: 65px;
			}
			#urisp-docs-search:focus {
				border-color: #6366f1 !important;
				background: #ffffff !important;
				box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
			}
			.urisp-docs-sidebar::-webkit-scrollbar {
				width: 5px;
			}
			.urisp-docs-sidebar::-webkit-scrollbar-track {
				background: transparent;
			}
			.urisp-docs-sidebar::-webkit-scrollbar-thumb {
				background: #cbd5e1;
				border-radius: 4px;
			}
			.urisp-docs-sidebar::-webkit-scrollbar-thumb:hover {
				background: #94a3b8;
			}
		</style>

		<script type="text/javascript">
			// Docs Page Loader Transition
			function revealDocsPage() {
				if (typeof jQuery !== 'undefined') {
					jQuery('.urisp-docs-page-wrapper .urisp-settings-loader').fadeOut(250, function () {
						jQuery(this).remove();
						jQuery('.urisp-docs-main-content').fadeIn(250);
					});
				} else {
					var loader = document.querySelector('.urisp-docs-page-wrapper .urisp-settings-loader');
					var content = document.querySelector('.urisp-docs-main-content');
					if (loader) loader.style.display = 'none';
					if (content) content.style.display = 'block';
				}
			}
			if (document.readyState === 'complete') {
				revealDocsPage();
			} else {
				window.addEventListener('load', revealDocsPage);
				setTimeout(revealDocsPage, 1000);
			}

			function urispDocsFilter(query) {
				query = query.toLowerCase().trim();
				var clearBtn = document.getElementById('urisp-docs-search-clear');
				if (clearBtn) {
					clearBtn.style.display = query.length > 0 ? 'inline-flex' : 'none';
				}

				var sections = document.querySelectorAll('.urisp-doc-section');
				var navItems = document.querySelectorAll('.urisp-doc-nav-item');

				sections.forEach(function(sec) {
					var text = sec.innerText.toLowerCase();
					if (!query || text.indexOf(query) !== -1) {
						sec.style.display = 'block';
					} else {
						sec.style.display = 'none';
					}
				});

				navItems.forEach(function(item) {
					var targetId = item.getAttribute('href').substring(1);
					var targetSec = document.getElementById(targetId);
					if (targetSec && targetSec.style.display === 'none') {
						item.style.display = 'none';
					} else {
						item.style.display = 'flex';
					}
				});
			}

			function urispClearSearch() {
				var input = document.getElementById('urisp-docs-search');
				if (input) {
					input.value = '';
					urispDocsFilter('');
					input.focus();
				}
			}

			// Active section tracking on scroll & click + Sticky Fallback
			document.addEventListener('DOMContentLoaded', function() {
				var links = document.querySelectorAll('.urisp-doc-nav-item');
				links.forEach(function(link) {
					link.addEventListener('click', function() {
						links.forEach(function(l) { l.classList.remove('active'); });
						this.classList.add('active');
					});
				});

				// Auto-highlight active link when scrolling
				if ('IntersectionObserver' in window) {
					var observer = new IntersectionObserver(function(entries) {
						entries.forEach(function(entry) {
							if (entry.isIntersecting) {
								var id = entry.target.getAttribute('id');
								var activeLink = document.querySelector('.urisp-doc-nav-item[href="#' + id + '"]');
								if (activeLink) {
									links.forEach(function(l) { l.classList.remove('active'); });
									activeLink.classList.add('active');
								}
							}
						});
					}, { rootMargin: '-20% 0px -70% 0px' });

					document.querySelectorAll('.urisp-doc-section').forEach(function(section) {
						observer.observe(section);
					});
				}

				// Bulletproof JavaScript Sticky Enforcer (handles cases where WP admin themes overflow breaks CSS sticky)
				var sidebar = document.querySelector('.urisp-docs-sidebar');
				var col = document.querySelector('.urisp-docs-sidebar-col');
				var body = document.querySelector('.urisp-docs-body');

				if (sidebar && col && body) {
					var adminBar = document.getElementById('wpadminbar');

					function updateStickyMenu() {
						var adminBarHeight = adminBar ? (adminBar.offsetHeight || 32) : 32;
						var topOffset = adminBarHeight + 20;
						var colRect = col.getBoundingClientRect();
						var bodyRect = body.getBoundingClientRect();
						var sidebarHeight = sidebar.offsetHeight;
						var bottomLimit = bodyRect.bottom - sidebarHeight;

						if (colRect.top < topOffset) {
							if (bottomLimit < topOffset) {
								sidebar.style.position = 'absolute';
								sidebar.style.top = Math.max(0, col.offsetHeight - sidebarHeight) + 'px';
								sidebar.style.width = '270px';
							} else {
								sidebar.style.position = 'fixed';
								sidebar.style.top = topOffset + 'px';
								sidebar.style.width = '270px';
							}
						} else {
							sidebar.style.position = 'relative';
							sidebar.style.top = '0px';
							sidebar.style.width = '270px';
						}
					}

					window.addEventListener('scroll', updateStickyMenu, { passive: true });
					window.addEventListener('resize', updateStickyMenu, { passive: true });
					updateStickyMenu();
				}
			});
		</script>
		<?php
	}
}
