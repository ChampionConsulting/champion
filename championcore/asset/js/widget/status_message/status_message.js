"use strict";

(function() {
	
	/**
	 * display a status message as needed
	 */
	function Controller() {
		
		this.dom = {};
	};
	
	/*
	 * initialisation
	 */
	Controller.prototype.init = function() {
		
		var self = this;
		
		this.render();
	};
	
	/*
	 * render the message data
	 */
	Controller.prototype.render = function() {
		
		var self = this;
		
		if ((typeof championcore != 'undefined') && (typeof championcore.status_messages == 'string') && (championcore.alert.active == 1)) {
			
			var decoded = jQuery.parseJSON( championcore.status_messages );
			
			var level = 'info';
			
			if (decoded.length > 0) {
				
				var collected_text = [];
				
				for (var k = 0; k < decoded.length; k++) {
					collected_text[k] = (decoded[k].message + "\n");
					
					if (decoded[k].level != 'info') {
						level = decoded[k].level;
					}
				}
				
				collected_text = collected_text.join( '' );
				
				// adjust icon according to level
				var icon = (championcore.base_url + "/championcore/asset/img/tick_green.jpg");
				
				if (level != 'info') {
					icon = (championcore.base_url + "/championcore/asset/img/cross_red.jpg");
				}
				
				swal(
					{
						button: championcore._('lang_sweetalert_ok'), //the old confirmButtonText
						icon: icon,
						title: championcore._('lang_sweetalert_saved'),
						text: collected_text,
						//type: "info",
						closeOnEsc: true,
						closeOnClickOutside: true,
						timer: championcore.alert.timeout
					}
				);
			}
		}
	};
	
	/**
	 * startup
	 */
	jQuery(document).ready(
		function() {
			var c = new Controller();
			c.init();
		}
	);
		
})();
