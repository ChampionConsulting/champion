/**
 * imports
 */

import { MeMi } from 'ChampionCore/widget/manage_navigation/message-bus';

import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

export default class Menu {

	/**
	 * @prop {string}
	 */
	public name: string;

	/**
	 * @prop {string}
	 */
	public url: string;

	/**
	 * @prop {boolean}
	 */
	public active: boolean;

	/**
	 * @prop {boolean}
	 */
	public nuke: boolean;

	/**
	 * @prop {Array<MenuItem>}
	 */
	public menu_items: Array<MeMi>;

	/**
	 * menu storage
	 * 
	 * @param {string} name
	 * @param {string} url
	 * @param {bool} active
	 * @param {bool} nuke
	 * @param {Array<MeMi>} menu_items
	 */
	constructor (name: string, url: string, active: boolean, nuke: boolean, menu_items: Array<MeMi>) {
		
		this.name   = name;
		this.url    = url;
		this.active = active;
		this.nuke   = nuke;
		
		this.menu_items = menu_items;
	};

	/**
	 * get an entry with this name
	 */
	public add (item: MeMi) : Menu {
		
		this.menu_items.push( item );
		
		return this;
	}

	/**
	 * build the active list for persisting state
	 * 
	 * @return {Record<string, any>}
	 */
	public build_active_list () : Record<string, any> {

		const result : Record<string, any> = {};
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const ooo = this.menu_items[k];
			
			//console.log( ooo.name );
			
			if (ooo instanceof MenuItem) {
				// corner case - top level
				result[ooo.name] = (ooo.active ? '1' : '0');
				
			} else if (ooo instanceof Menu) {
				// sub menu
				result[ooo.name] = ooo.build_active_list();
				
			} else {
				console.error( 'unknown menu entry - build_active_list', ooo);
			}
		}
		
		return result;
	}

	/**
	 * build the name change list for persisting state
	 * 
	 * @return {Record<string, any>}
	 */
	public build_change_list () : Record<string, any> {
		
		const result : Record<string, any> = {};

		for (let k = 0; k < this.menu_items.length; k++) {

			const ooo = this.menu_items[k];

			if (ooo instanceof MenuItem) {
				
				result[ooo.name] = ooo.name_changed;
				
			} else if (ooo instanceof Menu) {
				
				result[ooo.name] = ooo.build_change_list();
				
			} else {
				console.error( 'unknown menu entry - build_change_list', ooo);
			}
		}

		return result;
	};

	/**
	 * build the nav list for persisting state
	 * 
	 * @return {Record<string, any>}
	 */
	public build_nav_list () : Record<string, any> {
		
		const result : Record<string, any> = {};

		for (let k = 0; k < this.menu_items.length; k++) {

			const ooo = this.menu_items[k];

			if (ooo instanceof MenuItem) {
				
				result[ooo.name] = ooo.url;
				
			} else if (ooo instanceof Menu) {
				
				result[ooo.name] = ooo.build_nav_list();
				
			} else {
				console.error( 'unknown menu entry - build_nav_list', ooo);
			}
		}

		return result;
	};

	/**
	 * build the nuke list for persisting state
	 * @return {Record<string, any>}
	 */
	public build_nuke_list () : Record<string, any> {
		
		const result : Record<string, any> = {};
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const ooo = this.menu_items[k];
			
			if (ooo instanceof MenuItem) {
				// corner case - top level
				result[ooo.name] = (ooo.nuke ? '1' : '0');
				
			} else if (ooo instanceof Menu) {
				// sub menu
				result[ooo.name] = ooo.build_nuke_list();
				
			} else {
				console.error( 'unknown menu entry - build_nuke_list', ooo);
			}
		}
		
		return result;
	};

	/**
	 * build the open-in-new-tab list for persisting state
	 * @return {Record<string, any>}
	 */
	public build_open_in_new_tab_list () : Record<string, any> {
		
		const result : Record<string, any> = {};
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const ooo = this.menu_items[k];
			
			if (ooo instanceof MenuItem) {
				// corner case - top level
				result[ooo.name] = ((ooo.open_in_new_tab && (ooo.open_in_new_tab == '1')) ? '1' : '0');
				
			} else if (ooo instanceof Menu) {
				// sub menu
				result[ooo.name] = ooo.build_open_in_new_tab_list();
				
			} else {
				console.error( 'unknown menu entry - build_open_in_new_tab', ooo);
			}
		}
		
		return result;
	};

	/**
	 * build the rename list for persisting menu item renames
	 * 
	 * @return {Record<string, any>}
	 */
	public build_rename_list () : Record<string, any> {

		const result : Record<string, any> = {};
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const ooo = this.menu_items[k];
			
			console.log( ooo.name );
			
			if (ooo instanceof MenuItem) {
				// corner case - top level
				result[ooo.name] = (ooo.name_changed ?? ooo.name);
				
			} else if (ooo instanceof Menu) {
				// sub menu
				result[ooo.name] = ooo.build_active_list();
				
			} else {
				console.error( 'unknown menu entry - build_active_list', ooo);
			}
		}
		
		return result;
	}

	/**
	 * comparison operator
	 */
	public equals (arg: any) : boolean {
		
		let result = false;
		
		if (arg instanceof Menu) {
			
			result = ((this.name == arg.name) && (this.url == arg.url));
		}
		
		return result;
	}

	/**
	 * get an entry with this name
	 */
	public find_by_item (item: MeMi) :  MeMi | boolean {
		
		const detected = this.menu_items.findIndex(
			function (arg) {
				return (arg == item);
			}
		);
		
		let result : MeMi | boolean = false;
		
		if (detected > -1) {
			result = this.menu_items[ detected ];
		}
		
		return result;
	}

	/**
	 * get an entry with this name
	 */
	public find_by_name (name: string) : MeMi | boolean {
		
		const detected = this.menu_items.findIndex(
			function (arg) {
				return ((arg.name == name) && (arg instanceof Menu));
			}
		);
		
		let result : MeMi | boolean = false;
		
		if (detected > -1) {
			result = this.menu_items[ detected ];
		}
		
		return result;
	}

	/**
	 * does the menu with this name exist?
	 * @param string name
	 * @return bool
	 */
	public has (name: string) : boolean {
		
		const detected = this.has_menu(name) || this.has_menu_item(name);
		
		return detected;
	}

	/**
	 * does the menu with this name exist?
	 * @param string name
	 * @return bool
	 */
	public has_menu (name: string) : boolean {
		
		// ensure that top level "all" name is present
		const detected = this.menu_items.findIndex(
			function (arg) {
				return ((arg.name == name) && (arg instanceof Menu));
			}
		);
		
		const result = (detected > -1);
		
		return result;
	};

	/**
	 * does the menu=-item with this name exist?
	 */
	public has_menu_item (name: string) : boolean {
		
		const detected = this.menu_items.findIndex(
			function (arg) {
				return ((arg.name == name) && (arg instanceof MenuItem));
			}
		);
		
		const result = (detected > -1);
		
		return result;
	};

	/**
	 * pretty print the contents
	 * @param int offset
	 */
	public pretty_print (offset = 0) : string {
		
		const spacer = "=> ".repeat(offset);
		
		let result =
			spacer + "Menu " +
			JSON.stringify(
				{
					name:   this.name,
					url:    this.url,
					active: this.active,
					nuke:   this.nuke
				}
			) +
			"\n";
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const item = this.menu_items[k];
			
			result += item.pretty_print( offset + 1);
		}
		
		return result;
	};

	/**
	 * set/clear the active flag for menu
	 */
	public set_active (flag: boolean) : Menu {
		
		this.active = flag;
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const item = this.menu_items[k];
			
			item.set_active(flag);
		}
		
		return this;
	}

	/**
	 * set/clear the deleted flag for menu
	 */
	public set_nuked (flag: boolean) : Menu {
		
		this.nuke = flag;
		
		//console.log( 'set_nuked menu', this.name, this.nuke );
		
		for (let k = 0; k < this.menu_items.length; k++) {
			
			const item = this.menu_items[k];
			
			item.set_nuked(flag);
		}
		
		return this;
	}

}
