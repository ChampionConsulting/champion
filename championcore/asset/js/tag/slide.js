"use strict";

/**
 * wire up slide for the tag
 */
document.addEventListener(
	'DOMContentLoaded',
	function(evnt) {
		jQuery('.flexslider').flexslider(
			{
				animation: "slide",
				smoothHeight: false,
				directionNav: false,
				controlNav: true,
				keyboard: true,
				slideshowSpeed: 5000,
				animationSpeed: 600
			}
		);
		
		
		baguetteBox.run( ".championcore.tag.flexslider", {captions: true} );

	}
);
