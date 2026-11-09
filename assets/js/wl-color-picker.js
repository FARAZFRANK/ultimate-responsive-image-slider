/**
 * Ultimate Responsive Image Slider - Enhanced Color Picker Controller
 * 
 * Fixes:
 * 1. Hidden tab zero-dimension Iris canvas bug (recalculates dimensions on tab switch & picker open).
 * 2. Input event propagation (syncs hex value and triggers 'input' & 'change' for preview/save).
 * 3. Z-index stacking context in CSS grid (elevates active color item above sibling rows).
 * 4. RGBA / non-hex string sanitization to prevent Iris parsing exceptions.
 */
(function($) {
    'use strict';

    if (typeof $.fn.wpColorPicker !== 'function') {
        return;
    }

    /**
     * Convert an rgba string to hex or fallback to clean hex
     */
    function sanitizeColorForIris(val, fallback) {
        if (!val || typeof val !== 'string') return fallback || '#ffffff';
        val = val.trim();
        if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(val)) {
            return val;
        }
        var rgbaMatch = val.match(/rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
        if (rgbaMatch) {
            var r = parseInt(rgbaMatch[1], 10).toString(16).padStart(2, '0');
            var g = parseInt(rgbaMatch[2], 10).toString(16).padStart(2, '0');
            var b = parseInt(rgbaMatch[3], 10).toString(16).padStart(2, '0');
            return '#' + r + g + b;
        }
        if (/^[0-9a-fA-F]{6}$/.test(val) || /^[0-9a-fA-F]{3}$/.test(val)) {
            return '#' + val;
        }
        return fallback || '#ffffff';
    }

    /**
     * Recalculate Iris internal square and slider dimensions
     * This fixes the critical WordPress Iris bug where inputs initialized
     * in hidden tabs (display: none) receive dimensions of {w:0, h:0},
     * causing square clicks to divide by zero (NaN) and freeze the picker.
     */
    function refreshIrisDimensions($field) {
        var pickerObj = $field.data('wpWpColorPicker') || $field.data('iris');
        var $holder = null;
        if (pickerObj && pickerObj.picker) {
            $holder = pickerObj.picker;
        } else {
            $holder = $field.closest('.wp-picker-container').find('.iris-picker');
        }

        if ($holder && $holder.length) {
            var $square = $holder.find('.iris-square');
            if ($square.length && $square.width() > 0) {
                $square.data('dimensions', {
                    w: $square.width(),
                    h: $square.height()
                });
            }
            var $slider = $holder.find('.iris-slider');
            if ($slider.length && $slider.width() > 0) {
                $slider.data('dimensions', {
                    w: $slider.width(),
                    h: $slider.height()
                });
            }
        }
    }

    var urispColorOptions = {
        defaultColor: false,
        change: function(event, ui) {
            var $input = $(this);
            if (ui && ui.color) {
                var hexColor = ui.color.toString();
                if ($input.val() !== hexColor) {
                    $input.val(hexColor);
                }
            }
            // Trigger native input and change so listeners and serialize pick up the new value
            $input.trigger('input').trigger('change');

            if (typeof window.urisp_schedule_preview_reload === 'function') {
                window.urisp_schedule_preview_reload();
            }
        },
        clear: function(event) {
            var $input = $(this);
            $input.val('').trigger('input').trigger('change');

            if (typeof window.urisp_schedule_preview_reload === 'function') {
                window.urisp_schedule_preview_reload();
            }
        },
        hide: true,
        palettes: [
            '#000000', '#ffffff', '#6366f1', '#4f46e5',
            '#0ea5e9', '#0284c7', '#10b981', '#ef4444',
            '#f59e0b', '#8b5cf6', '#0f172a', '#334155'
        ]
    };

    /**
     * Initialize all color fields within a given container
     */
    function initColorPickers($container) {
        var $target = $container && $container.length ? $container : $(document);

        $target.find('.my-color-field').each(function() {
            var $this = $(this);

            // Sanitize default color if present
            var defCol = $this.attr('data-default-color');
            if (defCol && (defCol.indexOf('rgb') !== -1 || !defCol.startsWith('#'))) {
                var cleanDef = sanitizeColorForIris(defCol, '#0f172a');
                $this.attr('data-default-color', cleanDef);
            }

            // Sanitize initial value if invalid
            var currentVal = $this.val();
            if (currentVal && currentVal.indexOf('rgb') !== -1) {
                $this.val(sanitizeColorForIris(currentVal, '#0f172a'));
            }

            // If already initialized, refresh dimensions if now visible
            if ($this.data('wpWpColorPicker')) {
                if ($this.is(':visible') || $this.closest('.urisp-tab-content.active').length) {
                    refreshIrisDimensions($this);
                }
                return;
            }

            // Initialize wpColorPicker
            $this.wpColorPicker(urispColorOptions);

            // If it's now in a visible container, recalculate immediately
            if ($this.is(':visible') || $this.closest('.urisp-tab-content.active').length) {
                setTimeout(function() {
                    refreshIrisDimensions($this);
                }, 30);
            }
        });
    }

    // Expose globally for tab switching and dynamic content
    window.urisp_init_color_pickers = initColorPickers;
    window.urisp_refresh_iris = refreshIrisDimensions;

    $(document).ready(function() {
        // Initial pass on document ready
        initColorPickers();

        // Listen for tab switching to initialize/refresh hidden color pickers
        $(document).on('click', '.urisp-nav-tab', function() {
            var targetId = $(this).data('target');
            if (targetId) {
                setTimeout(function() {
                    var $activeTab = $('#' + targetId);
                    initColorPickers($activeTab);
                    $activeTab.find('.my-color-field').each(function() {
                        refreshIrisDimensions($(this));
                    });
                }, 60);
            }
        });

        // When the user clicks the "Select Color" swatch button
        $(document).on('click', '.urisp-settings-wrapper .wp-color-result', function(e) {
            var $btn = $(this);
            var $container = $btn.closest('.wp-picker-container');
            var $colorItem = $btn.closest('.urisp-color-item, .urisp-setting-field');
            var $field = $container.find('.my-color-field');

            // Close other color pickers to prevent overlapping dropdowns
            $('.urisp-settings-wrapper .wp-picker-container.wp-picker-active').not($container).each(function() {
                var $otherInput = $(this).find('.my-color-field');
                if ($otherInput.data('wpWpColorPicker')) {
                    $otherInput.wpColorPicker('close');
                }
            });
            $('.urisp-color-item.is-picker-active').not($colorItem).removeClass('is-picker-active');

            // Toggle active z-index class on the item container
            setTimeout(function() {
                if ($container.hasClass('wp-picker-active')) {
                    $colorItem.addClass('is-picker-active');
                    // Force refresh coordinates on the square & slider
                    refreshIrisDimensions($field);
                } else {
                    $colorItem.removeClass('is-picker-active');
                }
            }, 25);
        });

        // Close active state when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.wp-picker-container').length) {
                $('.urisp-color-item.is-picker-active').removeClass('is-picker-active');
            }
        });

        // Also allow manual typing into the hex input field to update preview and swatch
        $(document).on('keyup change paste', '.urisp-settings-wrapper input.wp-color-picker', function() {
            var $input = $(this);
            var val = $input.val().trim();
            if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(val)) {
                if (typeof window.urisp_schedule_preview_reload === 'function') {
                    window.urisp_schedule_preview_reload();
                }
            }
        });
    });

})(jQuery);