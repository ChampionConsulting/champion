"use strict";

/**
 * wire up date picker/etc on the open page
 */
(function() {
		
		/*
		 * constructor
		 */
		function Page() {
			
			this.widget = {};
		}
		
		/*
		 * blog
		 */
		Page.prototype.blog = function () {
			
			const node = jQuery( 'form input.blog-date-input' );
			
			// console.log( node );
			
			if (node.length > 0) {
				
				const fmt = node.data('format');
				
				// console.log( fmt );
				
				jQuery( 'form input.blog-date-input' ).pikaday(
					{
						format:  fmt//,
						//defaultDate: moment().toDate(),
						//setDefaultDate: true
					}
				);
				
				// tags
				this.widget.tag = new Championcore_Widget_ListTag( jQuery('input[name="blog_tags"]') );
				this.widget.tag.initialise();
				
				// blog title slug
				this.widget.blog_title_slug = new Championcore_Widget_BlogTitleSlug(
					document.getElementsByName('head1'   ).item(0),
					document.getElementsByName('blog_url').item(0)
				);
				this.widget.blog_title_slug.initialise();
			}
		};
		
		/**
		 * wire up CTRL+s for saving
		 */
		Page.prototype.keyboard_shortcuts = function () {
			
			document.querySelector('form#textfile').addEventListener(
				'keydown',
				function (evnt) {
					
					// console.log( evnt.CtrlKey, evnt.code );
					
					if ((evnt.ctrlKey && evnt.code == 'KeyS') || (evnt.metaKey && evnt.code == 'KeyM')) {
						
						evnt.preventDefault();
						evnt.stopPropagation();
						evnt.stopImmediatePropagation();
						
						//evnt.currentTarget.submit();
						
						document.querySelector("form#textfile#textfile button[name=savetext]").click();
					}
				}
			);
		};
		
		// wire up
		jQuery(document).ready(
			function() {
				const module = new Page();
				
				module.blog();
				
				module.keyboard_shortcuts();
			}
		);
		
})();