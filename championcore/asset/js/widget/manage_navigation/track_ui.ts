/**
 * imports
 */
import message_bus, {MeMi, TrackUiMenuChangeEvent, TrackUiMenuCloseEvent, TrackUiMenuOpenEvent} from 'ChampionCore/widget/manage_navigation/message-bus';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * data storage
 */
const track_ui = {
	
	/**
	 * state storage
	 */
	open_menu_list: ([] as Array<Menu>),
	
	/**
	 * event handlers
	 */
	event: {
		/**
		 * attach all the events we will use
		 */
		attach: function () {
			
			message_bus.on( 'track-ui-menu-open',  (evnt: TrackUiMenuOpenEvent) => this.menu_open(evnt.menu) );
			
			message_bus.on( 'track-ui-menu-close',  (evnt: TrackUiMenuCloseEvent) => this.menu_close(evnt.menu) );
		},
		
		/**
		 * handle event
		 */
		menu_open: function (menu: MeMi) {

			if (menu instanceof Menu) {
				
				if (track_ui.open_menu_list.indexOf(menu) == -1) {
					
					track_ui.open_menu_list.push( menu );
				}
			
				message_bus.emit( 'track-ui-menu-change', new TrackUiMenuChangeEvent(track_ui.open_menu_list) );
			}
		},
		
		/**
		 * handle event
		 */
		menu_close: function (menu: MeMi) {
			
			if (menu instanceof Menu) {

				const position = track_ui.open_menu_list.indexOf(menu);
				
				track_ui.open_menu_list = 
					([] as Array<Menu>).concat(
						track_ui.open_menu_list.slice( 0, position ),
						track_ui.open_menu_list.slice( position + 1)
					);
				
				message_bus.emit( 'track-ui-menu-change', new TrackUiMenuChangeEvent(track_ui.open_menu_list) );
			}
		}
	}
};

/**
 * INITIALISE
 */

// wire on events
//track_ui.event.attach();

export default track_ui;
