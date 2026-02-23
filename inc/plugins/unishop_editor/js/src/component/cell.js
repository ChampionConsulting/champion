"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'cell',
	{
		
		// filters
		filters: {
			
			// shorten a string and add ellipsis
			shorten: function (value) {
				
				const MAX_LEN = 10;
				
				const result = (value.length < MAX_LEN) ? value : (value.substring(0, MAX_LEN) + '...');
				
				return result;
			}
		},
		
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * column value
			 */
			value: function () {
				
				let result = '';
				
				for (let k = 0; k < this.node.children.length; k++) {
					
					const item = this.node.children.item(k); 
					
					if (item.tagName == this.name) {
						result = item.innerHTML;
						break;
					}
				}
				
				return result;
			}
			
		},
		
		// state
		data: function () {
			return {
				
				show_editor: false,
				
				edit_value: this.value
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
						
						attributes: {}
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
			'<div class="cell" v-on:click.stop.prevent="onclick_show_hide">',
				'<div class="show" v-show="!show_editor">',
					'<div class="cell_trigger">{{ value| shorten }}</div>',
					'<div class="cell_tooltip">{{ value }}</div>',
				'</div>',
				'<div class="edit" v-show="show_editor">',
					'<div class="row">',
						'<label>{{ name }}</label>',
						'<textarea v-model="edit_value"></textarea>',
						'<input type="submit" v-on:click.stop.prevent="onclick_change" value="Update" />',
					'</div>',
				'</div>',
			'</div>'
		].join("\n")
	}
);
