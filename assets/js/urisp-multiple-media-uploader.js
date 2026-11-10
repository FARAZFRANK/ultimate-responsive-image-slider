jQuery(function(jQuery) {
	var file_frame,
	uris_slider = {
		admin_thumb_ul: '',
		init: function() {
			this.admin_thumb_ul = jQuery('#uris-slides-container');
			if (typeof this.admin_thumb_ul.sortable === 'function') {
				this.admin_thumb_ul.sortable({
					placeholder: 'urisp-sortable-placeholder',
					revert: false,
					tolerance: 'pointer',
					cursor: 'move',
					cancel: '.uris-slide-delete-icon, .uris-slide-edit-icon, .urisp-slide-checkbox-wrap, input, textarea, button, a',
					helper: function(e, item) {
						// Fires BEFORE item is removed from grid — capture correct width
						var w = item.outerWidth();
						var h = item.outerHeight();
						// Lock ALL items to their current pixel width to prevent grid reflow
						item.parent().children('.uris-slide').each(function() {
							this.style.setProperty('width', jQuery(this).outerWidth() + 'px', 'important');
							this.style.setProperty('transition', 'none', 'important');
						});
						item.data('urisp-drag-w', w);
						item.data('urisp-drag-h', h);
						return item;
					},
					start: function(e, ui) {
						var w = ui.item.data('urisp-drag-w');
						var h = ui.item.data('urisp-drag-h');
						ui.item.addClass('urisp-dragging');
						ui.helper[0].style.setProperty('width', w + 'px', 'important');
						ui.placeholder[0].style.setProperty('width', w + 'px', 'important');
						ui.placeholder[0].style.setProperty('height', h + 'px', 'important');
						ui.placeholder[0].style.setProperty('visibility', 'visible', 'important');
					},
					stop: function(e, ui) {
						// Unlock all items — restore grid auto-sizing
						ui.item.parent().children('.uris-slide').each(function() {
							this.style.removeProperty('width');
							this.style.removeProperty('transition');
						});
						ui.item.removeClass('urisp-dragging');
						ui.item[0].style.removeProperty('width');
						if (typeof window.urisp_schedule_preview_reload === 'function') {
							window.urisp_schedule_preview_reload();
						}
					}
				});
			}
			this.admin_thumb_ul.on('click', '.uris-slide-delete-icon', function(e) {
				e.preventDefault();
				var $slide = jQuery(this).closest('.uris-slide');
				var confirmMsg = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_slide_confirm) ? urisp_uploader_i18n.delete_slide_confirm : 'Are you sure you want to delete this slide?';
				var title = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_slide_title) ? urisp_uploader_i18n.delete_slide_title : 'Delete Slide';
				var deleteText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_btn) ? urisp_uploader_i18n.delete_btn : 'Delete';
				var cancelText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.cancel_btn) ? urisp_uploader_i18n.cancel_btn : 'Cancel';

				var doDelete = function() {
					$slide.fadeOut(350, function() {
						jQuery(this).remove();
						uris_slider.updateBulkDeleteUI();
						if (typeof window.urisp_schedule_preview_reload === 'function') {
							window.urisp_schedule_preview_reload();
						}
					});
				};

				if (window.urispModal && window.urispModal.confirm) {
					urispModal.confirm({
						title: title,
						message: confirmMsg,
						confirmText: deleteText,
						cancelText: cancelText,
						type: 'danger'
					}).then(function(confirmed) {
						if (confirmed) {
							doDelete();
						}
					});
				} else if (confirm(confirmMsg)) {
					doDelete();
				}
				return false;
			});

			// Checkbox & Card click listener with Shift + Click range selection
			var lastCheckedCheckbox = null;

			function handleCheckboxRangeSelection(currentCheckbox, isShift, forceState) {
				var $allCheckboxes = uris_slider.admin_thumb_ul.find('.urisp-slide-checkbox');
				if (!currentCheckbox || $allCheckboxes.length === 0) return;

				if (typeof forceState !== 'undefined') {
					currentCheckbox.checked = forceState;
				}
				var isChecked = currentCheckbox.checked;

				if (isShift && lastCheckedCheckbox && lastCheckedCheckbox !== currentCheckbox) {
					var start = $allCheckboxes.index(currentCheckbox);
					var end = $allCheckboxes.index(lastCheckedCheckbox);
					var min = Math.min(start, end);
					var max = Math.max(start, end);

					$allCheckboxes.slice(min, max + 1).each(function() {
						this.checked = isChecked;
						var $slide = jQuery(this).closest('.uris-slide');
						if (isChecked) {
							$slide.addClass('urisp-slide-selected');
						} else {
							$slide.removeClass('urisp-slide-selected');
						}
					});
				} else {
					var $slide = jQuery(currentCheckbox).closest('.uris-slide');
					if (isChecked) {
						$slide.addClass('urisp-slide-selected');
					} else {
						$slide.removeClass('urisp-slide-selected');
					}
				}

				lastCheckedCheckbox = currentCheckbox;
				uris_slider.updateBulkDeleteUI();
			}

			// Direct click on input
			this.admin_thumb_ul.on('click', '.urisp-slide-checkbox', function(e) {
				e.stopPropagation();
				handleCheckboxRangeSelection(this, e.shiftKey);
			});

			// Card click in Bulk Select mode
			this.admin_thumb_ul.on('click', '.uris-slide', function(e) {
				if (!uris_slider.admin_thumb_ul.hasClass('urisp-bulk-mode')) return;
				if (jQuery(e.target).closest('.uris-slide-delete-icon, .uris-slide-edit-icon, input, textarea, a, button').length) {
					return;
				}
				var $checkbox = jQuery(this).find('.urisp-slide-checkbox');
				if ($checkbox.length) {
					var newState = !$checkbox[0].checked;
					handleCheckboxRangeSelection($checkbox[0], e.shiftKey, newState);
				}
			});

			// Bulk Select Mode Toggle Button
			jQuery('#urisp-bulk-toggle-btn').on('click', function(e) {
				e.preventDefault();
				var $ul = uris_slider.admin_thumb_ul;
				var isBulk = $ul.hasClass('urisp-bulk-mode');

				if (isBulk) {
					// Exit Bulk Select Mode
					$ul.removeClass('urisp-bulk-mode');
					jQuery('#urisp-bulk-actions-wrapper').fadeOut(150);
					jQuery(this).html('<span class="dashicons dashicons-forms"></span> Bulk Select')
						.removeClass('urisp-btn-active');
					$ul.find('.urisp-slide-checkbox').prop('checked', false)
						.closest('.uris-slide').removeClass('urisp-slide-selected');
				} else {
					// Enter Bulk Select Mode
					$ul.addClass('urisp-bulk-mode');
					jQuery('#urisp-bulk-actions-wrapper').css('display', 'inline-flex').hide().fadeIn(150);
					jQuery(this).html('<span class="dashicons dashicons-dismiss"></span> Cancel Selection')
						.addClass('urisp-btn-active');
				}
				uris_slider.updateBulkDeleteUI();
			});

			// Select All Button
			jQuery('#urisp-select-all-btn').on('click', function(e) {
				e.preventDefault();
				uris_slider.admin_thumb_ul.find('.urisp-slide-checkbox')
					.prop('checked', true)
					.closest('.uris-slide').addClass('urisp-slide-selected');
				uris_slider.updateBulkDeleteUI();
			});

			// Deselect All Button
			jQuery('#urisp-deselect-all-btn').on('click', function(e) {
				e.preventDefault();
				uris_slider.admin_thumb_ul.find('.urisp-slide-checkbox')
					.prop('checked', false)
					.closest('.uris-slide').removeClass('urisp-slide-selected');
				uris_slider.updateBulkDeleteUI();
			});

			// Delete Selected Slides
			jQuery('#urisp-delete-selected-btn').on('click', function(e) {
				e.preventDefault();
				var $checked = uris_slider.admin_thumb_ul.find('.urisp-slide-checkbox:checked');
				var count = $checked.length;
				if (count === 0) return false;

				var confirmMsg = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_selected_confirm) ? urisp_uploader_i18n.delete_selected_confirm : 'Are you sure you want to delete the selected slide(s)?';
				var title = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_selected_title) ? urisp_uploader_i18n.delete_selected_title : 'Delete Selected Slides';
				var deleteText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_btn) ? urisp_uploader_i18n.delete_btn : 'Delete';
				var cancelText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.cancel_btn) ? urisp_uploader_i18n.cancel_btn : 'Cancel';

				var doDeleteSelected = function() {
					$checked.each(function() {
						jQuery(this).closest('.uris-slide').fadeOut(350, function() {
							jQuery(this).remove();
							uris_slider.updateBulkDeleteUI();
							if (typeof window.urisp_schedule_preview_reload === 'function') {
								window.urisp_schedule_preview_reload();
							}
						});
					});
				};

				if (window.urispModal && window.urispModal.confirm) {
					urispModal.confirm({
						title: title,
						message: confirmMsg,
						confirmText: deleteText + ' (' + count + ')',
						cancelText: cancelText,
						type: 'danger'
					}).then(function(confirmed) {
						if (confirmed) {
							doDeleteSelected();
						}
					});
				} else if (confirm(confirmMsg)) {
					doDeleteSelected();
				}
				return false;
			});

			// Prevent default behavior for drag events on the whole document
			jQuery(document).on('dragover dragleave drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
			});

			// Highlight upload area on drag over
			var $uploadArea = jQuery('#uris-add-new-slide');
			$uploadArea.on('dragover dragenter', function(e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery(this).addClass('urisp-dragover');
			});

			// Remove highlight on drag leave
			$uploadArea.on('dragleave dragend drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery(this).removeClass('urisp-dragover');
			});

			// Handle file drop
			$uploadArea.on('drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery(this).removeClass('urisp-dragover');
				var files = e.originalEvent.dataTransfer.files;
				if (files.length > 0) {
					uris_slider.upload_files(files);
				}
			});
			
			jQuery('#uris-add-new-slide').on('click', function(event) {
				event.preventDefault();
				if (file_frame) {
					file_frame.open();
					return;
				}

				file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery(this).data('uploader_title'),
					button: {
						text: jQuery(this).data('uploader_button_text'),
					},
					multiple: true
				});

				file_frame.on('select', function() {
					var images = file_frame.state().get('selection').toJSON(),
							length = images.length;
					if (length > 0) {
						var ids = [];
						for (var i = 0; i < length; i++) {
							ids.push(images[i]['id']);
						}
						uris_slider.get_multiple_thumbnails(ids);
					}
				});
				file_frame.open();
			});
			
			jQuery('#uris-delete-all-slide').on('click', function(e) {
				e.preventDefault();
				var confirmMsg = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_all_confirm) ? urisp_uploader_i18n.delete_all_confirm : 'Are you sure you want to delete all the slides?';
				var title = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_all_title) ? urisp_uploader_i18n.delete_all_title : 'Delete All Slides';
				var deleteText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.delete_btn) ? urisp_uploader_i18n.delete_btn : 'Delete All';
				var cancelText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.cancel_btn) ? urisp_uploader_i18n.cancel_btn : 'Cancel';

				var doDeleteAll = function() {
					uris_slider.admin_thumb_ul.empty();
					uris_slider.updateBulkDeleteUI();
					if (typeof window.urisp_schedule_preview_reload === 'function') {
						window.urisp_schedule_preview_reload();
					}
				};

				if (window.urispModal && window.urispModal.confirm) {
					urispModal.confirm({
						title: title,
						message: confirmMsg,
						confirmText: deleteText,
						cancelText: cancelText,
						type: 'danger'
					}).then(function(confirmed) {
						if (confirmed) {
							doDeleteAll();
						}
					});
				} else if (confirm(confirmMsg)) {
					doDeleteAll();
				}
				return false;
			});

			this.updateBulkDeleteUI();
		},
		updateBulkDeleteUI: function() {
			var $ul = this.admin_thumb_ul;
			var $checkboxes = $ul.find('.urisp-slide-checkbox');
			var count = $checkboxes.filter(':checked').length;
			
			jQuery('#urisp-selected-count').text(count);
			
			if (count > 0) {
				jQuery('#urisp-delete-selected-btn').css('display', 'inline-flex');
			} else {
				jQuery('#urisp-delete-selected-btn').hide();
			}
		},
		get_thumbnail_uris: function(id, cb) {
			cb = cb || function() {
			};
			var data = {
				action: 'uris_get_thumbnail',
				imageid: id,
				nonce: typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.nonce : ''
			};
			jQuery.post(ajaxurl, data, function(response) {
				uris_slider.admin_thumb_ul.append(response);
				cb();
			});
		},
		get_multiple_thumbnails: function(ids) {
			var length = ids.length;
			var uploader = jQuery('#uris-add-new-slide');
			jQuery('#urisp-upload-loading-text').text('Adding ' + length + (length === 1 ? ' image...' : ' images...'));
			uploader.addClass('loading');

			var data = {
				action: 'uris_get_thumbnail',
				imageids: ids,
				nonce: typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.nonce : ''
			};

			jQuery.post(ajaxurl, data, function(response) {
				uris_slider.admin_thumb_ul.append(response);
				if (typeof window.urisp_schedule_preview_reload === 'function') {
					window.urisp_schedule_preview_reload();
				}
			}).always(function() {
				uploader.removeClass('loading');
			});
		},
		upload_files: function(files) {
			var total = files.length;
			if (total === 0) return;
			
			var uploader = jQuery('#uris-add-new-slide');
			jQuery('#urisp-upload-loading-text').text('Adding ' + total + (total === 1 ? ' image...' : ' images...'));
			uploader.addClass('loading');
			
			var formData = new FormData();
			formData.append('action', 'urisp_ajax_upload_file');
			formData.append('nonce', typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.nonce : '');
			
			for (var i = 0; i < total; i++) {
				formData.append('async-uploads[]', files[i]);
			}
			
			var ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : (typeof uris_ajax_object !== 'undefined' ? uris_ajax_object.ajax_url : '/wp-admin/admin-ajax.php');
			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					if (response.success) {
						uris_slider.admin_thumb_ul.append(response.data.html);
						if (typeof window.urisp_schedule_preview_reload === 'function') {
							window.urisp_schedule_preview_reload();
						}
					} else {
						var failMsg = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.upload_failed) ? urisp_uploader_i18n.upload_failed : 'Failed to upload files: ';
						var errMsg = failMsg + (response.data || 'Unknown error');
						if (window.urispModal && window.urispModal.alert) {
							urispModal.alert({
								title: 'Upload Failed',
								message: errMsg,
								type: 'danger'
							});
						} else {
							alert(errMsg);
						}
					}
				},
				error: function() {
					var errText = (typeof urisp_uploader_i18n !== 'undefined' && urisp_uploader_i18n.upload_error) ? urisp_uploader_i18n.upload_error : 'An error occurred while uploading files.';
					if (window.urispModal && window.urispModal.alert) {
						urispModal.alert({
							title: 'Upload Error',
							message: errText,
							type: 'danger'
						});
					} else {
						alert(errText);
					}
				}
			}).always(function() {
				uploader.removeClass('loading');
			});
		}
	};
	uris_slider.init();
});