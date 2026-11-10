<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template file rendered within metabox callback.

/**
 * Load Saved Ultimate Responsive Image Slider Settings
 */
$URISP_PostId = $post->ID;

// Try to load unified settings first
$settings = get_post_meta( $URISP_PostId, 'urisp_settings_' . $URISP_PostId, true );

// Fallback to legacy format and migrate on-the-fly
if ( empty( $settings ) || ! is_array( $settings ) ) {
	$legacy_key = 'WRIS_Gallery_Settings_' . $URISP_PostId;
	$legacy = get_post_meta( $URISP_PostId, $legacy_key, true );
	if ( ! empty( $legacy ) && is_array( $legacy ) ) {
		require_once( RISP_PLUGIN_DIR_PATH . 'includes/migration.php' );
		$settings = urisp_convert_legacy_to_unified( $legacy );
	}
}

// Merge with defaults
require_once( RISP_PLUGIN_DIR_PATH . 'includes/defaults.php' );
$settings = urisp_clean_loaded_settings( $settings );
$settings = wp_parse_args( (array) $settings, urisp_get_default_settings() );

// Helper for toggles
if ( ! function_exists( 'urisp_toggle_html' ) ) {
    function urisp_toggle_html($name, $current_val, $array_name = 'urisp_settings') {
        $checked = ($current_val == 1) ? 'checked="checked"' : '';
        return '<label class="urisp-toggle">
            <input type="checkbox" name="'.$array_name.'['.$name.']" value="1" '.$checked.'>
            <span class="urisp-toggle-slider"></span>
        </label>';
    }
}
?>

<div class="urisp-settings-wrapper">
    <!-- Settings Page Loader -->
    <div class="urisp-settings-loader">
        <div class="urisp-settings-loader-spinner"></div>
    </div>

    <div class="urisp-settings-main-content" style="display: none;">
        <!-- Top Settings Header Bar -->
        <div class="urisp-settings-header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; padding: 4px 2px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-admin-settings" style="color: var(--urisp-primary); font-size: 22px; width: 22px; height: 22px;"></span>
                <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #1e293b;"><?php esc_html_e('Slider Configuration Panel', 'ultimate-responsive-image-slider'); ?></h3>
            </div>
            <button type="button" class="urisp-btn urisp-reset-settings-trigger" style="height: 34px; padding: 0 14px; font-size: 13px; font-weight: 600; color: #b91c1c; border-color: #fecaca; background: #fff5f5; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border-radius: 6px; transition: all 0.2s ease;" title="<?php esc_attr_e('Reset all configuration settings back to default values', 'ultimate-responsive-image-slider'); ?>">
                <span class="dashicons dashicons-image-rotate" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px; color: #b91c1c;"></span>
                <span><?php esc_html_e('Reset Settings to Defaults', 'ultimate-responsive-image-slider'); ?></span>
            </button>
        </div>

        <?php
        $active_admin_tab = ( isset( $post ) && isset( $post->ID ) ) ? get_post_meta( $post->ID, '_urisp_active_admin_tab', true ) : '';
        if ( empty( $active_admin_tab ) ) {
            $active_admin_tab = 'urisp-tab-add-images';
        }
        ?>
        <input type="hidden" name="urisp_active_admin_tab" id="urisp_active_admin_tab" value="<?php echo esc_attr( $active_admin_tab ); ?>" />

        <div class="urisp-tabs-nav">
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-add-images' ) ? 'active' : ''; ?>" data-target="urisp-tab-add-images">
                <span class="dashicons dashicons-database"></span>
                <span><?php esc_html_e('Content Source', 'ultimate-responsive-image-slider'); ?></span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-general' ) ? 'active' : ''; ?>" data-target="urisp-tab-general">
                <span class="dashicons dashicons-layout"></span>
                <span><?php esc_html_e('General & Layout', 'ultimate-responsive-image-slider'); ?></span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-typography' ) ? 'active' : ''; ?>" data-target="urisp-tab-typography">
                <span class="dashicons dashicons-editor-textcolor"></span>
                <span><?php esc_html_e('Typography & Buttons', 'ultimate-responsive-image-slider'); ?></span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-navigation' ) ? 'active' : ''; ?>" data-target="urisp-tab-navigation">
                <span class="dashicons dashicons-ellipsis"></span>
                <span><?php esc_html_e('Navigation & Arrows', 'ultimate-responsive-image-slider'); ?></span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-advanced' ) ? 'active' : ''; ?>" data-target="urisp-tab-advanced">
                <span class="dashicons dashicons-admin-generic"></span>
                <span><?php esc_html_e('Advanced Settings', 'ultimate-responsive-image-slider'); ?></span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-watermark' ) ? 'active' : ''; ?>" data-target="urisp-tab-watermark">
                <span class="dashicons dashicons-shield"></span>
                <span><?php esc_html_e('Watermark', 'ultimate-responsive-image-slider'); ?></span>
                <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">PRO</span>
            </div>
            <div class="urisp-nav-tab <?php echo ( $active_admin_tab === 'urisp-tab-upgrade' ) ? 'active' : ''; ?>" data-target="urisp-tab-upgrade" style="background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.3);">
                <span class="dashicons dashicons-star-filled" style="color: #f59e0b;"></span>
                <span style="color: #d97706; font-weight: 700;"><?php esc_html_e('Upgrade to Pro', 'ultimate-responsive-image-slider'); ?></span>
            </div>
        </div>

        <!-- Reset Settings Alert & Note Banner -->
        <div id="urisp-reset-notice-banner" style="display: none; margin: 0 0 25px 0; padding: 14px 18px; border-left: 4px solid #f59e0b; background: #fffbeb; border-top: 1px solid #fef3c7; border-right: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: flex-start; gap: 12px; max-width: 78%;">
                    <span class="dashicons dashicons-warning" style="color: #d97706; font-size: 24px; width: 24px; height: 24px; flex-shrink: 0; margin-top: 2px;"></span>
                    <div>
                        <strong style="color: #92400e; font-size: 14px; display: block; margin-bottom: 2px;"><?php esc_html_e('Notice: Settings Reset to Default Values', 'ultimate-responsive-image-slider'); ?></strong>
                        <p style="margin: 0; color: #b45309; font-size: 13px; line-height: 1.5;">
                            <?php esc_html_e('All slider configuration settings have been restored to their default values in this form. Please note: these changes will only be permanently saved when you click the "Update" button.', 'ultimate-responsive-image-slider'); ?>
                        </p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" class="button button-primary" onclick="jQuery('#publish').trigger('click');" style="display: inline-flex; align-items: center; gap: 6px; height: 34px; font-weight: 600; padding: 0 14px;">
                        <span class="dashicons dashicons-saved" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
                        <span><?php esc_html_e('Save & Update Now', 'ultimate-responsive-image-slider'); ?></span>
                    </button>
                    <button type="button" id="urisp-dismiss-reset-notice" style="background: none; border: none; cursor: pointer; color: #92400e; padding: 4px; font-size: 20px; line-height: 1; border-radius: 4px;" title="<?php esc_attr_e('Dismiss notice', 'ultimate-responsive-image-slider'); ?>">&times;</button>
                </div>
            </div>
        </div>

        <div class="urisp-tabs-content-wrapper">

<div id="urisp-tab-add-images" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-add-images' ) ? 'active' : ''; ?>">
    
    <!-- Content Source Switcher Card -->
    <div class="urisp-card" style="margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="margin: 0 0 4px 0; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <span class="dashicons dashicons-database" style="color: var(--urisp-primary); font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php esc_html_e('Slider Content Source', 'ultimate-responsive-image-slider'); ?>
                </h3>
                <p style="margin: 0; color: #64748b; font-size: 13px;">
                    <?php esc_html_e('Choose whether your slides are manually uploaded images or dynamically populated from WordPress content.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>
            <div class="urisp-segmented-control urisp-source-switcher">
                <input type="radio" id="slider_source_manual" name="urisp_settings[slider_source]" value="manual" <?php checked(empty($settings['slider_source']) || $settings['slider_source'] === 'manual'); ?>>
                <label for="slider_source_manual"><span class="dashicons dashicons-format-image" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span><span><?php esc_html_e('Upload Images / Manual', 'ultimate-responsive-image-slider'); ?></span></label>

                <input type="radio" id="slider_source_dynamic" name="urisp_settings[slider_source]" value="dynamic" disabled>
                <label for="slider_source_dynamic" style="opacity: 0.75; cursor: not-allowed;"><span class="dashicons dashicons-admin-post" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span><span><?php esc_html_e('Dynamic Content', 'ultimate-responsive-image-slider'); ?></span> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></label>
            </div>
        </div>
    </div>

    <!-- Container A: Manual Uploads -->
    <div id="urisp-manual-source-container" style="<?php echo (empty($settings['slider_source']) || $settings['slider_source'] === 'manual') ? '' : 'display:none;'; ?>">

    <!-- Modern Video-Gallery-style Uploader & Buttons -->
    <div class="urisp-file-upload">
        <div id="uris-add-new-slide" class="urisp-image-upload-wrap" data-uploader_title="<?php esc_attr_e('Upload Slide', 'ultimate-responsive-image-slider'); ?>" data-uploader_button_text="<?php esc_attr_e('Select', 'ultimate-responsive-image-slider'); ?>">
            <div class="urisp-drag-text">
                <span class="dashicons dashicons-cloud-upload" style="font-size: 45px; width:45px; height:45px; color: var(--urisp-primary); margin: 0 auto 10px auto; display: block;"></span>
                <h3><?php esc_html_e('ADD NEW SLIDE / IMAGE', 'ultimate-responsive-image-slider'); ?></h3>
            </div>
            <div class="urisp-upload-loading">
                <span class="dashicons dashicons-update urisp-spin" style="font-size: 45px; width:45px; height:45px; color: var(--urisp-primary); margin: 0 auto 10px auto; display: block;"></span>
                <h3 id="urisp-upload-loading-text"><?php esc_html_e('Adding images...', 'ultimate-responsive-image-slider'); ?></h3>
            </div>
        </div>
    </div>

    <div class="urisp-slides-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0; flex-wrap: wrap; gap: 12px;">
        <div class="urisp-btn-group urisp-slide-tools" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <button type="button" id="urisp-bulk-toggle-btn" class="urisp-btn urisp-btn-secondary"><span class="dashicons dashicons-forms"></span><span><?php esc_html_e('Bulk Select', 'ultimate-responsive-image-slider'); ?></span></button>

            <div id="urisp-bulk-actions-wrapper" style="display: none; gap: 8px; align-items: center;">
                <button type="button" id="urisp-select-all-btn" class="urisp-btn urisp-btn-secondary"><span class="dashicons dashicons-text-page"></span><span><?php esc_html_e('Select All', 'ultimate-responsive-image-slider'); ?></span></button>
                <button type="button" id="urisp-deselect-all-btn" class="urisp-btn urisp-btn-secondary"><span class="dashicons dashicons-dismiss"></span><span><?php esc_html_e('Deselect All', 'ultimate-responsive-image-slider'); ?></span></button>
                <button type="button" id="urisp-delete-selected-btn" class="urisp-btn urisp-btn-danger" style="display: none;"><span class="dashicons dashicons-trash"></span><span><?php esc_html_e('Delete Selected', 'ultimate-responsive-image-slider'); ?> (<span id="urisp-selected-count">0</span>)</span></button>
                </div>

            <button type="button" class="urisp-btn urisp-btn-secondary" onclick="return URISSortSlides('ASC');"><span class="dashicons dashicons-sort"></span><span><?php esc_html_e('Sort Ascending', 'ultimate-responsive-image-slider'); ?></span></button>
            <button type="button" class="urisp-btn urisp-btn-secondary" onclick="return URISSortSlides('DESC');"><span class="dashicons dashicons-sort"></span><span><?php esc_html_e('Sort Descending', 'ultimate-responsive-image-slider'); ?></span></button>
        </div>

        <div class="urisp-btn-group urisp-slider-actions" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <button type="button" id="uris-delete-all-slide" class="urisp-btn urisp-btn-danger"><span class="dashicons dashicons-trash"></span><span><?php esc_html_e('Delete All Slides', 'ultimate-responsive-image-slider'); ?></span></button>
        </div>
    </div>

    <div id="uris-container" class="uris-container" style="width:100%;">
        <?php wp_nonce_field('urisp_save_images', 'urisp_images_nonce'); ?>
        <input type="hidden" id="uris-save-action" name="uris-save-action" value="uris-save-settings">
        
        <ul id="uris-slides-container" class="clearfix SortSlides urisp-imagebox">
            <?php
            $m = 0;
            $TotalImages = 0;
            $slide_link = "";
            /* load saved photos into urisp */
            if($RISP_AllPhotosDetails = get_post_meta( $post->ID, 'ris_all_photos_details', false) != false) {
                $RISP_AllPhotosDetails = get_post_meta( $post->ID, 'ris_all_photos_details', true);
                
                $TotalImages = count($RISP_AllPhotosDetails);
                
                if($TotalImages) {
                    if(is_array($RISP_AllPhotosDetails)){
                        foreach($RISP_AllPhotosDetails as $RISP_SinglePhotoDetails) {
                            
                            $slide_id = $RISP_SinglePhotoDetails['rpgp_image_id'];
                            $attachment = get_post( $slide_id ); // get all slide details
                            if(!$attachment) continue;
                            
                            $slide_alt = ( isset($RISP_SinglePhotoDetails['slide_alt']) && $RISP_SinglePhotoDetails['slide_alt'] !== '' ) ? $RISP_SinglePhotoDetails['slide_alt'] : get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
                            $slide_caption = $attachment->post_excerpt;
                            $slide_description = ( isset($RISP_SinglePhotoDetails['slide_desc']) && $RISP_SinglePhotoDetails['slide_desc'] !== '' ) ? $RISP_SinglePhotoDetails['slide_desc'] : $attachment->post_content;
                            $slide_src = $attachment->guid; //  full image URL
                            $slide_title = ( isset($RISP_SinglePhotoDetails['slide_title']) && $RISP_SinglePhotoDetails['slide_title'] !== '' ) ? $RISP_SinglePhotoDetails['slide_title'] : $attachment->post_title; // attachment title
                            $slide_medium = wp_get_attachment_image_src($slide_id, 'medium', true); // return is array	medium image URL
                            $UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
                            $slide_link = isset($RISP_SinglePhotoDetails['rpgp_external_link']) ? $RISP_SinglePhotoDetails['rpgp_external_link'] : '';
                            $slide_type = ( isset($RISP_SinglePhotoDetails['slide_type']) && in_array($RISP_SinglePhotoDetails['slide_type'], array('image', 'video'), true) ) ? $RISP_SinglePhotoDetails['slide_type'] : 'image';
                            $slide_video_url = isset($RISP_SinglePhotoDetails['video_url']) ? $RISP_SinglePhotoDetails['video_url'] : '';
                            $slide_cta_text = isset($RISP_SinglePhotoDetails['cta_text']) ? $RISP_SinglePhotoDetails['cta_text'] : '';
                            $slide_cta_url = isset($RISP_SinglePhotoDetails['cta_url']) ? $RISP_SinglePhotoDetails['cta_url'] : '';
                            $slide_cta_style = isset($RISP_SinglePhotoDetails['cta_style']) ? $RISP_SinglePhotoDetails['cta_style'] : 'default';
                            $slide_cta_target = isset($RISP_SinglePhotoDetails['cta_target']) ? $RISP_SinglePhotoDetails['cta_target'] : '_blank';
                            $slide_orig_image_id = isset($RISP_SinglePhotoDetails['orig_image_id']) ? absint($RISP_SinglePhotoDetails['orig_image_id']) : $slide_id;
                            $slide_orig_image_url = isset($RISP_SinglePhotoDetails['orig_image_url']) ? $RISP_SinglePhotoDetails['orig_image_url'] : (isset($slide_medium[0]) ? $slide_medium[0] : '');
                            $slide_video_thumb_url = isset($RISP_SinglePhotoDetails['video_thumb_url']) ? $RISP_SinglePhotoDetails['video_thumb_url'] : '';
                            ?>
                            <li id="<?php echo esc_attr( absint( $slide_id ) ); ?>" class="uris-slide" data-position="<?php echo esc_attr( absint( $slide_id ) ); ?>" data-orig-img-src="<?php echo esc_url($slide_orig_image_url); ?>" data-orig-img-id="<?php echo esc_attr($slide_orig_image_id); ?>">
                                <label class="urisp-slide-checkbox-wrap" title="<?php esc_attr_e('Select slide', 'ultimate-responsive-image-slider'); ?>">
                                    <input type="checkbox" class="urisp-slide-checkbox">
                                    <span class="urisp-checkbox-custom"></span>
                                </label>
                                <a class="uris-slide-delete-icon" href="#" title="<?php esc_attr_e('Delete slide', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-trash"></span></a>
                                <a class="uris-slide-edit-icon" href="#" title="<?php esc_attr_e('Edit slide details', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-edit"></span></a>
                                <div class="uris-slide-meta">
                                    <img src="<?php echo !empty($slide_video_thumb_url) ? esc_url($slide_video_thumb_url) : (isset($slide_medium[0]) ? esc_url($slide_medium[0]) : ''); ?>" class="uris-slide-image" alt="<?php echo esc_attr($slide_alt); ?>" style="width:100%; height:140px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0; display:block;">
                                    <?php if ( $slide_type === 'video' && ! empty( $slide_video_url ) ) { ?>
                                        <span class="urisp-slide-video-badge" title="<?php esc_attr_e('Video Slide', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-video-alt3"></span></span>
                                    <?php } ?>
                                    <?php if ( ! empty( $slide_cta_text ) ) { ?>
                                        <span class="urisp-slide-cta-badge" title="<?php esc_attr_e('Has CTA Button', 'ultimate-responsive-image-slider'); ?>"><span class="dashicons dashicons-button"></span></span>
                                    <?php } ?>
                                    <div class="urisp-slide-inputs" style="display: none;">
                                        <input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr( $UniqueString ); ?>" />
                                        <input type="hidden" id="rpgp_image_id[]" name="rpgp_image_id[]" value="<?php echo esc_attr( absint( $slide_id ) ); ?>">
                                        <input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_attr($slide_title); ?>">
                                        <textarea id="rpgp_image_desc[]" name="rpgp_image_desc[]" class="urisp_richeditbox_<?php echo absint($m); ?> uris-slide-input-text urisp-input-styled"><?php echo esc_textarea($slide_description); ?></textarea>
                                        <input type="text" id="rpgp_external_link[]" name="rpgp_external_link[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_url($slide_link); ?>">
                                        <input type="text" id="rpgp_image_alt[]" name="rpgp_image_alt[]" class="uris-slide-input-text urisp-input-styled" value="<?php echo esc_attr($slide_alt); ?>">
                                        <input type="hidden" id="rpgp_slide_type[]" name="rpgp_slide_type[]" class="urisp-input-slide-type" value="<?php echo esc_attr($slide_type); ?>">
                                        <input type="hidden" id="rpgp_video_url[]" name="rpgp_video_url[]" class="urisp-input-video-url" value="<?php echo esc_url($slide_video_url); ?>">
                                        <input type="hidden" id="rpgp_orig_image_id[]" name="rpgp_orig_image_id[]" class="urisp-input-orig-image-id" value="<?php echo esc_attr($slide_orig_image_id); ?>">
                                        <input type="hidden" id="rpgp_orig_image_url[]" name="rpgp_orig_image_url[]" class="urisp-input-orig-image-url" value="<?php echo esc_url($slide_orig_image_url); ?>">
                                        <input type="hidden" id="rpgp_video_thumb_url[]" name="rpgp_video_thumb_url[]" class="urisp-input-video-thumb-url" value="<?php echo esc_url($slide_video_thumb_url); ?>">
                                        <input type="hidden" id="rpgp_cta_text[]" name="rpgp_cta_text[]" class="urisp-input-cta-text" value="<?php echo esc_attr($slide_cta_text); ?>">
                                        <input type="hidden" id="rpgp_cta_url[]" name="rpgp_cta_url[]" class="urisp-input-cta-url" value="<?php echo esc_url($slide_cta_url); ?>">
                                        <input type="hidden" id="rpgp_cta_style[]" name="rpgp_cta_style[]" class="urisp-input-cta-style" value="<?php echo esc_attr($slide_cta_style); ?>">
                                        <input type="hidden" id="rpgp_cta_target[]" name="rpgp_cta_target[]" class="urisp-input-cta-target" value="<?php echo esc_attr($slide_cta_target); ?>">
                                    </div>
                                </div>
                            </li>
                            <?php
                            $m++;
                        } // end of for each
                    }
                }
            }
            ?>
        </ul>
    </div>
    
    <script>
    function URISSortSlides(order){
        if(order == "ASC") {
            jQuery(".SortSlides li").sort(sort_li).appendTo('.SortSlides');
            function sort_li(a, b) {
                return (jQuery(b).data('position')) > (jQuery(a).data('position')) ? 1 : -1;
            }
        }
        if(order == "DESC") {
            jQuery(".SortSlides li").sort(sort_li).appendTo('.SortSlides');
            function sort_li(a, b) {
                return (jQuery(b).data('position')) < (jQuery(a).data('position')) ? 1 : -1;
            }
        }
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    }
    </script>
    </div><!-- Close #urisp-manual-source-container -->

    <!-- Container B: Dynamic WordPress Content Query Builder -->
    <div id="urisp-dynamic-source-container" style="<?php echo (!empty($settings['slider_source']) && $settings['slider_source'] === 'dynamic') ? '' : 'display:none;'; ?>">
        <div style="margin-bottom: 24px; padding: 20px 24px; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 20px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">
                    <span class="dashicons dashicons-lock" style="font-size: 24px; width: 24px; height: 24px;"></span>
                </div>
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #92400e; font-size: 16px; font-weight: 700;">
                        <?php esc_html_e('Dynamic Feeds & WooCommerce Integration is a Pro Feature', 'ultimate-responsive-image-slider'); ?>
                    </h4>
                    <p style="margin: 0; color: #b45309; font-size: 13.5px; line-height: 1.5;">
                        <?php esc_html_e('Automatically populate slides from published WordPress posts, WooCommerce products with price tags, or custom post types. The controls below are dummy preview fields.', 'ultimate-responsive-image-slider'); ?>
                    </p>
                </div>
            </div>
            <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #f59e0b; border-color: #d97706; color: #fff; font-weight: 700; padding: 6px 20px; height: auto; border-radius: 8px; flex-shrink: 0; text-decoration: none;">
                <?php esc_html_e('Upgrade to Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
            </a>
        </div>
        <div class="urisp-card" style="opacity: 0.8;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0 0 4px 0;"><?php esc_html_e('Dynamic Content Query Settings', 'ultimate-responsive-image-slider'); ?></h3>
                    <p style="margin: 0; color: #64748b; font-size: 13px;"><?php esc_html_e('Slides are automatically fetched from published WordPress posts or custom post types using their Featured Images and Titles.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                    <span class="dashicons dashicons-update" style="font-size: 15px; width: 15px; height: 15px;"></span>
                    <?php esc_html_e('Auto-Syncing Content', 'ultimate-responsive-image-slider'); ?>
                </span>
            </div>

            <!-- Content Post Type -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-admin-post"></i> <?php esc_html_e('Content Post Type', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Select which post type to pull featured images and titles from.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <select id="urisp_dynamic_post_type" disabled>
                        <?php 
                        require_once( RISP_PLUGIN_DIR_PATH . 'includes/dynamic-source.php' );
                        $avail_pts = urisp_get_available_post_types();
                        $curr_pt = ! empty( $settings['dynamic_post_type'] ) ? $settings['dynamic_post_type'] : 'post';
                        foreach ( $avail_pts as $pt_key => $pt_label ) {
                            echo '<option value="' . esc_attr( $pt_key ) . '" ' . selected( $curr_pt, $pt_key, false ) . '>' . esc_html( $pt_label ) . ' (' . esc_html( $pt_key ) . ')</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Filter by Taxonomy -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-category"></i> <?php esc_html_e('Filter by Taxonomy', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Optionally filter content by a category or custom taxonomy.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <select id="urisp_dynamic_taxonomy" disabled>
                        <option value=""><?php esc_html_e('-- All Taxonomies / No Filter --', 'ultimate-responsive-image-slider'); ?></option>
                        <?php 
                        $curr_tax = ! empty( $settings['dynamic_taxonomy'] ) ? $settings['dynamic_taxonomy'] : '';
                        $avail_taxes = urisp_get_taxonomies_for_post_type( $curr_pt );
                        foreach ( $avail_taxes as $tax_key => $tax_label ) {
                            echo '<option value="' . esc_attr( $tax_key ) . '" ' . selected( $curr_tax, $tax_key, false ) . '>' . esc_html( $tax_label ) . ' (' . esc_html( $tax_key ) . ')</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Taxonomy Terms / Slugs -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-tag"></i> <?php esc_html_e('Taxonomy Terms / Slugs', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Comma-separated category or term slugs/IDs to filter by (leave blank for all).', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <input type="text" disabled readonly class="urisp-input-styled" value="<?php echo esc_attr( ! empty( $settings['dynamic_term'] ) ? $settings['dynamic_term'] : '' ); ?>" placeholder="e.g. news, featured, 12">
                </div>
            </div>

            <!-- Maximum Slides Count -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-images-alt2"></i> <?php esc_html_e('Maximum Slides Count', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Maximum number of dynamic slides to load (1 - 50).', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <input type="number" disabled readonly min="1" max="50" class="urisp-input-styled" value="<?php echo esc_attr( ! empty( $settings['dynamic_posts_count'] ) ? $settings['dynamic_posts_count'] : 10 ); ?>" style="width: 120px;">
                </div>
            </div>

            <!-- Dynamic Order By -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-sort"></i> <?php esc_html_e('Dynamic Order By', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Select criterion for sorting dynamic slides.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <?php $curr_orderby = ! empty( $settings['dynamic_orderby'] ) ? $settings['dynamic_orderby'] : 'date'; ?>
                    <select disabled>
                        <option value="date" <?php selected( $curr_orderby, 'date' ); ?>><?php esc_html_e('Published Date', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="title" <?php selected( $curr_orderby, 'title' ); ?>><?php esc_html_e('Title', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="modified" <?php selected( $curr_orderby, 'modified' ); ?>><?php esc_html_e('Last Modified Date', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="rand" <?php selected( $curr_orderby, 'rand' ); ?>><?php esc_html_e('Random', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="comment_count" <?php selected( $curr_orderby, 'comment_count' ); ?>><?php esc_html_e('Most Commented', 'ultimate-responsive-image-slider'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Dynamic Order Direction -->
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-arrow-down-alt2"></i> <?php esc_html_e('Dynamic Order Direction', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Sort order direction.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <div class="urisp-segmented-control">
                        <?php $curr_order = ! empty( $settings['dynamic_order'] ) ? strtoupper( $settings['dynamic_order'] ) : 'DESC'; ?>
                        <input type="radio" id="dynamic_order_desc" disabled value="DESC" <?php checked( $curr_order, 'DESC' ); ?>>
                        <label for="dynamic_order_desc"><?php esc_html_e('Descending (Newest first)', 'ultimate-responsive-image-slider'); ?></label>

                        <input type="radio" id="dynamic_order_asc" disabled value="ASC" <?php checked( $curr_order, 'ASC' ); ?>>
                        <label for="dynamic_order_asc"><?php esc_html_e('Ascending (Oldest first)', 'ultimate-responsive-image-slider'); ?></label>
                    </div>
                </div>
            </div>

            <!-- WooCommerce Product Display Options -->
            <?php 
            $wc_active = class_exists('WooCommerce');
            ?>
            <div id="urisp-woocommerce-config" style="<?php echo ( $curr_pt === 'product' ) ? '' : 'display:none;'; ?> margin-top: 20px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                <div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-cart" style="color: #4f46e5; font-size: 20px; width: 20px; height: 20px;"></span>
                        <strong style="color: #1e293b; font-size: 14px;"><?php esc_html_e('WooCommerce Product Display Options', 'ultimate-responsive-image-slider'); ?></strong>
                    </div>
                    <?php if ( ! $wc_active ) { ?>
                        <span style="font-size: 12px; color: #dc2626; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-weight: 500;">
                            <?php esc_html_e('WooCommerce is not active', 'ultimate-responsive-image-slider'); ?>
                        </span>
                    <?php } ?>
                </div>

                <div class="urisp-setting-row" style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #edf2f7;">
                    <div class="urisp-setting-label">
                        <h5><?php esc_html_e('Show Product Price', 'ultimate-responsive-image-slider'); ?></h5>
                        <p><?php esc_html_e('Display the product price badge on the top corner of the slide.', 'ultimate-responsive-image-slider'); ?></p>
                    </div>
                    <div class="urisp-setting-field">
                        <label class="urisp-toggle" style="cursor: not-allowed; opacity: 0.6;"><input type="checkbox" disabled value="0"><span class="urisp-toggle-slider"></span></label>
                    </div>
                </div>

                <div class="urisp-setting-row" style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #edf2f7;">
                    <div class="urisp-setting-label">
                        <h5><?php esc_html_e('Show On-Sale Badge', 'ultimate-responsive-image-slider'); ?></h5>
                        <p><?php esc_html_e('Display a highlighted "Sale!" badge on discounted products.', 'ultimate-responsive-image-slider'); ?></p>
                    </div>
                    <div class="urisp-setting-field">
                        <label class="urisp-toggle" style="cursor: not-allowed; opacity: 0.6;"><input type="checkbox" disabled value="0"><span class="urisp-toggle-slider"></span></label>
                    </div>
                </div>

                <div class="urisp-setting-row" style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #edf2f7;">
                    <div class="urisp-setting-label">
                        <h5><?php esc_html_e('Show Add to Cart / View Button', 'ultimate-responsive-image-slider'); ?></h5>
                        <p><?php esc_html_e('Display an action button linking directly to the product cart action.', 'ultimate-responsive-image-slider'); ?></p>
                    </div>
                    <div class="urisp-setting-field">
                        <label class="urisp-toggle" style="cursor: not-allowed; opacity: 0.6;"><input type="checkbox" disabled value="0"><span class="urisp-toggle-slider"></span></label>
                    </div>
                </div>
            </div>

            <!-- Safe Notice for Manual Slides -->
            <div style="margin-top: 20px; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 10px;">
                <span class="dashicons dashicons-info" style="color: #6366f1; font-size: 20px; width: 20px; height: 20px;"></span>
                <span><?php esc_html_e('Manual slides are preserved safely in your database. Switch back to "Upload Images / Manual" anytime to view and edit them.', 'ultimate-responsive-image-slider'); ?></span>
            </div>

        </div>
    </div>
</div>

<div id="urisp-tab-general" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-general' ) ? 'active' : ''; ?>">
    
    <!-- Card 1: Layout & Behavior Settings -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Layout & Behavior Settings', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row" style="display: block; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
            <div class="urisp-setting-label" style="max-width: 100%; margin-bottom: 14px;">
                <h5><i class="dashicons dashicons-layout"></i> <?php esc_html_e('Slider Layout', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Select from 5 responsive visual architectures. Click any card to apply the layout immediately.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field" style="width: 100%;">
                <div class="urisp-layout-selector-grid">
                    <?php
                    $layouts = array(
                        1 => array(
                            'title' => __( 'Layout 1: Standard', 'ultimate-responsive-image-slider' ),
                            'desc'  => __( 'Flagship slider with bottom title & desc navigation tabs', 'ultimate-responsive-image-slider' ),
                            'img'   => RISP_PLUGIN_URL . 'assets/img/layout-1.jpg',
                        ),
                        2 => array(
                            'title' => __( 'Layout 2: Grid', 'ultimate-responsive-image-slider' ),
                            'desc'  => __( 'Multi-column thumbnail gallery grid with lightbox modal', 'ultimate-responsive-image-slider' ),
                            'img'   => RISP_PLUGIN_URL . 'assets/img/layout-2.jpg',
                        ),
                        3 => array(
                            'title' => __( 'Layout 3: Thumbnails', 'ultimate-responsive-image-slider' ),
                            'desc'  => __( 'Classic responsive slider with horizontal bottom thumbnails', 'ultimate-responsive-image-slider' ),
                            'img'   => RISP_PLUGIN_URL . 'assets/img/layout-3.jpg',
                        ),
                        4 => array(
                            'title' => __( 'Layout 4: Filmstrip', 'ultimate-responsive-image-slider' ),
                            'desc'  => __( 'Vertical / side-docked thumbnails filmstrip layout', 'ultimate-responsive-image-slider' ),
                            'img'   => RISP_PLUGIN_URL . 'assets/img/layout-4.jpg',
                        ),
                        5 => array(
                            'title' => __( 'Layout 5: Carousel', 'ultimate-responsive-image-slider' ),
                            'desc'  => __( 'Dynamic perspective carousel with multi-slide visible view', 'ultimate-responsive-image-slider' ),
                            'img'   => RISP_PLUGIN_URL . 'assets/img/layout-5.jpg',
                        ),
                    );
                    foreach ( $layouts as $layout_id => $layout_data ) :
                        $is_free_layout = ( (int) $layout_id === 3 );
                        $is_active      = ( (int) $layout_id === 3 );
                    ?>
                    <div class="urisp-layout-card <?php echo $is_active ? 'selected' : ''; ?> <?php echo ! $is_free_layout ? 'urisp-pro-locked-card' : ''; ?>" data-layout-id="<?php echo absint($layout_id); ?>" data-is-pro="<?php echo ! $is_free_layout ? '1' : '0'; ?>">
                        <?php if ( $is_free_layout ) : ?>
                            <input type="radio" id="slider_layout_<?php echo absint($layout_id); ?>" name="urisp_settings[slider_layout]" value="<?php echo absint($layout_id); ?>" checked="checked">
                        <?php else : ?>
                            <span class="urisp-pro-badge" style="position: absolute; top: 10px; right: 10px; background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); z-index: 2;">PRO</span>
                        <?php endif; ?>
                        <img src="<?php echo esc_url( $layout_data['img'] ); ?>" alt="<?php echo esc_attr( $layout_data['title'] ); ?>" loading="lazy">
                        <strong><?php echo esc_html( $layout_data['title'] ); ?></strong>
                        <span class="urisp-layout-card-desc"><?php echo esc_html( $layout_data['desc'] ); ?></span>
                        <?php if ( $is_free_layout ) : ?>
                            <span class="urisp-layout-active-badge"><?php esc_html_e( 'Active', 'ultimate-responsive-image-slider' ); ?></span>
                        <?php else : ?>
                            <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="urisp-layout-active-badge urisp-pro-link" style="background: #fef3c7; color: #b45309; text-decoration: none;" onclick="event.stopPropagation();">
                                <?php esc_html_e( 'Unlock in Pro', 'ultimate-responsive-image-slider' ); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-art"></i> <?php esc_html_e('Skin Preset', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose an instant visual aesthetic theme for your slider.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-skin-preset-picker">
                    <?php 
                    $current_skin_preset = ! empty( $settings['skin_preset'] ) ? $settings['skin_preset'] : 'default';
                    $skin_presets = array(
                        'default'       => array(
                            'label'  => __('Default', 'ultimate-responsive-image-slider'),
                            'desc'   => __('Classic clean styling', 'ultimate-responsive-image-slider'),
                            'icon'   => 'dashicons-admin-appearance',
                            'is_pro' => false,
                        ),
                        'glassmorphism' => array(
                            'label'  => __('Glassmorphism', 'ultimate-responsive-image-slider'),
                            'desc'   => __('Frosted blur & glow', 'ultimate-responsive-image-slider'),
                            'icon'   => 'dashicons-visibility',
                            'is_pro' => true,
                        ),
                        'minimalist'    => array(
                            'label'  => __('Minimalist', 'ultimate-responsive-image-slider'),
                            'desc'   => __('Clean monochrome lines', 'ultimate-responsive-image-slider'),
                            'icon'   => 'dashicons-minus',
                            'is_pro' => true,
                        ),
                        'vibrant'       => array(
                            'label'  => __('Vibrant', 'ultimate-responsive-image-slider'),
                            'desc'   => __('Rich gradient accents', 'ultimate-responsive-image-slider'),
                            'icon'   => 'dashicons-color-picker',
                            'is_pro' => true,
                        ),
                        'neumorphic'    => array(
                            'label'  => __('Neumorphic', 'ultimate-responsive-image-slider'),
                            'desc'   => __('Soft tactile shadows', 'ultimate-responsive-image-slider'),
                            'icon'   => 'dashicons-star-filled',
                            'is_pro' => true,
                        ),
                    );
                    foreach ( $skin_presets as $skin_key => $skin_info ) :
                        $is_active_skin = ( $current_skin_preset === $skin_key );
                        $is_pro_skin    = ! empty( $skin_info['is_pro'] );
                    ?>
                    <label class="urisp-skin-choice <?php echo $is_active_skin ? 'active' : ''; ?>" style="<?php echo $is_pro_skin ? 'position: relative; opacity: 0.7; cursor: pointer;' : ''; ?>" <?php echo $is_pro_skin ? 'onclick="window.open(\'https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/\', \'_blank\'); return false;"' : ''; ?>>
                        <input type="radio" name="urisp_settings[skin_preset]" value="<?php echo esc_attr( $skin_key ); ?>" <?php checked( $current_skin_preset, $skin_key ); ?> <?php echo $is_pro_skin ? 'disabled' : ''; ?>>
                        <?php if ( $is_pro_skin ) : ?>
                            <span style="position: absolute; top: 6px; right: 6px; background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; line-height: 1.3; box-shadow: 0 1px 3px rgba(245, 158, 11, 0.3);">PRO</span>
                        <?php endif; ?>
                        <div class="urisp-skin-icon">
                            <span class="dashicons <?php echo esc_attr( $skin_info['icon'] ); ?>"></span>
                        </div>
                        <span class="urisp-skin-title"><?php echo esc_html( $skin_info['label'] ); ?></span>
                        <span class="urisp-skin-desc"><?php echo esc_html( $skin_info['desc'] ); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-controls-play"></i> <?php esc_html_e('Auto Play Slide Show', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Configure the slideshow auto-advance behavior.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="auto_slideshow_1" name="urisp_settings[auto_slideshow]" value="1" <?php checked($settings['auto_slideshow'], 1); ?>>
                    <label for="auto_slideshow_1"><?php esc_html_e('Yes', 'ultimate-responsive-image-slider'); ?></label>
                    
                    <input type="radio" id="auto_slideshow_2" name="urisp_settings[auto_slideshow]" value="2" <?php checked($settings['auto_slideshow'], 2); ?>>
                    <label for="auto_slideshow_2"><?php esc_html_e('Pause on Hover', 'ultimate-responsive-image-slider'); ?></label>
                    
                    <input type="radio" id="auto_slideshow_3" name="urisp_settings[auto_slideshow]" value="3" <?php checked($settings['auto_slideshow'], 3); ?>>
                    <label for="auto_slideshow_3"><?php esc_html_e('No', 'ultimate-responsive-image-slider'); ?></label>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-sort"></i> <?php esc_html_e('Slide Order', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose how slides are ordered.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="slide_order_asc" name="urisp_settings[slide_order]" value="ASC" <?php checked($settings['slide_order'], 'ASC'); ?>>
                    <label for="slide_order_asc"><?php esc_html_e('Ascending', 'ultimate-responsive-image-slider'); ?></label>
                    
                    <input type="radio" id="slide_order_desc" name="urisp_settings[slide_order]" value="DESC" <?php checked($settings['slide_order'], 'DESC'); ?>>
                    <label for="slide_order_desc"><?php esc_html_e('Descending', 'ultimate-responsive-image-slider'); ?></label>
                    
                    <input type="radio" id="slide_order_shuffle" name="urisp_settings[slide_order]" value="shuffle" <?php checked($settings['slide_order'], 'shuffle'); ?>>
                    <label for="slide_order_shuffle"><?php esc_html_e('Random', 'ultimate-responsive-image-slider'); ?></label>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-leftright"></i> <?php esc_html_e('Slide Distance', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Set the gap between slides in pixels.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[slide_distance]">
                    <?php 
                    $dist_options = array(0, 5, 10, 15, 20, 25);
                    $cur_dist = isset($settings['slide_distance']) ? (int)$settings['slide_distance'] : 5;
                    if ( ! in_array( $cur_dist, $dist_options, true ) ) {
                        $dist_options[] = $cur_dist;
                        sort($dist_options);
                    }
                    foreach($dist_options as $val): ?>
                    <option value="<?php echo absint($val); ?>" <?php selected($cur_dist, $val); ?>><?php echo absint($val); ?> px</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-clock"></i> <?php esc_html_e('Transition Speed', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Delay duration between automatic slide transitions.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[transition_speed]">
                    <?php 
                    $spd_options = array(1000, 2000, 3000, 4000, 5000, 6000, 7000);
                    $cur_spd = isset($settings['transition_speed']) ? (int)$settings['transition_speed'] : 5000;
                    if ( ! in_array( $cur_spd, $spd_options, true ) ) {
                        $spd_options[] = $cur_spd;
                        sort($spd_options);
                    }
                    foreach($spd_options as $val): ?>
                    <option value="<?php echo absint($val); ?>" <?php selected($cur_spd, $val); ?>><?php echo esc_html(($val/1000).' Seconds'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="1,3,4,5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-randomize"></i> <?php esc_html_e('Slider Transition Effect', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Animation style used when changing slides.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="transition_effect_0" name="urisp_settings[transition_effect]" value="0" <?php checked($settings['transition_effect'], 0); ?>>
                    <label for="transition_effect_0"><?php esc_html_e('Slide Animation', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="transition_effect_1" name="urisp_settings[transition_effect]" value="1" <?php checked($settings['transition_effect'], 1); ?>>
                    <label for="transition_effect_1"><?php esc_html_e('Fade Animation', 'ultimate-responsive-image-slider'); ?></label>
                </div>
            </div>
        </div>



        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-format-image"></i> <?php esc_html_e('Image Source Size', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose which image size WordPress loads. Using "Large" significantly improves page speed for high-resolution uploads.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[image_size]">
                    <option value="full" <?php selected(!empty($settings['image_size']) ? $settings['image_size'] : 'full', 'full'); ?>><?php esc_html_e('Full (Original)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="large" <?php selected(!empty($settings['image_size']) ? $settings['image_size'] : 'full', 'large'); ?>><?php esc_html_e('Large (max 1024px)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="medium_large" <?php selected(!empty($settings['image_size']) ? $settings['image_size'] : 'full', 'medium_large'); ?>><?php esc_html_e('Medium Large (max 768px)', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5>
                    <i class="dashicons dashicons-camera"></i> <?php esc_html_e('Ken Burns Zoom Effect', 'ultimate-responsive-image-slider'); ?>
                    <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">PRO</span>
                </h5>
                <p><?php esc_html_e('Cinematic slow pan and zoom animation on active slide images.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                    <label class="urisp-toggle" style="cursor: not-allowed; opacity: 0.6;" title="<?php esc_attr_e('Available in Pro version', 'ultimate-responsive-image-slider'); ?>">
                        <input type="checkbox" disabled value="0">
                        <span class="urisp-toggle-slider"></span>
                    </label>
                    <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-small" style="background: #fef3c7; border-color: #fde68a; color: #b45309; font-weight: 600; text-decoration: none;">
                        <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-smartphone"></i> <?php esc_html_e('Touch Swipe Sensitivity', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Adjust touch swipe gesture sensitivity on mobile devices and touchscreens.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <?php 
                    $t_thresh = ! empty($settings['touch_swipe_threshold']) ? absint($settings['touch_swipe_threshold']) : 50; 
                    if ( ! in_array( $t_thresh, array( 25, 50, 80 ), true ) ) {
                        $t_thresh = 50;
                    }
                    ?>
                    <input type="radio" id="touch_swipe_high" name="urisp_settings[touch_swipe_threshold]" value="25" <?php checked($t_thresh, 25); ?>>
                    <label for="touch_swipe_high" title="<?php esc_attr_e('Fast flick (25px threshold)', 'ultimate-responsive-image-slider'); ?>"><?php esc_html_e('High (25px)', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="touch_swipe_med" name="urisp_settings[touch_swipe_threshold]" value="50" <?php checked($t_thresh, 50); ?>>
                    <label for="touch_swipe_med" title="<?php esc_attr_e('Standard balanced (50px threshold)', 'ultimate-responsive-image-slider'); ?>"><?php esc_html_e('Medium (50px)', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="touch_swipe_low" name="urisp_settings[touch_swipe_threshold]" value="80" <?php checked($t_thresh, 80); ?>>
                    <label for="touch_swipe_low" title="<?php esc_attr_e('Firm drag (80px threshold)', 'ultimate-responsive-image-slider'); ?>"><?php esc_html_e('Low (80px)', 'ultimate-responsive-image-slider'); ?></label>
                </div>
            </div>
        </div>


    </div>
    
    <!-- Card 2: Title & Description Visibility -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Title & Description Visibility', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-heading"></i> <?php esc_html_e('Show Slider Title', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Display the main title above the slider.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('slide_title', $settings['slide_title']); ?>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-textcolor"></i> <?php esc_html_e('Show Slide Title inside Slider', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Display the individual slide title on top of the image.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('show_slide_title', $settings['show_slide_title']); ?>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-text-page"></i> <?php esc_html_e('Show Slide Description', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Display the individual slide description text.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('show_slide_desc', $settings['show_slide_desc']); ?>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Dimensions & Scaling -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Slider Dimensions & Scaling', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-expand"></i> <?php esc_html_e('Slider Dimensions', 'ultimate-responsive-image-slider'); ?></h5>
                <p id="urisp-dimensions-hint">
                    <?php 
                    $is_l2 = isset( $settings['slider_layout'] ) && (int) $settings['slider_layout'] === 2;
                    if ( $is_l2 ) {
                        esc_html_e('Configure width and height (Recommended size: 480x320 px for Layout 2).', 'ultimate-responsive-image-slider');
                    } else {
                        esc_html_e('Configure width and height (Recommended size: 1280x720 px).', 'ultimate-responsive-image-slider');
                    }
                    ?>
                </p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="number" name="urisp_settings[slider_width]" id="urisp_slider_width" value="<?php echo esc_attr( ! empty( $settings['slider_width'] ) && absint( $settings['slider_width'] ) > 100 ? absint( $settings['slider_width'] ) : ( $is_l2 ? 480 : 1280 ) ); ?>" style="width:85px; text-align:center; border-radius:6px; border:1px solid #ccc; padding:6px;" min="200" max="3840" step="10">
                    <select name="urisp_settings[width_mode]" id="urisp_width_mode">
                        <option value="custom" <?php selected( $settings['width_mode'] !== 'fullWidth', true ); ?>><?php esc_html_e( 'px', 'ultimate-responsive-image-slider' ); ?></option>
                        <option value="fullWidth" <?php selected( $settings['width_mode'], 'fullWidth' ); ?>><?php esc_html_e( 'Full Width', 'ultimate-responsive-image-slider' ); ?></option>
                    </select>
                    <span style="font-weight:bold; color:#888;">X</span>
                    <input type="number" name="urisp_settings[slider_height]" id="urisp_slider_height" value="<?php echo esc_attr( ! empty( $settings['slider_height'] ) ? absint( $settings['slider_height'] ) : ( $is_l2 ? 320 : 720 ) ); ?>" style="width:85px; text-align:center; border-radius:6px; border:1px solid #ccc; padding:6px;" min="100" max="2160" step="10">
                    <select name="urisp_settings[height_mode]" id="urisp_height_mode">
                        <option value="custom" <?php selected( $settings['height_mode'], 'custom' ); ?>><?php esc_html_e( 'px', 'ultimate-responsive-image-slider' ); ?></option>
                        <option value="auto" <?php selected( $settings['height_mode'], 'auto' ); ?>><?php esc_html_e( 'Auto', 'ultimate-responsive-image-slider' ); ?></option>
                    </select>
                    <button type="button" id="urisp-apply-recommended-dims" class="button button-small" style="margin-left:5px; height:32px; border-radius:6px; line-height:30px; font-size:12px; display:inline-flex; align-items:center; gap:4px;" title="<?php esc_attr_e( 'Click to apply recommended dimensions for this layout', 'ultimate-responsive-image-slider' ); ?>">
                        <i class="dashicons dashicons-image-rotate" style="font-size:14px; width:14px; height:14px; line-height:14px;"></i>
                        <span id="urisp-recommended-btn-text"><?php echo $is_l2 ? esc_html__( 'Use 480x320', 'ultimate-responsive-image-slider' ) : esc_html__( 'Use 1280x720', 'ultimate-responsive-image-slider' ); ?></span>
                    </button>
                </div>
                <p class="description" style="margin-top:6px; font-size:12px; color:#666;">
                    <?php esc_html_e( 'Sliders are fully responsive and automatically adapt down to tablet and mobile screens.', 'ultimate-responsive-image-slider' ); ?>
                </p>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-image-crop"></i> <?php esc_html_e('Image Scaling Mode', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose how slide images scale to fit the slider dimensions.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[scale_mode]">
                    <option value="cover" <?php selected($settings['scale_mode'], 'cover'); ?>><?php esc_html_e('Cover (Fill Container)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="contain" <?php selected($settings['scale_mode'], 'contain'); ?>><?php esc_html_e('Contain (Fit Inside)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="exactFit" <?php selected($settings['scale_mode'], 'exactFit'); ?>><?php esc_html_e('Exact Fit (Stretch)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="none" <?php selected($settings['scale_mode'], 'none'); ?>><?php esc_html_e('None (Original Image Size)', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-image-rotate-right"></i> <?php esc_html_e('Auto Scale Up', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Allow images to upscale larger than their original resolution if container is bigger.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('auto_scale', isset($settings['auto_scale']) ? $settings['auto_scale'] : 1); ?>
            </div>
        </div>
    </div>

    <!-- Card 4: Layout 5 Transition Direction (Pro Feature - Shifted to Bottom of Tab) -->
    <div class="urisp-card" data-layouts="5" style="border-left: 4px solid #f59e0b; background: #fdfdfd;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h3 style="margin: 0; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-lock" style="color: #d97706;"></span>
                <?php esc_html_e('Layout 5 Transition Direction', 'ultimate-responsive-image-slider'); ?>
                <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">PRO</span>
            </h3>
            <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="font-size: 12px; font-weight: 700; color: #b45309; text-decoration: none;">
                <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
            </a>
        </div>

        <div class="urisp-setting-row" data-layouts="5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Sliding Direction (Layout 5)', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></h5>
                <p><?php esc_html_e('Orientation for thumbnail carousel transition in Layout 5.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[sliding_direction]" disabled style="cursor: not-allowed; opacity: 0.7;">
                    <option value="vertical" <?php selected($settings['sliding_direction'], 'vertical'); ?>><?php esc_html_e('Vertical', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="horizontal" <?php selected($settings['sliding_direction'], 'horizontal'); ?>><?php esc_html_e('Horizontal', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="urisp-tab-typography" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-typography' ) ? 'active' : ''; ?>">
    
    <!-- Preload Google Fonts & Custom Font-Faces for Live Preview -->
    <?php wp_enqueue_style( 'urisp-google-preview-fonts', 'https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&family=Comic+Neue:wght@400;700&family=Lexend:wght@400;600&display=swap', array(), RISP_PLUGIN_VER ); ?>
    <style id="urisp-slider-custom-fonts">
        <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-sanitized custom font CSS rules.
        echo wp_strip_all_tags( urisp_fonts()->get_all_custom_fonts_css() ); ?>
    </style>

    <!-- Card 1: Typography Style Settings -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Typography & Alignment', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-bold"></i> <?php esc_html_e('Font Style', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Select typography for slide titles and descriptions, choose accessibility presets, or upload a custom font.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <select name="urisp_settings[font_style]" id="urisp_font_style_select" style="min-width: 260px;">
                        <?php 
                        $grouped_fonts = urisp_fonts()->get_grouped_fonts_for_select();
                        $custom_fonts  = $grouped_fonts['custom'];
                        $acc_fonts     = $grouped_fonts['accessibility'];
                        $sys_fonts     = $grouped_fonts['system'];

                        // 1. Web-Safe System Fonts (Active in Free)
                        echo '<optgroup label="' . esc_attr__( '🔤 Standard System Fonts', 'ultimate-responsive-image-slider' ) . '">';
                        foreach ( $sys_fonts as $sf ) {
                            echo '<option value="' . esc_attr( $sf ) . '" ' . selected( $settings['font_style'], $sf, false ) . '>' . esc_html( $sf ) . '</option>';
                        }
                        echo '</optgroup>';

                        // 2. Dyslexia & Accessibility Presets (Pro - Shifted Below Active)
                        if ( ! empty( $acc_fonts ) ) {
                            echo '<optgroup label="' . esc_attr__( '♿ Dyslexia & Accessibility Presets (Pro)', 'ultimate-responsive-image-slider' ) . '">';
                            foreach ( $acc_fonts as $af_key => $af_data ) {
                                echo '<option value="' . esc_attr( $af_data['name'] ) . '" disabled>' . esc_html( $af_data['label'] ) . '</option>';
                            }
                            echo '</optgroup>';
                        }

                        // 3. Custom Uploaded Fonts (Pro - Shifted Below Active)
                        if ( ! empty( $custom_fonts ) ) {
                            echo '<optgroup id="urisp-optgroup-custom" label="' . esc_attr__( '🌟 Uploaded Custom Fonts (Pro)', 'ultimate-responsive-image-slider' ) . '">';
                            foreach ( $custom_fonts as $cf ) {
                                echo '<option value="' . esc_attr( $cf['name'] ) . '" disabled>' . esc_html( $cf['name'] ) . ' (' . esc_html( strtoupper( $cf['format'] ) ) . ')</option>';
                            }
                            echo '</optgroup>';
                        } else {
                            echo '<optgroup id="urisp-optgroup-custom" label="' . esc_attr__( '🌟 Uploaded Custom Fonts (Pro)', 'ultimate-responsive-image-slider' ) . '" style="display:none;"></optgroup>';
                        }
                        ?>
                    </select>

                    <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" rel="noopener noreferrer" class="button" style="display: inline-flex; align-items: center; gap: 5px; height: 32px; font-weight: 600; color: #b45309; border-color: #fde68a; background: #fffbeb; border-radius: 6px; text-decoration: none;" title="<?php esc_attr_e( 'Custom font upload is a Pro feature - upgrade to unlock', 'ultimate-responsive-image-slider' ); ?>">
                        <span class="dashicons dashicons-external" style="font-size: 15px; width: 15px; height: 15px; line-height: 1; color: #d97706;"></span>
                        <span><?php esc_html_e( '+ Upload Font', 'ultimate-responsive-image-slider' ); ?></span>
                        <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 4px; border-radius: 3px;">PRO</span>
                    </a>

                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ris_gallery&page=urisp_global_settings&urisp_tab=custom-fonts' ) ); ?>" target="_blank" style="font-size: 12px; color: #6366f1; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="<?php esc_attr_e( 'Manage all custom and accessibility fonts in Global Settings', 'ultimate-responsive-image-slider' ); ?>">
                        <span class="dashicons dashicons-admin-generic" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></span>
                        <span><?php esc_html_e( 'Manage Fonts', 'ultimate-responsive-image-slider' ); ?> &rarr;</span>
                    </a>
                </div>

                <!-- Live Preview of Selected Typography -->
                <div id="urisp-font-preview-box" style="margin-top: 10px; padding: 10px 14px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; font-size: 15px; line-height: 1.5; color: #0f172a; font-family: '<?php echo esc_attr( ! empty( $settings['font_style'] ) ? $settings['font_style'] : ( ! empty( $settings['font_family'] ) ? $settings['font_family'] : 'Arial' ) ); ?>', sans-serif; transition: font-family 0.2s ease;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; font-weight: 600;">
                        <?php esc_html_e( 'Live Preview:', 'ultimate-responsive-image-slider' ); ?> <span id="urisp-current-font-label" style="color: #6366f1;"><?php echo esc_html( ! empty( $settings['font_style'] ) ? $settings['font_style'] : ( ! empty( $settings['font_family'] ) ? $settings['font_family'] : 'Arial' ) ); ?></span>
                    </div>
                    <div id="urisp-font-sample-text">
                        The quick brown fox jumps over the lazy dog (12345)
                    </div>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-textcolor"></i> <?php esc_html_e('Slide Overlay Font Sizes', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Set font size for the slide title and description overlay text.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:13px; font-weight:600; color:var(--urisp-text);"><?php esc_html_e('Title Size:', 'ultimate-responsive-image-slider'); ?></span>
                        <select name="urisp_settings[slide_in_title_size]" style="padding:6px 25px 6px 10px !important;">
                            <?php foreach(range(10, 32) as $ts): ?>
                            <option value="<?php echo absint($ts); ?>" <?php selected(isset($settings['slide_in_title_size']) ? $settings['slide_in_title_size'] : (isset($settings['title_font_size']) ? $settings['title_font_size'] : 16), $ts); ?>><?php echo absint($ts); ?> px</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:13px; font-weight:600; color:var(--urisp-text);"><?php esc_html_e('Description Size:', 'ultimate-responsive-image-slider'); ?></span>
                        <select name="urisp_settings[slide_in_desc_size]" style="padding:6px 25px 6px 10px !important;">
                            <?php foreach(range(6, 24) as $ds): ?>
                            <option value="<?php echo absint($ds); ?>" <?php selected(isset($settings['slide_in_desc_size']) ? $settings['slide_in_desc_size'] : (isset($settings['desc_font_size']) ? $settings['desc_font_size'] : 14), $ds); ?>><?php echo absint($ds); ?> px</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="1,2,3,4,5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-alignleft"></i> <?php esc_html_e('Caption Position', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Position the caption card inside the slide viewport.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[text_alignment]">
                    <option value="topLeft" <?php selected($settings['text_alignment'], 'topLeft'); ?>><?php esc_html_e('Top Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="topCenter" <?php selected($settings['text_alignment'], 'topCenter'); ?>><?php esc_html_e('Top Center', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="topRight" <?php selected($settings['text_alignment'], 'topRight'); ?>><?php esc_html_e('Top Right', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="centerLeft" <?php selected($settings['text_alignment'], 'centerLeft'); ?>><?php esc_html_e('Middle Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="centerCenter" <?php selected($settings['text_alignment'], 'centerCenter'); ?>><?php esc_html_e('Center', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="centerRight" <?php selected($settings['text_alignment'], 'centerRight'); ?>><?php esc_html_e('Middle Right', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottomLeft" <?php selected($settings['text_alignment'], 'bottomLeft'); ?>><?php esc_html_e('Bottom Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottomCenter" <?php selected($settings['text_alignment'], 'bottomCenter'); ?>><?php esc_html_e('Bottom Center', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottomRight" <?php selected($settings['text_alignment'], 'bottomRight'); ?>><?php esc_html_e('Bottom Right', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="1,2,3,4,5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-controls-play"></i> <?php esc_html_e('Title & Description Transition Effect', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Direction from which the slide title and description animate in.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[title_transition]">
                    <optgroup label="<?php esc_attr_e('Standard (Free)', 'ultimate-responsive-image-slider'); ?>">
                        <option value="up" <?php selected($settings['title_transition'], 'up'); ?>><?php esc_html_e('Slide From Up', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="down" <?php selected($settings['title_transition'], 'down'); ?>><?php esc_html_e('Slide From Down', 'ultimate-responsive-image-slider'); ?></option>
                    </optgroup>
                    <optgroup label="<?php esc_attr_e('Advanced Transitions (PRO)', 'ultimate-responsive-image-slider'); ?>">
                        <option value="left" disabled><?php esc_html_e('Slide From Left [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="right" disabled><?php esc_html_e('Slide From Right [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="bounce" disabled><?php esc_html_e('Bounce In [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="zoom" disabled><?php esc_html_e('Zoom In [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Typography Colors & Background Opacity -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Colors & Background Opacity', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-art"></i> <?php esc_html_e('Slide Typography Colors', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Configure colors for the title and description text.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field urisp-flex-column" style="width:100%;">
                <div class="urisp-color-grid urisp-grid-2-col">
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Title Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[slide_in_title_color]" type="text" value="<?php echo esc_attr($settings['slide_in_title_color']); ?>" class="my-color-field" data-default-color="#FFFFFF" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Description Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[slide_in_desc_color]" type="text" value="<?php echo esc_attr($settings['slide_in_desc_color']); ?>" class="my-color-field" data-default-color="#FFFFFF" />
                    </div>
                    <div class="urisp-color-item" data-layouts="1,3,4,5" style="grid-column: span 2;">
                        <label><?php esc_html_e('Title & Description BG Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[slide_in_title_bg_color]" type="text" value="<?php echo esc_attr($settings['slide_in_title_bg_color']); ?>" class="my-color-field" data-default-color="#000000" />
                    </div>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="1,3,4,5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-visibility"></i> <?php esc_html_e('Background Color Opacity', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Set the transparency level of the text backgrounds.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[bg_color_opacity]">
                    <option value="0" <?php selected($settings['bg_color_opacity'], "0"); ?>>0</option>
                    <?php for($i=1; $i<=9; $i++) { $val = "0.$i"; echo '<option value="' . esc_attr($val) . '" ' . selected($settings['bg_color_opacity'], $val, false) . '>' . esc_html($val) . '</option>'; } ?>
                    <option value="1" <?php selected($settings['bg_color_opacity'], "1"); ?>>1</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Card 3: Action Buttons & Links (CTA & Read More) Styling -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Action Buttons & Links (CTA & Read More)', 'ultimate-responsive-image-slider'); ?></h3>
        <p style="margin: 0 0 20px 0; color: #64748b; font-size: 13px;">
            <?php esc_html_e('Configure unified button and link styling for Call-To-Action buttons and Read More links across all slides.', 'ultimate-responsive-image-slider'); ?>
        </p>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-button"></i> <?php esc_html_e('Default Button / Link Style', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose the shape and design for action buttons and links.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="cta_style_filled" name="urisp_settings[cta_button_style]" value="filled" <?php checked(empty($settings['cta_button_style']) || $settings['cta_button_style'] === 'filled'); ?>>
                    <label for="cta_style_filled"><?php esc_html_e('Filled', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="cta_style_text" name="urisp_settings[cta_button_style]" value="text" <?php checked(!empty($settings['cta_button_style']) && $settings['cta_button_style'] === 'text'); ?>>
                    <label for="cta_style_text"><?php esc_html_e('Text Only', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="cta_style_outlined" name="urisp_settings[cta_button_style]" value="outlined" disabled style="opacity:0.5;">
                    <label for="cta_style_outlined" style="opacity:0.6; cursor:not-allowed;" title="<?php esc_attr_e('Available in Pro', 'ultimate-responsive-image-slider'); ?>">
                        <?php esc_html_e('Outlined', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 8px; font-weight: 800; padding: 1px 4px; border-radius: 3px; margin-left: 2px;">PRO</span>
                    </label>

                    <input type="radio" id="cta_style_pill" name="urisp_settings[cta_button_style]" value="pill" disabled style="opacity:0.5;">
                    <label for="cta_style_pill" style="opacity:0.6; cursor:not-allowed;" title="<?php esc_attr_e('Available in Pro', 'ultimate-responsive-image-slider'); ?>">
                        <?php esc_html_e('Pill', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 8px; font-weight: 800; padding: 1px 4px; border-radius: 3px; margin-left: 2px;">PRO</span>
                    </label>

                    <input type="radio" id="cta_style_gradient" name="urisp_settings[cta_button_style]" value="gradient" disabled style="opacity:0.5;">
                    <label for="cta_style_gradient" style="opacity:0.6; cursor:not-allowed;" title="<?php esc_attr_e('Available in Pro', 'ultimate-responsive-image-slider'); ?>">
                        <?php esc_html_e('Gradient', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 8px; font-weight: 800; padding: 1px 4px; border-radius: 3px; margin-left: 2px;">PRO</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-art"></i> <?php esc_html_e('Button & Link Colors', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Customize background, text, and hover states for buttons and links.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field urisp-flex-column" style="width:100%;">
                <div class="urisp-color-grid urisp-grid-2-col">
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Button Background / Link Color', 'ultimate-responsive-image-slider'); ?></label>
                        <?php 
                        $link_color_val = ! empty( $settings['cta_button_bg_color'] ) ? $settings['cta_button_bg_color'] : ( ! empty( $settings['read_more_link_color'] ) ? $settings['read_more_link_color'] : '#6366f1' );
                        ?>
                        <input name="urisp_settings[cta_button_bg_color]" type="text" value="<?php echo esc_attr($link_color_val); ?>" class="my-color-field" data-default-color="#6366f1" />
                        <input type="hidden" name="urisp_settings[read_more_link_color]" value="<?php echo esc_attr($link_color_val); ?>" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Button Text Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[cta_button_text_color]" type="text" value="<?php echo esc_attr(!empty($settings['cta_button_text_color']) ? $settings['cta_button_text_color'] : '#ffffff'); ?>" class="my-color-field" data-default-color="#ffffff" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Hover Background / Link Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[cta_button_hover_color]" type="text" value="<?php echo esc_attr(!empty($settings['cta_button_hover_color']) ? $settings['cta_button_hover_color'] : '#4f46e5'); ?>" class="my-color-field" data-default-color="#4f46e5" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Hover Text Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[cta_button_hover_text_color]" type="text" value="<?php echo esc_attr(!empty($settings['cta_button_hover_text_color']) ? $settings['cta_button_hover_text_color'] : '#ffffff'); ?>" class="my-color-field" data-default-color="#ffffff" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Read More Link Configuration Row -->
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-admin-links"></i> <?php esc_html_e('Read More Link / Button', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Configure how custom slide links are displayed (uses the button style & colors above).', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field urisp-flex-column" style="width:100%;">
                <div style="display:flex; gap:10px; width:100%; margin-bottom:12px; flex-wrap:wrap;">
                    <select name="urisp_settings[read_more_link_on]" style="flex:1;">
                        <option value="text" <?php selected($settings['read_more_link_on'], 'text'); ?>><?php esc_html_e('Show Link / Button On Slide Text', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="slide" <?php selected($settings['read_more_link_on'], 'slide'); ?>><?php esc_html_e('Link Entire Slide Image', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="0" <?php selected($settings['read_more_link_on'], '0'); ?>><?php esc_html_e('Disable Link', 'ultimate-responsive-image-slider'); ?></option>
                    </select>
                    <select name="urisp_settings[read_more_link_open]" style="flex:1;">
                        <option value="_new" <?php selected($settings['read_more_link_open'], '_new'); ?>><?php esc_html_e('Open In New Window', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="_self" <?php selected($settings['read_more_link_open'], '_self'); ?>><?php esc_html_e('Open In Same Window', 'ultimate-responsive-image-slider'); ?></option>
                    </select>
                </div>
                <div style="width:100%;">
                    <label style="display:block; margin-bottom:5px; font-size:12px; font-weight:600; color:#64748b;"><?php esc_html_e('Read More Button Text:', 'ultimate-responsive-image-slider'); ?></label>
                    <input type="text" name="urisp_settings[read_more_link_text]" value="<?php echo esc_attr($settings['read_more_link_text']); ?>" style="width:100%; max-width:320px; border-radius:6px; border:1px solid #cbd5e1; padding:8px 12px;">
                </div>
            </div>
        </div>
    </div>
</div>

<div id="urisp-tab-navigation" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-navigation' ) ? 'active' : ''; ?>">
    
    <!-- Card 1: Navigation Configurations -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Navigation Configurations', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row" data-layouts="1,3,5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-ellipsis"></i> <?php esc_html_e('Show Slider Navigation', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Enable thumbnails or detailed navigation at the bottom.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('detailed_navigation', $settings['detailed_navigation']); ?>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-marker"></i> <?php esc_html_e('Show Navigation Bullets', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Enable small bullet indicators for slides.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                    <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                    echo urisp_toggle_html('navigation_button', $settings['navigation_button']); ?>
                    <select name="urisp_settings[navigation_button_style]" style="padding:6px 25px 6px 10px !important;">
                        <option value="1" <?php selected($settings['navigation_button_style'], 1); ?>><?php esc_html_e('Circle Bullets', 'ultimate-responsive-image-slider'); ?></option>
                        <option value="0" <?php selected($settings['navigation_button_style'], 0); ?>><?php esc_html_e('Square Bullets', 'ultimate-responsive-image-slider'); ?></option>
                    </select>
                    <div style="display:inline-flex; align-items:center; gap:6px;">
                        <span style="font-size:12px; color:#64748b; font-weight:600;"><?php esc_html_e('Size:', 'ultimate-responsive-image-slider'); ?></span>
                        <select name="urisp_settings[navigation_button_size]">
                            <?php foreach(range(6, 20) as $bs): ?>
                            <option value="<?php echo absint($bs); ?>" <?php selected($settings['navigation_button_size'], $bs); ?>><?php echo absint($bs); ?> px</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-arrow-left-alt2"></i> <?php esc_html_e('Show Navigation Arrows', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Enable left/right arrows for manual sliding.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('sliding_arrow', $settings['sliding_arrow']); ?>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-forms"></i> <?php esc_html_e('Navigation Icon Shape', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Select corner radius / shape for arrows and fullscreen button.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="arrow_radius_circle" name="urisp_settings[navigation_arrow_radius]" value="circle" <?php checked($settings['navigation_arrow_radius'], 'circle'); ?>>
                    <label for="arrow_radius_circle"><?php esc_html_e('Circle', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="arrow_radius_curved" name="urisp_settings[navigation_arrow_radius]" value="curved" <?php checked($settings['navigation_arrow_radius'], 'curved'); ?>>
                    <label for="arrow_radius_curved"><?php esc_html_e('Curved', 'ultimate-responsive-image-slider'); ?></label>

                    <input type="radio" id="arrow_radius_sharp" name="urisp_settings[navigation_arrow_radius]" value="sharp" <?php checked($settings['navigation_arrow_radius'], 'sharp'); ?>>
                    <label for="arrow_radius_sharp"><?php esc_html_e('Sharp', 'ultimate-responsive-image-slider'); ?></label>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-arrow-right-alt"></i> <?php esc_html_e('Navigation Arrow Style', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose the icon style for previous and next navigation arrows.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-arrow-style-picker">
                    <?php
                    $current_arrow_style = ! empty( $settings['arrow_style'] ) ? $settings['arrow_style'] : 'chevron';
                    $arrow_styles = array(
                        'chevron' => array(
                            'label'  => __('Chevron', 'ultimate-responsive-image-slider'),
                            'prev'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                            'next'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'is_pro' => false,
                        ),
                        'caret' => array(
                            'label'  => __('Caret', 'ultimate-responsive-image-slider'),
                            'prev'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15 18l-6-6 6-6v12z"/></svg>',
                            'next'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M9 6l6 6-6 6V6z"/></svg>',
                            'is_pro' => false,
                        ),
                        'thin' => array(
                            'label'  => __('Thin', 'ultimate-responsive-image-slider'),
                            'prev'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 19 8 12 15 5"></polyline></svg>',
                            'next'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 19 16 12 9 5"></polyline></svg>',
                            'is_pro' => true,
                        ),
                        'bold' => array(
                            'label'  => __('Bold', 'ultimate-responsive-image-slider'),
                            'prev'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                            'next'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'is_pro' => true,
                        ),
                        'minimal' => array(
                            'label'  => __('Minimal', 'ultimate-responsive-image-slider'),
                            'prev'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                            'next'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                            'is_pro' => true,
                        ),
                    );
                    foreach ( $arrow_styles as $key => $style_info ) :
                        $is_active = ( $current_arrow_style === $key );
                        $is_pro    = ! empty( $style_info['is_pro'] );
                    ?>
                        <label class="urisp-arrow-choice <?php echo $is_active ? 'active' : ''; ?>" style="<?php echo $is_pro ? 'position: relative; opacity: 0.65; cursor: pointer;' : ''; ?>" <?php echo $is_pro ? 'onclick="window.open(\'https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/\', \'_blank\'); return false;"' : ''; ?>>
                            <input type="radio" name="urisp_settings[arrow_style]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current_arrow_style, $key ); ?> <?php echo $is_pro ? 'disabled' : ''; ?>>
                            <?php if ( $is_pro ) : ?>
                                <span style="position: absolute; top: 4px; right: 4px; background: #f59e0b; color: #fff; font-size: 8px; font-weight: 800; padding: 1px 4px; border-radius: 3px; line-height: 1.2;">PRO</span>
                            <?php endif; ?>
                            <div class="urisp-arrow-preview">
                                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-defined hardcoded SVG markup for preview.
                                echo $style_info['prev']; ?>
                                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-defined hardcoded SVG markup for preview.
                                echo $style_info['next']; ?>
                            </div>
                            <span class="urisp-arrow-title"><?php echo esc_html( $style_info['label'] ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Thumbnail & Layout Specific Navigation (Layout 3 & Layout 5) -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Thumbnail Navigation Settings (Layout 3 & Layout 5)', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row" data-layouts="3">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-align-center"></i> <?php esc_html_e('Thumbnail Position', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Placement of the thumbnail carousel in Layout 3.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[navigation_position]">
                    <option value="bottom" <?php selected($settings['navigation_position'], 'bottom'); ?>><?php esc_html_e('Bottom', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="top" <?php selected($settings['navigation_position'], 'top'); ?>><?php esc_html_e('Top', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="left" <?php selected($settings['navigation_position'], 'left'); ?>><?php esc_html_e('Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="right" <?php selected($settings['navigation_position'], 'right'); ?>><?php esc_html_e('Right', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="3">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-format-image"></i> <?php esc_html_e('Thumbnail Dimensions', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Set width and height for individual thumbnail images.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="text" name="urisp_settings[thumbnail_width]" value="<?php echo esc_attr($settings['thumbnail_width']); ?>" style="width:80px; text-align:center; border-radius:6px; border:1px solid #ccc; padding:6px;">
                    <span style="font-size:12px; color:#888;">px (W)</span>
                    <span style="font-weight:bold; color:#888;">X</span>
                    <input type="text" name="urisp_settings[thumbnail_height]" value="<?php echo esc_attr($settings['thumbnail_height']); ?>" style="width:80px; text-align:center; border-radius:6px; border:1px solid #ccc; padding:6px;">
                    <span style="font-size:12px; color:#888;">px (H)</span>
                </div>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="3">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-art"></i> <?php esc_html_e('Thumbnail Active Style', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Select active indicator style for selected thumbnail.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[thumbnail_style]">
                    <option value="border" <?php selected($settings['thumbnail_style'], 'border'); ?>><?php esc_html_e('Border Highlight (Default)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="none" <?php selected($settings['thumbnail_style'], 'none'); ?>><?php esc_html_e('None', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="pointer" disabled><?php esc_html_e('Pointer Arrow [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="glow" disabled><?php esc_html_e('Glow / Shadow [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="zoom" disabled><?php esc_html_e('Zoom Active [PRO]', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>


    </div>

    <!-- Card 3: Navigation Colors -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Navigation Colors', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-admin-appearance"></i> <?php esc_html_e('Control Colors', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Customize the appearance of arrows, bullets, and thumbnail highlights.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field urisp-flex-column" style="width:100%;">
                <div class="urisp-color-grid urisp-grid-2-col">
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Arrow Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[navigation_arrow_color]" type="text" value="<?php echo esc_attr($settings['navigation_arrow_color']); ?>" class="my-color-field" data-default-color="#FFFFFF" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Arrow / Icon BG Color', 'ultimate-responsive-image-slider'); ?></label>
                        <?php 
                        $arrow_bg_val = ( ! empty( $settings['navigation_arrow_bg_color'] ) && strpos( $settings['navigation_arrow_bg_color'], 'rgb' ) === false ) ? $settings['navigation_arrow_bg_color'] : '#0f172a';
                        ?>
                        <input name="urisp_settings[navigation_arrow_bg_color]" type="text" value="<?php echo esc_attr($arrow_bg_val); ?>" class="my-color-field" data-default-color="#0f172a" />
                    </div>
                    <div class="urisp-color-item">
                        <label><?php esc_html_e('Bullet Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[navigation_button_color]" type="text" value="<?php echo esc_attr($settings['navigation_button_color']); ?>" class="my-color-field" data-default-color="#0080FF" />
                    </div>
                    <div class="urisp-color-item" data-layouts="1,5">
                        <label><?php esc_html_e('Thumbnail BG Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[thumbnail_bg_color]" type="text" value="<?php echo esc_attr($settings['thumbnail_bg_color']); ?>" class="my-color-field" data-default-color="#0080FF" />
                    </div>
                    <div class="urisp-color-item" data-layouts="3,5">
                        <label><?php esc_html_e('Selected Thumbnail Border / Active Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[thumbnail_style_color]" type="text" value="<?php echo esc_attr(! empty($settings['thumbnail_style_color']) ? $settings['thumbnail_style_color'] : '#000000'); ?>" class="my-color-field" data-default-color="#000000" />
                    </div>
                    <div class="urisp-color-item" data-layouts="1,5">
                        <label><?php esc_html_e('Thumbnail Title Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[thumbnail_title_color]" type="text" value="<?php echo esc_attr($settings['thumbnail_title_color']); ?>" class="my-color-field" data-default-color="#FFFFFF" />
                    </div>
                    <div class="urisp-color-item" data-layouts="1,5">
                        <label><?php esc_html_e('Thumbnail Description Color', 'ultimate-responsive-image-slider'); ?></label>
                        <input name="urisp_settings[thumbnail_desc_color]" type="text" value="<?php echo esc_attr($settings['thumbnail_desc_color']); ?>" class="my-color-field" data-default-color="#FFFFFF" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Layout 5 Navigation Box Settings (Pro Only - Shifted Below Active Settings) -->
    <div class="urisp-card" data-layouts="5" style="border-left: 4px solid #f59e0b; background: #fdfdfd;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h3 style="margin: 0; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-lock" style="color: #d97706;"></span>
                <?php esc_html_e('Layout 5 Side Navigation Box Settings', 'ultimate-responsive-image-slider'); ?>
                <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">PRO</span>
            </h3>
            <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="font-size: 12px; font-weight: 700; color: #b45309; text-decoration: none;">
                <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
            </a>
        </div>

        <div class="urisp-setting-row" data-layouts="5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-align-center"></i> <?php esc_html_e('Navigation Position (Layout 5)', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></h5>
                <p><?php esc_html_e('Placement of the thumbnail / navigation box in Layout 5.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php $current_l5_pos = ! empty( $settings['navigation_position_l5'] ) ? $settings['navigation_position_l5'] : ( ! empty( $settings['navigation_position'] ) ? $settings['navigation_position'] : 'right' ); ?>
                <select name="urisp_settings[navigation_position_l5]" disabled style="cursor: not-allowed; opacity: 0.7;">
                    <option value="right" <?php selected($current_l5_pos, 'right'); ?>><?php esc_html_e('Right', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="left" <?php selected($current_l5_pos, 'left'); ?>><?php esc_html_e('Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="top" <?php selected($current_l5_pos, 'top'); ?>><?php esc_html_e('Top', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottom" <?php selected($current_l5_pos, 'bottom'); ?>><?php esc_html_e('Bottom', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row" data-layouts="5">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-list-view"></i> <?php esc_html_e('Navigation Content (Layout 5)', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">PRO</span></h5>
                <p><?php esc_html_e('Choose what content to display inside navigation boxes in Layout 5.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <select name="urisp_settings[navigation_content]" disabled style="cursor: not-allowed; opacity: 0.7;">
                    <option value="both" <?php selected($settings['navigation_content'], 'both'); ?>><?php esc_html_e('Title & Description', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="title" <?php selected($settings['navigation_content'], 'title'); ?>><?php esc_html_e('Title Only', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="number" <?php selected($settings['navigation_content'], 'number'); ?>><?php esc_html_e('Numbering (1, 2, 3...)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="thumbnail" <?php selected($settings['navigation_content'], 'thumbnail'); ?>><?php esc_html_e('Thumbnails', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="urisp-tab-advanced" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-advanced' ) ? 'active' : ''; ?>">
    
    <!-- Card 1: Fullscreen Controls (Active Free Feature) -->
    <div class="urisp-card">
        <h3><?php esc_html_e('Fullscreen Controls', 'ultimate-responsive-image-slider'); ?></h3>
        
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-editor-expand"></i> <?php esc_html_e('Fullscreen Mode', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Enable fullscreen viewing option on the slider.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped HTML toggle markup.
                echo urisp_toggle_html('fullscreen', isset($settings['fullscreen']) ? $settings['fullscreen'] : (isset($settings['fullscreen_button']) ? $settings['fullscreen_button'] : 0)); ?>
            </div>
        </div>
    </div>

    <!-- Card 2: Interactive Pro Features (Lightbox & Mouse Wheel) -->
    <div class="urisp-card" style="background: #fbfbfe; border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            <h3 style="margin: 0; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-star-filled" style="color: #d97706;"></span>
                <?php esc_html_e('Pro Interactive Features', 'ultimate-responsive-image-slider'); ?>
                <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">PRO</span>
            </h3>
            <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="font-size: 12px; font-weight: 700; color: #b45309; text-decoration: none;">
                <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
            </a>
        </div>
        
        <!-- Mouse Wheel Navigation (PRO Dummy) -->
        <div class="urisp-setting-row" style="padding-top: 5px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-update"></i> <?php esc_html_e('Mouse Wheel Navigation', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">PRO</span></h5>
                <p><?php esc_html_e('Allow site visitors to scroll through slides with their mouse wheel. Available in Pro.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:16px; align-items:center; opacity: 0.75;">
                    <label class="urisp-toggle" style="cursor: not-allowed;">
                        <input type="checkbox" disabled value="0">
                        <span class="urisp-toggle-slider"></span>
                    </label>
                    <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="font-size: 12px; font-weight: 700; color: #b45309; text-decoration: none;">
                        <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Lightbox Options (PRO Dummy) -->
        <div class="urisp-setting-row" style="padding-top: 14px;">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-format-gallery"></i> <?php esc_html_e('Lightbox Options', 'ultimate-responsive-image-slider'); ?> <span style="background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">PRO</span></h5>
                <p><?php esc_html_e('Allow images to open in Fancybox or LightGallery modals. (Native fullscreen mode is active above for Free).', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display:flex; gap:16px; align-items:center; opacity: 0.75;">
                    <label class="urisp-toggle" style="cursor: not-allowed;">
                        <input type="checkbox" disabled value="0">
                        <span class="urisp-toggle-slider"></span>
                    </label>
                    <select disabled style="padding:6px 25px 6px 10px !important; cursor: not-allowed;">
                        <option>Fancy Box (Pro)</option>
                        <option>Light Gallery (Pro)</option>
                    </select>
                    <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" style="font-size: 12px; font-weight: 700; color: #b45309; text-decoration: none;">
                        <?php esc_html_e('Unlock in Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Reset All Settings to Defaults -->
    <div class="urisp-card" style="border-left: 4px solid #ef4444; background: #fffaf0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="margin: 0 0 5px 0; color: #b91c1c; display: flex; align-items: center; gap: 8px;">
                    <span class="dashicons dashicons-image-rotate" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php esc_html_e('Reset All Settings to Defaults', 'ultimate-responsive-image-slider'); ?>
                </h3>
                <p style="margin: 0; color: #64748b; font-size: 13px;">
                    <?php esc_html_e('Restore all layout, typography, navigation, colors, and watermark options to their original default values. Uploaded slides and images will not be affected.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>
            <button type="button" class="urisp-btn urisp-reset-settings-trigger" style="color: #ffffff; background: #dc2626; border-color: #b91c1c; font-weight: 600; padding: 0 16px; height: 38px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border-radius: 6px;" title="<?php esc_attr_e('Reset all settings to defaults', 'ultimate-responsive-image-slider'); ?>">
                <span class="dashicons dashicons-image-rotate" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
                <span><?php esc_html_e('Reset Settings', 'ultimate-responsive-image-slider'); ?></span>
            </button>
        </div>
    </div>
</div>

<div id="urisp-tab-watermark" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-watermark' ) ? 'active' : ''; ?>">
    <div style="margin-bottom: 24px; padding: 20px 24px; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 20px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">
                <span class="dashicons dashicons-shield" style="font-size: 24px; width: 24px; height: 24px;"></span>
            </div>
            <div>
                <h4 style="margin: 0 0 4px 0; color: #92400e; font-size: 16px; font-weight: 700;">
                    <?php esc_html_e('Dual-Layer Watermark Protection is a Pro Feature', 'ultimate-responsive-image-slider'); ?>
                </h4>
                <p style="margin: 0; color: #b45309; font-size: 13.5px; line-height: 1.5;">
                    <?php esc_html_e('Protect your images with server-side text and PNG logo watermarks across 7 positions. The preview below demonstrates how watermarks appear in the Pro version.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>
        </div>
        <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: #f59e0b; border-color: #d97706; color: #fff; font-weight: 700; padding: 6px 20px; height: auto; border-radius: 8px; flex-shrink: 0; text-decoration: none;">
            <?php esc_html_e('Upgrade to Pro', 'ultimate-responsive-image-slider'); ?> &rarr;
        </a>
    </div>
    
    <!-- Watermark Engine Card -->
    <div class="urisp-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
            <div>
                <h3 style="margin: 0 0 5px 0;"><?php esc_html_e('Watermark Protection Engine', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13px;"><?php esc_html_e('Burn permanent, high-quality text or graphic logo watermarks directly into your slider images with server-side caching.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <?php 
                $gd_support = extension_loaded( 'gd' ) && function_exists( 'imagettftext' );
                if ( $gd_support ) {
                    echo '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 9999px; font-size: 12px; font-weight: 600;"><span class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px;"></span> ' . esc_html__( 'GD Engine Ready', 'ultimate-responsive-image-slider' ) . '</span>';
                } else {
                    echo '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #fffbeb; color: #d97706; border: 1px solid #fde68a; border-radius: 9999px; font-size: 12px; font-weight: 600;"><span class="dashicons dashicons-warning" style="font-size: 16px; width: 16px; height: 16px;"></span> ' . esc_html__( 'GD Not Found - CSS Fallback Active', 'ultimate-responsive-image-slider' ) . '</span>';
                }
                ?>
                <button type="button" disabled class="urisp-btn urisp-btn-secondary" style="font-size: 12px; padding: 0 12px; height: 32px; cursor: not-allowed; opacity: 0.6;" title="Watermark is a Pro feature"><span class="dashicons dashicons-trash" style="font-size: 15px; width: 15px; height: 15px;"></span><span><?php esc_html_e('Purge Cache (Pro)', 'ultimate-responsive-image-slider'); ?></span></button>
            </div>
        </div>

        <!-- Master Watermark Toggle -->
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-shield"></i> <?php esc_html_e('Enable Watermark', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Enable watermark protection for all slides in this slider (can be customized per slide in the slide editor).', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <label class="urisp-toggle" style="cursor: not-allowed; opacity: 0.6;"><input type="checkbox" disabled value="0"><span class="urisp-toggle-slider"></span></label><span style="font-size: 12px; color: #d97706; font-weight: 700; margin-left: 10px;">Available in Pro</span>
            </div>
        </div>

        <!-- Watermark Type (Text vs Image) -->
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-admin-generic"></i> <?php esc_html_e('Watermark Type', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose whether to stamp custom copyright text or a graphic PNG logo watermark.', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div class="urisp-segmented-control">
                    <input type="radio" id="urisp_wm_type_text" disabled value="text" <?php checked(empty($settings['watermark_type']) || $settings['watermark_type'] === 'text'); ?>>
                    <label for="urisp_wm_type_text"><span class="dashicons dashicons-editor-textcolor" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span><span><?php esc_html_e('Text Watermark', 'ultimate-responsive-image-slider'); ?></span></label>

                    <input type="radio" id="urisp_wm_type_image" disabled value="image" <?php checked(!empty($settings['watermark_type']) && $settings['watermark_type'] === 'image'); ?>>
                    <label for="urisp_wm_type_image"><span class="dashicons dashicons-format-image" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span><span><?php esc_html_e('Logo Image', 'ultimate-responsive-image-slider'); ?></span></label>
                </div>
            </div>
        </div>

        <!-- Text Watermark Settings Group -->
        <div id="urisp-wm-text-config" style="<?php echo (empty($settings['watermark_type']) || $settings['watermark_type'] === 'text') ? '' : 'display:none;'; ?>">
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-editor-textcolor"></i> <?php esc_html_e('Watermark Text', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Enter copyright or brand text. TrueType font OpenSans-Bold is applied with a smooth drop-shadow for contrast on any background.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <input type="text" disabled readonly class="urisp-input-styled" value="<?php echo esc_attr(!empty($settings['watermark_text']) ? $settings['watermark_text'] : '© ' . get_bloginfo('name')); ?>" placeholder="e.g. © 2026 My Brand" style="max-width: 380px;">
                </div>
            </div>

            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-editor-textdirectionleft"></i> <?php esc_html_e('Text Font Size', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Font size in points (8 to 72). Automatically calibrated based on slide resolution.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <div style="display: flex; align-items: center; gap: 12px; width: 100%; max-width: 320px;">
                        <input type="range" disabled id="urisp_wm_text_size_range" min="8" max="72" step="1" value="<?php echo esc_attr(!empty($settings['watermark_size']) ? $settings['watermark_size'] : 20); ?>" style="flex: 1;">
                        <span id="urisp_wm_text_size_badge" style="font-weight: 700; font-size: 13px; color: var(--urisp-primary); min-width: 45px;"><?php echo esc_html(!empty($settings['watermark_size']) ? $settings['watermark_size'] : 20); ?>pt</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Image Watermark Settings Group -->
        <div id="urisp-wm-image-config" style="<?php echo (!empty($settings['watermark_type']) && $settings['watermark_type'] === 'image') ? '' : 'display:none;'; ?>">
            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-format-image"></i> <?php esc_html_e('Logo Image', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Select a transparent PNG or JPG logo to stamp onto slides. Alpha transparency is preserved flawlessly.', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%; max-width: 480px;">
                        <input type="text" id="urisp_watermark_image_url" disabled readonly class="urisp-input-styled" value="<?php echo esc_attr(!empty($settings['watermark_image']) ? $settings['watermark_image'] : ''); ?>" placeholder="https://..." style="flex: 1; min-width: 240px;">
                        <button type="button" id="urisp_wm_upload_logo_btn" class="urisp-btn urisp-btn-secondary" style="white-space: nowrap;"><span class="dashicons dashicons-upload"></span><span><?php esc_html_e('Upload Logo', 'ultimate-responsive-image-slider'); ?></span></button>
                    </div>
                    <div id="urisp-wm-logo-preview" style="margin-top: 10px; <?php echo empty($settings['watermark_image']) ? 'display:none;' : ''; ?>">
                        <img src="<?php echo esc_url(!empty($settings['watermark_image']) ? $settings['watermark_image'] : ''); ?>" alt="Logo Preview" style="max-height: 60px; max-width: 180px; padding: 6px; background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 6px; object-fit: contain;">
                    </div>
                </div>
            </div>

            <div class="urisp-setting-row">
                <div class="urisp-setting-label">
                    <h5><i class="dashicons dashicons-image-rotate"></i> <?php esc_html_e('Logo Scale / Size', 'ultimate-responsive-image-slider'); ?></h5>
                    <p><?php esc_html_e('Percentage scale relative to the slide width (5% to 100%).', 'ultimate-responsive-image-slider'); ?></p>
                </div>
                <div class="urisp-setting-field">
                    <div style="display: flex; align-items: center; gap: 12px; width: 100%; max-width: 320px;">
                        <input type="range" disabled id="urisp_wm_image_size_range" min="5" max="100" step="1" value="<?php echo esc_attr(!empty($settings['watermark_image_size']) ? $settings['watermark_image_size'] : 25); ?>" style="flex: 1;">
                        <span id="urisp_wm_image_size_badge" style="font-weight: 700; font-size: 13px; color: var(--urisp-primary); min-width: 45px;"><?php echo esc_html(!empty($settings['watermark_image_size']) ? $settings['watermark_image_size'] : 25); ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Position & Opacity (Shared) -->
        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-move"></i> <?php esc_html_e('Watermark Position', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Choose where the watermark appears on the image (7 precision anchor positions).', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <?php $pos = !empty($settings['watermark_position']) ? $settings['watermark_position'] : 'bottom-right'; ?>
                <select disabled class="urisp-input-styled" style="max-width: 240px;">
                    <option value="bottom-right" <?php selected($pos, 'bottom-right'); ?>><?php esc_html_e('Bottom Right (Recommended)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottom-center" <?php selected($pos, 'bottom-center'); ?>><?php esc_html_e('Bottom Center', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="bottom-left" <?php selected($pos, 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="center" <?php selected($pos, 'center'); ?>><?php esc_html_e('Center (Maximum Security)', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="top-right" <?php selected($pos, 'top-right'); ?>><?php esc_html_e('Top Right', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="top-center" <?php selected($pos, 'top-center'); ?>><?php esc_html_e('Top Center', 'ultimate-responsive-image-slider'); ?></option>
                    <option value="top-left" <?php selected($pos, 'top-left'); ?>><?php esc_html_e('Top Left', 'ultimate-responsive-image-slider'); ?></option>
                </select>
            </div>
        </div>

        <div class="urisp-setting-row">
            <div class="urisp-setting-label">
                <h5><i class="dashicons dashicons-visibility"></i> <?php esc_html_e('Watermark Opacity', 'ultimate-responsive-image-slider'); ?></h5>
                <p><?php esc_html_e('Controls watermark transparency (10% faint watermark to 100% solid watermark).', 'ultimate-responsive-image-slider'); ?></p>
            </div>
            <div class="urisp-setting-field">
                <div style="display: flex; align-items: center; gap: 12px; width: 100%; max-width: 320px;">
                    <input type="range" disabled id="urisp_wm_opacity_range" min="10" max="100" step="1" value="<?php echo esc_attr(!empty($settings['watermark_opacity']) ? $settings['watermark_opacity'] : 70); ?>" style="flex: 1;">
                    <span id="urisp_wm_opacity_badge" style="font-weight: 700; font-size: 13px; color: var(--urisp-primary); min-width: 45px;"><?php echo esc_html(!empty($settings['watermark_opacity']) ? $settings['watermark_opacity'] : 70); ?>%</span>
                </div>
            </div>
        </div>

    </div>
</div>

        
        <!-- Upgrade to Pro Tab Content -->
        <div id="urisp-tab-upgrade" class="urisp-tab-content <?php echo ( $active_admin_tab === 'urisp-tab-upgrade' ) ? 'active' : ''; ?>">
    <!-- Upgrade to Pro Hero Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #312e81 100%); color: #fff; padding: 40px 32px; border-radius: 16px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25); text-align: center; margin-bottom: 30px; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.08);">
        <!-- Background Ambient Glow -->
        <div style="position: absolute; top: -80px; left: 50%; transform: translateX(-50%); width: 450px; height: 180px; background: radial-gradient(circle, rgba(245, 158, 11, 0.3) 0%, rgba(99, 102, 241, 0) 70%); pointer-events: none;"></div>
        
        <div style="position: relative; z-index: 2;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 6px 18px; border-radius: 9999px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 18px; backdrop-filter: blur(8px);">
                <span class="dashicons dashicons-star-filled" style="font-size: 14px; width: 14px; height: 14px; line-height: 14px; color: #fbbf24;"></span>
                <span><?php esc_html_e('Premium Superpowers & Infinite Layouts', 'ultimate-responsive-image-slider'); ?></span>
            </div>

            <h1 style="color: #ffffff; font-size: 32px; font-weight: 800; margin: 0 0 14px 0; letter-spacing: -0.5px; line-height: 1.25;">
                <?php esc_html_e('Supercharge Your Sliders with Ultimate Responsive Image Slider Pro', 'ultimate-responsive-image-slider'); ?>
            </h1>

            <p style="color: #c7d2fe; font-size: 16px; max-width: 760px; margin: 0 auto 28px auto; line-height: 1.6; font-weight: 400;">
                <?php esc_html_e('Unlock all 5 responsive architectures, YouTube & self-hosted MP4 video sliders, automated WooCommerce product feeds, server-side watermarking, custom font uploaders, and real-time impression analytics.', 'ultimate-responsive-image-slider'); ?>
            </p>

            <div style="display: flex; justify-content: center; align-items: center; gap: 14px; flex-wrap: wrap;">
                <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-color: #d97706; color: #0f172a; font-weight: 800; font-size: 16px; padding: 12px 32px; height: auto; border-radius: 10px; box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: transform 0.2s ease;">
                    <span><?php esc_html_e('Upgrade to Pro Now', 'ultimate-responsive-image-slider'); ?></span>
                    <span style="font-size: 18px;">&rarr;</span>
                </a>
                <a href="#urisp-pro-comparison-table" style="color: #e0e7ff; font-size: 14px; font-weight: 600; text-decoration: underline; padding: 10px 16px;">
                    <?php esc_html_e('View Full Comparison Table', 'ultimate-responsive-image-slider'); ?> &darr;
                </a>
            </div>
        </div>
    </div>

    <!-- Section 1: Pro Features Grid -->
    <div style="margin-bottom: 40px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">
                <?php esc_html_e('Why Upgrade? Explore What Pro Unlocks', 'ultimate-responsive-image-slider'); ?>
            </h2>
            <p style="font-size: 15px; color: #64748b; max-width: 650px; margin: 0 auto;">
                <?php esc_html_e('Built for photographers, agencies, and online stores who need peak performance, security, and aesthetics.', 'ultimate-responsive-image-slider'); ?>
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 20px;">
            <!-- Feature 1 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #4338ca;">
                        <span class="dashicons dashicons-layout" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">5 Layouts</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('All 5 Visual Architectures', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Access Standard Hero Slider, Lightbox Popups (Layout 2), Bottom Thumbnails, Vertical Side Filmstrip (Layout 4), and 3D Perspective Carousel (Layout 5).', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: #dc2626;">
                        <span class="dashicons dashicons-video-alt3" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #fef2f2; color: #dc2626; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Full Video Suite</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('YouTube & Self-Hosted MP4', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Embed YouTube videos, YouTube Shorts, Dailymotion, and custom uploaded MP4/WebM videos with 1-click automatic high-res cover thumbnail fetchers.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706;">
                        <span class="dashicons dashicons-shield" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #fffbeb; color: #d97706; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Image Protection</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('Dual-Layer Watermarking', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Safeguard your original photographs against theft. Stamp custom text and transparent PNG logos across 7 anchor positions with adjustable opacity.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <span class="dashicons dashicons-cart" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #f0fdf4; color: #16a34a; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">WooCommerce</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('WooCommerce & Dynamic Sliders', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Automatically turn recent posts, specific categories, or WooCommerce products into live carousels with product prices, sale badges, and Add to Cart buttons.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0f2fe; display: flex; align-items: center; justify-content: center; color: #0284c7;">
                        <span class="dashicons dashicons-search" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #f0f9ff; color: #0284c7; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Popup Lightbox</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('Full-Screen Lightbox Engine', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Allow visitors to expand any image or video into a distraction-free, responsive full-screen modal with thumbnail scroller, zoom gesture, and keyboard nav.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fae8ff; display: flex; align-items: center; justify-content: center; color: #a855f7;">
                        <span class="dashicons dashicons-editor-customchar" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #faf5ff; color: #a855f7; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Typography & A11y</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('Custom Fonts & Dyslexia Library', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Upload your own .woff2 and .ttf brand typography files, or enable 1-click accessibility reading fonts like OpenDyslexic and Lexend for inclusive websites.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 7 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #ffedd5; display: flex; align-items: center; justify-content: center; color: #ea580c;">
                        <span class="dashicons dashicons-camera" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #fff7ed; color: #ea580c; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Cinematic FX</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('Ken Burns Pan & Zoom', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Breathe cinematic life into static images with smooth pan-and-zoom motion effects, selectable intensity (Subtle, Medium, Dramatic), and custom durations.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>

            <!-- Feature 8 -->
            <div class="urisp-card" style="padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #0f172a;">
                        <span class="dashicons dashicons-chart-area" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <span style="background: #f8fafc; color: #0f172a; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Analytics</span>
                </div>
                <h3 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php esc_html_e('Real-Time Analytics & CTR Tracking', 'ultimate-responsive-image-slider'); ?></h3>
                <p style="margin: 0; color: #64748b; font-size: 13.5px; line-height: 1.55;">
                    <?php esc_html_e('Monitor slider engagement with an integrated analytics dashboard. Track overall views, individual slide clicks, click-through rates, and export CSV reports.', 'ultimate-responsive-image-slider'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Section 2: Comprehensive Comparison Table -->
    <div id="urisp-pro-comparison-table" class="urisp-card" style="padding: 30px; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; margin-bottom: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <?php esc_html_e('Feature Comparison: Free vs. Pro', 'ultimate-responsive-image-slider'); ?>
            </h2>
            <p style="font-size: 14px; color: #64748b; margin: 0;">
                <?php esc_html_e('Compare side-by-side what is included in the Free version versus the full Pro edition.', 'ultimate-responsive-image-slider'); ?>
            </p>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 16px 20px; font-size: 16px; font-weight: 700; color: #0f172a; width: 50%;">
                            <?php esc_html_e('Plugin Feature / Capability', 'ultimate-responsive-image-slider'); ?>
                        </th>
                        <th style="padding: 16px 20px; font-size: 15px; font-weight: 700; color: #64748b; text-align: center; width: 25%; background: #f8fafc; border-top-left-radius: 8px;">
                            <?php esc_html_e('Free Version', 'ultimate-responsive-image-slider'); ?>
                        </th>
                        <th style="padding: 16px 20px; font-size: 16px; font-weight: 800; color: #4338ca; text-align: center; width: 25%; background: #eef2ff; border-top-right-radius: 8px;">
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 16px; width: 16px; height: 16px;"></span>
                                <?php esc_html_e('Pro Version', 'ultimate-responsive-image-slider'); ?>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Category 1: Layouts -->
                    <tr style="background: #f1f5f9;">
                        <td colspan="3" style="padding: 10px 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                            <?php esc_html_e('Slider Layouts & Responsive Architectures', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Layout 1: Standard Horizontal Slider with Auto-Scale', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Layout 3: Classic Thumbnail Carousel Slider', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Layout 2: Lightbox Gallery & Popup Preview Slider', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Layout 4: Vertical / Side Filmstrip Navigation Slider', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Layout 5: 3D Perspective Carousel & Vertical Mode', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>

                    <!-- Category 2: Video Integration -->
                    <tr style="background: #f1f5f9;">
                        <td colspan="3" style="padding: 10px 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                            <?php esc_html_e('Video Sliders & Media Integration', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Vimeo Video Support (Direct & Channels)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('YouTube & YouTube Shorts Video Integration', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Self-Hosted MP4 / WebM / OGG Video Slider', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Automatic HD Video Thumbnail & Title Retrieval', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; font-size: 13px; color: #64748b;">
                            <?php esc_html_e('Vimeo Only', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('All Providers + Media Lib', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>

                    <!-- Category 3: Commerce & Dynamic -->
                    <tr style="background: #f1f5f9;">
                        <td colspan="3" style="padding: 10px 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                            <?php esc_html_e('WooCommerce & Dynamic Content Feeds', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Manual Image & Video Slides Upload', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; font-weight: 600; color: #16a34a;">
                            <?php esc_html_e('Unlimited', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('Unlimited', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('WooCommerce Live Product Carousel (Price + Cart Button)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Dynamic Post Slider (Recent Posts, CPTs, Taxonomy)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Call-to-Action (CTA) Slide Buttons (5 Styles & Colors)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>

                    <!-- Category 4: Security & Typography -->
                    <tr style="background: #f1f5f9;">
                        <td colspan="3" style="padding: 10px 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                            <?php esc_html_e('Watermark Security & Custom Typography', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Server-Side Dynamic Text & Logo Watermarking', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('7 Positions + Transparency', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Custom Brand Font Uploader (.woff2, .ttf, .otf)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Dyslexia & Accessibility Presets (OpenDyslexic, Lexend)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>

                    <!-- Category 5: Visuals, Analytics & Support -->
                    <tr style="background: #f1f5f9;">
                        <td colspan="3" style="padding: 10px 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #475569;">
                            <?php esc_html_e('Visual FX, Analytics & Priority Support', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Ken Burns Cinematic Pan-and-Zoom Animation', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <span class="dashicons dashicons-yes-alt" style="color: #16a34a; font-size: 20px;"></span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Visual Skin Presets (Glassmorphism, Neumorphic, Vibrant)', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; font-size: 13px; color: #64748b;">
                            <?php esc_html_e('Default Only', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('5 Designer Presets', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Real-Time Analytics Dashboard & CTR Tracking', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; color: #94a3b8;">
                            <span class="dashicons dashicons-dismiss" style="color: #cbd5e1; font-size: 20px;"></span>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('Included + CSV Export', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 14px 20px; color: #1e293b; font-weight: 600;">
                            <?php esc_html_e('Customer Support & Lifetime Updates', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #fafafa; font-size: 13px; color: #64748b;">
                            <?php esc_html_e('Community Forums', 'ultimate-responsive-image-slider'); ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: center; background: #f5f7ff; font-weight: 700; color: #4338ca;">
                            <?php esc_html_e('Priority 24/7 Dedicated Support', 'ultimate-responsive-image-slider'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Bottom Call-To-Action Box -->
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 2px dashed #cbd5e1; border-radius: 16px; padding: 32px; text-align: center;">
        <h3 style="margin: 0 0 10px 0; font-size: 22px; font-weight: 800; color: #0f172a;">
            <?php esc_html_e('Ready to Upgrade and Transform Your Sliders?', 'ultimate-responsive-image-slider'); ?>
        </h3>
        <p style="margin: 0 auto 20px auto; max-width: 600px; color: #64748b; font-size: 15px; line-height: 1.5;">
            <?php esc_html_e('Instant license delivery, 100% seamless backward compatibility with your existing Free sliders, and a 14-day money-back guarantee.', 'ultimate-responsive-image-slider'); ?>
        </p>
        <a href="https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/" target="_blank" class="button button-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border-color: #4338ca; color: #fff; font-weight: 800; font-size: 16px; padding: 12px 36px; height: auto; border-radius: 10px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-unlock" style="font-size: 18px; width: 18px; height: 18px; line-height: 18px;"></span>
            <span><?php esc_html_e('Get Ultimate Responsive Image Slider Pro Now', 'ultimate-responsive-image-slider'); ?> &rarr;</span>
        </a>
    </div>

        </div>

        </div><!-- Close .urisp-tabs-content-wrapper -->
    </div><!-- Close .urisp-settings-main-content -->
</div><!-- Close outer .urisp-settings-wrapper -->

<!-- Edit Slide Modal -->
<div id="urisp-edit-slide-modal" class="urisp-modal">
    <div class="urisp-modal-overlay"></div>
    <div class="urisp-modal-content">
        <div class="urisp-modal-header">
            <h3><span class="dashicons dashicons-edit"></span> <?php esc_html_e('Edit Slide Details', 'ultimate-responsive-image-slider'); ?></h3>
            <button type="button" class="urisp-modal-close">&times;</button>
        </div>
        <div class="urisp-modal-body">
            <div class="urisp-modal-preview-wrapper" style="position: relative;">
                <img id="urisp-modal-image-preview" src="" alt="Slide Preview">
                <span id="urisp-modal-thumb-badge" style="display: none; position: absolute; top: 8px; right: 8px; background: rgba(15, 23, 42, 0.85); color: #fff; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 4px; backdrop-filter: blur(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.3); pointer-events: none; z-index: 5;">
                    <span class="dashicons dashicons-video-alt3" style="font-size: 13px; width: 13px; height: 13px; vertical-align: middle; line-height: 13px;"></span>
                    <span id="urisp-modal-thumb-badge-text"><?php esc_html_e('Fetched Video Cover', 'ultimate-responsive-image-slider'); ?></span>
                </span>
            </div>
            <div class="urisp-modal-form-fields">
                <div class="urisp-modal-field" style="margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #edf2f7;">
                    <label style="font-weight: 600; font-size: 13px; color: var(--urisp-text); display: block; margin-bottom: 6px;"><?php esc_html_e('Slide Media Type', 'ultimate-responsive-image-slider'); ?></label>
                    <div class="urisp-segmented-control">
                        <input type="radio" id="urisp_modal_type_image" name="urisp_modal_slide_type" value="image" checked>
                        <label for="urisp_modal_type_image"><span class="dashicons dashicons-format-image" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span> <?php esc_html_e('Image', 'ultimate-responsive-image-slider'); ?></label>

                        <input type="radio" id="urisp_modal_type_video" name="urisp_modal_slide_type" value="video">
                        <label for="urisp_modal_type_video"><span class="dashicons dashicons-video-alt3" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span> <?php esc_html_e('Video', 'ultimate-responsive-image-slider'); ?></label>
                    </div>
                </div>

                <div class="urisp-modal-field" id="urisp-modal-video-field" style="display: none; margin-bottom: 15px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="margin-bottom: 12px;">
                        <label for="urisp-modal-video-provider" style="display: block; font-weight: 600; font-size: 13px; color: var(--urisp-text); margin-bottom: 6px;">
                            <span class="dashicons dashicons-video-alt3" style="color: #4f46e5; vertical-align: middle;"></span>
                            <?php esc_html_e('Video Type', 'ultimate-responsive-image-slider'); ?>
                        </label>
                        <select id="urisp-modal-video-provider" class="urisp-input-styled" style="width: 100%; padding: 7px 10px; font-size: 13px;">
                            <option value="vimeo" selected="selected">Vimeo (Active in Free)</option>
                            <option value="youtube" disabled>YouTube (Pro Only)</option>
                            <option value="self_hosted" disabled><?php esc_html_e('Self-Hosted MP4 / WebM (Pro Only)', 'ultimate-responsive-image-slider'); ?></option>
                        </select>
                    </div>

                    <div style="margin-bottom: 6px;">
                        <label for="urisp-modal-video-input" style="display: block; font-weight: 600; font-size: 13px; color: var(--urisp-text); margin-bottom: 6px;">
                            <?php esc_html_e('Video URL', 'ultimate-responsive-image-slider'); ?>
                        </label>
                        <input type="url" id="urisp-modal-video-input" class="urisp-input-styled" style="width: 100%;" placeholder="https://vimeo.com/...">
                    </div>

                    <div id="urisp-video-fetch-actions" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #cbd5e1;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <button type="button" id="urisp-fetch-video-thumb-btn" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 5px; height: 32px; font-weight: 600; color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">
                                <span class="dashicons dashicons-format-image" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
                                <span><?php esc_html_e('Fetch Thumbnail', 'ultimate-responsive-image-slider'); ?></span>
                            </button>
                            <button type="button" id="urisp-revert-video-thumb-btn" class="button" style="display: none; align-items: center; gap: 5px; height: 32px; color: #64748b; border-color: #cbd5e1; background: #ffffff;">
                                <span class="dashicons dashicons-undo" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
                                <span><?php esc_html_e('Revert', 'ultimate-responsive-image-slider'); ?></span>
                            </button>
                            <label style="font-size: 12px; color: #475569; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; margin: 0;">
                                <input type="checkbox" id="urisp-modal-fetch-title" checked>
                                <?php esc_html_e('Also fill title', 'ultimate-responsive-image-slider'); ?>
                            </label>
                        </div>
                        <span id="urisp-video-fetch-status" style="font-size: 12px; font-weight: 500; display: none;"></span>
                        <p style="margin: 6px 0 0 0; font-size: 11px; color: #64748b; width: 100%;"><?php esc_html_e('Click Fetch Thumbnail to download the YouTube/Vimeo cover, or Revert to restore your original uploaded image.', 'ultimate-responsive-image-slider'); ?></p>
                    </div>

                    <div id="urisp-video-self-hosted-hint" style="display: none; margin-top: 10px; padding: 8px 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 11px; color: #166534;">
                        <span class="dashicons dashicons-yes-alt" style="font-size: 14px; width: 14px; height: 14px; line-height: 14px; vertical-align: middle;"></span>
                        <?php esc_html_e('Self-hosted video plays directly inside a native responsive HTML5 player with controls. Your uploaded slide image serves as the video cover.', 'ultimate-responsive-image-slider'); ?>
                    </div>
                </div>


                <div class="urisp-modal-field">
                    <label><?php esc_html_e('Slide Title', 'ultimate-responsive-image-slider'); ?></label>
                    <input type="text" id="urisp-modal-title-input" class="urisp-input-styled">
                </div>
                <div class="urisp-modal-field">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="margin: 0; font-weight: 600; font-size: 13px; color: var(--urisp-text);"><?php esc_html_e('Slide Description', 'ultimate-responsive-image-slider'); ?></label>
                        <div class="urisp-desc-toolbar" style="display: flex; gap: 2px; background: #f1f5f9; padding: 2px 4px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <button type="button" class="urisp-tb-btn" data-action="bold" title="<?php esc_attr_e('Bold', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; font-weight: 700; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;">B</button>
                            <button type="button" class="urisp-tb-btn" data-action="italic" title="<?php esc_attr_e('Italic', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; font-style: italic; font-family: serif; font-weight: 700; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;">I</button>
                            <button type="button" class="urisp-tb-btn" data-action="underline" title="<?php esc_attr_e('Underline', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; text-decoration: underline; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;">U</button>
                            <button type="button" class="urisp-tb-btn" data-action="strike" title="<?php esc_attr_e('Strikethrough', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; text-decoration: line-through; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;">S</button>
                            <span style="display: inline-block; width: 1px; height: 16px; background: #cbd5e1; margin: 5px 2px;"></span>
                            <button type="button" class="urisp-tb-btn" data-action="link" title="<?php esc_attr_e('Insert Link', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;"><span class="dashicons dashicons-admin-links" style="font-size: 15px; width: 15px; height: 15px; line-height: 15px;"></span></button>
                            <button type="button" class="urisp-tb-btn" data-action="ul" title="<?php esc_attr_e('Bullet List', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;"><span class="dashicons dashicons-editor-ul" style="font-size: 15px; width: 15px; height: 15px; line-height: 15px;"></span></button>
                            <button type="button" class="urisp-tb-btn" data-action="ol" title="<?php esc_attr_e('Numbered List', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;"><span class="dashicons dashicons-editor-ol" style="font-size: 15px; width: 15px; height: 15px; line-height: 15px;"></span></button>
                            <button type="button" class="urisp-tb-btn" data-action="heading" title="<?php esc_attr_e('Heading (H3)', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; font-weight: 700; font-size: 11px; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #334155;">H3</button>
                            <span style="display: inline-block; width: 1px; height: 16px; background: #cbd5e1; margin: 5px 2px;"></span>
                            <button type="button" class="urisp-tb-btn" data-action="clear" title="<?php esc_attr_e('Remove Formatting', 'ultimate-responsive-image-slider'); ?>" style="width: 26px; height: 26px; border: none; background: transparent; cursor: pointer; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #ef4444;"><span class="dashicons dashicons-editor-removeformatting" style="font-size: 15px; width: 15px; height: 15px; line-height: 15px;"></span></button>
                        </div>
                    </div>
                    <textarea id="urisp-modal-desc-input" class="urisp-input-styled" rows="5" placeholder="<?php esc_attr_e('Enter slide description...', 'ultimate-responsive-image-slider'); ?>"></textarea>
                </div>
                <div class="urisp-modal-field">
                    <label><?php esc_html_e('Slide Link URL', 'ultimate-responsive-image-slider'); ?></label>
                    <input type="text" id="urisp-modal-link-input" class="urisp-input-styled">
                </div>
                <div class="urisp-modal-field">
                    <label><?php esc_html_e('Slide Alt Text', 'ultimate-responsive-image-slider'); ?></label>
                    <input type="text" id="urisp-modal-alt-input" class="urisp-input-styled">
                </div>

                
            </div>
        </div>
        <div class="urisp-modal-footer">
            <div class="urisp-modal-nav-buttons">
                <button type="button" id="urisp-modal-prev-btn" class="urisp-btn urisp-btn-secondary"><span class="dashicons dashicons-arrow-left-alt2"></span><span><?php esc_html_e('Previous', 'ultimate-responsive-image-slider'); ?></span></button>
                <button type="button" id="urisp-modal-next-btn" class="urisp-btn urisp-btn-secondary"><span><?php esc_html_e('Next', 'ultimate-responsive-image-slider'); ?></span><span class="dashicons dashicons-arrow-right-alt2"></span></button>
            </div>
            <button type="button" id="urisp-modal-save-btn" class="urisp-btn urisp-btn-primary"><span class="dashicons dashicons-saved"></span><span><?php esc_html_e('Save & Close', 'ultimate-responsive-image-slider'); ?></span></button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Ensure "Copy Slider Shortcode" metabox is placed below the "Publish" metabox
    var $submitDiv = $('#submitdiv');
    var $shortcodeBox = $('#urisp_shortcode_meta_box');
    if ($submitDiv.length && $shortcodeBox.length) {
        if ($submitDiv.next('#urisp_shortcode_meta_box').length === 0) {
            $submitDiv.after($shortcodeBox);
        }
    }

    // Tab Switching & Persistence Logic
    var currentPostId = $('#post_ID').val() || '<?php echo isset($post->ID) ? (int)$post->ID : 0; ?>';

    function urispActivateTab(target, updateUrl) {
        if (!target || !$('#' + target).length) return;
        $('.urisp-nav-tab').removeClass('active');
        $('.urisp-nav-tab[data-target="' + target + '"]').addClass('active');
        $('.urisp-tab-content').removeClass('active');
        $('#' + target).addClass('active');

        $('#urisp_active_admin_tab').val(target);

        if (currentPostId) {
            try {
                sessionStorage.setItem('urisp_active_tab_' + currentPostId, target);
                localStorage.setItem('urisp_active_tab_' + currentPostId, target);
            } catch (err) {}
        }

        if (updateUrl !== false && window.history && window.history.replaceState) {
            try {
                window.history.replaceState(null, null, '#' + target);
            } catch (err) {}
        }

        if (typeof window.urisp_init_color_pickers === 'function') {
            setTimeout(function() {
                window.urisp_init_color_pickers($('#' + target));
            }, 50);
        }
    }

    $(document).on('click', '.urisp-nav-tab', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        if (!target) return;
        urispActivateTab(target, true);
    });

    // Check stored tab on page load (URL hash, sessionStorage, localStorage, or hidden input)
    var initialHash = window.location.hash ? window.location.hash.replace('#', '') : '';
    var storedTab = '';
    if (initialHash && $('#' + initialHash).length) {
        storedTab = initialHash;
    } else if (currentPostId) {
        try {
            storedTab = sessionStorage.getItem('urisp_active_tab_' + currentPostId) || localStorage.getItem('urisp_active_tab_' + currentPostId);
        } catch (err) {}
    }
    if (!storedTab) {
        storedTab = $('#urisp_active_admin_tab').val();
    }
    if (storedTab && $('#' + storedTab).length) {
        urispActivateTab(storedTab, false);
    }

    // Save active tab before post form submits
    $('form#post').on('submit', function() {
        var currentActiveTab = $('.urisp-nav-tab.active').data('target');
        if (currentActiveTab) {
            $('#urisp_active_admin_tab').val(currentActiveTab);
            if (currentPostId) {
                try {
                    sessionStorage.setItem('urisp_active_tab_' + currentPostId, currentActiveTab);
                    localStorage.setItem('urisp_active_tab_' + currentPostId, currentActiveTab);
                } catch (err) {}
            }
        }
    });

    // Settings Page Loader Transition
    function revealSettingsPage() {
        $('.urisp-settings-loader').fadeOut(250, function () {
            $(this).remove();
            $('.urisp-settings-main-content').fadeIn(250, function() {
                urispApplyLayoutConditionalVisibility();
            });
        });
    }
    if (document.readyState === 'complete') {
        revealSettingsPage();
    } else {
        $(window).on('load', revealSettingsPage);
    }

    // Dynamic Layout-Specific Settings Visibility
    function urispApplyLayoutConditionalVisibility(layout) {
        // Free version exclusively operates on Layout 3 (Thumbnail Slider)
        layout = '3';

        // Update layout selector cards visual state
        $('.urisp-layout-card').removeClass('selected');
        $('.urisp-layout-card[data-is-pro="0"]').addClass('selected');
        $('.urisp-layout-card[data-is-pro="0"] .urisp-layout-active-badge').text('<?php echo esc_js( __( 'Active', 'ultimate-responsive-image-slider' ) ); ?>');

        // Toggle rows and elements with data-layouts attribute
        $('[data-layouts]').each(function() {
            var allowedLayouts = ($(this).attr('data-layouts') || '').split(',');
            if ($.inArray(layout, allowedLayouts) !== -1) {
                $(this).stop(true, true).slideDown(200);
            } else {
                $(this).stop(true, true).slideUp(200);
            }
        });

        // Hide cards if all their child rows/items are hidden
        setTimeout(function() {
            $('.urisp-card').each(function() {
                var $card = $(this);
                var $rows = $card.find('.urisp-setting-row');
                if ($rows.length > 0) {
                    var visibleRows = $rows.filter(function() {
                        return $(this).css('display') !== 'none';
                    }).length;
                    if (visibleRows === 0) {
                        $card.stop(true, true).slideUp(200);
                    } else {
                        $card.stop(true, true).slideDown(200);
                    }
                }
            });
        // Dynamic Recommended Dimensions for Layout 2 vs others
        if (layout === '2') {
            $('#urisp-dimensions-hint').text('<?php echo esc_js( __( 'Configure width and height (Recommended size: 480x320 px for Layout 2).', 'ultimate-responsive-image-slider' ) ); ?>');
            $('#urisp-recommended-btn-text').text('<?php echo esc_js( __( 'Use 480x320', 'ultimate-responsive-image-slider' ) ); ?>');
        } else {
            $('#urisp-dimensions-hint').text('<?php echo esc_js( __( 'Configure width and height (Recommended size: 1280x720 px).', 'ultimate-responsive-image-slider' ) ); ?>');
            $('#urisp-recommended-btn-text').text('<?php echo esc_js( __( 'Use 1280x720', 'ultimate-responsive-image-slider' ) ); ?>');
        }
        }, 50);
    }

    // Initialize visibility on ready
    urispApplyLayoutConditionalVisibility();

    // Trigger on layout card click (handle free vs pro)
    $(document).on('click', '.urisp-layout-card', function(e) {
        if ($(this).data('is-pro') == '1' || $(this).hasClass('urisp-pro-locked-card')) {
            e.preventDefault();
            e.stopPropagation();
            var proMsg = <?php echo wp_json_encode(__('This layout is available exclusively in Ultimate Responsive Image Slider Pro. Would you like to view Pro features?', 'ultimate-responsive-image-slider')); ?>;
            var openPro = function() {
                window.open('https://wpfrank.com/plugins/ultimate-responsive-image-slider-pro/', '_blank');
            };
            if (window.urispModal && window.urispModal.confirm) {
                urispModal.confirm({
                    title: <?php echo wp_json_encode(__('Pro Feature', 'ultimate-responsive-image-slider')); ?>,
                    message: proMsg,
                    confirmText: <?php echo wp_json_encode(__('View Pro Features', 'ultimate-responsive-image-slider')); ?>,
                    cancelText: <?php echo wp_json_encode(__('Maybe Later', 'ultimate-responsive-image-slider')); ?>,
                    type: 'info'
                }).then(function(confirmed) {
                    if (confirmed) {
                        openPro();
                    }
                });
            } else if (confirm(proMsg)) {
                openPro();
            }
            return false;
        }
        var $radio = $(this).find('input[name="urisp_settings[slider_layout]"]');
        if ($radio.length && !$radio.is(':checked')) {
            $radio.prop('checked', true).trigger('change');
        }
    });

    // Trigger on layout radio button change
    $(document).on('change', 'input[name="urisp_settings[slider_layout]"]', function() {
        var selectedLayout = $(this).val();
        urispApplyLayoutConditionalVisibility(selectedLayout);

        // Auto-switch dimensions if currently at defaults
        var currentW = $('#urisp_slider_width').val();
        var currentH = $('#urisp_slider_height').val();
        if (String(selectedLayout) === '2' && (currentW === '1280' || currentW === '') && (currentH === '720' || currentH === '')) {
            $('#urisp_slider_width').val(480).trigger('change');
            $('#urisp_slider_height').val(320).trigger('change');
        } else if (String(selectedLayout) !== '2' && (currentW === '480' || currentW === '') && (currentH === '320' || currentH === '')) {
            $('#urisp_slider_width').val(1280).trigger('change');
            $('#urisp_slider_height').val(720).trigger('change');
        }
    });

    // Handle "Use Recommended" button click
    $(document).on('click', '#urisp-apply-recommended-dims', function(e) {
        e.preventDefault();
        var selectedLayout = $('input[name="urisp_settings[slider_layout]"]:checked').val() || '3';
        if (String(selectedLayout) === '2') {
            $('#urisp_slider_width').val(480).trigger('change');
            $('#urisp_slider_height').val(320).trigger('change');
        } else {
            $('#urisp_slider_width').val(1280).trigger('change');
            $('#urisp_slider_height').val(720).trigger('change');
        }
    });

    // Dynamic Source Visibility Toggle
    function urispUpdateWooVisibility() {
        var source = $('input[name="urisp_settings[slider_source]"]:checked').val();
        var postType = $('#urisp_dynamic_post_type').val();
        if (source === 'dynamic' && postType === 'product') {
            $('#urisp-woocommerce-config').stop(true, true).slideDown(200);
        } else {
            $('#urisp-woocommerce-config').stop(true, true).slideUp(200);
        }
    }

    function urispUpdateSourceVisibility() {
        var source = $('input[name="urisp_settings[slider_source]"]:checked').val();
        if (source === 'dynamic') {
            $('#urisp-manual-source-container').stop(true, true).slideUp(200);
            $('#urisp-dynamic-source-container').stop(true, true).slideDown(200);
        } else {
            $('#urisp-dynamic-source-container').stop(true, true).slideUp(200);
            $('#urisp-manual-source-container').stop(true, true).slideDown(200);
        }
        urispUpdateWooVisibility();
    }
    urispUpdateSourceVisibility();

    $(document).on('change', 'input[name="urisp_settings[slider_source]"]', function() {
        urispUpdateSourceVisibility();
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    });



    $(document).on('change', 'input[name="urisp_settings[arrow_style]"], input[name="urisp_settings[skin_preset]"], input[name="urisp_settings[ken_burns]"], select[name="urisp_settings[ken_burns_intensity]"]', function() {
        if ($(this).attr('name') === 'urisp_settings[arrow_style]') {
            $(this).closest('.urisp-arrow-style-picker').find('.urisp-arrow-choice').removeClass('active');
            $(this).closest('.urisp-arrow-choice').addClass('active');
        }
        if ($(this).attr('name') === 'urisp_settings[skin_preset]') {
            $(this).closest('.urisp-skin-preset-picker').find('.urisp-skin-choice').removeClass('active');
            $(this).closest('.urisp-skin-choice').addClass('active');
        }
        if ($(this).attr('name') === 'urisp_settings[ken_burns]') {
            if ($(this).is(':checked')) {
                $('#urisp-ken-burns-intensity-wrap').stop(true, true).css('display', 'inline-flex').hide().fadeIn(200);
            } else {
                $('#urisp-ken-burns-intensity-wrap').stop(true, true).fadeOut(200);
            }
        }
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    });

    // Slide Edit Modal Handling
    var activeSlideLi = null;

    // Open Modal
    $(document).on('click', '.uris-slide-edit-icon', function(e) {
        e.preventDefault();
        activeSlideLi = $(this).closest('.uris-slide');
        openEditModal();
    });

    function openEditModal() {
        if (!activeSlideLi || activeSlideLi.length === 0) return;

        // Get values from hidden inputs
        var title = activeSlideLi.find('input[name="rpgp_image_label[]"]').val();
        var desc = activeSlideLi.find('textarea[name="rpgp_image_desc[]"]').val();
        var link = activeSlideLi.find('input[name="rpgp_external_link[]"]').val();
        var alt = activeSlideLi.find('input[name="rpgp_image_alt[]"]').val();
        var imgUrl = activeSlideLi.find('.uris-slide-image').attr('src');

        // Populate modal
        $('#urisp-modal-image-preview').attr('src', imgUrl);
        $('#urisp-modal-title-input').val(title);
        $('#urisp-modal-desc-input').val(desc);
        $('#urisp-modal-link-input').val(link);
        $('#urisp-modal-alt-input').val(alt);

        var origImgId = activeSlideLi.find('input[name="rpgp_orig_image_id[]"]').val() || activeSlideLi.attr('id');
        var origImgUrl = activeSlideLi.find('input[name="rpgp_orig_image_url[]"]').val() || imgUrl;
        var videoThumbUrl = activeSlideLi.find('input[name="rpgp_video_thumb_url[]"]').val() || '';

        // Store back in inputs if missing
        if (!activeSlideLi.find('input[name="rpgp_orig_image_url[]"]').val() && origImgUrl) {
            activeSlideLi.find('input[name="rpgp_orig_image_url[]"]').val(origImgUrl);
        }
        if (!activeSlideLi.find('input[name="rpgp_orig_image_id[]"]').val() && origImgId) {
            activeSlideLi.find('input[name="rpgp_orig_image_id[]"]').val(origImgId);
        }

        // Show/hide Revert button and thumbnail badge
        if (videoThumbUrl || (imgUrl && origImgUrl && imgUrl !== origImgUrl)) {
            $('#urisp-revert-video-thumb-btn').css('display', 'inline-flex');
            $('#urisp-modal-thumb-badge-text').text('<?php esc_attr_e("Fetched Video Cover", 'ultimate-responsive-image-slider'); ?>');
            $('#urisp-modal-thumb-badge').show();
        } else {
            $('#urisp-revert-video-thumb-btn').hide();
            $('#urisp-modal-thumb-badge').hide();
        }
        $('#urisp-video-fetch-status').hide().text('');

        var slideType = activeSlideLi.find('input[name="rpgp_slide_type[]"]').val() || 'image';
        var videoUrl = activeSlideLi.find('input[name="rpgp_video_url[]"]').val() || '';

        if (slideType === 'video') {
            $('#urisp_modal_type_video').prop('checked', true);
            $('#urisp-modal-video-field').show();
        } else {
            $('#urisp_modal_type_image').prop('checked', true);
            $('#urisp-modal-video-field').hide();
        }
        $('#urisp-modal-video-input').val(videoUrl);

        var detectedProvider = 'youtube';
        if (videoUrl) {
            if (videoUrl.match(/vimeo\.com/i)) {
                detectedProvider = 'vimeo';
            } else if (videoUrl.match(/\.(mp4|webm|ogg)($|\?)/i) || videoUrl.match(/\/wp-content\/uploads\//i)) {
                detectedProvider = 'self_hosted';
            }
        }
        $('#urisp-modal-video-provider').val(detectedProvider);
        updateVideoProviderUI(detectedProvider);

        var ctaText = activeSlideLi.find('input[name="rpgp_cta_text[]"]').val() || '';
        var ctaUrl = activeSlideLi.find('input[name="rpgp_cta_url[]"]').val() || '';
        var ctaStyle = activeSlideLi.find('input[name="rpgp_cta_style[]"]').val() || 'default';
        var ctaTarget = activeSlideLi.find('input[name="rpgp_cta_target[]"]').val() || '_blank';

        $('#urisp-modal-cta-text-input').val(ctaText);
        $('#urisp-modal-cta-url-input').val(ctaUrl);
        $('#urisp-modal-cta-style-input').val(ctaStyle);
        $('#urisp-modal-cta-target-input').val(ctaTarget);

                // Update Nav button states
        updateModalNavButtons();

        // Show Modal
        $('body').css('overflow', 'hidden');
        $('#urisp-edit-slide-modal').addClass('urisp-active');
    }

    function updateVideoProviderUI(provider) {
        if (provider === 'self_hosted') {
            $('#urisp-modal-video-input').attr('placeholder', 'https://example.com/wp-content/uploads/video.mp4');
            $('#urisp-video-fetch-actions').hide();
            $('#urisp-video-self-hosted-hint').show();
        } else if (provider === 'vimeo') {
            $('#urisp-modal-video-input').attr('placeholder', 'https://vimeo.com/...');
            $('#urisp-video-fetch-actions').show();
            $('#urisp-video-self-hosted-hint').hide();
        } else {
            // youtube default
            $('#urisp-modal-video-input').attr('placeholder', 'https://www.youtube.com/watch?v=...');
            $('#urisp-video-fetch-actions').show();
            $('#urisp-video-self-hosted-hint').hide();
        }
    }

    $(document).on('change', '#urisp-modal-video-provider', function() {
        var provider = $(this).val();
        updateVideoProviderUI(provider);
    });

    $(document).on('input paste', '#urisp-modal-video-input', function() {
        var val = $(this).val();
        if (val) {
            var newProvider = null;
            if (val.match(/vimeo\.com/i)) {
                newProvider = 'vimeo';
            } else if (val.match(/\.(mp4|webm|ogg)($|\?)/i) || val.match(/\/wp-content\/uploads\//i)) {
                newProvider = 'self_hosted';
            } else if (val.match(/\.(mp4|webm|ogg)($|\?)/i) || val.match(/\/wp-content\/uploads\//i)) {
                $('#urisp-video-fetch-status').html('<strong><?php esc_attr_e("Self-Hosted is a Pro Feature:", "ultimate-responsive-image-slider"); ?></strong> <?php esc_attr_e("Available in Pro version.", "ultimate-responsive-image-slider"); ?>').css({'color': '#d97706', 'display': 'inline-block'});
            } else if (val.match(/(youtube\.com|youtu\.be)/i)) {
                $('#urisp-video-fetch-status').html('<strong><?php esc_attr_e("YouTube is a Pro Feature:", "ultimate-responsive-image-slider"); ?></strong> <?php esc_attr_e("Free supports Vimeo only.", "ultimate-responsive-image-slider"); ?>').css({'color': '#d97706', 'display': 'inline-block'});
            }
            if (newProvider && $('#urisp-modal-video-provider').val() !== newProvider) {
                $('#urisp-modal-video-provider').val(newProvider);
                updateVideoProviderUI(newProvider);
            }
        }
    });

    $(document).on('change', 'input[name="urisp_modal_slide_type"]', function() {
        if ($(this).val() === 'video') {
            $('#urisp-modal-video-field').slideDown(200);
        } else {
            $('#urisp-modal-video-field').slideUp(200);
        }
    });

    function saveModalDataToActiveSlide() {
        if (!activeSlideLi || activeSlideLi.length === 0) return;

        // Get values from modal inputs
        var title = $('#urisp-modal-title-input').val();
        var desc = $('#urisp-modal-desc-input').val();
        var link = $('#urisp-modal-link-input').val();
        var alt = $('#urisp-modal-alt-input').val();
        var slideType = $('input[name="urisp_modal_slide_type"]:checked').val() || 'image';
        var videoUrl = $('#urisp-modal-video-input').val() || '';
        if (slideType === 'video' && videoUrl) {
            var isVimeo = videoUrl.match(/(?:vimeo\.com\/(?:video\/)?)([0-9]+)/i);
            if (!isVimeo) {
                var vimeoNotice = '<?php echo esc_js(__("YouTube and Self-Hosted videos are available in Ultimate Responsive Image Slider Pro. Free version supports Vimeo only.", "ultimate-responsive-image-slider")); ?>';
                if (window.urispModal && window.urispModal.alert) {
                    urispModal.alert({
                        title: <?php echo wp_json_encode(__('Pro Feature Notice', 'ultimate-responsive-image-slider')); ?>,
                        message: vimeoNotice,
                        type: 'info'
                    });
                } else {
                    alert(vimeoNotice);
                }
                slideType = 'image';
                videoUrl = '';
            }
        }
        var ctaText = $('#urisp-modal-cta-text-input').val() || '';
        var ctaUrl = $('#urisp-modal-cta-url-input').val() || '';
        var ctaStyle = $('#urisp-modal-cta-style-input').val() || 'default';
        var ctaTarget = $('#urisp-modal-cta-target-input').val() || '_blank';

        // Write values back to hidden inputs
        activeSlideLi.find('input[name="rpgp_image_label[]"]').val(title);
        activeSlideLi.find('textarea[name="rpgp_image_desc[]"]').val(desc);
        activeSlideLi.find('input[name="rpgp_external_link[]"]').val(link);
        activeSlideLi.find('input[name="rpgp_image_alt[]"]').val(alt);
        activeSlideLi.find('input[name="rpgp_slide_type[]"]').val(slideType);
        activeSlideLi.find('input[name="rpgp_video_url[]"]').val(videoUrl);
        activeSlideLi.find('input[name="rpgp_cta_text[]"]').val(ctaText);
        activeSlideLi.find('input[name="rpgp_cta_url[]"]').val(ctaUrl);
        activeSlideLi.find('input[name="rpgp_cta_style[]"]').val(ctaStyle);
        activeSlideLi.find('input[name="rpgp_cta_target[]"]').val(ctaTarget);

                // Update slide image in grid if preview was updated
        var currentModalImg = $('#urisp-modal-image-preview').attr('src');
        if (currentModalImg) {
            activeSlideLi.find('.uris-slide-image').attr('src', currentModalImg);
        }

        var fetchedThumb = activeSlideLi.data('fetched-thumb-url');
        if (typeof fetchedThumb !== 'undefined') {
            activeSlideLi.find('input[name="rpgp_video_thumb_url[]"]').val(fetchedThumb);
        }
        var fetchedAttachId = activeSlideLi.data('fetched-attachment-id');
        if (typeof fetchedAttachId !== 'undefined' && fetchedAttachId) {
            activeSlideLi.find('input[name="rpgp_image_id[]"]').val(fetchedAttachId);
        }

        // Visual badge for video slides in grid
        if (slideType === 'video' && videoUrl) {
            if (!activeSlideLi.find('.urisp-slide-video-badge').length) {
                activeSlideLi.find('.uris-slide-meta').append('<span class="urisp-slide-video-badge" title="<?php esc_attr_e( 'Video Slide', 'ultimate-responsive-image-slider' ); ?>"><span class="dashicons dashicons-video-alt3"></span></span>');
            }
        } else {
            activeSlideLi.find('.urisp-slide-video-badge').remove();
        }

        // Visual badge for CTA button in grid
        if (ctaText) {
            if (!activeSlideLi.find('.urisp-slide-cta-badge').length) {
                activeSlideLi.find('.uris-slide-meta').append('<span class="urisp-slide-cta-badge" title="<?php esc_attr_e( 'Has CTA Button', 'ultimate-responsive-image-slider' ); ?>"><span class="dashicons dashicons-button"></span></span>');
            }
        } else {
            activeSlideLi.find('.urisp-slide-cta-badge').remove();
        }

        window.urisp_form_is_dirty = true;
        if (typeof urisp_form_is_dirty !== 'undefined') {
            urisp_form_is_dirty = true;
        }
    }

    function updateModalNavButtons() {
        if (!activeSlideLi) return;

        var prevLi = activeSlideLi.prev('.uris-slide');
        var nextLi = activeSlideLi.next('.uris-slide');

        $('#urisp-modal-prev-btn').prop('disabled', prevLi.length === 0);
        $('#urisp-modal-next-btn').prop('disabled', nextLi.length === 0);
        
        // Add visual disabled state class
        if (prevLi.length === 0) {
            $('#urisp-modal-prev-btn').addClass('urisp-btn-disabled');
        } else {
            $('#urisp-modal-prev-btn').removeClass('urisp-btn-disabled');
        }
        if (nextLi.length === 0) {
            $('#urisp-modal-next-btn').addClass('urisp-btn-disabled');
        } else {
            $('#urisp-modal-next-btn').removeClass('urisp-btn-disabled');
        }
    }

            // Fetch Video Thumbnail Button Handler
    $(document).on('click', '#urisp-fetch-video-thumb-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!activeSlideLi || activeSlideLi.length === 0) return;

        var videoUrl = $.trim($('#urisp-modal-video-input').val());
        if (!videoUrl) {
            $('#urisp-video-fetch-status').text('<?php esc_attr_e("Please enter a video URL first.", 'ultimate-responsive-image-slider'); ?>').css({'color': '#ef4444', 'display': 'inline-block'});
            $('#urisp-modal-video-input').focus();
            return;
        }

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px; animation: urisp-spin 1s linear infinite;"></span> <?php esc_attr_e("Fetching...", 'ultimate-responsive-image-slider'); ?>');
        $('#urisp-video-fetch-status').text('<?php esc_attr_e("Fetching video cover...", 'ultimate-responsive-image-slider'); ?>').css({'color': '#6366f1', 'display': 'inline-block'});

        var ajaxEndpoint = (typeof ajaxurl !== 'undefined') ? ajaxurl : ((typeof uris_ajax_object !== 'undefined' && uris_ajax_object.ajax_url) ? uris_ajax_object.ajax_url : '/wp-admin/admin-ajax.php');

        $.ajax({
            url: ajaxEndpoint,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'urisp_fetch_video_thumbnail',
                nonce: '<?php echo esc_attr( wp_create_nonce("urisp_ajax_nonce") ); ?>',
                video_url: videoUrl,
                slider_id: <?php echo absint($URISP_PostId); ?>
            },
            timeout: 10000,
            success: function(response) {
                $btn.prop('disabled', false).html(origHtml);
                if (response.success && response.data) {
                    var data = response.data;
                    var previewSrc = data.preview_url || data.thumbnail_url;
                    
                    $('#urisp-modal-image-preview').attr('src', previewSrc);
                    $('#urisp-modal-thumb-badge-text').text(data.provider + ' Cover');
                    $('#urisp-modal-thumb-badge').show();

                    activeSlideLi.data('fetched-thumb-url', data.thumbnail_url);
                    activeSlideLi.data('fetched-preview-url', previewSrc);
                    if (data.attachment_id) {
                        activeSlideLi.data('fetched-attachment-id', data.attachment_id);
                        activeSlideLi.find('input[name="rpgp_image_id[]"]').val(data.attachment_id);
                    }
                    activeSlideLi.find('input[name="rpgp_video_thumb_url[]"]').val(data.thumbnail_url);
                    activeSlideLi.find('.uris-slide-image').attr('src', previewSrc);

                    if ($('#urisp-modal-fetch-title').is(':checked') && data.title) {
                        if (!$('#urisp-modal-title-input').val()) {
                            $('#urisp-modal-title-input').val(data.title);
                        }
                    }

                    $('#urisp-revert-video-thumb-btn').css('display', 'inline-flex');
                    $('#urisp-video-fetch-status').text('✓ ' + data.message).css({'color': '#16a34a', 'display': 'inline-block'});
                    window.urisp_form_is_dirty = true;
                } else if (response.data && response.data.is_pro_feature) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#urisp-video-fetch-status').html('<strong>' + (response.data.message || '') + '</strong>').css({'color': '#d97706', 'display': 'inline-block'});
                } else {
                    clientSideVideoFetch(videoUrl, $btn, origHtml);
                }
            },
            error: function() {
                clientSideVideoFetch(videoUrl, $btn, origHtml);
            }
        });
    });

    // Revert Video Thumbnail to Original Image
    $(document).on('click', '#urisp-revert-video-thumb-btn', function(e) {
        e.preventDefault();
        if (!activeSlideLi || activeSlideLi.length === 0) return;

        var origImgUrl = activeSlideLi.find('input[name="rpgp_orig_image_url[]"]').val() || activeSlideLi.data('orig-img-src');
        var origImgId = activeSlideLi.find('input[name="rpgp_orig_image_id[]"]').val() || activeSlideLi.data('orig-img-id');

        if (origImgUrl) {
            $('#urisp-modal-image-preview').attr('src', origImgUrl);
            activeSlideLi.find('.uris-slide-image').attr('src', origImgUrl);
        }
        if (origImgId) {
            activeSlideLi.find('input[name="rpgp_image_id[]"]').val(origImgId);
        }

        activeSlideLi.removeData('fetched-thumb-url');
        activeSlideLi.removeData('fetched-preview-url');
        activeSlideLi.removeData('fetched-attachment-id');
        activeSlideLi.find('input[name="rpgp_video_thumb_url[]"]').val('');

        $('#urisp-revert-video-thumb-btn').hide();
        $('#urisp-modal-thumb-badge').hide();
        $('#urisp-video-fetch-status').text('<?php esc_attr_e("Reverted to original uploaded image.", 'ultimate-responsive-image-slider'); ?>').css({'color': '#4f46e5', 'display': 'inline-block'});
        window.urisp_form_is_dirty = true;
    });

    // Client-side fallback if server cannot reach Vimeo directly
    function clientSideVideoFetch(videoUrl, $btn, origBtnHtml) {
        // Block YouTube in Free version
        var ytMatch = videoUrl.match(/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/i);
        if (ytMatch) {
            $btn.prop('disabled', false).html(origBtnHtml);
            $('#urisp-video-fetch-status').html('<strong><?php esc_attr_e("YouTube is a Pro Feature:", "ultimate-responsive-image-slider"); ?></strong> <?php esc_attr_e("Free version supports Vimeo videos only. Upgrade to Pro for YouTube and Self-Hosted videos.", "ultimate-responsive-image-slider"); ?>').css({'color': '#d97706', 'display': 'inline-block'});
            return;
        }

        // Block Self-Hosted in Free version
        if (videoUrl.match(/\.(mp4|webm|ogg)($|\?)/i) || videoUrl.match(/\/wp-content\/uploads\//i)) {
            $btn.prop('disabled', false).html(origBtnHtml);
            $('#urisp-video-fetch-status').html('<strong><?php esc_attr_e("Self-Hosted Video is a Pro Feature:", "ultimate-responsive-image-slider"); ?></strong> <?php esc_attr_e("Free version supports Vimeo videos only. Upgrade to Pro for Self-Hosted videos.", "ultimate-responsive-image-slider"); ?>').css({'color': '#d97706', 'display': 'inline-block'});
            return;
        }

        // Vimeo check
        var vimeoMatch = videoUrl.match(/(?:vimeo\.com\/(?:video\/)?)([0-9]+)/i);
        if (vimeoMatch && vimeoMatch[1]) {
            var vimeoId = vimeoMatch[1];
            $.ajax({
                url: 'https://vimeo.com/api/oembed.json?url=' + encodeURIComponent('https://vimeo.com/' + vimeoId),
                type: 'GET',
                dataType: 'json',
                timeout: 6000,
                success: function(vData) {
                    $btn.prop('disabled', false).html(origBtnHtml);
                    if (vData && vData.thumbnail_url) {
                        var vThumb = vData.thumbnail_url.replace('_640', '_1280');
                        applyClientFetchedThumbnail('Vimeo', vThumb, vimeoId, vData.title || '');
                    } else {
                        $('#urisp-video-fetch-status').text('<?php esc_attr_e("Could not retrieve Vimeo thumbnail.", 'ultimate-responsive-image-slider'); ?>').css({'color': '#ef4444', 'display': 'inline-block'});
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html(origBtnHtml);
                    $('#urisp-video-fetch-status').text('<?php esc_attr_e("Could not connect to Vimeo.", 'ultimate-responsive-image-slider'); ?>').css({'color': '#ef4444', 'display': 'inline-block'});
                }
            });
            return;
        }

        $btn.prop('disabled', false).html(origBtnHtml);
        $('#urisp-video-fetch-status').text('<?php esc_attr_e("Please enter a valid YouTube or Vimeo URL.", 'ultimate-responsive-image-slider'); ?>').css({'color': '#ef4444', 'display': 'inline-block'});
    }

    function applyClientFetchedThumbnail(provider, thumbUrl, videoId, videoTitle) {
        $('#urisp-modal-image-preview').attr('src', thumbUrl);
        $('#urisp-modal-thumb-badge-text').text(provider + ' Cover');
        $('#urisp-modal-thumb-badge').show();

        activeSlideLi.data('fetched-thumb-url', thumbUrl);
        activeSlideLi.data('fetched-preview-url', thumbUrl);
        activeSlideLi.find('input[name="rpgp_video_thumb_url[]"]').val(thumbUrl);
        activeSlideLi.find('.uris-slide-image').attr('src', thumbUrl);

        if ($('#urisp-modal-fetch-title').is(':checked') && videoTitle) {
            if (!$('#urisp-modal-title-input').val()) {
                $('#urisp-modal-title-input').val(videoTitle);
            }
        }

        $('#urisp-revert-video-thumb-btn').css('display', 'inline-flex');
        $('#urisp-video-fetch-status').text('✓ <?php esc_attr_e("Cover thumbnail loaded from", 'ultimate-responsive-image-slider'); ?> ' + provider + '!').css({'color': '#16a34a', 'display': 'inline-block'});
        window.urisp_form_is_dirty = true;
    }


    // Modal Navigation
    $('#urisp-modal-prev-btn').on('click', function() {
        saveModalDataToActiveSlide();
        var prevLi = activeSlideLi.prev('.uris-slide');
        if (prevLi.length > 0) {
            activeSlideLi = prevLi;
            openEditModal();
        }
    });

    $('#urisp-modal-next-btn').on('click', function() {
        saveModalDataToActiveSlide();
        var nextLi = activeSlideLi.next('.uris-slide');
        if (nextLi.length > 0) {
            activeSlideLi = nextLi;
            openEditModal();
        }
    });

    function closeEditModal() {
        saveModalDataToActiveSlide();
        $('#urisp-edit-slide-modal').removeClass('urisp-active');
        if (!$('#urisp-preview-modal').hasClass('urisp-active')) {
            $('body').css('overflow', '');
        }
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    }
    window.urisp_save_modal_data = saveModalDataToActiveSlide;
    window.urisp_close_edit_modal = closeEditModal;

    // Close Modal
    $('.urisp-modal-close, .urisp-modal-overlay, #urisp-modal-save-btn').on('click', function() {
        closeEditModal();
    });

    // ESC key closes edit modal (preview modal takes priority if both are open)
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27 && $('#urisp-edit-slide-modal').hasClass('urisp-active')) {
            if ($('#urisp-preview-modal').hasClass('urisp-active')) {
                return;
            }
            closeEditModal();
        }
    });

    // Inline Formatting Toolbar for Description
    $(document).on('click', '.urisp-tb-btn', function(e) {
        e.preventDefault();
        var action = $(this).data('action');
        var $textarea = $('#urisp-modal-desc-input');
        var el = $textarea[0];
        if (!el) return;

        var start = el.selectionStart;
        var end = el.selectionEnd;
        var val = el.value;
        var selected = val.substring(start, end);

        function wrap(openTag, closeTag, placeholder) {
            var textToWrap = selected || placeholder || '';
            var replacement = openTag + textToWrap + closeTag;
            el.value = val.substring(0, start) + replacement + val.substring(end);
            el.focus();
            var newCursorPos = start + openTag.length + textToWrap.length;
            el.setSelectionRange(start + openTag.length, newCursorPos);
        }

        switch (action) {
            case 'bold':
                wrap('<strong>', '</strong>', 'bold text');
                break;
            case 'italic':
                wrap('<em>', '</em>', 'italic text');
                break;
            case 'underline':
                wrap('<u>', '</u>', 'underlined text');
                break;
            case 'strike':
                wrap('<del>', '</del>', 'text');
                break;
            case 'heading':
                wrap('<h3>', '</h3>', 'Heading');
                break;
            case 'link':
                var url = prompt('<?php esc_html_e("Enter link URL:", 'ultimate-responsive-image-slider'); ?>', 'https://');
                if (url) {
                    wrap('<a href="' + url + '">', '</a>', selected || 'link text');
                }
                break;
            case 'ul':
                if (selected) {
                    var lines = selected.split('\n');
                    var items = lines.map(function(line) { return '  <li>' + line + '</li>'; }).join('\n');
                    wrap('<ul>\n', '\n</ul>', items);
                } else {
                    wrap('<ul>\n  <li>', '</li>\n  <li>Item 2</li>\n</ul>', 'Item 1');
                }
                break;
            case 'ol':
                if (selected) {
                    var lines = selected.split('\n');
                    var items = lines.map(function(line) { return '  <li>' + line + '</li>'; }).join('\n');
                    wrap('<ol>\n', '\n</ol>', items);
                } else {
                    wrap('<ol>\n  <li>', '</li>\n  <li>Item 2</li>\n</ol>', 'Item 1');
                }
                break;
            case 'clear':
                if (selected) {
                    var clean = selected.replace(/<[^>]*>?/gm, '');
                    el.value = val.substring(0, start) + clean + val.substring(end);
                } else {
                    el.value = val.replace(/<[^>]*>?/gm, '');
                }
                el.focus();
                break;
        }
    });

    // Watermark Type Switching (Text vs Logo Image)
        // Watermark Range Sliders Live Badges
    $('#urisp_wm_text_size_range').on('input change', function() {
        $('#urisp_wm_text_size_badge').text($(this).val() + 'pt');
    });
    $('#urisp_wm_image_size_range').on('input change', function() {
        $('#urisp_wm_image_size_badge').text($(this).val() + '%');
    });
    $('#urisp_wm_opacity_range').on('input change', function() {
        $('#urisp_wm_opacity_badge').text($(this).val() + '%');
    });

    // Listen to changes on settings fields to update preview
    $(document).on('change', 'input[name^="urisp_settings["], select[name^="urisp_settings["], textarea[name^="urisp_settings["]', function() {
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    });

    // Listen to keyup on text inputs (with debounce)
    $(document).on('keyup', 'input[name^="urisp_settings["][type="text"], textarea[name^="urisp_settings["]', function() {
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    });

    // Default Settings Object from PHP
    window.urisp_default_settings = <?php echo wp_json_encode( urisp_get_default_settings() ); ?>;

    // Dismiss Reset Settings Notice Banner
    $(document).on('click', '#urisp-dismiss-reset-notice', function(e) {
        e.preventDefault();
        $('#urisp-reset-notice-banner').slideUp(200);
    });

    // Reset Settings Trigger Click Handler
    $(document).on('click', '.urisp-reset-settings-trigger', function(e) {
        e.preventDefault();
        urispResetSettingsToDefaults();
    });

    function urispResetSettingsToDefaults() {
        if (!window.urisp_default_settings) {
            var loadErrMsg = <?php echo wp_json_encode(__('Default settings could not be loaded.', 'ultimate-responsive-image-slider')); ?>;
            if (window.urispModal && window.urispModal.alert) {
                urispModal.alert({
                    title: <?php echo wp_json_encode(__('Error', 'ultimate-responsive-image-slider')); ?>,
                    message: loadErrMsg,
                    type: 'danger'
                });
            } else {
                alert(loadErrMsg);
            }
            return;
        }

        var confirmMsg = <?php echo wp_json_encode(__("Are you sure you want to reset all slider settings to their default values?\n\nYour uploaded slides, images, and content will NOT be affected.\n\nClick Reset to apply default settings in this form, then click 'Update' to save permanently.", 'ultimate-responsive-image-slider')); ?>;

        var executeReset = function() {
            var defaults = window.urisp_default_settings;

            // Iterate through all default settings
            $.each(defaults, function(key, defaultVal) {
                // 1. Radio buttons (segmented controls, radio choices, etc.)
                var $radios = $('input[name="urisp_settings[' + key + ']"][type="radio"]');
                if ($radios.length > 0) {
                    $radios.prop('checked', false);
                    var $match = $radios.filter(function() {
                        return String($(this).val()) === String(defaultVal);
                    });
                    if ($match.length > 0) {
                        $match.prop('checked', true).trigger('change');
                    }
                    // Visual radio pickers (arrow style, skin preset)
                    if (key === 'arrow_style') {
                        $('.urisp-arrow-style-picker .urisp-arrow-choice').removeClass('active');
                        $match.closest('.urisp-arrow-choice').addClass('active');
                    }
                    if (key === 'skin_preset') {
                        $('.urisp-skin-preset-picker .urisp-skin-choice').removeClass('active');
                        $match.closest('.urisp-skin-choice').addClass('active');
                    }
                    return;
                }

                // 2. Checkboxes / Toggles
                var $checkbox = $('input[name="urisp_settings[' + key + ']"][type="checkbox"]');
                if ($checkbox.length > 0) {
                    var isChecked = (defaultVal == 1 || defaultVal === true || defaultVal === '1');
                    $checkbox.prop('checked', isChecked).trigger('change');
                    return;
                }

                // 3. Color Pickers (wp-color-picker)
                var $colorInput = $('input[name="urisp_settings[' + key + ']"].urisp-color-field, input[name="urisp_settings[' + key + ']"].wp-color-picker');
                if ($colorInput.length > 0) {
                    if (typeof $.fn.wpColorPicker === 'function' && $colorInput.closest('.wp-picker-container').length) {
                        $colorInput.wpColorPicker('color', defaultVal);
                    } else {
                        $colorInput.val(defaultVal).trigger('input').trigger('change');
                    }
                    return;
                }

                // 4. Range Sliders
                var $range = $('input[name="urisp_settings[' + key + ']"][type="range"]');
                if ($range.length > 0) {
                    $range.val(defaultVal).trigger('input').trigger('change');
                    return;
                }

                // 5. Select Dropdowns
                var $select = $('select[name="urisp_settings[' + key + ']"]');
                if ($select.length > 0) {
                    $select.val(defaultVal).trigger('change');
                    return;
                }

                // 6. Textareas / Inputs (text, number, url, hidden)
                var $input = $('input[name="urisp_settings[' + key + ']"]:not([type="radio"]):not([type="checkbox"]):not([type="range"]), textarea[name="urisp_settings[' + key + ']"]');
                if ($input.length > 0) {
                    $input.val(defaultVal).trigger('input').trigger('change');
                    return;
                }
            });

            // Reset Watermark live previews & badges
            $('#urisp-wm-logo-preview').hide();
            $('#urisp_wm_opacity_badge').text((defaults['watermark_opacity'] || 50) + '%');
            $('#urisp_wm_text_size_badge').text((defaults['watermark_size'] || 24) + 'pt');
            $('#urisp_wm_image_size_badge').text((defaults['watermark_image_size'] || 15) + '%');

            // Layout-specific conditional updates
            if (typeof urispApplyLayoutConditionalVisibility === 'function') {
                urispApplyLayoutConditionalVisibility(defaults['slider_layout'] || 3);
            }
            if (typeof urispUpdateSourceVisibility === 'function') {
                urispUpdateSourceVisibility();
            }

            // Mark form as dirty so WordPress and preview know changes exist
            window.urisp_form_is_dirty = true;

            // Trigger preview reload if preview modal is active
            if (typeof window.urisp_schedule_preview_reload === 'function') {
                window.urisp_schedule_preview_reload();
            }

            // Show note banner at the top and scroll to it
            var $banner = $('#urisp-reset-notice-banner');
            $banner.stop(true, true).slideDown(300);
            if ($banner.length) {
                $('html, body').animate({
                    scrollTop: $banner.offset().top - 80
                }, 300);
            }

            // Show beautified alert dialog
            var alertMsg = <?php echo wp_json_encode(__("All slider settings have been reset to default values!\n\nPlease note: You must click the 'Update' button to save these changes permanently.", 'ultimate-responsive-image-slider')); ?>;
            if (window.urispModal && window.urispModal.alert) {
                urispModal.alert({
                    title: <?php echo wp_json_encode(__('Settings Reset', 'ultimate-responsive-image-slider')); ?>,
                    message: alertMsg,
                    type: 'success',
                    confirmText: <?php echo wp_json_encode(__('Got It', 'ultimate-responsive-image-slider')); ?>
                });
            } else {
                alert(alertMsg);
            }
        };

        if (window.urispModal && window.urispModal.confirm) {
            urispModal.confirm({
                title: <?php echo wp_json_encode(__('Reset Settings', 'ultimate-responsive-image-slider')); ?>,
                message: confirmMsg,
                confirmText: <?php echo wp_json_encode(__('Reset to Defaults', 'ultimate-responsive-image-slider')); ?>,
                cancelText: <?php echo wp_json_encode(__('Cancel', 'ultimate-responsive-image-slider')); ?>,
                type: 'warning'
            }).then(function(confirmed) {
                if (confirmed) {
                    executeReset();
                }
            });
        } else if (confirm(confirmMsg)) {
            executeReset();
        }
    }

    // ==========================================
    // FONT STYLE & QUICK UPLOAD HANDLERS
    // ==========================================
    $('#urisp_font_style_select').on('change', function() {
        var selectedFont = $(this).val();
        $('#urisp-font-preview-box').css('font-family', "'" + selectedFont + "', sans-serif");
        $('#urisp-current-font-label').text(selectedFont);
        if (typeof window.urisp_schedule_preview_reload === 'function') {
            window.urisp_schedule_preview_reload();
        }
    });

});
</script>
