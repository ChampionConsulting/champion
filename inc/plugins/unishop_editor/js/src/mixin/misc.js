"use strict";

/**
 * mixin - useful methods for components
 */
window.champion_xml_editor_app.mixins.push(
	{
		
		// methods
		methods: {
			
			
			/**
			 * find child nodes with set tag-name
			 */
			find_child_node_by_tag_name: function (tag_name, node_list) {
				
				let result = [];
				
				for (let k = 0; k < node_list.length; k++) {
					
					const item = node_list.item(k); 
					
					if (item.tagName == tag_name) {
						result.push( item );
					}
				}
				
				return result;
			}
			
			
		}
	}
);
