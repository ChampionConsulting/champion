"use strict";

/**
 * the list of tags widget
 */
function Championcore_Widget_ListTag( dom_node ) {
	
	var self = this;
	
	this.dom  = {};
	this.dom.node = dom_node;
	
	this.state = {};
	this.state.tags = [];
}

/*
 * initialise
 * hide the dom node and insert the widget
 */
Championcore_Widget_ListTag.prototype.initialise = function() {
	
	var self = this;
	
	// hide
	this.dom.node.hide();
	
	// create the widget
	var widget = jQuery( '<div class="championcore widget list_tag"><input type="text" name="add" value="" /> <div class="add"><i class="fa fa-plus-square"></i></div><ul class="clear-fix"></ul></div>' );
	
	this.dom.node.after( widget );
	
	// dom nodes
	this.dom.input = jQuery( 'input', widget );
	this.dom.list  = jQuery( 'ul',    widget );
	
	// set the initial state
	this.set_state( this.dom.node.val() );
	
	this.render();
	
	// wire up the events we will need
	jQuery( '.add',  widget ).on( 'click', null, {'self': self}, function(evnt){ evnt.data.self.on_click_add(evnt); } );
	jQuery( 'input', widget ).on( 'keyup', null, {'self': self}, function(evnt){ evnt.data.self.on_keyup_add(evnt); } );
	
	jQuery( 'ul', widget ).on( 'click', ' li .delete_list_tag', {'self': self}, function(evnt){ evnt.data.self.on_click_delete(evnt); } );
};

/*
 * click handler - add button
 */
Championcore_Widget_ListTag.prototype.on_click_add = function(evnt) {
	
	var self = this;
	
	var tag = this.dom.input.val();
	
	if (tag.length > 0) {
	
		this.set_state( this.dom.node.val() + ', '  + tag );
		this.render();
	}
};

/*
 * click handler - delete button
 */
Championcore_Widget_ListTag.prototype.on_click_delete = function(evnt) {
	
	var self = this;
	
	var li_delete = jQuery(evnt.target);
	
	var tag = li_delete.data('item');
	
	var index = this.state.tags.indexOf( tag );
	
	var left  = this.state.tags.slice( 0, index );
	var right = this.state.tags.slice(    index + 1);
	
	this.state.tags = left.concat( right );
	
	this.render();
};

/*
 * click handler - key up in the tag input field
 */
Championcore_Widget_ListTag.prototype.on_keyup_add = function(evnt) {
	
	var self = this;
	
	var key = evnt.which;
	
	if (key == 13) { //enter key pressed
		this.on_click_add( evnt );
	}
};

/*
 * render the new state
 */
Championcore_Widget_ListTag.prototype.render = function() {
	
	var self = this;
	
	// update the node
	var tmp = this.state.tags.join( ', ' );
	
	this.dom.node.val( tmp );
	
	// update the tag list
	this.dom.list.html('');
	
	for (var k = 0; k < this.state.tags.length; k++) {
		
		var tag = this.state.tags[k];
		
		var html = '<li>' + tag + ' <div class="delete_list_tag" data-item="' + tag + '">x</div></li>';
		
		var item = jQuery( html );
		
		this.dom.list.append( item );
	}
};

/*
 * set the state from a string
 */
Championcore_Widget_ListTag.prototype.set_state = function( arg ) {
	
	var self = this;
	
	var tags = arg.split(',');
	
	// de-duplicate and clean up white space
	var unique = [];
	for (var k = 0; k < tags.length; k++) {
		
		var tmp = tags[k].trim();
		
		if ((tmp.length > 0) && (unique.indexOf(tmp) == -1)) {
			unique.push( tmp );
		}
	}
	
	// sort into order (inplace)
	unique.sort();
	
	// update state
	this.state.tags = unique;
};
