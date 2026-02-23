"use strict";

(
function () {
	
	/**
	 * dom nodes
	 */
	const dom = {
		button: {}
	};
	
	/**
	 * state management
	 */
	const state = {
	};
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function onclick_select_all (evnt) {
		
		evnt.stopPropagation();
		evnt.stopImmediatePropagation();
		evnt.preventDefault();
		
		dom.checkboxes.forEach(
			(element) => { console.log(element); element.checked = true; }
		);
	}
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function onclick_select_none (evnt) {
		
		evnt.stopPropagation();
		evnt.stopImmediatePropagation();
		evnt.preventDefault();
		
		dom.checkboxes.forEach(
			(element) => { console.log(element); element.checked = false; }
		);
	}
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function on_load (evnt) {
		
		console.log( 'on_load' );
		
		// DOM elements
		dom.button.select_all  = document.querySelector( '.championcore form .select_all' );
		dom.button.select_none = document.querySelector( '.championcore form .select_none' );
		
		dom.checkboxes = document.querySelectorAll( '.championcore form input[type="checkbox"]' );
		
		// event handlers
		dom.button.select_all .addEventListener( 'click', (evnt) => onclick_select_all(evnt) );
		dom.button.select_none.addEventListener( 'click', (evnt) => onclick_select_none(evnt) );
	}
	
	// wire in event handler
	window.addEventListener( 'load', on_load );
}
)();
