"use strict";

import { defineComponent } from "vue";

import message_bus, {InlineEditLoadItemEvent} from "ChampionCore/widget/inline-edit/message-bus";
		
/**
 * the Vue component
 */
export default defineComponent(
	{
		data: function () {
			return {
			};
		},
		methods: {
			/*
			* event handler - click on edit in toolbar
			*/
			onclick_edit: function (evnt: Event) {
				
				message_bus.emit('load_item', new InlineEditLoadItemEvent(this.id, this.type) )
			}
		},
		props: [
			'header',
			'id',
			'type',
			
			'widget_id'
		],
		template: `
<div class="championcore-inline-edit-toolbar">
	{{ header }}: {{ id }}
	<span class="edit" v-on:click.stop.prevent="onclick_edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>

	<championcore-inline-edit-global-save v-bind:header="header" v-bind:id="id" v-bind:type="type" v-bind:widget_id="widget_id"></championcore-inline-edit-global-save>
</div>
`
	}
);
