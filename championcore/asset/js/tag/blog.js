"use strict";

(function(){

	/**
	 * the blog widget
	 */
	function Championcore_Tag_Blog() {
		
		var self = this;
	}
	
	/*
	 * initialise
	 */
	Championcore_Tag_Blog.prototype.initialise = function() {
		
		var self = this;
		
		/*
		@logic We add a Masonry style CSS class by default to the blog here, pseudo-masonry.
		There was a design decision in the past to add this by default.
		*/
		jQuery('.grid.flexbox.tag-blog-content-loop.grid .grid-item').addClass( 'pseudo-masonry' );
		
		/*
		// masonry - per item
		jQuery('.grid.tag-blog-content-loop').masonry(
			{
				// options
				itemSelector: '.grid-item',
				columnWidth: 400
			}
		);
		
		// masonry - INSIDE item
		jQuery('.blog-item-grid').masonry(
			{
				// options
				itemSelector: '.blog-item-grid-item',
				columnWidth: 400
			}
		);
		*/
	};
	
	/**
	 * start up
	 */
	document.addEventListener(
		'DOMContentLoaded',
		function(evnt) {
			
			var tag = new Championcore_Tag_Blog();
			tag.initialise();
		}
	);

})();
