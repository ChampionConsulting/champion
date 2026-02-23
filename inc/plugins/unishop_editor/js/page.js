"use strict";
/**
 * champion xml editor
 */

window.champion_xml_editor_app.vm = new Vue(
	{
		el: "#champion_xml_editor_app",
		
		// state
		data: {
			shop_dom: {},
			shop_xml: '',
			token:    ''
		},
		
		// instance methods
		methods: {
			
			// load xml
			io_load_xml: function () {
				
				const self = this;
				
				window.fetch(
					(championcore.admin_url + '/index.php?p=unishop_editor'),
					{
						method:      'get',
						mode:        'same-origin',
						cache:       'no-cache',
						credentials: 'same-origin',
						headers: {
							'Content-Type': 'application/json'
						}
					}
				).then(
					function (response) {
						self.io_response( response );
					}
				);
					
			},
			
			// load xml - parse response
			io_response: function (response) {
				
				const self = this;
				
				response.json().then(
					function (data) {
						
						self.shop_xml = data.content;
						self.token    = data.token;
						
						self.parse_xml( self.shop_xml );
					}
				);
			},
			
			// save xml
			io_save_xml: function () {
				
				const self = this;
				
				const form_data = new FormData();
				form_data.append( 'content', self.shop_xml );
				form_data.append( 'token',   self.token );
				
				window.fetch(
					(championcore.admin_url + '/index.php?p=unishop_editor'),
					{
						method:      'POST',
						body:        form_data,
						
						mode:        'same-origin',
						cache:       'no-cache',
						credentials: 'same-origin',
					}
				).then(
					function (response) {
						self.io_response( response );
					}
				);
			},
			
			// parse shop xml
			parse_xml: function (xml) {
				
				const self = this;
				
				const parser = new DOMParser();
				
				self.shop_dom = parser.parseFromString( xml, 'application/xml');
			}
		},
		
		// lifestyle hook
		created: function () {
			
			const self = this;
			
			this.io_load_xml();
			
			// event listener = champion-xml-editor-event-add
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-add',
				function (payload) {
					
					window.aaa = self.shop_dom;
					
					const node = self.shop_dom.createElement( 'item' );
					node.innerHTML = [
						'<name>name</name>',
						'<price>price</price>',
						'<sku>sku</sku>',
						'<description>description</description>',
						'<rating>rating</rating>',
						'<category>category</category>',
						'<thumbs>thumbs</thumbs>',
						'<photos>photos</photos>',
						'<quantity min="0" max="0">quantity</quantity>',
						'<shipping>shipping</shipping>',
						'<options></options>'
					].join("\n");
					
					// insert new node
					self.shop_dom.children.item(0).appendChild( node );
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-delete
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-delete',
				function (payload) {
					
					window.aaa = self.shop_dom;
					
					const node = self.shop_dom.querySelectorAll( 'item' ).item(payload.row_index);
					
					console.log( node );
					
					// remove node
					self.shop_dom.children.item(0).removeChild( node );
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-update
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-update',
				function (payload) {
					
					const node = self.shop_dom.querySelectorAll( 'item' ).item(payload.row_index).querySelectorAll( payload.name ).item(0);
					
					node.innerHTML = payload.value;
					
					// update attributes
					for (let k in payload.attributes) {
						
						node.attributes[ k ].nodeValue = payload.attributes[ k ];
					}
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-option-add
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-option-add',
				function (payload) {
					
					const node = self.shop_dom.createElement( 'option' );
					
					node.setAttribute( 'name', payload.attributes.name );
					
					const parent = self.shop_dom
						.querySelectorAll( 'item'    ).item(payload.row_index)
						.querySelectorAll( 'options' ).item(0);
					
					// insert new node
					parent.appendChild( node );
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-option-entry-add
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-option-entry-add',
				function (payload) {
					
					const node = self.shop_dom.createElement( 'entry' );
					
					if (payload.attributes.price.length > 0) {
						node.setAttribute( 'price', payload.attributes.price );
					}
					if (payload.attributes.value.length > 0) {
						node.setAttribute( 'value', payload.attributes.value );
					}
					
					const text = payload.attributes.value + ((payload.attributes.price.length > 0) ? (' - ' + payload.attributes.currency + payload.attributes.price) : '');
					
					node.innerHTML = text;
					
					const parent = self.shop_dom
						.querySelectorAll( 'item'    ).item(payload.row_index)
						.querySelectorAll( 'options' ).item(0)
						.querySelectorAll( 'option'  ).item( payload.option_index );
					
					// insert new node
					parent.appendChild( node );
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-option-entry-delete
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-option-entry-delete',
				function (payload) {
					
					const parent = self.shop_dom
						.querySelectorAll( 'item'    ).item(payload.row_index)
						.querySelectorAll( 'options' ).item(0)
						.querySelectorAll( 'option'  ).item( payload.option_index );
						
					const node = parent.querySelectorAll( 'entry'   ).item( payload.entry_index );
					
					// remove node
					parent.removeChild( node );
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
			
			// event listener = champion-xml-editor-event-option-entry-update
			window.champion_xml_editor_app.event_bus.$on(
				'champion-xml-editor-event-option-entry-update',
				function (payload) {
					
					const node = self.shop_dom
						.querySelectorAll( 'item'    ).item(payload.row_index)
						.querySelectorAll( 'options' ).item(0)
						.querySelectorAll( 'option'  ).item( payload.option_index )
						.querySelectorAll( 'entry'   ).item( payload.entry_index );
					
					
					// update attributes
					for (let k in payload.attributes) {
						
						if (payload.attributes[ k ]) {
							node.attributes[ k ].nodeValue = payload.attributes[ k ];
						}
					}
					
					node.innerHTML = (payload.attributes['price'] ? ('$' + payload.attributes['price'] + ' - ') : '') + payload.attributes['value'];
					
					// update server
					const serialiser = new XMLSerializer();
					
					self.shop_xml = serialiser.serializeToString( self.shop_dom );
					
					self.io_save_xml();
				}
			);
		}
	}
);
