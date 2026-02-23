/**
 * imports
 */
import message_bus, {MeMi, DnDChangeEvent, MenuAddEvent, MenuActivateToggleEvent, MenuAddNonChampionEvent, MenuDeleteEvent, MenuDeleteToggleEvent, MenuItemActivateToggleEvent, MenuItemDeleteEvent, MenuItemDeleteToggleEvent, MenuItemMoveDownEvent, MenuItemMoveUpEvent, MenuItemNameChangedEvent, StateSaveEvent, StorageChangeEvent} from 'ChampionCore/widget/manage_navigation/message-bus';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * result of the storage find
 */
class FindResult {
	public found: boolean     = false;
	public path:  Array<MeMi> = []
};

/**
 * data storage
 */
const storage = {
	
	/**
	 * state storage
	 */
	root: new Menu('', '', false, false, []), // dummy value
	
	/**
	 * event handlers
	 */
	event: {
		/**
		 * attach all the events we will use
		 */
		attach: function () {
			
			message_bus.on( 'dnd-change', this.dnd_change);
			
			message_bus.on( 'menu-add',  this.menu_add );
			
			message_bus.on( 'menu-add-non-champion',  this.menu_add_non_champion );
			
			message_bus.on( 'menu-delete', this.menu_delete );
			
			message_bus.on( 'menu-activate-toggle', this.menu_activate_toggle);
			
			message_bus.on( 'menu-delete-toggle', this.menu_delete_toggle );
			
			message_bus.on( 'menu-item-delete', this.menu_item_delete);
			
			message_bus.on( 'menu-item-activate-toggle', this.menu_item_activate_toggle);

			message_bus.on( 'menu-item-delete-toggle', this.menu_item_delete_toggle);

			message_bus.on( 'menu-item-move-down', this.menu_item_move_down );
			message_bus.on( 'menu-item-move-up',   this.menu_item_move_up   );

			message_bus.on( 'menu-item-name-changed', this.menu_item_name_changed );
			
			message_bus.on( 'state-save', this.save_state);
		},
		
		/**
		 * handle event
		 */
		dnd_change: function(evnt: DnDChangeEvent) {
			
			const path = [];
			
			const probe = storage.find( storage.get_root(), evnt.item );

			console.log( 'dnd_change', probe );
			
			for (let k = 0; k < probe.path.length; k++) {
				
				const tmp = probe.path[k];
				
				if (tmp.name != 'ROOT') {
					path.push( tmp.name );
				}
			}
			
			//console.log( path );
			
			storage.set_sub_menus(
				path,
				evnt.item
			);
			
			//console.log( storage.get_root().pretty_print() );
			
			message_bus.emit( 'storage-change', new StorageChangeEvent(storage.get_root()) );
		},
		
		/**
		 * handle event
		 */
		menu_add: function (evnt: MenuAddEvent) { //parent, name) {
			
			const path = [];
			
			//console.log( parent, name );
			
			const probe = storage.find( storage.get_root(), evnt.parent );
			
			for (let k = 0; k < probe.path.length; k++) {
				
				if (probe.path[k].name != 'ROOT') {
					path.push( probe.path[k].name );
				}
			}
			
			path.push( evnt.menu_item_new );
			
			//console.log( path );
			
			
			/*
			// corner case - root menu
			if (parent == false) {
				
				path.unshift( 'ROOT' );
			}
			*/
			storage.set_sub_menus(
				path,
				new Menu( evnt.menu_item_new, '', true, false, [] )
			);
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_add_non_champion: function(evnt: MenuAddNonChampionEvent) { // parent, name: string, url: string) {
			
			const path = [];
			
			//console.log( parent, name );
			
			const probe = storage.find( storage.get_root(), evnt.parent );
			
			for (let k = 0; k < probe.path.length; k++) {
				
				if (probe.path[k].name != 'ROOT') {
					path.push( probe.path[k].name );
				}
			}
			
			path.push( evnt.name );
			
			//console.log( path );
			
			
			/*
			// corner case - root menu
			if (parent == false) {
				
				path.unshift( 'ROOT' );
			}
			*/
			storage.set_sub_menus(
				path,
				new MenuItem( evnt.name, evnt.url, true, false, evnt.open_in_new_tab)
			);
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_delete: function (evnt: MenuDeleteEvent) { //item) {
			
			storage.remove( storage.get_root(), evnt.item );
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_activate_toggle: function (evnt: MenuActivateToggleEvent) { // item) {
			
			const probe = storage.find( storage.get_root(), evnt.item );
			
			if (probe.path.length > 0) {
				const a = probe.path.pop();
				
				if (a) {
					a.set_active( !a.active );
				}
			}
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_delete_toggle: function (evnt: MenuDeleteToggleEvent) { // item) {
			
			//console.log( 'menu_delete_toggle item.name', evnt.item.name );
			
			const probe = storage.find( storage.get_root(), evnt.item );
			
			//console.log( 'menu_delete_toggle probe', probe, evnt.item.name );
			
			if (probe.path.length > 0) {
				const a = probe.path.pop();
				
				if (a) {
					a.set_nuked( !a.nuke );
				}
			}
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_item_delete: function (evnt: MenuItemDeleteEvent) { // parent, item) {
			
			storage.remove( storage.get_root(), evnt.item );
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_item_activate_toggle: function (evnt: MenuItemActivateToggleEvent) { // parent, item) {
			
			const probe = storage.find( storage.get_root(), evnt.item );
			
			if (probe.path.length > 0) {
				const a = probe.path.pop();
				
				if (a) {
					a.set_active( !a.active );
				}
			}
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		menu_item_delete_toggle: function (evnt: MenuItemDeleteToggleEvent) {
			
			//const probe = storage.find( storage.get_root(), evnt.item );

			const probe = storage.find( evnt.menu, evnt.item );

			//console.log( 'menu_item_delete_toggle probe', probe );
			
			if (probe.path.length > 0) {
				const a = probe.path.pop();
				
				if (a) {
					a.set_nuked( !a.nuke );
				}
			}
			
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},

		/**
		 * handle event
		 */
		menu_item_move_down: function (evnt: MenuItemMoveDownEvent) {

			console.log( 'menu_item_move_down', evnt.item );

			const probe = storage.find( storage.get_root(), evnt.item );

			console.log( 'probe', probe );

			if (probe.path.length >= 3) {

				const item = probe.path.pop() as MenuItem;
				const menu = probe.path.pop() as Menu;

				if (menu && item) {

					let working : Array<MeMi> = [];

					for (let k = 0; k < menu.menu_items.length; k++) {

						if (menu.menu_items[k].name == item.name) {

							if (k > 0) {
								working = working.concat( menu.menu_items.slice( 0, k) );
							}

							if ((k+1) < menu.menu_items.length) {
								working.push( menu.menu_items[ k + 1] );
							}

							working.push( menu.menu_items[ k ] );

							if ((k+2) < menu.menu_items.length) {
								working = working.concat( menu.menu_items.slice( k + 2) );
							}

							break;
						}
					}

					console.log( 'menu_item_move_down working', working );

					menu.menu_items = working;
				}
			}

			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},

		/**
		 * handle event
		 */
		menu_item_move_up: function (evnt: MenuItemMoveUpEvent) {

			console.log( 'menu_item_move_up', evnt.item );

			const probe = storage.find( storage.get_root(), evnt.item );

			console.log( 'probe', probe );

			if (probe.path.length >= 3) {

				const item = probe.path.pop() as MenuItem;
				const menu = probe.path.pop() as Menu;
				
				if (menu && item) {

					let working : Array<MeMi> = [];

					for (let k = 0; k < menu.menu_items.length; k++) {

						if (menu.menu_items[k].name == item.name) {

							if ((k-1) > 0) {
								working = working.concat( menu.menu_items.slice( 0, k - 1) );
							}

							working.push( menu.menu_items[ k] );

							if ((k-1) >= 0) {
								working.push( menu.menu_items[ k - 1] );
							}

							if ((k+1) < menu.menu_items.length) {
								working = working.concat( menu.menu_items.slice( k + 1) );
							}

							break;
						}
					}

					console.log( 'menu_item_move_up working', working );

					menu.menu_items = working;
				}
			}

			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},

		/**
		 * handle event
		 */
		menu_item_name_changed: function (evnt: MenuItemNameChangedEvent) : void {

			console.log( 'menu-item-name-change', evnt );

			// done
			message_bus.emit( 'storage-change', new StorageChangeEvent( storage.get_root() ) );
		},
		
		/**
		 * handle event
		 */
		save_state: function (evnt: StateSaveEvent) {
			
			storage.save_state();
		}
	},
	
	/**
	 * find a menu/menu-item
	 */
	find: function (menu: Menu, thingy: MeMi) : FindResult {
		
		const result = new FindResult();

		if (menu.name == thingy.name) {
			
			result.found = true;
			result.path.push( menu );
			result.path.push( thingy );
			
		} else {
			
			if (menu instanceof Menu) {
				
				for (let p = 0; p < menu.menu_items.length; p++) {
					
					const item = menu.menu_items[p];

					if (item instanceof Menu) {
						// Menu
						const probe = this.find( item, thingy);
						
						if (probe.found == true) {

							console.log( 'MENU - find interim probe', probe, 'item', item, 'thingy', thingy );
							
							result.found = true;
							result.path  = ([menu] as Array<MeMi>).concat( probe.path );
							
							break;
						}

					} else if (item instanceof MenuItem) {
						// MenuItem
						if (item.name == thingy.name) {

							console.log( 'MENU-ITEM - find item', item );
			
							result.found = true;
							result.path.push( menu );
							result.path.push( item );
							
							break;
						}
					}
				}
			}
		}
		
		return result;
	},
	
	/**
	 * get full menu
	 */
	get_root: function () : Menu {
		
		const root = this.root;
		
		// ensure that top level "all" name is present
		let detected = root.has_menu('all');
		
		if (detected == false) {
			
			root.add(
				new Menu( 'all', 'all', true, false, [] )
			);
		}
		
		// ensure that top level "pending" name is present
		detected = root.has_menu('pending');
		
		if (detected == false) {
			
			root.add(
				new Menu( 'pending', 'pending', true, false, [] )
			);
		}

		//console.log( 'root', root );
		
		return root;
	},
	
	/**
	 * import the server state and massage into something the JS can use
	 * @param string input_state
	 * @return array
	 */
	import_server_state: function (input_state: any) {
		
		const result = [];
		
		for (let k in input_state) {
			
			const item = input_state[k];
			
			// menu
			if ((typeof item.active == 'undefined') && (typeof item.url == 'undefined')) {
				
				const content = this.import_server_state( item );
				
				const tmp : Menu = new Menu( k, '', true, false, content );
				
				result.push( tmp );
			}
			
			// menu item
			if ((typeof item.active == 'boolean') && (typeof item.url == 'string')) {
				
				const tmp = new MenuItem(
					k,
					item.url,
					item.active,
					false,
					(item.open_in_new_tab ? item.open_in_new_tab : '0')
				);
				
				result.push( tmp );
			}
		}
		
		return result;
	},
	
	/**
	 * merge menu items into one
	 */
	merge_array: function (menu_items: Array<MeMi>, name: string) : Menu {
		
		const result = new Menu( name, '', true, false, [] );
		
		for (let k = 0; k < menu_items.length; k++) {
			
			const item = menu_items[k];
			
			result.menu_items.push( item );
		}
		
		return result;
	},
	
	/**
	 * remove an item from menu
	 * @param object thingy the object to search for
	 * @return void
	 */
	remove: function (menu: Menu, thingy: MeMi) {
		
		// safety - cant delete all menu
		if (thingy.name == 'all') {
			return;
		}
		
		// safety - cant delete pending menu
		if (thingy.name == 'pending') {
			return;
		}

		console.log( 'storage -> remove', menu, thingy );
		
		// process delete
		const probe = storage.find( menu, thingy );
		
		const removed_list = [];
		
		if (probe.path.length > 1) {
			
			const a = probe.path.pop();
			const b = probe.path.pop();

			if (a && b) {
				
				//console.log( 'a', a.pretty_print() );
				//console.log( 'b', b.pretty_print() );
				
				const tmp = [];

				if (b instanceof Menu) {
					
					for (let k = 0; k < b.menu_items.length; k++) {
						
						const item = b.menu_items[k];
						
						if (item != a) {
							tmp.push( item );
						} else {
							removed_list.push( item );
						}
					}
					
					b.menu_items = tmp;
				}
			}
		}
		
		//console.log( menu.pretty_print() );
		
		// next step - moved deleted items onto the pending list
		const pending_menu = this.get_root().find_by_name('pending');
		
		//console.log( 'pending_menu', pending_menu );

		if (pending_menu instanceof Menu) {
			
			while (removed_list.length > 0) {
				
				const qqq = removed_list.pop();
				
				if (qqq instanceof MenuItem) {
					pending_menu.add( qqq );
				}
				
				if (qqq instanceof Menu) {
					
					for (let k = 0; k < qqq.menu_items.length; k++) {
						pending_menu.add( qqq.menu_items[k] );
					}
				}
			}
		}
	},
	
	/**
	 * transmit the form
	 */
	save_state: function () {
		
		console.log( this.root.pretty_print() );
		
		// consolidate
		const data = {
			active:          this.root.build_active_list(),
			change_list:     this.root.build_change_list(),
			nav_list:        this.root.build_nav_list(),
			nuke:            this.root.build_nuke_list(),
			open_in_new_tab: this.root.build_open_in_new_tab_list(),
			
			csrf_token: window.championcore.manage_navigation.csrf_token
		};
		
		// now post
		const deferred = jQuery.post(
			window.championcore.admin_url + '/index.php?p=manage_navigation&method=put',
			data
		);
		
		deferred.done(
			function () {
				window.location.href = (window.championcore.admin_url + '/index.php?p=manage_navigation');
			}
		);
	},
	
	/**
	 * set sub menus for menu
	 * @param array menu For a single menu use ['a']  for deep menus use ['a', 'b', 'c']
	 * @param object arg the new sub menu
	 * @return void
	 */
	set_sub_menus: function (menu: Array<string>, arg: MeMi) {
		
		let root = this.root;
		
		for (let k = 0; k < menu.length; k++) {
			
			const index = menu[k];
			
			let found = false;
			
			// find menu item
			for (let p = 0; p < root.menu_items.length; p++) {
				
				const item = root.menu_items[p];
				
				if ((item.name == index) && (item instanceof Menu)) {
					
					root = item;
					
					found = true;
				}
			}
			
			// no menu item
			if ((found == false) && (k == (menu.length - 1))) {
				
				root.menu_items.push( arg );
			}
		}
	}
};

/**
 * INITIALISE
 */
storage.root = storage.merge_array(
	storage.import_server_state( window.championcore.manage_navigation.data ),
	'ROOT'
);

// wire on events
storage.event.attach();




/**
 * exports
 */
window.championcore.manage_navigation.component.storage = storage;

export default storage;
