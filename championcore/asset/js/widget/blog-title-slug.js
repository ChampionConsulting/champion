"use strict";

/**
 * auto populate the blog title slug from the blog title
 */
function Championcore_Widget_BlogTitleSlug( dom_node, target_node ) {
	
	var self = this;
	
	this.dom  = {};
	this.dom.node   = dom_node;
	this.dom.target = target_node;
	
	this.state = {};
}

/**
 * initialise
 * attach the event handlers
 */
Championcore_Widget_BlogTitleSlug.prototype.initialise = function() {
	
	var self = this;
	
	// wire up the events we will need
	self.dom.node.addEventListener( 'blur', function(evnt){ self.on_blur(evnt); } );
};

/**
 * click handler - blur
 */
Championcore_Widget_BlogTitleSlug.prototype.on_blur = function(evnt) {
	
	var self = this;
	
	var value = this.dom.node.value;
	
	if (value.length > 0) {
		
		var re = new RegExp( '[^a-zA-Z0-9\-\u00C0-\u00FF]', 'gu' );
		
		var last = false;
		
		console.log( last != value );
		
		while (last != value) {
			last  = value;
			value = value.replace( re, '-');
			console.log( value );
		}

		// Deal with issues like "blog - title" becoming "blog---title" (same for blog- title)
		while (value.indexOf('--') !== -1) {
			value = value.replace(/--/g, '-');
		}

		// If last character is a "-" then remove it.
		var lastChar = value.charAt(value.length-1);
		if (lastChar == '-') {
			value = value.substring(0, value.length - 1);
		}

		value = value.toLowerCase();
			
		self.dom.target.value = value;
	}
};
