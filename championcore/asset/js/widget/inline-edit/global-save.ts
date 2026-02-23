"use strict";

import { defineComponent } from "vue";

import message_bus, {InlineEditGlobalSaveEvent, InlineEditModalOpenEvent} from "ChampionCore/widget/inline-edit/message-bus";

/**
 * the Vue component
 */
export default defineComponent(
	{
		
		/**
		 * lifecyle - created
		 * created
		 */
		created: function () {
		
			const self = this;
			
			/*
			 * custom event - from child component
			 */
			message_bus.on(
				'modal_open',
				function(evnt: InlineEditModalOpenEvent) {
					
					if ((evnt.packed.id == self.id) && (evnt.packed.type == self.type)) {
						
						self.item = evnt.packed.item;
					}
				}
			);
		},
		
		/**
		 * component state
		 */
		data: function() {
			return {
				
				item: {}
			};
		},
		methods: {
			/*
				* event handler - click on save
				*/
			onclick_save: function(evnt: Event) {
				
				const self = this;
				
				message_bus.emit('global_save', new InlineEditGlobalSaveEvent(true, {id: self.id, type: self.type, item: self.item}) );
			}
		},
		props: [
			'header',
			'id',
			'type',
			
			'widget_id'
		],
		template: '<div class="championcore-inline-edit-global-save"><span v-on:click.stop.prevent="onclick_save"><i class="fa fa-save" aria-hidden="true"></i></span> </div>'
	}
);
