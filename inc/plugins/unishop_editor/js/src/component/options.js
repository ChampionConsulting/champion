"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'options',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
			
			/**
			 * column value
			 */
			option_list: function () {
				
				const list   = this.find_child_node_by_tag_name( 'options', this.node.children );
				const parent = list[0]; 
				
				const result = [];
				
				for (let k = 0; k < parent.children.length; k++) {
					
					const item = parent.children.item(k); 
					
					result.push(
						{
							k:    k,
							node: item
						}
					);
				}
				
				// console.log( 'option_list', result );
				
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
		},
		
		// props
		props: [
			'row_index',
			'name',
			'node'
		],
		
		// template to insert
		template: [
			'<div class="cell options">',
				'<options-option-add v-bind:row_index="row_index"></options-option-add>',
				'<template v-for="option in option_list">',
					'<options-option v-bind:row_index="row_index" v-bind:option_index="option.k" v-bind:node="option.node"></options-option>',
				'</template>',
			'</div>'
		].join("\n")
	}
);
