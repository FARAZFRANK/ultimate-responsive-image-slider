/**
 * Ultimate Responsive Image Slider - Modern Dialog & Alert Modal System
 * Provides sleek, branded modal dialogs for alert and confirm replacements.
 */
(function(window, document) {
    'use strict';

    var ICONS = {
        danger: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',
        warning: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        info: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        success: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
    };

    var activeResolver = null;
    var lastActiveElement = null;
    var container = null;

    function createDialogMarkup() {
        if (container) return container;

        var el = document.createElement('div');
        el.className = 'urisp-dialog-backdrop';
        el.id = 'urisp-dialog-container';
        el.setAttribute('role', 'dialog');
        el.setAttribute('aria-modal', 'true');
        el.setAttribute('aria-hidden', 'true');
        el.innerHTML = [
            '<div class="urisp-dialog-card">',
            '  <button type="button" class="urisp-dialog-close" aria-label="Close dialog">&times;</button>',
            '  <div class="urisp-dialog-icon-wrapper"></div>',
            '  <div class="urisp-dialog-body">',
            '    <h3 class="urisp-dialog-title"></h3>',
            '    <div class="urisp-dialog-message"></div>',
            '  </div>',
            '  <div class="urisp-dialog-footer">',
            '    <button type="button" class="urisp-dialog-btn urisp-dialog-btn-cancel">Cancel</button>',
            '    <button type="button" class="urisp-dialog-btn urisp-dialog-btn-confirm">Confirm</button>',
            '  </div>',
            '</div>'
        ].join('');

        // Backdrop click to dismiss
        el.addEventListener('click', function(e) {
            if (e.target === el) {
                closeDialog(false);
            }
        });

        var closeBtn = el.querySelector('.urisp-dialog-close');
        var cancelBtn = el.querySelector('.urisp-dialog-btn-cancel');
        var confirmBtn = el.querySelector('.urisp-dialog-btn-confirm');

        closeBtn.addEventListener('click', function() {
            closeDialog(false);
        });

        cancelBtn.addEventListener('click', function() {
            closeDialog(false);
        });

        confirmBtn.addEventListener('click', function() {
            closeDialog(true);
        });

        document.body.appendChild(el);
        container = el;
        return container;
    }

    // Keyboard handlers
    document.addEventListener('keydown', function(e) {
        if (!container || !container.classList.contains('urisp-dialog-active')) return;

        if (e.key === 'Escape' || e.keyCode === 27) {
            e.preventDefault();
            closeDialog(false);
        } else if (e.key === 'Enter' || e.keyCode === 13) {
            // If focus is on cancel button, let it cancel
            if (document.activeElement && document.activeElement.classList.contains('urisp-dialog-btn-cancel')) {
                return;
            }
            e.preventDefault();
            closeDialog(true);
        }
    });

    function closeDialog(result) {
        if (!container || !container.classList.contains('urisp-dialog-active')) return;

        container.classList.remove('urisp-dialog-active');
        container.setAttribute('aria-hidden', 'true');

        if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
            try { lastActiveElement.focus(); } catch (err) {}
        }

        if (activeResolver) {
            var resolver = activeResolver;
            activeResolver = null;
            resolver(result);
        }
    }

    function openDialog(options) {
        createDialogMarkup();
        lastActiveElement = document.activeElement;

        var type = options.type || 'info';
        if (!ICONS[type]) type = 'info';

        var title = options.title || (type === 'danger' ? 'Confirm Action' : (type === 'warning' ? 'Attention' : (type === 'success' ? 'Success' : 'Notice')));
        var message = options.message || '';
        var isConfirm = options.isConfirm !== false;
        var confirmText = options.confirmText || (isConfirm ? 'Confirm' : 'OK');
        var cancelText = options.cancelText || 'Cancel';

        // Update elements
        var card = container.querySelector('.urisp-dialog-card');
        var iconWrapper = container.querySelector('.urisp-dialog-icon-wrapper');
        var titleEl = container.querySelector('.urisp-dialog-title');
        var messageEl = container.querySelector('.urisp-dialog-message');
        var cancelBtn = container.querySelector('.urisp-dialog-btn-cancel');
        var confirmBtn = container.querySelector('.urisp-dialog-btn-confirm');

        // Reset classes
        card.className = 'urisp-dialog-card urisp-dialog--' + type;
        iconWrapper.className = 'urisp-dialog-icon-wrapper urisp-dialog-icon--' + type;
        iconWrapper.innerHTML = ICONS[type] || ICONS.info;

        titleEl.textContent = title;
        messageEl.textContent = message;

        confirmBtn.className = 'urisp-dialog-btn urisp-dialog-btn-confirm urisp-dialog-btn-' + type;
        confirmBtn.textContent = confirmText;

        if (isConfirm) {
            cancelBtn.style.display = '';
            cancelBtn.textContent = cancelText;
            confirmBtn.style.minWidth = '';
        } else {
            cancelBtn.style.display = 'none';
            confirmBtn.style.minWidth = '140px';
        }

        container.setAttribute('aria-hidden', 'false');
        // Force reflow for smooth animation
        void container.offsetWidth;
        container.classList.add('urisp-dialog-active');

        // Focus primary action
        setTimeout(function() {
            if (confirmBtn) confirmBtn.focus();
        }, 60);

        return new Promise(function(resolve) {
            activeResolver = function(result) {
                if (typeof options.callback === 'function') {
                    options.callback(result);
                }
                resolve(result);
            };
        });
    }

    var urispModal = {
        /**
         * Confirm dialog replacement
         * @param {Object|string} options
         * @param {Function} [onConfirm]
         * @param {Function} [onCancel]
         * @returns {Promise<boolean>}
         */
        confirm: function(options, onConfirm, onCancel) {
            var opts = {};
            if (typeof options === 'string') {
                var lower = options.toLowerCase();
                var detectedType = 'info';
                var detectedTitle = 'Confirmation';
                var detectedConfirm = 'Confirm';

                if (lower.indexOf('delete') !== -1 || lower.indexOf('remove') !== -1) {
                    detectedType = 'danger';
                    detectedTitle = 'Delete Confirmation';
                    detectedConfirm = 'Delete';
                } else if (lower.indexOf('reset') !== -1) {
                    detectedType = 'warning';
                    detectedTitle = 'Reset Settings';
                    detectedConfirm = 'Reset';
                }

                opts = {
                    title: detectedTitle,
                    message: options,
                    type: detectedType,
                    confirmText: detectedConfirm,
                    cancelText: 'Cancel',
                    isConfirm: true
                };
            } else if (typeof options === 'object' && options !== null) {
                opts = Object.assign({}, options);
                opts.isConfirm = true;
            }

            return openDialog(opts).then(function(confirmed) {
                if (confirmed && typeof onConfirm === 'function') {
                    onConfirm();
                } else if (!confirmed && typeof onCancel === 'function') {
                    onCancel();
                }
                return confirmed;
            });
        },

        /**
         * Alert dialog replacement
         * @param {Object|string} options
         * @param {Function} [onClose]
         * @returns {Promise<boolean>}
         */
        alert: function(options, onClose) {
            var opts = {};
            if (typeof options === 'string') {
                var lower = options.toLowerCase();
                var detectedType = 'info';
                var detectedTitle = 'Notice';

                if (lower.indexOf('fail') !== -1 || lower.indexOf('error') !== -1) {
                    detectedType = 'danger';
                    detectedTitle = 'Error';
                } else if (lower.indexOf('pro') !== -1) {
                    detectedType = 'info';
                    detectedTitle = 'Pro Feature';
                } else if (lower.indexOf('success') !== -1 || lower.indexOf('reset to default') !== -1) {
                    detectedType = 'success';
                    detectedTitle = 'Success';
                }

                opts = {
                    title: detectedTitle,
                    message: options,
                    type: detectedType,
                    confirmText: 'OK',
                    isConfirm: false
                };
            } else if (typeof options === 'object' && options !== null) {
                opts = Object.assign({}, options);
                opts.isConfirm = false;
            }

            return openDialog(opts).then(function(result) {
                if (typeof onClose === 'function') {
                    onClose(result);
                }
                return result;
            });
        },

        /**
         * Toast notification system
         * @param {Object|string} options
         * @param {string} [type] 'success' | 'info' | 'warning' | 'danger'
         */
        toast: function(options, type) {
            var msg = '';
            var toastType = type || 'info';
            var duration = 3000;

            if (typeof options === 'string') {
                msg = options;
            } else if (typeof options === 'object' && options !== null) {
                msg = options.message || '';
                toastType = options.type || toastType;
                duration = options.duration || duration;
            }

            var toastContainer = document.querySelector('.urisp-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'urisp-toast-container';
                document.body.appendChild(toastContainer);
            }

            var toastEl = document.createElement('div');
            toastEl.className = 'urisp-toast urisp-toast--' + toastType;
            toastEl.innerHTML = [
                '<div class="urisp-toast-icon">' + (ICONS[toastType] || ICONS.info) + '</div>',
                '<div class="urisp-toast-msg">' + msg + '</div>',
                '<button type="button" class="urisp-toast-close" aria-label="Dismiss">&times;</button>'
            ].join('');

            toastContainer.appendChild(toastEl);

            // Trigger animation
            void toastEl.offsetWidth;
            toastEl.classList.add('urisp-toast-active');

            function dismissToast() {
                toastEl.classList.remove('urisp-toast-active');
                setTimeout(function() {
                    if (toastEl.parentNode) {
                        toastEl.parentNode.removeChild(toastEl);
                    }
                }, 250);
            }

            toastEl.querySelector('.urisp-toast-close').addEventListener('click', dismissToast);

            setTimeout(dismissToast, duration);
        }
    };

    window.urispModal = urispModal;

    // Gracefully intercept native window.alert so all unexpected browser alerts are beautified automatically
    try {
        var _nativeAlert = window.alert;
        window.alert = function(msg) {
            if (window.urispModal && typeof window.urispModal.alert === 'function') {
                return window.urispModal.alert(msg);
            }
            return _nativeAlert ? _nativeAlert.apply(window, arguments) : undefined;
        };
    } catch (e) {}

    // Attach to document when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createDialogMarkup);
    } else {
        createDialogMarkup();
    }

})(window, document);
