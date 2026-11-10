var urisp_form_is_dirty = false;
var urisp_preview_loaded = false;

jQuery(document).ready(function($) {
	// Track changes on settings fields
	$('#post').on('change input', 'input, select, textarea', function() {
		urisp_form_is_dirty = true;
	});

	function urisp_open_modal() {
		$('#urisp-preview-modal').addClass('urisp-active');
		$('body').css('overflow', 'hidden');
	}

	function urisp_close_modal() {
		$('#urisp-preview-modal').removeClass('urisp-active');
		if (!$('#urisp-edit-slide-modal').hasClass('urisp-active')) {
			$('body').css('overflow', '');
		}
	}

	$('#urisp-floating-preview-btn').on('click', function() {
		// If Edit Slide details modal is currently open, sync its data first!
		if ($('#urisp-edit-slide-modal').hasClass('urisp-active')) {
			if (typeof window.urisp_save_modal_data === 'function') {
				window.urisp_save_modal_data();
			}
			if (typeof window.urisp_close_edit_modal === 'function') {
				window.urisp_close_edit_modal();
			}
			urisp_form_is_dirty = true;
		}

		urisp_open_modal();

		var iframe = $('#urisp-preview-iframe');
		var currentSrc = iframe.attr('src');
		var dataSrc = iframe.attr('data-src');

		// If settings were modified while editing, save & reload
		if (urisp_form_is_dirty) {
			urisp_reload_preview();
		} else if (!currentSrc || currentSrc === 'about:blank') {
			// First time opening without modifications: load preview directly with zero delay!
			if (dataSrc) {
				$('#urisp-preview-overlay').css('display', 'flex');
				iframe.attr('src', dataSrc);
			}
		}
	});

	$('#urisp-close-preview-modal, #urisp-preview-modal-backdrop').on('click', function() {
		urisp_close_modal();
	});

	// ESC key closes preview
	$(document).on('keydown', function(e) {
		if (e.keyCode === 27 && $('#urisp-preview-modal').hasClass('urisp-active')) {
			urisp_close_modal();
			e.stopPropagation();
		}
	});

	$('#urisp-preview-iframe').on('load', function() {
		var src = $(this).attr('src');
		if (src && src !== 'about:blank') {
			urisp_preview_loaded = true;
			$('#urisp-preview-overlay').fadeOut(120);
		}
	});
});

var urisp_preview_timer = null;
function urisp_schedule_preview_reload() {
	if (!jQuery('#urisp-preview-modal').hasClass('urisp-active')) {
		return; // Don't reload if preview modal is closed
	}
	if (urisp_preview_timer) {
		clearTimeout(urisp_preview_timer);
	}
	urisp_preview_timer = setTimeout(function() {
		urisp_reload_preview();
	}, 750);
}
window.urisp_schedule_preview_reload = urisp_schedule_preview_reload;

function urisp_reload_preview() {
	var btn = jQuery('#urisp-refresh-preview');
	var icon = btn.find('.dashicons');
	icon.addClass('urisp-spin');
	btn.prop('disabled', true);
	jQuery('#urisp-preview-overlay').css('display', 'flex');
	
	
	var formData = jQuery('#post').serializeArray();
	formData.push({name: 'action', value: 'urisp_ajax_save_settings'});
	formData.push({name: 'nonce', value: typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.nonce : ''});
	formData.push({name: 'post_id', value: typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.post_id : 0});
	
	if (typeof uris_ajax_object === 'undefined' || !uris_ajax_object.ajax_url) {
		jQuery('#urisp-preview-overlay').fadeOut(120);
		icon.removeClass('urisp-spin');
		btn.prop('disabled', false);
		return;
	}

	jQuery.post(uris_ajax_object.ajax_url, formData, function(response) {
		if (response.success) {
			urisp_form_is_dirty = false;
			var iframe = jQuery('#urisp-preview-iframe');
			var baseSrc = iframe.attr('data-src') || iframe.attr('src');
			if (baseSrc && baseSrc !== 'about:blank') {
				var cleanSrc = baseSrc.replace(/&_t=\d+/, '') + '&_t=' + new Date().getTime();
				iframe.attr('src', cleanSrc);
			}
		} else {
			var i18n = (typeof uris_ajax_object !== 'undefined' && uris_ajax_object.i18n) ? uris_ajax_object.i18n : {};
			var serverMessage = (response.data && typeof response.data === 'object' && response.data.message) ? response.data.message : '';
			var errNotice = serverMessage || i18n.preview_error || 'Failed to update live preview. Please update the post manually.';
			if (window.urispModal && window.urispModal.alert) {
				urispModal.alert({
					title: 'Preview Notice',
					message: errNotice,
					type: 'warning'
				});
			} else {
				alert(errNotice);
			}
			jQuery('#urisp-preview-overlay').fadeOut(120);
		}
	}).fail(function() {
		jQuery('#urisp-preview-overlay').fadeOut(120);
	}).always(function() {
		icon.removeClass('urisp-spin');
		btn.prop('disabled', false);
	});
}