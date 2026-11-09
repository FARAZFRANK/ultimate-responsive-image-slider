(function(blocks, blockEditor, element, components) {
	'use strict';

	if (!blocks || !element || !components || typeof blocks.registerBlockType !== 'function') {
		return;
	}

	var el = element.createElement;
	var registerBlockType = blocks.registerBlockType;
	var InspectorControls = blockEditor.InspectorControls;
	var BlockControls = blockEditor.BlockControls;
	var useBlockProps = blockEditor.useBlockProps;
	var SelectControl = components.SelectControl;
	var PanelBody = components.PanelBody;
	var ToolbarGroup = components.ToolbarGroup;
	var ToolbarButton = components.ToolbarButton;
	var Disabled = components.Disabled;
	var ServerSideRender = (window.wp && window.wp.serverSideRender && window.wp.serverSideRender.default)
		? window.wp.serverSideRender.default
		: (window.wp && (window.wp.serverSideRender || (window.wp.components && window.wp.components.ServerSideRender)));
	var useRef = element.useRef;
	var useEffect = element.useEffect;

	registerBlockType('urisp/slider-select', {
		apiVersion: 3,
		title: 'Ultimate Responsive Image Slider',
		description: 'Display a responsive image slider with thumbnails, lightboxes, and touch gestures.',
		icon: 'images-alt2',
		category: 'widgets',
		keywords: [ 'slider', 'gallery', 'carousel', 'responsive image slider', 'urisp', 'slideshow', 'image' ],
		attributes: {
			sliderId: {
				type: 'string',
				default: ''
			}
		},

		edit: function(props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps = useBlockProps();
			var wrapperRef = useRef(null);

			// Initialize slider inside Gutenberg editor when ServerSideRender finishes
			useEffect(function() {
				if (!attributes.sliderId || !wrapperRef.current) return;

				function tryInitSlider() {
					if (typeof jQuery !== 'undefined' && typeof jQuery.fn.sliderPro !== 'undefined' && wrapperRef.current) {
						var $slider = jQuery(wrapperRef.current).find('.slider-pro');
						if ($slider.length) {
							$slider.find('.urisp-frontend-loader-wrap').hide();
							$slider.find('.urisp-skeleton-loading').removeClass('urisp-skeleton-loading');

							if (!$slider.hasClass('sp-initialised')) {
								$slider.sliderPro({
									width: '100%',
									height: 500,
									autoHeight: false,
									arrows: true,
									buttons: true,
									thumbnailArrows: true,
									thumbnailWidth: 120,
									thumbnailHeight: 80,
									fade: false,
									touchSwipe: false,
									autoplay: false,
									init: function() {
										$slider.find('.urisp-frontend-loader-wrap').hide();
										$slider.find('.urisp-skeleton-loading').removeClass('urisp-skeleton-loading');
										setTimeout(function() {
											jQuery(window).trigger('resize');
										}, 100);
									}
								});
							}
						}
					}
				}

				var observer = new MutationObserver(function() {
					tryInitSlider();
				});

				observer.observe(wrapperRef.current, { childList: true, subtree: true });
				var timer1 = setTimeout(tryInitSlider, 200);
				var timer2 = setTimeout(tryInitSlider, 600);

				return function() {
					clearTimeout(timer1);
					clearTimeout(timer2);
					observer.disconnect();
				};
			}, [attributes.sliderId]);

			// Load localized sliders data
			var sData = window.urisp_gutenberg_data || { sliders: [], edit_url_base: '', create_slider_url: '' };

			// Build sliders list
			var sliderOptions = [{ label: '-- Select Slider --', value: '' }];
			if (sData.sliders && sData.sliders.length > 0) {
				sData.sliders.forEach(function(s) {
					sliderOptions.push({ label: s.title + ' (ID: ' + s.id + ')', value: s.id.toString() });
				});
			}

			// Direct edit slider link if selected
			var editUrl = (attributes.sliderId && sData.edit_url_base)
				? sData.edit_url_base + attributes.sliderId
				: '';

			// Slider selector control for inspector
			var sliderSelector = el(SelectControl, {
				label: 'Choose Slider',
				value: attributes.sliderId,
				options: sliderOptions,
				onChange: function(value) {
					setAttributes({ sliderId: value });
				}
			});

			// Build the editor view
			var editorContent;

			if (attributes.sliderId && ServerSideRender) {
				// Live preview using ServerSideRender inside wrapper ref
				editorContent = el('div', { ref: wrapperRef, style: { pointerEvents: 'none', display: 'flow-root', minHeight: '120px' } },
					el(Disabled, {},
						el(ServerSideRender, {
							block: 'urisp/slider-select',
							attributes: attributes
						})
					)
				);
			} else {
				// Placeholder when no slider is selected
				editorContent = el('div', {
					style: {
						background: 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
						color: '#ffffff',
						padding: '36px 24px',
						borderRadius: '12px',
						textAlign: 'center',
						boxShadow: '0 4px 15px rgba(79, 70, 229, 0.15)',
						fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
					}
				},
					el('span', { className: 'dashicons dashicons-images-alt2', style: { fontSize: '40px', width: '40px', height: '40px', marginBottom: '12px', color: '#c7d2fe', display: 'block', margin: '0 auto 12px auto' } }),
					el('h4', { style: { margin: '0 0 8px 0', fontSize: '18px', fontWeight: '700', color: '#ffffff' } }, 'Ultimate Responsive Image Slider'),
					el('p', { style: { margin: '0 0 18px 0', opacity: 0.9, fontSize: '13px', color: '#e0e7ff' } }, 
						'Select a slider from the list below to preview and embed on this page.'
					),
					el('div', { style: { maxWidth: '340px', margin: '0 auto', textAlign: 'left' } },
						el(SelectControl, {
							value: attributes.sliderId,
							options: sliderOptions,
							onChange: function(value) {
								setAttributes({ sliderId: value });
							}
						})
					),
					sData.create_slider_url ? el('div', { style: { marginTop: '16px' } },
						el('a', {
							href: sData.create_slider_url,
							target: '_blank',
							rel: 'noopener noreferrer',
							style: {
								color: '#ffffff',
								fontSize: '12px',
								textDecoration: 'underline',
								opacity: 0.85
							}
						}, '+ Create a New Slider')
					) : null
				);
			}

			return el('div', blockProps,
				// Block toolbar controls
				el(BlockControls, {},
					el(ToolbarGroup, {},
						editUrl ? el(ToolbarButton, {
							icon: 'edit',
							title: 'Edit Slider Settings (opens in new tab)',
							onClick: function() {
								window.open(editUrl, '_blank');
							}
						}) : null
					)
				),

				// Sidebar Inspector controls
				el(InspectorControls, {},
					el(PanelBody, { title: 'Slider Settings', initialOpen: true },
						sliderSelector,
						editUrl ? el('p', { style: { marginTop: '14px', paddingTop: '10px', borderTop: '1px solid #e2e8f0' } },
							el('a', {
								href: editUrl,
								target: '_blank',
								rel: 'noopener noreferrer',
								style: {
									display: 'inline-flex',
									alignItems: 'center',
									gap: '4px',
									color: '#6366f1',
									fontSize: '12.5px',
									fontWeight: '600',
									textDecoration: 'none'
								}
							},
								el('span', { className: 'dashicons dashicons-external', style: { fontSize: '15px', width: '15px', height: '15px' } }),
								'Edit this Slider in New Tab'
							)
						) : null
					)
				),
				editorContent
			);
		},

		save: function() {
			return null;
		}
	});
})(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);
