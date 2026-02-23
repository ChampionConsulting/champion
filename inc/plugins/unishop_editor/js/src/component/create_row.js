"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'create-row',
	{
		// mixins
		mixins: [],
		
		// computed values
		computed: {
		},
		
		// state
		data: function () {
			return {
			}
		},
		
		// methods
		methods: {
			
			/**
			 * add a new row
			 */
			onclick_add: function () {
				
				window.champion_xml_editor_app.event_bus.$emit(
					'champion-xml-editor-event-add',
					{
					}
				);
			}
		},
		
		// props
		props: [
		],
		
		// template to insert
		template: [
			'<div class="add-row">',
				'<button class="button" v-on:click.stop.prevent="onclick_add">Add row</button>',
			'</div>'
		].join("\n")
	}
);
