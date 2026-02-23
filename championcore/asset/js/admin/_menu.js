"use strict";

(function() {
	
	/**
	 * display message if update not available
	 */
	function Controller() {
		
		this.dom = {};
	};
	
	/*
	 * initialisation
	 */
	Controller.prototype.init = function() {
		
		const self = this;
		
		// update link
		this.dom.link_update = document.querySelector( '.cd-nav > .cd-top-nav .update_start' );
		
		if (this.dom.link_update) {
			this.dom.link_update.addEventListener(
				'click',
				(evnt) => self.on_click_link(evnt)
			);
		}
		
		// user link
		this.dom.link_user = document.querySelector( '.cd-main-content > aside > .cd-side-nav .manage_users' );
		
		if (this.dom.link_user) {
			this.dom.link_user.addEventListener(
				'click',
				(evnt) => self.on_click_link(evnt)
			);
		}
		
		this.render();
	};
	
	/**
	 * click handler
	 */
	Controller.prototype.on_click_link = function (evnt) {
		
		const probe = evnt.currentTarget.dataset.alert;
		
		if (probe === '1') {
			
			console.log( 'boo' );
			
			evnt.preventDefault();
			evnt.stopPropagation();
			evnt.stopImmediatePropagation();
			
			const icon = (championcore.base_url + "/championcore/asset/img/cross_red.jpg");
			
			swal(
				{
					button: championcore._('lang_sweetalert_ok'), //the old confirmButtonText
					icon: icon,
					title: championcore._('lang_not_supported_in_this_version'),
					text:  championcore._('lang_not_supported_in_this_version'),
					//type: "info",
					closeOnEsc: true,
					closeOnClickOutside: true,
					timer: championcore.alert.timeout
				}
			);
		}
	};
	
	/*
	 * render the message data
	 */
	Controller.prototype.render = function() {
		
		const self = this;
		
		
	};
	
	/**
	 * startup
	 */
	document.addEventListener(
		'DOMContentLoaded',
		function() {
			const c = new Controller();
			c.init();
		}
	);
	
})();
