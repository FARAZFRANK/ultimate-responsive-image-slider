<?php
/**
 * Ultimate Responsive Image Slider - Slide Data Loader (Free Version)
 * Optimized for standard image slides and Vimeo video integration.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Loads formatted slide data for a specific slider post ID
 * 
 * @param int    $post_id     The slider post ID
 * @param string $slide_order 'ASC', 'DESC', or 'shuffle'
 * @param string $image_size  WordPress image size name ('full', 'large', 'medium_large', 'medium')
 * @return array Array of formatted slide objects
 */
function uris_load_slides( $post_id, $slide_order = 'ASC', $image_size = null ) {
	$settings = function_exists('uris_get_slider_settings') ? uris_get_slider_settings( $post_id ) : array();

	if ( $image_size === null ) {
		$image_size = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'full';
	}
	if ( ! in_array( $image_size, array( 'full', 'large', 'medium_large', 'medium' ), true ) ) {
		$image_size = 'full';
	}

	$photos = get_post_meta( $post_id, 'ris_all_photos_details', true );
	if ( empty( $photos ) || ! is_array( $photos ) ) {
		return array();
	}

	if ( $slide_order === 'DESC' ) {
		$photos = array_reverse( $photos );
	} elseif ( $slide_order === 'shuffle' ) {
		shuffle( $photos );
	}

	$slides = array();
	$post_title = get_the_title( $post_id );

	foreach ( $photos as $photo ) {
		if ( ! isset( $photo['rpgp_image_id'] ) ) {
			continue;
		}

		$id = absint( $photo['rpgp_image_id'] );
		$attachment = get_post( $id );
		if ( ! $attachment ) {
			continue;
		}

		$title = ( isset( $photo['slide_title'] ) && $photo['slide_title'] !== '' ) ? $photo['slide_title'] : $attachment->post_title;
		$desc  = ( isset( $photo['slide_desc'] ) && $photo['slide_desc'] !== '' ) ? $photo['slide_desc'] : $attachment->post_content;
		$alt   = ( isset( $photo['slide_alt'] ) && $photo['slide_alt'] !== '' ) ? $photo['slide_alt'] : get_post_meta( $id, '_wp_attachment_image_alt', true );

		if ( empty( $alt ) && ! empty( $title ) ) {
			$alt = $title;
		}
		if ( empty( $alt ) && empty( $title ) ) {
			$alt = $post_title;
		}

		$full_src = wp_get_attachment_image_src( $id, 'full', false );
		$slide_src = ( $image_size === 'full' ) ? $full_src : wp_get_attachment_image_src( $id, $image_size, false );
		if ( ! is_array( $slide_src ) || empty( $slide_src[0] ) ) {
			$slide_src = $full_src;
		}
		$medium_src = wp_get_attachment_image_src( $id, 'medium', false );

		$link = isset( $photo['rpgp_external_link'] ) ? esc_url_raw( $photo['rpgp_external_link'] ) : '';

		// Video support (Vimeo active in Free)
		$slide_type = ( isset( $photo['slide_type'] ) && $photo['slide_type'] === 'video' ) ? 'video' : 'image';
		$video_url  = isset( $photo['video_url'] ) ? esc_url_raw( $photo['video_url'] ) : '';
		$video_info = ! empty( $video_url ) ? uris_parse_video_url( $video_url ) : null;

		if ( $slide_type === 'video' ) {
			if ( ! empty( $photo['video_thumb_url'] ) ) {
				$slide_src  = array( $photo['video_thumb_url'] );
				$medium_src = array( $photo['video_thumb_url'] );
				$full_src   = array( $photo['video_thumb_url'] );
			} elseif ( ! empty( $video_info['thumb_url'] ) && ( empty( $slide_src[0] ) || strpos( $slide_src[0], 'default' ) !== false ) ) {
				$slide_src  = array( $video_info['thumb_url'] );
				$medium_src = array( $video_info['thumb_url'] );
				$full_src   = array( $video_info['thumb_url'] );
			}
		}

		$slides[] = array(
			'id'                 => $id,
			'title'              => $title,
			'description'        => nl2br( $desc ),
			'url'                => is_array( $slide_src ) ? $slide_src[0] : '',
			'src'                => $slide_src,
			'full'               => $full_src,
			'medium'             => $medium_src,
			'alt'                => $alt,
			'link'               => $link,
			'slide_type'         => $slide_type,
			'video_url'          => $video_url,
			'video_info'         => $video_info,
			'cta_text'           => '',
			'cta_url'            => '',
			'cta_style'          => 'default',
			'cta_target'         => '_self',
		);
	}

	return $slides;
}

/**
 * Parses Vimeo video URL to extract ID and thumbnail
 * 
 * @param string $url
 * @return array|null
 */
function uris_parse_video_url( $url ) {
	$url = trim( $url );
	if ( empty( $url ) ) return null;

	// Vimeo match
	if ( preg_match( '/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $url, $matches ) ) {
		$vimeo_id = $matches[3];
		$thumb = uris_fetch_vimeo_thumbnail( $vimeo_id, $url );
		return array(
			'provider'  => 'vimeo',
			'id'        => $vimeo_id,
			'embed_url' => 'https://player.vimeo.com/video/' . $vimeo_id,
			'thumb_url' => $thumb,
		);
	}

	return null;
}

/**
 * Fetches and caches Vimeo video thumbnail via oEmbed
 */
/**
 * Renders video play button overlay for video slides (Vimeo only in Free version)
 *
 * @param array $slide Slide data array
 */
function uris_render_video_slide_overlay( $slide ) {
	if ( empty( $slide['slide_type'] ) || $slide['slide_type'] !== 'video' || empty( $slide['video_url'] ) || empty( $slide['video_info'] ) ) {
		return;
	}
	$provider = ! empty( $slide['video_info']['provider'] ) ? $slide['video_info']['provider'] : ( ! empty( $slide['video_info']['type'] ) ? $slide['video_info']['type'] : '' );
	if ( $provider !== 'vimeo' ) {
		return;
	}

	$embed_url = ! empty( $slide['video_info']['embed_url'] ) ? $slide['video_info']['embed_url'] : '';
	if ( empty( $embed_url ) ) {
		return;
	}
	$video_type = 'vimeo';
	?>
	<div class="urisp-video-play-overlay">
		<button type="button" class="urisp-video-play-btn" data-video-url="<?php echo esc_url( $embed_url ); ?>" data-video-type="<?php echo esc_attr( $video_type ); ?>" aria-label="<?php esc_attr_e( 'Play Video', 'ultimate-responsive-image-slider' ); ?>" title="<?php esc_attr_e( 'Play Video', 'ultimate-responsive-image-slider' ); ?>">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"></polygon></svg>
		</button>
	</div>
	<?php
}

if ( ! function_exists( 'urisp_render_video_slide_overlay' ) ) {
	function urisp_render_video_slide_overlay( $slide ) {
		uris_render_video_slide_overlay( $slide );
	}
}

function uris_fetch_vimeo_thumbnail( $vimeo_id, $url = '' ) {
	$cache_key = 'uris_vimeo_thumb_' . md5( $vimeo_id );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$thumb = '';
	$endpoint = 'https://vimeo.com/api/oembed.json?url=' . rawurlencode( 'https://vimeo.com/' . $vimeo_id );
	$response = wp_remote_get( $endpoint, array( 'timeout' => 5, 'sslverify' => false ) );
	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! empty( $body['thumbnail_url'] ) ) {
			$thumb = esc_url_raw( $body['thumbnail_url'] );
		}
	}

	if ( ! empty( $thumb ) ) {
		set_transient( $cache_key, $thumb, 7 * DAY_IN_SECONDS );
	}

	return $thumb;
}

// Backward compatibility alias
if ( ! function_exists( 'urisp_load_slides' ) ) {
	function urisp_load_slides( $post_id, $slide_order = 'ASC', $image_size = null ) {
		return uris_load_slides( $post_id, $slide_order, $image_size );
	}
}
if ( ! function_exists( 'urisp_parse_video_url' ) ) {
	function urisp_parse_video_url( $url ) {
		return uris_parse_video_url( $url );
	}
}

/**
 * Renders frontend loading spinner HTML
 * 
 * @param int $post_id
 * @return void
 */
function urisp_render_frontend_loader_html( $post_id ) {
	if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( function_exists('is_admin') && is_admin() && ! ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) ) ) {
		return;
	}

	require_once( URIS_PLUGIN_DIR_PATH . 'includes/defaults.php' );
	$global_settings = function_exists('uris_get_global_settings') ? uris_get_global_settings() : array();
	if ( empty( $global_settings['show_loader'] ) || $global_settings['show_loader'] == '0' ) {
		return;
	}

	$color = ! empty( $global_settings['loader_color'] ) ? esc_attr( $global_settings['loader_color'] ) : '#6366f1';

	echo '<div class="urisp-frontend-loader-wrap" style="--urisp-loader-color: ' . esc_attr( $color ) . ';">';
	echo '<div class="urisp-loader-spinner"></div>';
	echo '</div>';
}

if ( ! function_exists( 'uris_render_frontend_loader_html' ) ) {
	function uris_render_frontend_loader_html( $post_id ) {
		urisp_render_frontend_loader_html( $post_id );
	}
}

/**
 * Returns slide container CSS classes including skeleton loading if enabled
 * 
 * @param int $post_id
 * @return string
 */
function urisp_get_slide_container_class( $post_id ) {
	return 'sp-slide';
}

if ( ! function_exists( 'uris_get_slide_container_class' ) ) {
	function uris_get_slide_container_class( $post_id ) {
		return urisp_get_slide_container_class( $post_id );
	}
}

/**
 * Empty stub for CTA button layer in Free
 */
if ( ! function_exists( 'urisp_render_cta_button_layer' ) ) {
	function urisp_render_cta_button_layer( $slide, $settings = array(), $is_layer = true ) {
		return;
	}
}
if ( ! function_exists( 'uris_render_cta_button_layer' ) ) {
	function uris_render_cta_button_layer( $slide, $settings = array(), $is_layer = true ) {
		return;
	}
}