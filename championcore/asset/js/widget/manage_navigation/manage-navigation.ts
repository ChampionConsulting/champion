
/**
 * imports
 */

import {createApp }     from 'vue';
import draggable from 'vuedraggable';
//import Sortable  from 'sortablejs';

import message_bus, {StorageChangeEvent, TrackUiMenuChangeEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';
import track_ui    from 'ChampionCore/widget/manage_navigation/track_ui';

import ComponentAddMenu          from 'ChampionCore/widget/manage_navigation/component/add-menu';
import ComponentExpander         from 'ChampionCore/widget/manage_navigation/component/expander';
import ComponentManageNavigation from 'ChampionCore/widget/manage_navigation/component/manage-navigation';
import ComponentMenuItem         from 'ChampionCore/widget/manage_navigation/component/menu-item';
import ComponentMenuList         from 'ChampionCore/widget/manage_navigation/component/menu-list';
import ComponentNonChampionButton   from 'ChampionCore/widget/manage_navigation/component/non-champion-button';
import ComponentSaveButton       from 'ChampionCore/widget/manage_navigation/component/save-button';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * vue app
 */
const app = createApp(
	{
		/**
		 * components used in this widget/component
		 */
		components: {
			'draggable': draggable,

			'manage-navigation': ComponentManageNavigation
		},
		
		/**
		 * state
		 */
		data () {

			return {
				menu: storage.get_root(),
				
				open_menus: ([] as Array<Menu>)
			};
		},
		
		/**
		 * behaviour
		 */
		methods: {
		},
		
		/**
		 * life cycle hook
		 */
		mounted: function () {
			
			const self = this;

			// always have the "all" menu open
			const all_menu = storage.get_root().find_by_name( 'all' );

			this.open_menus.push( all_menu );

			// always have the "inactive" menu open
			const pending_menu = storage.get_root().find_by_name( 'pending' );

			this.open_menus.push( pending_menu );
			
			/*
			 * custom event - storage change
			 */
			message_bus.on(
				'storage-change',
				function (root: StorageChangeEvent) {

					console.log( 'storage change event', root.menu );
					
					self.menu = new Menu('dummy', '', false, false, []);
					
					self.$nextTick(
						() => self.menu = root.menu
					);
				}
			);
			
			/*
			 * custom event - open menu list change
			 */
			message_bus.on(
				'track-ui-menu-change',
				function (evnt: TrackUiMenuChangeEvent) {
					
					self.open_menus = [];
					
					self.$nextTick(
						function () {
							self.open_menus = evnt.open_menu_list;
						}
					);
				}
			);

			/**
			 * wire up track-ui events
			 */
			track_ui.event.attach();
		}
	}
);

/**
 * add components
 */
app.component( 'add-menu',         ComponentAddMenu );
app.component( 'expander',         ComponentExpander );
app.component( 'menu-item',        ComponentMenuItem );
app.component( 'menu-list',        ComponentMenuList );
app.component( 'non-champion-button', ComponentNonChampionButton );
app.component( 'save-button',      ComponentSaveButton );

/**
 * mount
 */
app.mount( '.championcore.manage-navigation' );

/**
 * exports
 */
export default app;


window.championcore.manage_navigation.app = app;

