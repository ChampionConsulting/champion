"use strict";

import {createApp } from 'vue';

import message_bus, {InlineEditGlobalSaveEvent, InlineEditLoadItemEvent, InlineEditModalOpenEvent, InlineEditModalOpenEvent_Packed, InlineEditModalOpenEvent_Packed_Item, InlineEditSaveItemEvent} from 'ChampionCore/widget/inline-edit/message-bus';

import ComponentContent    from "ChampionCore/widget/inline-edit/content";
import ComponentGlobalSave from "ChampionCore/widget/inline-edit/global-save"
import ComponentModal      from "ChampionCore/widget/inline-edit/modal";
import ComponentToolbar    from "ChampionCore/widget/inline-edit/toolbar";

/**
 * state management
 */
class AppState {
};

// start up
const app = createApp(
	{
		data () : AppState {
			return {};
		},

		methods: {
			
			/*
			* load data for an item
			*/
			load_item: function (id: string, type: string, widget_id: string) {

				const params = new URLSearchParams(
					{
						id:   id,
						type: type,

						widget_id: widget_id
					}
				);
				
				window.fetch(
					window.championcore.admin_url + '/index.php?p=rest&' + params.toString() 
				).then(
					(response) => response.json()
				).then(
					function(item: InlineEditModalOpenEvent_Packed_Item) {

						const packed = {
							item: (item as InlineEditModalOpenEvent_Packed_Item), // (item.body as InlineEditModalOpenEvent_Packed_Item),
							id:   id,
							type: type,

							widget_id: widget_id
						};
						
						message_bus.emit( 'modal_open', new InlineEditModalOpenEvent(true, packed) );
					}
				);
			},
			
			/*
			* save data for an item
			*/
			save_item: function (id: string, type: string, item: any) {
				
				// NB
				item.id = id;

				const params = new URLSearchParams(
					{
						'id':   id,
						'type': type
					}
				);

				const form_data = new FormData();
				Object.keys(item).forEach(
					(field, index, collection) =>  form_data.set(field, item[ field ])
				);
				form_data.delete('id');
				form_data.delete('type');
				
				window.fetch(
					window.championcore.admin_url + '/index.php?p=rest&' + params.toString(),
					{
						method: 'POST',
						//headers: {
						//	"Content-Type": "application/json"
						//},
						body: form_data // JSON.stringify( item )
					}
				).then(
					(response) => response.json()
				).then(
					function(data: any) {
						window.location.reload();
					}
				);
			}
		},

		/**
		 * life cycle hook
		 */
		mounted () {

			const self = this;

			// custom event - from child component
			message_bus.on(
				'load_item',
				function (evnt: InlineEditLoadItemEvent) {
					self.load_item(evnt.id, evnt.type);
				}
			);

			// custom event - from child component
			message_bus.on(
				'save_item',
				function (evnt: InlineEditSaveItemEvent) {
					self.save_item(evnt.id, evnt.type, evnt.item);
				}
			);
		}
	}
);

/**
 * add components
 */
app.component( 'championcore-inline-edit-content',     ComponentContent );
app.component( 'championcore-inline-edit-global-save', ComponentGlobalSave );
app.component( 'championcore-inline-edit-modal',       ComponentModal );
app.component( 'championcore-inline-edit-toolbar',     ComponentToolbar );

// mount
const app_element = document.querySelector( '.inner.group');

if (app_element) {
	app.mount( app_element );
}

/**
 * exports
 */
window.championcore.inline_edit.app = app;
