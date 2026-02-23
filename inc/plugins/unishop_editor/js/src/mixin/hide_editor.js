"use strict";

/**
 * mixin - all editors should be closed when a new editor is opened
 */
window.champion_xml_editor_app.mixins.push(
	{
		// lifecycle event - created
		created: function () {
			
			const self = this;
			
			// event listener = 'champion-xml-editor-event-editor-hide'
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-editor-hide',
				function (payload) {
					self.show_editor = false;
				}
			);
		},
		
		// methods
		methods: {
			
			
			/**
			 * update a cells content
			 */
			update_cell_content: function (row_index, element_name, value) {
				
				
			}
			
			
		},
		
		// state
		data: function () {
			return {
				show_editor: false
			};
		}
	}
);
