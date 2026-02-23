"use strict";

/**
 * wire up croppie on the open page
 */
(function() {
		
		/*
		 * constructor
		 */
		function Page() {
			
			this.dom = {};
			
			this.widget = {};
		}
		
		/*
		 * media
		 */
		Page.prototype.media = function() {
			
			var self = this;
			
			this.dom.box = jQuery('.content-wrapper .championcore-croppie');
			
			var src = this.dom.box.data('src');
			
			this.dom.box.croppie(
				{
					url: src,
					
					viewport: {
						width:  this.dom.box.width()/4,
						height: this.dom.box.width()/4
						
					}
				}
			);
			
			// form handler
			jQuery('.content-wrapper .btn-crop').on('click', null, {self: self}, function(evnt){ evnt.data.self.on_click(evnt);} );
			
		};
		
		/*
		 * event handler - form submit - export croppie data
		 */
		Page.prototype.on_click = function(evnt) {
			
			var self = this;
			
			var promise = this.dom.box.croppie(
				'result',
				{
					type: 'canvas',
					size: 'original'
				}
			);
			
			promise.then(
				function (data) {
					
					jQuery('#caption_form input[name="croppie-data"]').val(data);
					
					jQuery('.content-wrapper .crop-thumbnail').css( 'display', 'block' );
					jQuery('.content-wrapper .crop-thumbnail').prop(    'src', data );
				}
			);
			
			return promise;
		};
		
		// wire up
		jQuery(document).ready(
			function() {
				var module = new Page();
				
				window.setTimeout(
					function() {
						module.media();
					},
					1000
				);
			}
		);
		
})();