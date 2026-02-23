"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'options-option',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * the name of the option
			 */
			entries: function () {
				
				const list = this.find_child_node_by_tag_name( 'entry', this.node.children );
				
				const result = [];
				
				for (let k = 0; k < list.length; k++) {
					
					const item = list[k]; 
					
					result.push(
						{
							k:    k,
							node: item
						}
					);
				}
				
				return result;
			},
			
			/**
			 * the name of the option
			 */
			option_name: function () {
				
				let result = this.node.attributes['name'].value;
				
				return result;
			}
		},
		
		// state
		data: function () {
			return {
			}
		},
		
		// methods
		methods: {
			
			/**
			 * show/hide the edit mode
			 */
			onclick_show_hide: function () {
			},
		},
		
		// props
		props: [
			'option_index',
			'row_index',
			'node'
		],
		
		// template to insert
		template: [
			'<div class="options-option" v-on:click.stop.prevent="onclick_show_hide">',
				'<p>{{ option_name }}</p>',
				'<ul>',
					'<li v-for="entry in entries"> <options-option-entry v-bind:node="entry.node" v-bind:row_index="row_index" v-bind:option_index="option_index" v-bind:entry_index="entry.k"></options-option-entry> </li>',
					'<li> <options-option-entry-add v-bind:row_index="row_index" v-bind:option_index="option_index"></options-option-entry-add> </li>',
				'</ul>',
			'</div>'
		].join("\n")
	}
);
