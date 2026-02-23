"use strict";

/**
 * forward blog creation onwards
 */
(function() {
		
		/*
		 * constructor
		 */
		function Page() {
		}
		
		/*
		 * blog
		 */
		Page.prototype.blog = function() {
			
			var type_type = jQuery('form.create-form input[name="savepath"]').val();
			
			if (type_type.indexOf('blog') >= 0) {
				type_type = 'blog';
			}
			
			
			
			if (type_type == 'blog') {
				// jQuery('form.create-form button').click();
			}
		};
		
		// wire up
		jQuery(document).ready(
			function() {
				var module = new Page();
				
				module.blog();
			}
		);
		
})();