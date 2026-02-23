
/**
 * imports
 */
import mitt from 'mitt';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * composite type
 */
export type MeMi = Menu | MenuItem;

/**
 * Event base class - item
 */
class EventItemBase {
	public constructor (item : MeMi) {
		this.item = item;
	}

	public item : MeMi;
}

/**
 * Event base class - menu
 */
class EventMenuBase {
	public constructor (menu: Menu) {
		this.menu = menu;
	}

	public menu : Menu;
}

/**
 * Event base class - menu and item
 */
class EventMenuItemBase {
	public constructor (menu: Menu, item : MenuItem) {
		this.item = item;
		this.menu = menu;
	}

	public item : MenuItem;
	public menu : Menu;
}

/**
 * Event
 */
export class DnDChangeEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuActivateToggleEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuAddEvent {
	public constructor (parent : MeMi, menu_item_new: string) {

		this.menu_item_new = menu_item_new;
		this.parent        = parent;
	}

	public menu_item_new : string = '';
	public parent : MeMi;
}

/**
 * event
 */
export class MenuAddNonChampionEvent {

	public constructor (parent: Menu, name: string, url: string, open_in_new_tab: string) {
		this.parent          = parent;
		this.name            = name;
		this.url             = url;
		this.open_in_new_tab = open_in_new_tab;
	}

	public parent: Menu;
	public name           : string = '';
	public url            : string = '';
	public open_in_new_tab: string = '0';
}

/**
 * Event
 */
export class MenuDeleteEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuDeleteToggleEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuItemActivateToggleEvent extends EventMenuItemBase {};

/**
 * Event
 */
export class MenuItemDeleteEvent extends EventMenuItemBase {};

/**
 * Event
 */
export class MenuItemDeleteToggleEvent extends EventMenuItemBase {};

/**
 * Event
 */
export class MenuItemMoveDownEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuItemMoveUpEvent extends EventItemBase {};

/**
 * Event
 */
export class MenuItemNameChangedEvent extends EventItemBase {};

/**
 * Event
 */
export class StorageChangeEvent extends EventMenuBase {};

/**
 * Event
 */
export class StateSaveEvent {
}

/**
 * Event
 */
export class TrackUiMenuChangeEvent {

	public constructor (open_menu_list: Array<MeMi>) {

		this.open_menu_list = open_menu_list;
	}

	public open_menu_list : Array<MeMi>;
}

/**
 * Event
 */
export class TrackUiMenuCloseEvent extends EventMenuBase {};

/**
 * Event 
 */
export class TrackUiMenuOpenEvent extends EventMenuBase {};

/**
 * possible events
 */
type ManageNavigationEvent = {
	'dnd-change':           DnDChangeEvent,

	'menu-activate-toggle': MenuActivateToggleEvent,
	'menu-add':             MenuAddEvent,
	'menu-add-non-champion':   MenuAddNonChampionEvent,
	'menu-delete':          MenuDeleteEvent,
	'menu-delete-toggle':   MenuDeleteToggleEvent,

	'menu-item-activate-toggle': MenuItemActivateToggleEvent,
	'menu-item-delete':          MenuItemDeleteEvent,
	'menu-item-delete-toggle':   MenuItemDeleteToggleEvent,

	'menu-item-move-down': MenuItemMoveDownEvent,
	'menu-item-move-up':   MenuItemMoveUpEvent,

	'menu-item-name-changed': MenuItemNameChangedEvent,
	
	'storage-change': StorageChangeEvent,
	'state-save':     StateSaveEvent,

	'track-ui-menu-change': TrackUiMenuChangeEvent,
	'track-ui-menu-close':  TrackUiMenuCloseEvent,
	'track-ui-menu-open':   TrackUiMenuOpenEvent
}

/**
 * message bus component
 */
const message_bus = mitt<ManageNavigationEvent>();

/**
 * exports
 */
window.championcore.manage_navigation.component.message_bus = message_bus;

/**
 * exports
 */
export default message_bus;
