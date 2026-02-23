
document.addEventListener(
	'DOMContentLoaded',
	function (evnt) {

		// keep track of state
		const state = {
			bb: false,

			show: false,

			timer: 0
		};

		window.state = state;

		// slider effect
		function effect_after_show () {

			console.log( state );

			state.show = true;

			state.timer = window.setInterval(
				() => {
					if (state.show && state.bb) {
						const flag = baguetteBox.showNext();

						if (!flag) {
							baguetteBox.show( 0 );
						}
					}
				},
				3000
			);
		}

		// slider effect 
		function effect_after_hide () {

			state.show = false;

			window.clearInterval( state.timer );
		}
		
		// start up
		state.bb = baguetteBox.run(
			'.gallery',
			{
				animation: 'slideIn',
				captions: true,

				afterShow: effect_after_show,
				afterHide: effect_after_hide
			}
		);
	}
);
