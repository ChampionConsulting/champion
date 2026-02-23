"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'quantity',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * column value
			 */
			value: function () {
				
				const list = this.find_child_node_by_tag_name( this.name, this.node.children );
				
				const result = list[0].innerHTML;
				
				return result;
			},
			
			/**
			 * max
			 */
			max: function () {
				
				const list   = this.find_child_node_by_tag_name( this.name, this.node.children );
				
				const result = list[0].attributes['max'].nodeValue;
				
				return result;
			},
			
			/**
			 * min
			 */
			min: function () {
				
				const list   = this.find_child_node_by_tag_name( this.name, this.node.children );
				
				const result = list[0].attributes['min'].nodeValue;
				
				return result;
			}
			
		},
		
		// state
		data: function () {
			return {
				
				show_editor: false,
				
				edit_value: this.value,
				edit_max:   this.max,
				edit_min:   this.min
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
					'champion-xml-editor-event-update',
					{
						row_index: this.row_index,
						name:      this.name,
						value:     this.edit_value,
						
						attributes: {
							max: this.edit_max,
							min: this.edit_min
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
					
					this.edit_value = this.value;
					this.edit_max   = this.max;
					this.edit_min   = this.min;
				}
			},
		},
		
		// props
		props: [
			'row_index',
			'name',
			'node'
		],
		
		// template to insert
		template: [
			'<div class="cell quantity" v-on:click.stop.prevent="onclick_show_hide">',
				'<div class="show" v-show="!show_editor">{{ value }}</div>',
				'<div class="edit" v-show="show_editor">',
					'<div class="row">',
						'<label>Quantity:</label>',
						'<input v-model="edit_value" />',
					'</div>',
					'<div class="row">',
						'<label>Max:</label>',
						'<input v-model="edit_max" />',
					'</div>',
					'<div class="row">',
						'<label>Min:</label>',
						'<input v-model="edit_min" />',
					'</div>',
					'<div class="row">',
						'<input type="submit" v-on:click.stop.prevent="onclick_change" value="Update" />',
					'</div>',
				'</div>',
			'</div>'
		].join("\n")
	}
);
