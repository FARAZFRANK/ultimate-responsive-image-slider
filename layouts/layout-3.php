<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template file rendered within uris_shortcode() function scope.

/**
 * Layout 3 - Load All RISP Custom Post Type
 */
$post_id = $URISP_Id;
require_once( RISP_PLUGIN_DIR_PATH . 'includes/slide-loader.php' );
// Dynamic source disabled in free
$global_settings = urisp_get_global_settings();
if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = urisp_get_slider_settings( $post_id );
}
$slider_image_size = ! empty( $RISP_Image_Size ) ? $RISP_Image_Size : ( ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'full' );
$slides = urisp_load_slides( $post_id, isset( $RISP_L3_Slide_Order ) ? $RISP_L3_Slide_Order : 'ASC', $slider_image_size );
$TotalImages = count( $slides );
$Post_Title = get_the_title( $post_id );
static $urisp_l3_instance_count = 0;
$urisp_l3_instance_count++;
$slider_instance_id = ( $urisp_l3_instance_count > 1 ) ? $post_id . '-' . $urisp_l3_instance_count : $post_id;
?>
<script type="text/javascript">
(function() {
	function initSliderPro_<?php echo esc_js(str_replace('-', '_', $slider_instance_id)); ?>() {
		if (typeof jQuery === 'undefined' || typeof jQuery.fn.sliderPro === 'undefined') {
			setTimeout(initSliderPro_<?php echo esc_js(str_replace('-', '_', $slider_instance_id)); ?>, 50);
			return;
		}
		var $ = jQuery;
		var $slider = $( '#slider-pro-3-<?php echo esc_js($slider_instance_id); ?>' );
		if (!$slider.length) return;

		if ($slider.data('sliderPro')) {
			$slider.sliderPro('destroy');
		}

		$slider.sliderPro({
			//autoplay
			<?php if($RISP_L3_Auto_Slideshow == 1) { ?>
			autoplay: true,
			autoplayOnHover: 'none',
			<?php } else if($RISP_L3_Auto_Slideshow == 2) { ?>
			autoplay: true,
			autoplayOnHover: 'pause',
			<?php } else { ?>
			autoplay: false,
			autoplayOnHover: 'none',
			<?php } ?>
			
			//thumbnails
			thumbnailWidth: <?php echo !empty($RISP_L3_Thumbnail_Width) ? absint($RISP_L3_Thumbnail_Width) : '120'; ?>,
			thumbnailHeight: <?php echo !empty($RISP_L3_Thumbnail_Height) ? absint($RISP_L3_Thumbnail_Height) : '80'; ?>,
			<?php if($RISP_L3_Navigation_Position == "top") { ?>
			thumbnailsPosition: 'top',
			<?php } ?>
			<?php if($RISP_L3_Navigation_Position == "bottom") { ?>
			thumbnailsPosition: 'bottom',
			<?php } ?>
			<?php if($RISP_L3_Thumbnail_Style == "pointer") { ?>
			thumbnailPointer: true, 
			<?php } ?>
			
			// Sizing & Dimensions
			<?php 
			$current_width_mode    = ! empty( $RISP_L3_Width ) ? $RISP_L3_Width : ( ! empty( $settings['width_mode'] ) ? $settings['width_mode'] : 'custom' );
			$current_height_mode   = ! empty( $RISP_L3_Height ) ? $RISP_L3_Height : ( ! empty( $settings['height_mode'] ) ? $settings['height_mode'] : 'custom' );
			$current_slider_width  = ! empty( $RISP_L3_Slider_Width ) ? absint( $RISP_L3_Slider_Width ) : ( ! empty( $settings['slider_width'] ) ? absint( $settings['slider_width'] ) : 1280 );
			$current_slider_height = ! empty( $RISP_L3_Slider_Height ) ? absint( $RISP_L3_Slider_Height ) : ( ! empty( $settings['slider_height'] ) ? absint( $settings['slider_height'] ) : 720 );
			if ( $current_slider_width < 100 ) $current_slider_width = 1280;
			if ( $current_slider_height < 50 ) $current_slider_height = 720;
			$aspect_ratio_calc = round( $current_slider_width / max( $current_slider_height, 1 ), 4 );

			if ( $current_width_mode === 'fullWindow' ) { ?>
			forceSize: 'fullWindow',
			width: <?php echo esc_js( $current_slider_width ); ?>,
			height: <?php echo esc_js( $current_slider_height ); ?>,
			<?php } elseif ( $current_width_mode === 'fullWidth' ) { ?>
			forceSize: 'fullWidth',
			width: <?php echo esc_js( $current_slider_width ); ?>,
			<?php } else { ?>
			forceSize: 'none',
			width: <?php echo esc_js( $current_slider_width ); ?>,
			<?php } ?>
			
			// Height Configuration
			<?php if ( $current_height_mode === 'auto' ) { ?>
			autoHeight: true,
			<?php } elseif ( $current_height_mode === 'fixed' ) { ?>
			height: <?php echo esc_js( $current_slider_height ); ?>,
			aspectRatio: -1,
			autoHeight: false,
			<?php } else { ?>
			height: <?php echo esc_js( $current_slider_height ); ?>,
			aspectRatio: <?php echo esc_js( $aspect_ratio_calc ); ?>,
			autoHeight: false,
			<?php } ?>

			autoSlideSize: <?php echo ! empty( $settings['auto_slide_size'] ) ? 'true' : 'false'; ?>,
			responsive: true,
			imageScaleMode: '<?php echo esc_attr($RISP_Slider_Scale_Mode); ?>',
			allowScaleUp: <?php if($RISP_Slider_Auto_Scale == 1) echo "true"; else echo "false"; ?>,
			//thumbnail
			thumbnailArrows: true,
			
			autoplayDelay: <?php if($RISP_L3_Slider_Transition_Speed != "") echo absint($RISP_L3_Slider_Transition_Speed); else echo "5000"; ?>,
			arrows: <?php if($RISP_L3_Sliding_Arrow == 1) echo "true"; else echo "false"; ?>,
			buttons: <?php if($RISP_L3_Navigation_Button == 1) echo "true"; else echo "false"; ?>,
			waitForLayers: false,
			autoScaleLayers: false,
			slideAnimationDuration: 400,
			fadeDuration: 350,
			fadeOutPreviousSlide: false,
			fade: <?php echo ( isset( $RISP_L3_Slider_Transition_Effect ) && (int) $RISP_L3_Slider_Transition_Effect === 1 ) ? "true" : "false"; ?>,
			smallSize: 500,
			mediumSize: 1000,
			largeSize: 3000,
			startSlide: 0,
			loop: true,
			autoplayDirection: 'normal',
			touchSwipe: true,
			touchSwipeThreshold: <?php echo isset($RISP_Touch_Swipe_Threshold) ? absint($RISP_Touch_Swipe_Threshold) : 50; ?>,
			<?php if($RISP_L3_Slide_Order == "shuffle") { ?>
			shuffle: true,
			<?php } ?>
			slideDistance: <?php echo isset($RISP_L3_Slide_Distance) ? absint($RISP_L3_Slide_Distance) : 5; ?>,
			fullScreen: <?php echo ! empty( $RISP_FullScreen ) ? "true" : "false"; ?>,
			breakpoints: {
				1200: {
					thumbnailsPosition: 'bottom',
					thumbnailWidth: 100,
					thumbnailHeight: 100
				}
			},
			lazyLoading: <?php echo ! empty( $global_settings['lazy_load'] ) ? 'true' : 'false'; ?>,
			init: function() {
				$slider.find('.urisp-frontend-loader-wrap').fadeOut(300);
				$slider.find('.urisp-skeleton-loading').removeClass('urisp-skeleton-loading');
			},
			gotoSlideComplete: function(event) {
				if (typeof event.index !== 'undefined') {
					var slideNum = event.index + 1;
					var total = <?php echo ! empty($slides) ? count($slides) : 0; ?>;
					var $currentSlide = $slider.find('.sp-slide').eq(event.index);
					var title = $currentSlide.find('.title-in').first().text().trim() || $currentSlide.find('img.sp-image').attr('alt') || '';
					var announcement = 'Slide ' + slideNum + ' of ' + total + (title ? ': ' + title : '');
					$('#urisp-sr-announce-<?php echo esc_js($post_id); ?>').text(announcement);
				}
			}
		});

		setTimeout(function() {
			$slider.find('.urisp-frontend-loader-wrap').fadeOut(200);
			$slider.find('.urisp-skeleton-loading').removeClass('urisp-skeleton-loading');
		}, 600);

		// WCAG 2.1 AA Keyboard Navigation & Focus Operability
		$slider.on('keydown.urispaccessibility', function(e) {
			if (e.target !== this && !$(e.target).hasClass('sp-slide') && !$(e.target).hasClass('sp-image')) {
				if ($(e.target).is('input, textarea, select, a, button')) {
					return;
				}
			}
			if (e.key === 'ArrowLeft' || e.keyCode === 37) {
				e.preventDefault();
				$slider.sliderPro('previousSlide');
			} else if (e.key === 'ArrowRight' || e.keyCode === 39) {
				e.preventDefault();
				$slider.sliderPro('nextSlide');
			}
		});

		function applyAccessibilityAttrs() {
			$slider.find('.sp-previous-arrow').attr({
				'aria-label': '<?php echo esc_js( __( 'Previous Slide', 'ultimate-responsive-image-slider' ) ); ?>',
				'role': 'button',
				'tabindex': '0'
			});
			$slider.find('.sp-next-arrow').attr({
				'aria-label': '<?php echo esc_js( __( 'Next Slide', 'ultimate-responsive-image-slider' ) ); ?>',
				'role': 'button',
				'tabindex': '0'
			});
			$slider.find('.sp-full-screen-button').attr({
				'aria-label': '<?php echo esc_js( __( 'Toggle Fullscreen', 'ultimate-responsive-image-slider' ) ); ?>',
				'role': 'button',
				'tabindex': '0'
			});
			$slider.find('.sp-button').each(function(idx) {
				$(this).attr({
					'aria-label': '<?php echo esc_js( __( 'Go to slide', 'ultimate-responsive-image-slider' ) ); ?> ' + (idx + 1),
					'role': 'button',
					'tabindex': '0'
				});
			});
			$slider.find('.sp-thumbnail').each(function(idx) {
				$(this).attr({
					'aria-label': '<?php echo esc_js( __( 'Thumbnail for slide', 'ultimate-responsive-image-slider' ) ); ?> ' + (idx + 1),
					'role': 'button',
					'tabindex': '0'
				});
			});
		}
		applyAccessibilityAttrs();
		setTimeout(applyAccessibilityAttrs, 500);

		$slider.find('.sp-arrow, .sp-button, .sp-thumbnail').on('keydown', function(e) {
			if (e.key === 'Enter' || e.keyCode === 13 || e.key === ' ' || e.keyCode === 32) {
				e.preventDefault();
				$(this).trigger('click');
			}
		});

		
		// Lightbox is a Pro feature

		// Video slide inline play click handler
		$slider.off('click.urispvideo', '.urisp-video-play-btn, .urisp-video-play-overlay').on('click.urispvideo', '.urisp-video-play-btn, .urisp-video-play-overlay', function(e) {
			var $overlay = $(this).hasClass('urisp-video-play-overlay') ? $(this) : $(this).closest('.urisp-video-play-overlay');
			var $btn = $overlay.find('.urisp-video-play-btn');
			var $slide = $overlay.closest('.sp-slide');
			var $item = $slide.find('a.item');

			e.preventDefault();
			e.stopPropagation();

			var videoUrl = $btn.data('video-url');
			var videoType = $btn.data('video-type');
			if (!videoUrl || videoType !== 'vimeo') return;

			if ($slider.data('sliderPro')) {
				$slider.sliderPro('stopAutoplay');
			}

			var $frame;
			if (videoType === 'html5') {
				$frame = $('<video class="urisp-video-inline-frame" controls autoplay playsinline><source src="' + videoUrl + '"></video>');
			} else {
				var embedSrc = videoUrl;
				if (embedSrc.indexOf('autoplay=') === -1) {
					embedSrc += (embedSrc.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
				}
				$frame = $('<iframe class="urisp-video-inline-frame" src="' + embedSrc + '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>');
			}

			$overlay.addClass('urisp-video-hidden').hide();
			$slide.addClass('urisp-video-playing');
			$slide.find('.urisp-video-inline-frame').remove();
			$slide.append($frame);
		});

		$slider.on('gotoSlide.sliderPro', function() {
			$slider.find('.sp-slide.urisp-video-playing').each(function() {
				var $s = $(this);
				$s.removeClass('urisp-video-playing');
				$s.find('.urisp-video-inline-frame').remove();
				$s.find('.urisp-video-play-overlay').removeClass('urisp-video-hidden').show();
			});
		});

		// fullscreen slide on mouse click
		$slider.find('.sp-slide').on('click', function() {
			if (!$slider.hasClass('sp-swiping')) {
				$slider.find('.sp-full-screen-button').trigger('click');
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initSliderPro_<?php echo esc_js(str_replace('-', '_', $slider_instance_id)); ?>);
	} else {
		initSliderPro_<?php echo esc_js(str_replace('-', '_', $slider_instance_id)); ?>();
	}
})();
</script>
<?php 
$s_id  = '#slider-pro-3-' . esc_attr($slider_instance_id);
$s_cls = '.slider-pro-3-' . esc_attr($post_id);

if ( ! isset( $RISP_Skin_Preset ) ) {
	$RISP_Skin_Preset = ! empty( $settings['skin_preset'] ) ? $settings['skin_preset'] : 'default';
}
$is_default_skin = empty( $RISP_Skin_Preset ) || $RISP_Skin_Preset === 'default';

if ( ! isset( $RISP_Arrow_Border_Radius ) ) {
	$nav_radius = ! empty( $settings['navigation_arrow_radius'] ) ? $settings['navigation_arrow_radius'] : 'circle';
	$RISP_Arrow_Border_Radius = '50%';
	if ( $nav_radius === 'curved' ) {
		$RISP_Arrow_Border_Radius = '8px';
	} elseif ( $nav_radius === 'sharp' ) {
		$RISP_Arrow_Border_Radius = '0px';
	}
}

$caption_align = ! empty( $RISP_L3_Text_Alignment ) ? $RISP_L3_Text_Alignment : ( ! empty( $settings['text_alignment'] ) ? $settings['text_alignment'] : 'bottomCenter' );
$card_align = 'center';
if ( strpos( $caption_align, 'Left' ) !== false ) {
	$card_align = 'left';
} elseif ( strpos( $caption_align, 'Right' ) !== false ) {
	$card_align = 'right';
}
$flex_justify = 'center';
if ( $card_align === 'left' ) {
	$flex_justify = 'flex-start';
} elseif ( $card_align === 'right' ) {
	$flex_justify = 'flex-end';
}
ob_start(); 
?>

<?php echo esc_attr( $s_id ); ?>,
<?php echo esc_attr( $s_cls ); ?> {
	--urisp-cta-bg: <?php echo esc_attr( ! empty( $RISP_CTA_Button_BG_Color ) ? $RISP_CTA_Button_BG_Color : '#ffffff' ); ?>;
	--urisp-cta-text: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Text_Color ) ? $RISP_CTA_Button_Text_Color : '#000000' ); ?>;
	--urisp-cta-hover-bg: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Color ) ? $RISP_CTA_Button_Hover_Color : '#000000' ); ?>;
	--urisp-cta-hover-text: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Text_Color ) ? $RISP_CTA_Button_Hover_Text_Color : '#ffffff' ); ?>;
	font-family: 'Open Sans', Arial;
<?php if ( isset( $current_width_mode ) && ( $current_width_mode === 'fullWidth' || $current_width_mode === 'fullWindow' ) ) : ?>
	/* Override theme container max-width constraints (e.g., Astra/block layouts) in Full Width mode. */
	max-width: none !important;
<?php endif; ?>
}
<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card {
	background: <?php echo esc_attr( function_exists( 'urisp_hex_to_rgba' ) ? urisp_hex_to_rgba( $RISP_Slide_In_Title_BG_Color, $RISP_BG_Color_Opacity ) : $RISP_Slide_In_Title_BG_Color ); ?> !important;
	max-width: 85% !important;
	min-width: 260px;
	width: auto !important;
	width: -webkit-fit-content !important;
	width: -moz-fit-content !important;
	width: fit-content !important;
	white-space: normal !important;
	word-wrap: break-word !important;
	overflow-wrap: break-word !important;
	padding: 16px 24px;
	border-radius: 12px;
	box-sizing: border-box;
	text-align: <?php echo esc_attr( $card_align ); ?>;
	pointer-events: auto;
	box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
	z-index: 20;
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card .title-in,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card .title-in {
	color: <?php echo esc_attr($RISP_Slide_In_Title_Color); ?> !important;
	font-family: <?php echo esc_attr($RISP_Font_Style); ?> !important;
	font-weight: 700;
	font-size: <?php echo esc_attr($RISP_Slide_In_Title_Size); ?>px !important;
	line-height: 1.35;
	margin: 0 0 8px 0;
	padding: 0;
	background: transparent !important;
	border: none !important;
	box-shadow: none !important;
	text-align: <?php echo esc_attr( $card_align ); ?>;
	white-space: normal !important;
	word-wrap: break-word !important;
	overflow-wrap: break-word !important;
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card .desc-in,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card .desc-in {
	color: <?php echo esc_attr($RISP_Slide_In_Desc_Color); ?> !important;
	font-family: <?php echo esc_attr($RISP_Font_Style); ?> !important;
	font-size: <?php echo esc_attr($RISP_Slide_In_Desc_Size); ?>px !important;
	line-height: 1.55;
	margin: 0;
	padding: 0;
	background: transparent !important;
	border: none !important;
	box-shadow: none !important;
	white-space: normal !important;
	word-wrap: break-word !important;
	overflow-wrap: break-word !important;
	text-align: <?php echo esc_attr( $card_align ); ?>;
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card .urisp-slide-actions-wrap,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card .urisp-slide-actions-wrap {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	align-items: center;
	justify-content: <?php echo esc_attr( $flex_justify ); ?>;
	margin-top: 14px;
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card .urisp-cta-wrap.urisp-cta-inline,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card .urisp-cta-wrap.urisp-cta-inline {
	margin: 0 !important;
	display: inline-flex !important;
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card a.read-more-color.urisp-cta-btn,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card a.read-more-color.urisp-cta-btn {
	margin: 0 !important;
	display: inline-flex !important;
	<?php if ( ! empty( $RISP_CTA_Button_Style ) && $RISP_CTA_Button_Style === 'text' ) { ?>
	background: transparent !important;
	color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_BG_Color ) ? $RISP_CTA_Button_BG_Color : '#ffffff' ); ?> !important;
	border: 2px solid transparent !important;
	<?php } else { ?>
	background-color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_BG_Color ) ? $RISP_CTA_Button_BG_Color : '#ffffff' ); ?> !important;
	color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Text_Color ) ? $RISP_CTA_Button_Text_Color : '#000000' ); ?> !important;
	border: 2px solid <?php echo esc_attr( ! empty( $RISP_CTA_Button_BG_Color ) ? $RISP_CTA_Button_BG_Color : '#ffffff' ); ?> !important;
	<?php } ?>
}

<?php echo esc_attr( $s_id ); ?> .urisp-slide-caption-card a.read-more-color.urisp-cta-btn:hover,
<?php echo esc_attr( $s_cls ); ?> .urisp-slide-caption-card a.read-more-color.urisp-cta-btn:hover {
	<?php if ( ! empty( $RISP_CTA_Button_Style ) && $RISP_CTA_Button_Style === 'text' ) { ?>
	background: transparent !important;
	color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Color ) ? $RISP_CTA_Button_Hover_Color : '#000000' ); ?> !important;
	border-color: transparent !important;
	<?php } else { ?>
	background-color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Color ) ? $RISP_CTA_Button_Hover_Color : '#000000' ); ?> !important;
	color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Text_Color ) ? $RISP_CTA_Button_Hover_Text_Color : '#ffffff' ); ?> !important;
	border-color: <?php echo esc_attr( ! empty( $RISP_CTA_Button_Hover_Color ) ? $RISP_CTA_Button_Hover_Color : '#000000' ); ?> !important;
	<?php } ?>
}

<?php echo esc_attr( $s_id ); ?> .title-in,
<?php echo esc_attr( $s_cls ); ?> .title-in {
	color: <?php echo esc_attr($RISP_Slide_In_Title_Color); ?> !important;
	font-family: <?php echo esc_attr($RISP_Font_Style); ?> !important;
	font-weight: bolder;
	font-size: <?php echo esc_attr($RISP_Slide_In_Title_Size); ?>px !important;
	text-align: center;
}

<?php echo esc_attr( $s_id ); ?> .title-in-bg,
<?php echo esc_attr( $s_cls ); ?> .title-in-bg {
	background: <?php echo esc_attr( function_exists( 'urisp_hex_to_rgba' ) ? urisp_hex_to_rgba( $RISP_Slide_In_Title_BG_Color, $RISP_BG_Color_Opacity ) : $RISP_Slide_In_Title_BG_Color ); ?> !important;
	max-width: 100%;
	min-width: 30%;
	transform: initial !important;
}

<?php echo esc_attr( $s_id ); ?> .desc-in,
<?php echo esc_attr( $s_cls ); ?> .desc-in {
	color: <?php echo esc_attr($RISP_Slide_In_Desc_Color); ?> !important;
	font-family: <?php echo esc_attr($RISP_Font_Style); ?> !important;
	font-size: <?php echo esc_attr($RISP_Slide_In_Desc_Size); ?>px !important;
	text-align: center;
	white-space: normal !important;
	word-wrap: break-word !important;
	overflow-wrap: break-word !important;
}
<?php echo esc_attr( $s_id ); ?> .desc-in-bg,
<?php echo esc_attr( $s_cls ); ?> .desc-in-bg {
	background: <?php echo esc_attr( function_exists( 'urisp_hex_to_rgba' ) ? urisp_hex_to_rgba( $RISP_Slide_In_Title_BG_Color, $RISP_BG_Color_Opacity ) : $RISP_Slide_In_Title_BG_Color ); ?> !important;
	max-width: 100%;
	min-width: 30%;
	transform: initial !important;
}

<?php echo esc_attr( $s_id ); ?> .read-more-color,
<?php echo esc_attr( $s_cls ); ?> .read-more-color {
	font-family: <?php echo esc_attr($RISP_Font_Style); ?> !important;
	-webkit-box-shadow: none;
	box-shadow: none;
}

<?php echo esc_attr( $s_id ); ?> a:hover,
<?php echo esc_attr( $s_cls ); ?> a:hover {
	-webkit-box-shadow: none;
	box-shadow: none;
}

<?php if ( $is_default_skin ) { ?>
/* navigation button/bullets color */
<?php echo esc_attr( $s_id ); ?> .sp-button,
<?php echo esc_attr( $s_cls ); ?> .sp-button {
	border: 2px solid <?php echo esc_attr($RISP_Naviagtion_Button_Color); ?> !important;
	width: <?php echo esc_attr($RISP_Naviagtion_Button_Size); ?>px !important;
	height: <?php echo esc_attr($RISP_Naviagtion_Button_Size); ?>px !important;
	<?php if( isset($RISP_L3_Navigation_Button_Style) && (int)$RISP_L3_Navigation_Button_Style === 0 ) { ?>
	border-radius: 0% !important;
	<?php } else { ?>
	border-radius: 50% !important;
	<?php } ?>
}
<?php echo esc_attr( $s_id ); ?> .sp-selected-button,
<?php echo esc_attr( $s_cls ); ?> .sp-selected-button {
	background-color: <?php echo esc_attr($RISP_Naviagtion_Button_Color); ?> !important;
	<?php if( isset($RISP_L3_Navigation_Button_Style) && (int)$RISP_L3_Navigation_Button_Style === 0 ) { ?>
	border-radius: 0% !important;
	<?php } else { ?>
	border-radius: 50% !important;
	<?php } ?>
}

/* navigation arrow color */
<?php echo esc_attr( $s_id ); ?> .sp-arrow,
<?php echo esc_attr( $s_cls ); ?> .sp-arrow {
	color: <?php echo esc_attr($RISP_Naviagtion_Arrow_Color); ?> !important;
	background: <?php echo esc_attr($RISP_Naviagtion_Arrow_BG_Color); ?> !important;
}
<?php echo esc_attr( $s_id ); ?> .sp-next-arrow::after, <?php echo esc_attr( $s_id ); ?> .sp-next-arrow::before, <?php echo esc_attr( $s_id ); ?> .sp-previous-arrow::after, <?php echo esc_attr( $s_id ); ?> .sp-previous-arrow::before,
<?php echo esc_attr( $s_cls ); ?> .sp-next-arrow::after, <?php echo esc_attr( $s_cls ); ?> .sp-next-arrow::before, <?php echo esc_attr( $s_cls ); ?> .sp-previous-arrow::after, <?php echo esc_attr( $s_cls ); ?> .sp-previous-arrow::before {
    background-color: <?php echo esc_attr($RISP_Naviagtion_Arrow_Color); ?> !important;
}

<?php echo esc_attr( $s_id ); ?> .sp-full-screen-button,
<?php echo esc_attr( $s_cls ); ?> .sp-full-screen-button {
	color: <?php echo esc_attr($RISP_Naviagtion_Arrow_Color); ?> !important;
	background: <?php echo esc_attr($RISP_Naviagtion_Arrow_BG_Color); ?> !important;
}
<?php } ?>

<?php if ( empty( $RISP_L3_Navigation_Button ) ) { ?>
<?php echo esc_attr( $s_id ); ?> .sp-buttons,
<?php echo esc_attr( $s_cls ); ?> .sp-buttons {
	display: none !important;
}
<?php } ?>

<?php if ( empty( $RISP_L3_Sliding_Arrow ) ) { ?>
<?php echo esc_attr( $s_id ); ?> .sp-arrows,
<?php echo esc_attr( $s_cls ); ?> .sp-arrows {
	display: none !important;
}
<?php } ?>

<?php if ( empty( $RISP_FullScreen ) ) { ?>
<?php echo esc_attr( $s_id ); ?> .sp-full-screen-button,
<?php echo esc_attr( $s_cls ); ?> .sp-full-screen-button {
	display: none !important;
}
<?php } ?>

/* Navigation Arrow Style */
<?php if ( ! empty( $RISP_Arrow_Style ) && $RISP_Arrow_Style === 'caret' ) { ?>
<?php echo esc_attr( $s_id ); ?> .sp-previous-arrow::before,
<?php echo esc_attr( $s_cls ); ?> .sp-previous-arrow::before {
	-webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='black'%3E%3Cpath d='M15 18l-6-6 6-6v12z'/%3E%3C/svg%3E") !important;
	mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='black'%3E%3Cpath d='M15 18l-6-6 6-6v12z'/%3E%3C/svg%3E") !important;
}
<?php echo esc_attr( $s_id ); ?> .sp-next-arrow::before,
<?php echo esc_attr( $s_cls ); ?> .sp-next-arrow::before {
	-webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='black'%3E%3Cpath d='M9 6l6 6-6 6V6z'/%3E%3C/svg%3E") !important;
	mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='black'%3E%3Cpath d='M9 6l6 6-6 6V6z'/%3E%3C/svg%3E") !important;
}
<?php } ?>

/* Navigation Icon Shape (applies to all skins and layouts) */
<?php echo esc_attr( $s_id ); ?> .sp-arrow,
<?php echo esc_attr( $s_id ); ?> .sp-full-screen-button,
<?php echo esc_attr( $s_cls ); ?> .sp-arrow,
<?php echo esc_attr( $s_cls ); ?> .sp-full-screen-button,
<?php echo esc_attr( $s_cls ); ?>.slider-pro .sp-arrow,
<?php echo esc_attr( $s_cls ); ?>.slider-pro .sp-full-screen-button {
	border-radius: <?php echo esc_attr($RISP_Arrow_Border_Radius); ?> !important;
}

/* Thumbnail background and text colors */


<?php echo esc_attr( $s_id ); ?> .sp-thumbnail-title,
<?php echo esc_attr( $s_cls ); ?> .sp-thumbnail-title,
<?php echo esc_attr( $s_id ); ?> .thumb-title,
<?php echo esc_attr( $s_cls ); ?> .thumb-title {
	color: <?php echo esc_attr($RISP_Thumbnail_Title_Color); ?> !important;
}

<?php echo esc_attr( $s_id ); ?> .sp-thumbnail-description,
<?php echo esc_attr( $s_cls ); ?> .sp-thumbnail-description,
<?php echo esc_attr( $s_id ); ?> .thumb-desc,
<?php echo esc_attr( $s_cls ); ?> .thumb-desc {
	color: <?php echo esc_attr($RISP_Thumbnail_Desc_Color); ?> !important;
}

/* border color */
<?php if($RISP_L3_Thumbnail_Style == "border") { ?>
<?php echo esc_attr( $s_id ); ?> .sp-selected-thumbnail,
<?php echo esc_attr( $s_cls ); ?> .sp-selected-thumbnail {
	border: 4px solid <?php echo esc_attr($RISP_L3_Thumbnail_Style_Color); ?> !important;
}
<?php } ?>

/* pointer color - bottom */
<?php if( $RISP_L3_Navigation_Position == "bottom") { ?>
<?php echo esc_attr( $s_id ); ?> .sp-selected-thumbnail::before,
<?php echo esc_attr( $s_cls ); ?> .sp-selected-thumbnail::before {
	border-bottom: 5px solid <?php echo esc_attr($RISP_L3_Thumbnail_Style_Color); ?> !important;
}
<?php echo esc_attr( $s_id ); ?> .sp-selected-thumbnail::after,
<?php echo esc_attr( $s_cls ); ?> .sp-selected-thumbnail::after {
	border-bottom: 13px solid <?php echo esc_attr($RISP_L3_Thumbnail_Style_Color); ?> !important;
}
<?php } ?>

/* pointer color - top */
<?php if( $RISP_L3_Navigation_Position == "top") { ?>
<?php echo esc_attr( $s_id ); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::before,
<?php echo esc_attr( $s_cls ); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::before {
    border-bottom: 5px solid <?php echo esc_attr($RISP_L3_Thumbnail_Style_Color); ?>;
}
<?php echo esc_attr( $s_id ); ?> .sp-top-thumbnails,
<?php echo esc_attr( $s_cls ); ?> .sp-top-thumbnails {
	right: 0 !important;
}
<?php echo esc_attr( $s_id ); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::after,
<?php echo esc_attr( $s_cls ); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::after {
    border-top: 13px solid <?php echo esc_attr($RISP_L3_Thumbnail_Style_Color); ?> !important;
}
<?php } ?>

.fnf{
	background-color: #272e31;
	border-radius: 5px;
	color: #fff;
	font-family: initial;
	text-align: center;
	padding:12px;
}
.uris-slider-title{
	font-family: <?php echo esc_attr($RISP_Font_Style); ?>;
}

@media (max-width: 860px) {
	<?php echo esc_attr( $s_id ); ?> .sp-layer,
	<?php echo esc_attr( $s_cls ); ?> .sp-layer {
		font-size: 18px;
	}
	<?php echo esc_attr( $s_id ); ?> .hide-medium-screen,
	<?php echo esc_attr( $s_cls ); ?> .hide-medium-screen {
		display: none;
	}
}

@media (max-width: 640px) {
	<?php echo esc_attr( $s_id ); ?> .sp-layer,
	<?php echo esc_attr( $s_cls ); ?> .sp-layer {
		font-size: 12px;
	}
	<?php echo esc_attr( $s_id ); ?> .hide-small-screen,
	<?php echo esc_attr( $s_cls ); ?> .hide-small-screen {
		display: none;
	}
}
<?php
$slider_css = ob_get_clean();
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-sanitized generated CSS string.
echo "<style type=\"text/css\">\n" . wp_strip_all_tags( $slider_css ) . "\n</style>\n";
?>
<?php  if($RISP_Slide_Title) { ?>
<h3 class="uris-slider-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
<?php }

if($TotalImages>0) { 
	if ( ! isset( $RISP_Arrow_Style ) ) {
		$RISP_Arrow_Style = ! empty( $settings['arrow_style'] ) ? $settings['arrow_style'] : 'chevron';
	}
	if ( ! isset( $RISP_Ken_Burns ) ) {
		$RISP_Ken_Burns = ! empty( $settings['ken_burns'] ) ? $settings['ken_burns'] : 0;
	}
	if ( ! isset( $RISP_Ken_Burns_Intensity ) ) {
		$RISP_Ken_Burns_Intensity = ! empty( $settings['ken_burns_intensity'] ) ? $settings['ken_burns_intensity'] : 'subtle';
	}
	if ( ! isset( $RISP_Pause_On_Hover ) ) {
		$RISP_Pause_On_Hover = isset( $settings['pause_on_hover'] ) ? $settings['pause_on_hover'] : 1;
	}
	if ( ! isset( $RISP_Skin_Preset ) ) {
		$RISP_Skin_Preset = ! empty( $settings['skin_preset'] ) ? $settings['skin_preset'] : 'default';
	}
	$ken_burns_class = ! empty( $RISP_Ken_Burns ) ? ' urisp-has-kenburns urisp-kb-' . esc_attr( $RISP_Ken_Burns_Intensity ) : '';
	$skin_preset_class = ( ! empty( $RISP_Skin_Preset ) && $RISP_Skin_Preset !== 'default' ) ? ' urisp-skin-' . esc_attr( $RISP_Skin_Preset ) : '';
?>
	<div id="slider-pro-3-<?php echo esc_attr($slider_instance_id); ?>" class="slider-pro sp-no-js slider-pro-3-<?php echo esc_attr($post_id); ?> urisp-arrow-<?php echo esc_attr($RISP_Arrow_Style); ?><?php echo esc_attr( $ken_burns_class ); ?><?php echo esc_attr( $skin_preset_class ); ?>" data-slider-id="<?php echo esc_attr($post_id); ?>" role="region" aria-roledescription="carousel" aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" tabindex="0">
		<?php urisp_render_frontend_loader_html( $post_id ); ?>
		<!---- slides div start ---->
		<div class="sp-slides aniimated-thumbnials">
			<?php
			if ( ! empty( $slides ) ) {
				$slide_total = count( $slides );
				$slide_index = 0;
				$Link = "";
				foreach ( $slides as $slide ) {
					$slide_index++;
					$SlideId   = $slide['id'];
					$Title     = $slide['title'];
					$Desc      = $slide['description'];
					$Slide_Url = $slide['url'];
					$Slide_Alt = $slide['alt'];
					$Link      = $slide['link'];

					$Full_Url = ( ! empty( $slide['full'] ) && is_array( $slide['full'] ) ) ? $slide['full'][0] : $Slide_Url;
					$is_video = ( ! empty( $slide['slide_type'] ) && $slide['slide_type'] === 'video' && ! empty( $slide['video_url'] ) && ! empty( $slide['video_info'] ) && ! empty( $slide['video_info']['provider'] ) && $slide['video_info']['provider'] === 'vimeo' );
					if ( $is_video && false /* Lightbox pro only */ ) {
						$Slide_Link_Url = $slide['video_url'];
						$target         = '_blank';
					} elseif ( $RISP_Read_More_Link_ON == "slide" ) {
						$Slide_Link_Url = $Link;
						$target         = $RISP_Read_More_Link_Open;
					} else {
						$Slide_Link_Url = $Full_Url;
					}
					$is_lazy = ! empty( $global_settings['lazy_load'] );
					$img_placeholder = esc_url( RISP_PLUGIN_URL . 'assets/css/images/blank.gif' );
					?>
												<div class="<?php echo esc_attr( urisp_get_slide_container_class( $post_id ) ); ?>"<?php
					if ( function_exists( 'urisp_get_slide_aria_attrs' ) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped ARIA attributes string.
						echo urisp_get_slide_aria_attrs( $slide_index, $slide_total, $slide );
					}
					?>>
					<?php if ( $RISP_Read_More_Link_ON == "slide" && ! empty( $Slide_Link_Url ) ) { ?>
					<a href="<?php echo esc_url( $Slide_Link_Url ); ?>" target="<?php echo esc_attr( $target ); ?>">
						<?php if ( $is_lazy ) { ?>
						<img src="<?php echo esc_url( $img_placeholder ); ?>" class="sp-image" alt="<?php echo esc_attr( $Slide_Alt ); ?>"
							data-src="<?php echo esc_url( $Slide_Url ); ?>"
							data-retina="<?php echo esc_url( $Slide_Url ); ?>" />
						<?php } else { ?>
						<img src="<?php echo esc_url( $Slide_Url ); ?>" class="sp-image" alt="<?php echo esc_attr( $Slide_Alt ); ?>" />
						<?php } ?>
					</a>
					<?php } else { ?>
						<?php if ( $is_lazy ) { ?>
						<img src="<?php echo esc_url( $img_placeholder ); ?>" class="sp-image" alt="<?php echo esc_attr( $Slide_Alt ); ?>"
							data-src="<?php echo esc_url( $Slide_Url ); ?>"
							data-retina="<?php echo esc_url( $Slide_Url ); ?>" />
						<?php } else { ?>
						<img src="<?php echo esc_url( $Slide_Url ); ?>" class="sp-image" alt="<?php echo esc_attr( $Slide_Alt ); ?>" />
						<?php } ?>
					<?php } ?>

					<?php 
					if ( $is_video && function_exists( 'urisp_render_video_slide_overlay' ) ) {
						urisp_render_video_slide_overlay( $slide );
					}
					?>
					
					<!-- Unified Slide Caption Card -->
					<?php 
					$has_title     = ( ! empty( $RISP_Show_Slide_Title ) && $Title !== "" );
					$has_desc      = ( ! empty( $RISP_Show_Slide_Desc ) && $Desc !== "" );
					$has_read_more = ( ! empty( $Link ) && $RISP_Read_More_Link_ON === "text" );
					$has_cta       = ( ! empty( $slide['cta_text'] ) );

					if ( $has_title || $has_desc || $has_read_more || $has_cta ) {
						$align = ! empty( $RISP_L3_Text_Alignment ) ? $RISP_L3_Text_Alignment : 'bottomCenter';
						$v_offset = '35';
						if ( strpos( $align, 'top' ) !== false ) {
							$v_offset = '30';
						} elseif ( strpos( $align, 'center' ) !== false ) {
							$v_offset = '0';
						}
						$is_center = ( strpos( $align, 'Center' ) !== false );
					?>
					<div class="sp-layer sp-image-text urisp-slide-caption-card hide-small-screen" 
						data-position="<?php echo esc_attr( $align ); ?>"
						<?php if ( ! $is_center ) { ?> data-horizontal="40" <?php } ?>
						data-vertical="<?php echo esc_attr( $v_offset ); ?>"
						data-show-transition="<?php echo esc_attr( ! empty( $RISP_L3_Title_Transition ) ? $RISP_L3_Title_Transition : 'up' ); ?>" 
						data-show-delay="300" 
						data-hide-transition="<?php echo ( ! empty( $RISP_L3_Title_Transition ) && $RISP_L3_Title_Transition === 'down' ) ? 'up' : 'down'; ?>" 
						data-hide-delay="200">

						<?php if ( $has_title ) { ?>
						<h3 class="title-in"><?php echo esc_html( $Title ); ?></h3>
						<?php } ?>

						<?php if ( $has_desc ) { ?>
						<div class="desc-in hide-medium-screen"><?php echo wp_kses_post( $Desc ); ?></div>
						<?php } ?>

						<?php if ( $has_read_more || $has_cta ) { ?>
						<div class="urisp-slide-actions-wrap">
							<?php if ( $has_read_more ) { ?>
							<a class="read-more-color urisp-cta-btn urisp-cta-<?php echo esc_attr( ! empty( $RISP_CTA_Button_Style ) ? $RISP_CTA_Button_Style : 'filled' ); ?>" target="<?php echo esc_attr( $RISP_Read_More_Link_Open ); ?>" href="<?php echo esc_url( $Link ); ?>"><?php echo esc_html( $RISP_Read_More_Link_Text ); ?></a>
							<?php } ?>
							<?php 
							if ( function_exists( 'urisp_render_cta_button_layer' ) ) {
								urisp_render_cta_button_layer( $slide, $settings, false );
							}
							?>
						</div>
						<?php } ?>
					</div>
					<?php } ?>

					<?php 
					if ( function_exists( 'urisp_render_woocommerce_slide_layers' ) ) {
						urisp_render_woocommerce_slide_layers( $slide, $settings );
					}
					?>
				</div>
				<?php } //end of for each
			} //end of is_array 
			?>
		</div>
		<!---- slides div end ---->
		
		<?php if($RISP_L3_Slider_Navigation == 1) { ?>
		<!-- slides thumbnails div start -->
		<div class="sp-thumbnails">
			<?php
			if ( ! empty( $slides ) ) {
				foreach ( $slides as $slide ) {
					$Slide_Thumb = ! empty( $slide['medium'] ) ? $slide['medium'][0] : $slide['url'];
					$Slide_Alt   = $slide['alt'];
					?>
					<?php if ( $is_lazy ) { ?>
					<img class="sp-thumbnail" src="<?php echo esc_url( $img_placeholder ); ?>" data-src="<?php echo esc_url( $Slide_Thumb ); ?>" alt="<?php echo esc_attr( $Slide_Alt ); ?>" />
					<?php } else { ?>
					<img class="sp-thumbnail" src="<?php echo esc_url( $Slide_Thumb ); ?>" alt="<?php echo esc_attr( $Slide_Alt ); ?>" />
					<?php } ?>
					<?php
				}
			}
			?>
		</div>
		<?php } ?>
		<!-- slides thumbnails div end -->
	</div>
<?php if ( function_exists( 'urisp_render_sr_announcement_element' ) ) { urisp_render_sr_announcement_element( $post_id ); } ?>
<?php } else { ?><div class="fnf">&#10006; <?php esc_html_e('No Slides Found In Slider.', 'ultimate-responsive-image-slider'); ?></div> <?php } ?>