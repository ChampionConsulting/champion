"use strict";

import { defineComponent } from "vue";

import message_bus, {InlineEditModalOpenEvent, InlineEditSaveItemEvent} from "ChampionCore/widget/inline-edit/message-bus";

/**
 * modal component
 */

let redactor_loaded = 0;

/**
 * modal
 */
export default defineComponent(
	{
		created: function() {
			let self = this;
			
			/*
				* custom event - from child component
				*/
			message_bus.on(
				'modal_open',
				function(evnt: InlineEditModalOpenEvent) {
					
					// limit what is edited in modal
					if (evnt.packed.type == 'blog') {
						self.render(evnt.flag, evnt.packed);
					}
				}
			);
			
		},
		data: function() {
			return {
				item: {},
				flag: false,
				id:   '',
				type: '',
				
				widget_id: ''
			};
		},
		methods: {
			/*
			* event handler - close modal
			*/
			onclick_close: function(evnt: Event) {
				
				this.render(false, {});
			},
			
			
			/*
			* event handler - update state
			*/
			onclick_update: function(id: string,type : string, item: any, widget_id: string) {
				
				this.render(false, {});
				
				//item.html = document.getElementById('modal-content-html').val();
				item.html = window.$R('#modal-content-html', 'source.getCode');
				
				this.redactor_tear_down();
				
				message_bus.emit('save_item', new InlineEditSaveItemEvent(id, type, item, widget_id) )
			},
			
			/**
			 * tear down redactor
			 */
			redactor_tear_down: function () {
				
				const self = this;
				
				window.$R('#modal-content-html', 'destroy');
				
				redactor_loaded = 0;
			},
			
			/*
				* show the modal
				*/
			render: function(flag: boolean, packed: any) {
				
				let self = this;
				
				// open
				if (flag === true) {
					this.item = packed.item;
					this.flag = true;
					this.id   = packed.id;
					this.type = packed.type;
					
					this.widget_id = packed.widget_id;
					
					if (redactor_loaded == 0) {
						// start redactor - NB delay
						window.setTimeout(
							function() {
								
								if (redactor_loaded > 0) {
									return;
								}
								
								redactor_loaded++;
								
								window.$R('#modal-content-html', 
									{
											lang: (window.championcore.lang_short || "en"),
											pastePlainText: true,
											paragraphize: true,
											replaceDivs: false,
											autoresize: true,
											minHeight: 300,
											buttonSource: true,
											imageUpload:      window.championcore.admin_url + '/inc/editor_images.php',
											imageManagerJson: window.championcore.admin_url + '/inc/data_json.php',
											fileUpload:       window.championcore.admin_url + '/inc/editor_files.php',
											fileManagerJson:  window.championcore.admin_url + '/inc/data_json.php',
											imageResizable: true,
											imagePosition: true,
											plugins: [
													'alignment',
													//'clips',
													'filemanager',
													'fontcolor',
													//'fontsize',
													//'fontfamily',
													//'fullscreen',
													
													//'imagemanager',
													'champion_redactor_imagemanager',
													
													'inlinestyle',
													//'properties',
													//'table',
													//'textdirection',
													'video' 
													//'widget',
													//'mail'
													
													//'codemirror'
													],
											/*
											codemirror: {
													lineNumbers: true,
													mode: 'xml',
													indentUnit: 4
											},
											*/
											clips: [
												['More', '##more##']
											],
											air: true,
											styles: true,
											/*
											toolbarFixed: true,
											toolbarFixedTarget: '#textfile',
											toolbarOverflow: true,
											*/
											
											/*
											clickToEdit:   true,
											clickToSave:   {title: championcore.translations.lang_save_button },
											clickToCancel: {title: championcore.translations.lang_cancel_button },
											callbacks: {
												clickStart: function (html) {
													console.log( 'redactor click to edit - start' );
												},
												clickSave: function (html) {
													self.onclick_save();
												},
												clickCancel: function (html) {
													self.onclick_cancel();
												}
											}
											*/
									}
								);
							},
							3000
						);
					}
					
				} else if (flag == false) {
					// close
					this.item = {};
					this.flag = false;
					this.id   = '';
					this.type = '';
					
					this.widget_id = '';
				}
			}
			
		},
		template: [
			'<div class="championcore-inline-edit-modal" v-show="flag">',
				'<div class="championcore-inline-edit-modal-container">',
					'<header><span class="close" v-on:click.stop.prevent="onclick_close"><i class="fa fa-times" aria-hidden="true"></i></span></header>',
					'<form class="championcore-inline-edit-modal-container--modal-content">',
						
						//author
						'<div class="row author" v-show="(type == \'blog\')">',
							'<label for="modal-content-author">Author:</label>',
							'<input type="text" id="modal-content-author" v-model="item.author" />',
						'</div>',
						
						//title
						'<div class="row title" v-show="(type == \'block\') || (type==\'page\')">',
							'<label for="modal-content-title">Title:</label>',
							'<input type="text" id="modal-content-title" v-model="item.title" />',
						'</div>',
						
						//date
						'<div class="row date" v-show="(type == \'blog\')">',
							'<label for="modal-content-date">Date:</label>',
							'<input type="text" id="modal-content-date" v-model="item.date" />',
						'</div>',
						
						//description
						'<div class="row description" v-show="(type == \'block\') || (type==\'page\')">',
							'<label for="modal-content-description">Description:</label>',
							'<textarea id="modal-content-description" v-model="item.description"></textarea>',
						'</div>',
						
						//html
						'<div class="row html">',
							'<label for="modal-content-html">Content:</label>',
							'<textarea id="modal-content-html">{{item.html}}</textarea>',
						'</div>',
						
						//tags
						'<div class="row tags" v-show="(type == \'blog\')">',
							'<label for="modal-content-tags">Tags:</label>',
							'<input type="text" id="modal-content-tags" v-model="item.tags" />',
						'</div>',
						
						
					'</form>',
					'<footer>',
						'<button v-on:click.stop.prevent="onclick_update(id,type,item)">Update</button>',
						'<button v-on:click.stop.prevent="onclick_close($event)">Cancel</button>',
				
					'</footer>',
				'</div>',
			'</div>'
		].join("\n")
	}
);
