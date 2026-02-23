"use strict";

/**
 * imports
 */
import mitt from 'mitt';

/**
 * event
 */
export class InlineEditGlobalSaveEvent {

	public constructor (flag: boolean, packed: any) {

		this.flag   = flag;
		this.packed = packed;
	}

	public flag:   boolean;
	public packed: any;
}

/**
 * event
 */
export class InlineEditLoadItemEvent {
	
	public constructor (id: string, type: string) {

		this.id        = id;
		this.type      = type;
	}
	
	public id:        string;
	public type:      string;

};

/**
 * event
 */
export class InlineEditModalOpenEvent {

	public constructor (flag: boolean, packed: InlineEditModalOpenEvent_Packed) {

		this.flag   = flag;
		this.packed = packed;
	}

	public flag:   boolean;
	public packed: InlineEditModalOpenEvent_Packed;
}

export interface InlineEditModalOpenEvent_Packed {
	item: InlineEditModalOpenEvent_Packed_Item;
	id:       string;
	type:     string

	widget_id: string;
}

export interface InlineEditModalOpenEvent_Packed_Item {
	html: string;
}


/**
 * event
 */
export class InlineEditSaveItemEvent {

	public constructor (id: string, type: string, item: any, widget_id: string) {

		this.id        = id;
		this.type      = type;
		this.item      = item;
		this.widget_id = widget_id;
	}
	
	public id:        string;
	public type:      string;
	public item:      any;
	public widget_id: string;
}

/**
 * possible events
 */
type InlineEditEvent = {
	'global_save': InlineEditGlobalSaveEvent,
	'load_item':   InlineEditLoadItemEvent,
	'modal_open':  InlineEditModalOpenEvent,
	'save_item':   InlineEditSaveItemEvent
};

/**
 * message bus
 */
const message_bus = mitt<InlineEditEvent>();

/**
 * exports
 */
window.championcore.inline_edit.component.message_bus = message_bus;

export default message_bus;
