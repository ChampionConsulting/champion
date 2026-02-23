"use strict";

(
function () {
	
	/**
	 * dom nodes
	 */
	const dom = {
		radio: {}
	};
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function on_click_radio (evnt) {
		
		const is_upload = dom.radio.upload.checked;
		const is_ftp    = dom.radio.ftp   .checked;
		
		if (is_upload) {
			
			dom.from_upload.style.display = 'block';
			dom.from_ftp   .style.display = 'none';
		}
		
		if (is_ftp) {
			
			dom.from_upload.style.display = 'none';
			dom.from_ftp   .style.display = 'block';
		}
	}
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function on_load (evnt) {
		
		// DOM elements
		dom.from_upload = document.querySelector('.from_upload');
		dom.from_ftp    = document.querySelector('.from_ftp');
		
		dom.radio.upload = document.querySelector('input[name="from_type"][value="upload"]');
		dom.radio.ftp    = document.querySelector('input[name="from_type"][value="ftp"]');
		
		// wire in click handle
		dom.radio.upload.addEventListener( 'click', on_click_radio );
		dom.radio.ftp   .addEventListener( 'click', on_click_radio );
	}
	
	// wire in event handler
	window.addEventListener( 'load', on_load );
}
)();
