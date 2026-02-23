"use strict";
/**
 * cell component in the editor
 */
Vue.component(
	'options-option-entry-add',
	{
		// mixins
		mixins: window.champion_xml_editor_app.mixins,
		
		// computed values
		computed: {
		},
		
		// state
		data: function () {
			return {
				edit_currency: '$',
				edit_value:    '',
				edit_price:    ''
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
					'champion-xml-editor-event-option-entry-add',
					{
						option_index: this.option_index,
						row_index:    this.row_index,
						
						attributes: {
							currency: this.edit_currency,
							price:    this.edit_price,
							value:    this.edit_value
						}
					}
				);
			}
		},
		
		// props
		props: [
			'option_index',
			'row_index'
		],
		
		// template to insert
		template: [
			'<div class="options-option-entry-add">',
				'<div class="row">',
					'<label>Currency:</label>',
					'<select v-model="edit_currency" name="edit_currency">',
						'<option value="€">€ EUR</option>',
						'<option value="£">£ GBP</option>',
						'<option value="₽">₽ RUB</option>',
						'<option value="$">$ USD</option>',
						'<option value="¥">¥ JPY</option>',
						
					'</select>',
				'</div>',
				'<div class="row">',
					'<label>Price:</label>',
					'<input v-model="edit_price" name="edit_price" />',
				'</div>',
				'<div class="row">',
					'<label>Value:</label>',
					'<input v-model="edit_value" name="edit_value" />',
				'</div>',
				'<div class="row">',
					'<input type="submit" v-on:click.stop.prevent="onclick_change" value="Add New" />',
				'</div>',
			'</div>'
		].join("\n")
	}
);
