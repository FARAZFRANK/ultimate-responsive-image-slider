<?php
/**
 * Ultimate Responsive Image Slider - Analytics Teaser (Free Version)
 * Displays a feature showcase for Pro analytics with the modern loading spinner.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class URIS_Analytics_Teaser {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ), 20 );
	}

	public static function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=ris_gallery',
			__( 'Analytics', 'ultimate-responsive-image-slider' ),
			__( 'Analytics', 'ultimate-responsive-image-slider' ) . ' <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px;">PRO</span>',
			'manage_options',
			'urisp_analytics',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		?>
		<div class="wrap urisp-analytics-wrap" style="position: relative; min-height: 450px; max-width: 1100px; margin: 25px auto;">
			<!-- Administrative Loading Spinner Overlay -->
			<div class="urisp-settings-loader">
				<div class="urisp-settings-loader-spinner"></div>
			</div>

			<div class="urisp-analytics-main-content" style="display: none;">
				<div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: #fff; padding: 36px 40px; border-radius: 16px; margin-bottom: 28px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
					<div style="max-width: 650px;">
						<span style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; margin-bottom: 12px;">
							★ Pro Analytics Engine
						</span>
						<h1 style="color: #fff; font-size: 28px; font-weight: 800; margin: 0 0 10px 0;">
							<?php esc_html_e( 'Track Slider Impressions, Views & Click-Through Rates', 'ultimate-responsive-image-slider' ); ?>
						</h1>
						<p style="color: #94a3b8; font-size: 15px; line-height: 1.6; margin: 0;">
							<?php esc_html_e( 'Discover which sliders perform best with real-time impression tracking, individual slide view metrics, link CTR analysis, interactive Chart.js growth curves, and CSV data exports.', 'ultimate-responsive-image-slider' ); ?>
						</p>
					</div>
					<div>
						<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #f59e0b; border-color: #d97706; color: #1e1b4b; font-weight: 800; font-size: 15px; padding: 10px 28px; height: auto; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);">
							<?php esc_html_e( 'Upgrade to Pro Analytics', 'ultimate-responsive-image-slider' ); ?> &rarr;
						</a>
					</div>
				</div>

				<!-- Mockup Metrics Row -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
					<div class="urisp-card" style="padding: 20px; border-left: 4px solid #6366f1;">
						<div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Total Slider Views', 'ultimate-responsive-image-slider' ); ?></div>
						<div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;">24,850</div>
						<div style="font-size: 12px; color: #16a34a; font-weight: 600;">↑ 18.2% vs last month</div>
					</div>
					<div class="urisp-card" style="padding: 20px; border-left: 4px solid #10b981;">
						<div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Slide Impressions', 'ultimate-responsive-image-slider' ); ?></div>
						<div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;">118,420</div>
						<div style="font-size: 12px; color: #16a34a; font-weight: 600;">↑ 24.5% vs last month</div>
					</div>
					<div class="urisp-card" style="padding: 20px; border-left: 4px solid #f59e0b;">
						<div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Slide Link Clicks', 'ultimate-responsive-image-slider' ); ?></div>
						<div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;">3,640</div>
						<div style="font-size: 12px; color: #16a34a; font-weight: 600;">↑ 9.4% vs last month</div>
					</div>
					<div class="urisp-card" style="padding: 20px; border-left: 4px solid #ec4899;">
						<div style="font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Avg. Click-Through Rate', 'ultimate-responsive-image-slider' ); ?></div>
						<div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;">3.07%</div>
						<div style="font-size: 12px; color: #64748b; font-weight: 500;">High engagement</div>
					</div>
				</div>

				<!-- Mockup Interactive Chart Preview -->
				<div class="urisp-card" style="padding: 28px; text-align: center; background: #ffffff;">
					<h3 style="margin: 0 0 16px 0; color: #0f172a; font-size: 18px;"><?php esc_html_e( 'Interactive Real-Time Performance Curves', 'ultimate-responsive-image-slider' ); ?></h3>
					<div style="height: 220px; background: repeating-linear-gradient(0deg, #f8fafc, #f8fafc 1px, transparent 1px, transparent 40px); border: 1px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: center; position: relative;">
						<div style="text-align: center; padding: 20px;">
							<span class="dashicons dashicons-chart-area" style="font-size: 48px; width: 48px; height: 48px; color: #6366f1; margin-bottom: 10px;"></span>
							<p style="margin: 0 0 14px 0; color: #64748b; font-size: 14px;"><?php esc_html_e( 'Unlock 7-day, 30-day, and all-time interactive charts with date-filtering in Pro.', 'ultimate-responsive-image-slider' ); ?></p>
							<a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #4338ca; border-color: #3730a3; font-weight: 700;">
								<?php esc_html_e( 'Unlock Analytics Dashboard', 'ultimate-responsive-image-slider' ); ?> &rarr;
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		(function() {
			function revealAnalyticsPage() {
				if (typeof jQuery !== 'undefined') {
					var $ = jQuery;
					var $loader = $('.urisp-analytics-wrap > .urisp-settings-loader');
					var $content = $('.urisp-analytics-main-content');
					if ($loader.length && $content.length && $content.is(':hidden')) {
						$loader.fadeOut(250, function() {
							$content.fadeIn(250);
						});
					}
				} else {
					var loader = document.querySelector('.urisp-analytics-wrap > .urisp-settings-loader');
					var content = document.querySelector('.urisp-analytics-main-content');
					if (loader) loader.style.display = 'none';
					if (content) content.style.display = 'block';
				}
			}

			if (document.readyState === 'complete') {
				setTimeout(revealAnalyticsPage, 120);
			} else {
				window.addEventListener('load', function() {
					setTimeout(revealAnalyticsPage, 120);
				});
			}
			setTimeout(revealAnalyticsPage, 1000);
		})();
		</script>
		<?php
	}
}
URIS_Analytics_Teaser::init();