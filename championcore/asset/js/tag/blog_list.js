"use strict";

(function(){

	/**
	 * the list of tags widget
	 */
	function Championcore_Tag_BlogList() {
		
		var self = this;
	}
	
	/*
	 * initialise
	 */
	Championcore_Tag_BlogList.prototype.initialise = function() {
		
		var self = this;
	};
	
	/**
	 * start up
	 */
	document.addEventListener(
		'DOMContentLoaded',
		function(evnt) {
			
			var tag = new Championcore_Tag_BlogList();
			tag.initialise();
		}
	);

})();
