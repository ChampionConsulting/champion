/**
 * blog featured image
 */

import {createApp } from 'vue';
import draggable    from 'vuedraggable';
import Sortable     from 'sortablejs';

import { GlobalMessageEventBase, useGlobalMessageBus } from 'ChampionCore/vue/composable/global_message_bus';

import ChampionCoreVueModal from 'ChampionCore/vue/component/modal';

import { AiImageGenerationEvent } from 'ChampionCore/widget/ai-image-generation/ai-image-generation-event';

/**
 * message bus
 */
const {global_message_bus} = useGlobalMessageBus();

console.log( 'blog featured image');

// start up
const app = createApp(
	{
		/**
		 * components used in this widget/component
		 */
		components: {
			'championcore-vue-modal': ChampionCoreVueModal
		},

		/**
		 * created callback
		 */
		created: function () {
			
			this.load_image_list();
		},
		
		/**
		 * app mounted callback
		 */
		mounted: function () {

			const self = this;

			console.log( 'blog featured image -> mounted');

			// attribute
			const node = document.querySelector( 'blog-featured-image' );

			if (node) {

				console.log( node.attributes );

				this.picked = node.attributes.meta_featured_image.value;
			}
			
			//this.picked = this.$el.attributes.meta_featured_image.value;

			// handle global message bus event - ai-image-generation
			/*
			 * custom event - storage change
			 */
			global_message_bus.on(
				'ai-image-generation',
				function (evnt) {

					console.log( 'blog_featured_image: ai-image-generation evennt', evnt );

					self.picked = evnt.url;
					self.picked = self.picked.replace( window.championcore.base_url, '');
				}
			);
		},

		/**
		 * lifecycle method
		 */
		setup (props) {

			console.log( 'blog featured image -> setup');

			return {
			};
		},
		
		/**
		 * element instance is attached to
		 *
		el:   '#blog-featured-image',
		*/
		
		/**
		 * state
		 */
		data: function () {
			return {
				image_list: [],
	
				modal_open: false,
				
				picked: '',
				
				selected_folder: '/'
			};
		},
		
		/**
		 * methods
		 */
		methods: {
			
			/*
			 * images to select
			 */
			load_image_list: function () {
				
				const self = this;
				
				window.fetch(
					window.championcore.admin_url + '/inc/data_json.php?filter=' + window.encodeURI(self.selected_folder),
				).then(
					function (response) {
						
						response.json().then(
							function (data) {
								
								self.image_list = data;
							}
						);
					}
				);
			},
			
			/**
			 * open the dialog
			 */
			onclick_open: function () {
				
				const self = this;
				
				self.modal_open = false;
				
				this.$nextTick(
					() => {
						self.modal_open = true;
					}
				);
			},
			
			/*
			* save data for an item
			*/
			onclick_select: function (item) {
				
				const self = this;
				
				self.picked = item.url;
				
				if (item.champion_type != 'folder') {
					// images
					self.$refs.meta_featured_image.value = self.picked;
					
					self.modal_open = false;
					
				} else {
					// folders
					self.selected_folder = self.picked.split('/content/media');
					
					self.selected_folder = self.selected_folder[1] ? self.selected_folder[1] : '/';
					self.selected_folder = self.selected_folder.replace('//', '/');
					
					self.load_image_list();
				}
			}
		},
		
		/**
		 * watched
		 */
		watch: {
		}
	}
);

app.mount( '#blog-featured-image' );

/**
 * exports
 */
window.championcore.blog_featured_image = {app: app};

export default app;
