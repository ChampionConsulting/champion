"use strict";

jQuery(document).ready(
	function() {
		jQuery(".bar-container").css("height","0%").animate({height:"165px"},800);
	}
);

jQuery(document).ready(
	function() {
		jQuery('#content').masonry(
			{
				// options
				itemSelector: '.stats-group',
				gutter: 0
			}
		);
	}
);
