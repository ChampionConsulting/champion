"use strict";
/**
 * add an option
 */
Vue.component(
	'options-option-add',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
		},
		
		// state
		data: function () {
			return {
				edit_name: ''
			}
		},
		
		// methods
		methods: {
			
			/**
			 * change a cell value
			 */
			onclick_change: function () {
				
				this.show_editor = false;
				
				window.champion_xml_editor_app.event_bus.$emit(
					'champion-xml-editor-event-option-add',
					{
						row_index: this.row_index,
						
						attributes: {
							name: this.edit_name
						}
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
			'<div class="options-option-add">',
				'<div class="row">',
					'<label>Name:</label>',
					'<input v-model="edit_name" />',
				'</div>',
				'<div class="row">',
					'<input type="submit" v-on:click.stop.prevent="onclick_change" value="Add Option" />',
				'</div>',
			'</div>'
		].join("\n")
	}
);
