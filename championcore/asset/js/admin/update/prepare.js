"use strict";

(
function () {
	
	/**
	 * dom nodes
	 */
	const dom = {
	};
	
	/**
	 * state management
	 */
	const state = {
	};
	
	/**
	 * periodically poll server and update status line
	 * @return void
	 */
	function tick_tock () {
		
		window.fetch(
			championcore.admin_url + '/index.php?p=update_status'
		).then (
			(response) => {
				return response.json();
			}
		).then(
			(data) => {
				
				if (data && data.prepare && data.prepare.status_line) {
					dom.status_line.innerHTML = data.prepare.status_line;
				}
				
				// stop polling when done
				if (data && data.prepare && data.prepare.done) {
					window.clearInterval( state.timer );
					state.timer = false;
				}
			}
		)
	}
	
	/**
	 * do a form PUT in the background - so processing can happen
	 * @return void
	 */
	function trigger_put () {
		
		window.fetch(
			championcore.admin_url + '/index.php?p=update_prepare',
			{
				 method: 'PUT'
			}
		).then (
			(response) => {
				return response.json();
			}
		).then(
			(data) => {
				
				console.log( 'trigger_put', data );
				
				// move along when done - add a small delay so user can cancel
				window.setTimeout(
					function () {
						window.location.href = championcore.admin_url + '/index.php?p=update_results';
					},
					10000
				);
			}
		)
	}
	
	/**
	 * event handler
	 * @param Event evnt
	 * @return void
	 */
	function on_load (evnt) {
		
		console.log( 'on_load' );
		
		// DOM elements
		dom.status_line = document.querySelector('.championcore form .spinner p');
		
		// start polling
		state.timer = window.setInterval(
			tick_tock,
			1000
		);
		
		// start processing
		trigger_put();
	}
	
	// wire in event handler
	window.addEventListener( 'load', on_load );
}
)();
