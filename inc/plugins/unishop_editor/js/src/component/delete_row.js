"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'delete-row',
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
					'champion-xml-editor-event-delete',
					{
						row_index: this.row_index
					}
				);
			}
		},
		
		// props
		props: [
			'row_index'
		],
		
		// template to insert
		template: [
			'<div class="delete-row">',
				'<button class="button" v-on:click.stop.prevent="onclick_add">Delete row</button>',
			'</div>'
		].join("\n")
	}
);
