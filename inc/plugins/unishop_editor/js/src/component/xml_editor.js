"use strict";
/**
 * root component for the editor
 */
Vue.component(
	'xml-editor',
	{
		// computed values
		computed: {
			
			/**
			 * rows in the data
			 */
			rows: function () {
				
				let result = [];
				
				if (this.shop_dom instanceof XMLDocument) {
					
					const list = this.shop_dom.querySelectorAll( 'item' );
					
					for (let k = 0; k < list.length; k++) {
						
						const node = list.item( k );
						
						result.push(
							{ 
								k: k,
								node: node
							}
						);
					}
				}
				
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
			 * hide the editors
			 */
			onclick_hide: function () {
				
				// hide all editors
					window.champion_xml_editor_app.event_bus.$emit( 'champion-xml-editor-event-editor-hide', {} );
			}
		},
		
		// props
		props: [
			'shop_dom'
		],
		
		// template to insert
		template: [
			'<div class="xml-editor" v-on:click.stop.prevent="onclick_hide">',
				'<create-row></create-row>',
				'<table>',
					/*
					'<thead>',
						'<tr>',
							'<th colspan="5"></th>',
							'<th> Options </th>',
							'<th></th>',
						'</tr>',
					'</thead>',
					*/
					'<tbody>',
						'<template v-for="row in rows">',
							'<tr>',
								'<th> Name </th>',
								'<th> Price </th>',
								'<th> SKU </th>',
								'<th> Description </th>',
								'<th> Rating </th>',
								
								'<th> Category </th>',
								'<th> Thumbs </th>',
								'<th> Photos </th>',
								'<th> Quantity </th>',
								'<th> Shipping </th>',
								
								'<th> Options </th>',
								'<th></th>',
							'</tr>',
							'<tr>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="name" ></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="price"></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="sku"></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="description"></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="rating"></cell> </td>',
								
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="category"></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="thumbs"></cell> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="photos"></cell> </td>',
								'<td> <quantity v-bind:row_index="row.k" v-bind:node="row.node" name="quantity"></quantity> </td>',
								'<td> <cell v-bind:row_index="row.k" v-bind:node="row.node" name="shipping"></cell> </td>',
								
								'<td rowspan="1"> <!-- cell v-bind:row_index="row.k" v-bind:node="row.node" name="options"></cell --> <options v-bind:row_index="row.k" v-bind:node="row.node" name="options"></options> </td>',
								'<td rowspan="1"> <delete-row v-bind:row_index="row.k"></delete-row> </td>',
							'</tr>',
							
						'</template>',
					'</tbody>',
				'</table>',
				
			'</div>'
		].join("\n")
	}
);
