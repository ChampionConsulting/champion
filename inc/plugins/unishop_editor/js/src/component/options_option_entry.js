"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'options-option-entry',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * entry price
			 */
			price: function () {
				
				let result = this.node.attributes['price'] ? this.node.attributes['price'].value : false;
				
				return result;
			},
			
			/**
			 * composite text
			 */
			text: function () {
				
				let result = this.node.innerHTML;
				
				return result;
			},
			
			/**
			 * entry value
			 */
			value: function () {
				
				let result = this.node.attributes['value'].value ? this.node.attributes['value'].value : false;
				
				return result;
			}
		},
		
		// state
		data: function () {
			return {
				
				show_editor: false,
				
				edit_value: this.value,
				edit_price: this.price
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
					'champion-xml-editor-event-option-entry-update',
					{
						entry_index:  this.entry_index,
						option_index: this.option_index,
						row_index:    this.row_index,
						
						attributes: {
							price: this.edit_price,
							value: this.edit_value
						}
					}
				);
			},
			
			/**
			 * show/hide the edit mode
			 */
			onclick_show_hide: function () {
				
				// hide all editors
					window.champion_xml_editor_app.event_bus.$emit( 'champion-xml-editor-event-editor-hide', {} );
				
				if (this.show_editor == true) {
					
				} else {
					
					// show the editor we want
					this.show_editor = true;
					
					this.edit_price = this.price;
					this.edit_value = this.value;
				}
			},
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
			'<div class="options-option-entry" v-on:click.stop.prevent="onclick_show_hide">',
				'<div class="show" v-show="!show_editor">',
					'{{ text }}',
					'<options-option-entry-delete v-bind:node="node" v-bind:row_index="row_index" v-bind:option_index="option_index" v-bind:entry_index="entry_index"></options-option-entry-delete>',
				'</div>',
				'<div class="edit" v-show="show_editor">',
					'<div class="row" v-if="price">',
						'<label>Price:</label>',
						'<input v-model="edit_price" />',
					'</div>',
					'<div class="row" v-if="value" >',
						'<label>Value:</label>',
						'<input v-model="edit_value" />',
					'</div>',
					'<div class="row">',
						'<input type="submit" v-on:click.stop.prevent="onclick_change" value="Update" />',
					'</div>',
				'</div>',
			'</div>'
		].join("\n")
	}
);
