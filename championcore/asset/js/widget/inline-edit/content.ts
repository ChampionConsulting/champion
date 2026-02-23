"use strict";

import { defineComponent } from "vue";

import message_bus, {InlineEditGlobalSaveEvent, InlineEditLoadItemEvent, InlineEditModalOpenEvent, InlineEditModalOpenEvent_Packed, InlineEditModalOpenEvent_Packed_Item, InlineEditSaveItemEvent} from "ChampionCore/widget/inline-edit/message-bus";
		
/**
 * the Vue component
 */
export default defineComponent(
	{
		// lifecyle - created
		// created
		created () {
		
			const self = this;
			
			/**
			 * custom event - from child component
			 */
			message_bus.on(
				'global_save',
				function(evnt: InlineEditGlobalSaveEvent) {
					
					self.custom_on_global_save( evnt.flag, evnt.packed );
				}
			);
			
			/**
			 * custom event - from child component
			 */
			message_bus.on(
				'modal_open',
				function (evnt: InlineEditModalOpenEvent) {
					
					if ((evnt.packed.id == self.id) && (evnt.packed.type == self.type) && (self.type != 'blog')) {
						
						self.item = (evnt.packed.item as InlineEditModalOpenEvent_Packed_Item);
						
						self.redactor_instantiate();
						
						//window.RedactorX('#' + self.element_id + ' div.edit_box', 'source.setCode', self.item.html );
					}
				}
			);
		},
		
		// lifecyle - mounted - template plugged in
		mounted () {

			const self = this;
			
			self.$nextTick(
				function () {
					//component.redactor_instantiate();
				}
			);
		},
		
		// computed data
		computed: {
			/*
			* DOM node ID
			*/
			element_id: function () : string {
				
				const result = 'championcore-inline-edit-content_' + this.widget_id;

				return result;
			},

			/*
			* DOM node ID - textarea
			*/
			element_id_textarea: function () : string {
				
				const result = this.element_id + '_textarea';

				return result;
			}
		},
		
		// state
		data () {
			return {
				
				item: ({html: ''} as InlineEditModalOpenEvent_Packed_Item),
				
				redactor_on: false,

				redactor_instance: (false as any | boolean),

				redactor_content: ''
			};
		},
		
		// behaviour
		methods: {
			
			/**
			 * custom event handler - global save
			 */
			custom_on_global_save: function(flag: boolean, packed: any) {
				
				const self = this;
				
				if ((packed.id == self.id) && (packed.type == self.type)) {
					
					if (self.redactor_on) {
						
						self.onclick_save();
					}
				}
			},
			
			/*
			* event handler - content area click
			*/
			onclick_content_area: function (evnt: Event) {

				evnt.preventDefault();
				evnt.stopPropagation();
				evnt.stopImmediatePropagation();
				
				const self = this;
				
				// load data to start redactor
				if (!self.redactor_on) {
					if (self.id && self.type) {
						message_bus.emit(
							'load_item',
							new InlineEditLoadItemEvent(self.id, self.type)
						);
					}
				}
			},
			
			
			/*
			* event handler - save
			*/
			onclick_save: function () {
				
				const self = this;
				
				//self.item.html = self.redactor_instance.editor.getContent();
				self.item.html = self.redactor_content;

				console.log( 'onclick_save', self.item.html );
				
				if (self.id && self.type && self.widget_id) {
					message_bus.emit(
						'save_item', new
						InlineEditSaveItemEvent(self.id, self.type, self.item, self.widget_id)
					);
				}
				
				self.redactor_tear_down();
			},
			
			/*
			* event handler - update state
			*/
			onclick_cancel: function() {
				
				const self = this;
				
				this.redactor_tear_down();
			},
			
			/*
			* create the redactor instance
			*/
			redactor_instantiate: function () : void {
				
				const self = this;
				
				window.setTimeout(
					function () {
						
						const element_selector = '#' + self.element_id + ' div.edit_box';

						const textarea_selector = '#' + self.element_id + ' textarea';

						const node_element  = document.querySelector(element_selector);
						const node_textarea = document.querySelector(textarea_selector);

						const save_bar_selector = element_selector + ' .championcore-inline-edit-global-save';
						const save_bar = document.querySelector( save_bar_selector );

						// set offset
						const offset = save_bar?.getBoundingClientRect().top;

						console.log( save_bar_selector, save_bar, save_bar?.getBoundingClientRect() );

						if (offset) {
							(save_bar as HTMLElement).style.top = offset + 'px';
						}

						self.redactor_on = true;

						if (node_element && node_textarea) {

							//////////////////////////////////////////////////
							// use the packed version instead of the page HTML
							//const text = (self.$refs.edit_box as HTMLElement).innerHTML;
							const text = self.item.html;
							console.log( 'text', text );
							////////////////////////////////////////////////

							(self.$refs.textarea as HTMLTextAreaElement).value = text;

							self.redactor_content = text;
						}

						console.log( 'starting redactor for', element_selector, node_element, textarea_selector, node_textarea );

						self.redactor_instance = window.RedactorX(
							textarea_selector,
							{
								codemirror: false,
								control:    true,

								editor: {
									focus: true,
									lang: (window.championcore.lang_short || "en"),
									minHeight: 300,
								},

								image: {
									upload:      window.championcore.admin_url + '/inc/editor_images.php'//,
									//imageManagerJson: window.championcore.admin_url + '/inc/data_json.php',
								},

								paste: {
									plaintext: true,
								},

								plugins: [
									'alignment',
									
									//'champion_redactor_imagemanager',

									'inlineformat',
									'video' 
								],

								subscribe: {
									'editor.change': function (evnt: any) {
										console.log( evnt );

										self.redactor_content = evnt.params.html;
									}
								},

								/* toolbarFixedTarget: element_selector */
								
								/*
								cleanOnEnter:       false,
								cleanInlineOnEnter: false,
								removeScript:       false,
								removeNewLines:     true,
								removeComments:     false,
								replaceTags:        false,
								
								paragraph: false,

								

								pastePlainText: true,
								paragraphize: true,
								replaceDivs: false,
								autoresize: true,
								
								buttonSource: true,
								
								fileUpload:       window.championcore.admin_url + '/inc/editor_files.php',
								fileManagerJson:  window.championcore.admin_url + '/inc/data_json.php',
								
								imageResizable: true,
								imagePosition: true,
								

								clips: [
									['More', '##more##']
								],
								air: true,
								styles: true,
								
								//clickToEdit:   true,

								clickToSave:   {title: window.championcore.translations.lang_save_button },
								clickToCancel: {title: window.championcore.translations.lang_cancel_button },
								callbacks: {
									clickStart: function (html: string) {
										console.log( 'redactor click to edit - start' );
									},
									clickSave: function (html: string) {
										console.log( 'redactor click to edit - save' );
										self.onclick_save();
									},
									clickCancel: function (html: string) {
										console.log( 'redactor click to edit - cancel' );
										self.onclick_cancel();
									}
								}
								*/
							}
						);

						if (!(window as any).redactor_instance) {
							(window as any).redactor_instance = [];
						}

						(window as any).redactor_instance.push( self.redactor_instance ); ////////

						console.log( self.redactor_instance );

					},
					1000
				);
				
				
			},
			
			/**
			 * tear down redactor
			 */
			redactor_tear_down: function () {
				
				const self = this;
				
				window.RedactorX(
					//('#' + self.element_id + ' div.edit_box'),
					('#' + self.element_id + ' textarea'),
					'destroy'
				);
				self.redactor_on = false;

				self.redactor_instance = false;
			}
		},
		
		// data from parent
		props: {
			//item: Object, // unused prop
			
			header: String,
			id:     String,
			type:   String,
			
			widget_id: String
		},
		
		// template
		template: [
			'<div v-bind:id="element_id" class="championcore-inline-edit-content" v-on:click.prevent.stop="onclick_content_area($event)">',
				'<div v-show="!redactor_on" ref="edit_box" class="edit_box">',
					'<slot></slot>',
				'</div>',
				'<textarea ref="textarea" v-show="redactor_on" v-bind:id="element_id_textarea"></textarea>',
			'</div>'
		].join('')
		
		/*
		'<div v-if="redactor_on">',
					'<div class="btn btn-secondary" v-on:click.prevent.stop="onclick_save">Save</div>',
					'<div class="btn btn-secondary" v-on:click.prevent.stop="onclick_cancel">Cancel</div>',
				'</div>',
		*/
	}
);
