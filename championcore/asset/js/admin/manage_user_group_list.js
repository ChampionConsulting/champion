"use strict";

/**
 * manage user group list
 */
(function() {
		
		/**
		 * constructor
		 */
		function Page() {
			
			this.dom = {};
		}
		
		/**
		 * initialise
		 */
		Page.prototype.init = function () {
			
			const self = this;
			
			// wire up the dom
			this.dom.li = document.querySelectorAll('.championcore.manage_user_group_list ul.user_group_list > li > span');
			
			// wire up the event handler
			this.dom.li.forEach(
				function (node) {
					node.addEventListener('click', function (evnt) { self.render_toggle_form(evnt); } );
				}
			);
		};
		
		/**
		 * show/hide the form
		 */
		Page.prototype.render_toggle_form = function (evnt) {
			
			const self = this;
			
			const element = evnt.currentTarget;
			
			element.parentNode.querySelector('form').classList.toggle('show');
		};
		
		// wire up
		document.addEventListener(
			'DOMContentLoaded',
			function(evnt) {
				const module = new Page();
				
				module.init();
			}
		);
		
})();
