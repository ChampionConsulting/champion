"use strict";

/**
 * wire up JS inline translations
 */
window.championcore._ = function( arg ) {
	
	var result = arg;
	
	if (window.championcore.translations[arg]) {
		
		result = window.championcore.translations[arg];
	}
	
	return result;
};
