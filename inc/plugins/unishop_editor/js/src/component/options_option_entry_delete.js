"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'options-option-entry-delete',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * composite text
			 */
			text: function () {
				
				let result = this.node.innerHTML;
				
				return result;
			},
		},
		
		// state
		data: function () {
			return {
			}
		},
		
		// methods
		methods: {
			
			/**
			 * nuke
			 */
			onclick_delete: function () {
				
				this.show_editor = false;
				
				window.champion_xml_editor_app.event_bus.$emit(
					'champion-xml-editor-event-option-entry-delete',
					{
						entry_index:  this.entry_index,
						option_index: this.option_index,
						row_index:    this.row_index,
						
						attributes: {
						}
					}
				);
			}
		},
		
		// props
		props: [
			'entry_index',
			'option_index',
			'row_index',
			'node'
		],
		
		// template to insert
		template: [
			'<div class="options-option-entry-delete">',
				'<button class="button" v-on:click.stop.prevent="onclick_delete">Delete</button>',
			'</div>'
		].join("\n")
	}
);
