"use strict";

(function() {
	
	function Controller() {
		
		this.dom = {};
	};
	
	/*
	 * initialisation
	 */
	Controller.prototype.init = function() {
		
		var self = this;
		
		this.dom.input        = jQuery( '.create-form input[name="newname"]' );
		
		this.dom.radio_folder = jQuery( '.create-form #page_create_page_type_folder' );
		this.dom.radio_page   = jQuery( '.create-form #page_create_page_type_page'   );
		
		this.dom.input.on( 'blur', null, {'self': self}, function (evnt) {return evnt.data.self.onblur_input(  evnt);} );
		
		this.dom.radio_folder.on( 'click', null, {'self': self}, function (evnt) {return evnt.data.self.onclick_radio_folder(evnt);} );
		this.dom.radio_page.on(   'click', null, {'self': self}, function (evnt) {return evnt.data.self.onclick_radio_page(  evnt);} );
	};
	
	/*
	 * click event handler
	 */
	Controller.prototype.onclick_radio_folder = function(evnt) {
		
		var self = this;
		
		var name = this.dom.input.val();
		
		name = name.replace( '.txt', '' );
		
		this.dom.input.val( name );
	};
	
	/*
	 * click event handler
	 */
	Controller.prototype.onclick_radio_page = function(evnt) {
		
		var self = this;
		
		var name = this.dom.input.val();
		
		name = name.replace( '.txt', '' );
		
		this.dom.input.val( name + '.txt' );
	};
	
	/*
	 * blur event handler
	 */
	Controller.prototype.onblur_input = function(evnt) {
		
		var self = this;
		
		var is_folder = this.dom.radio_folder.prop('checked');
		var is_page   = this.dom.radio_page.prop(  'checked');
		
		var name = this.dom.input.val();
		
		name = name.replace( '.txt', '' );
		name = name.replace( ' ', '-' );

		if (is_folder) {
			this.dom.input.val( name );
		}
		
		if (is_page) {	
			this.dom.input.val( name + '.txt' );
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
