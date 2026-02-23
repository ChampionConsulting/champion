
import {defineComponent} from 'vue';

import draggable from 'vuedraggable';
//import Sortable  from 'sortablejs';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus, {MeMi, DnDChangeEvent, MenuActivateToggleEvent, MenuDeleteEvent, MenuDeleteToggleEvent, MenuItemMoveDownEvent, MenuItemMoveUpEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * vue component
 */
export default defineComponent(
	{

		/**
		 * components used in this component
		 */
		components: {
			'draggable': draggable
		},

		/**
		 * computed
		 */
		computed: {

			/**
			 * the translated menu name
			 */
			display_menu_name: function () {
				
				return (arg: string) : string => {

					let result = arg;

					switch (result) {
						
						case 'all':
							result = this.translate('lang_settings_navigation_menu_all');
							break;
							
						case 'pending':
							result = this.translate('lang_settings_navigation_menu_pending');
							break;
					}

					console.log( 'display_menu_name', arg, result );

					return result;
				};
			},

			/**
			 * dummy control item
			 */
			dummy_control_item: function () {

				return () => new Menu('dummy', '', false, false, [])
			}
		},
		
		/**
		 * state
		 */
		data () {
			return {
				
				data_list: []
			}
		},
		
		/**
		 *
		 */
		methods: {
			
			/**
			 * check that menu item is a MenuItem
			 */
			is_menu_item_type: function (item: any) : boolean{
				return (item instanceof MenuItem);
			},
			
			/**
			 * check that menu is on the open menu list
			 */
			is_menu_open: function (item: MeMi) : boolean {
				
				/*
				const result =
					this.is_menu_type(item) &&
					(this.open_menus.indexOf(item) > 0);
				*/
				
				// console.log( 'is_menu_open', item.name, item, this.open_menus );
				
				let result = false;
				
				if (this.is_menu_type(item)) {
					
					for (let k = 0; k < this.open_menus.length; k++) {
						
						const tmp = this.open_menus[k];
						
						// console.log( item.name, tmp.name );
						
						if (item.equals(tmp)) {
							result = true;
							break;
						}
					}
				}
				
				return result;
			},
			
			/**
			 * check that menu item is a Menu
			 */
			is_menu_type: function (item: MeMi) : boolean {
				return (item instanceof Menu);
			},
			
			/**
			 * toggle activation
			 */
			onclick_active: function (item: MeMi) {
				
				message_bus.emit( 'menu-activate-toggle', new MenuActivateToggleEvent(item) );
			},
			
			/**
			 * delete a menu item
			 */
			onclick_nuke: function (item: MeMi) {
				
				message_bus.emit( 'menu-delete-toggle', new MenuDeleteToggleEvent(item) );
			},
			
			/**
			 * delete a menu item
			 */
			onclick_menu_item_delete: function (item: MeMi) {
				
				message_bus.emit( 'menu-delete', new MenuDeleteEvent(item) );
			},

			/**
			 * move a menu item
			 */
			onclick_menu_item_down: function (item: MeMi) {

				message_bus.emit( 'menu-item-move-down', new MenuItemMoveDownEvent( item ) );
			},

			/**
			 * move a menu item
			 */
			onclick_menu_item_up: function (item: MeMi) {
				
				message_bus.emit( 'menu-item-move-up', new MenuItemMoveUpEvent( item ) );
			},
			
			/**
			 * DnD done
			 */
			on_dnd_change_menu: function (arg: MeMi) {

				console.log( 'on_dnd_change-menu', arg );
				console.log( this.menu );
				
				message_bus.emit( 'dnd-change', new DnDChangeEvent(arg) ); // this.menu
			}
		},
		
		/**
		 * props
		 */
		props: [
			'menu',
			'open_menus',
			'parent'
		],

		/**
		 * lifecycle hook
		 */
		setup () {

			const translate = useTranslate();

			return { translate };
		},
		
		/**
		 * html  <!-- v-for="item in menu.menu_items" :key="item.name" -->  :options="{draggable:'.menu-box'}"
		 */
		template: `
<!-- start menu-list -->
<div class="menu-list">

	<draggable
		class="draggble"
		v-model="menu.menu_items"
		item-key="name"
		group="{name: menu.name}"
		v-on:change="on_dnd_change_menu">

		<template #item="{element}">
		
			<div class="menu-box">
				<div class="menu-box-container">
					
					
					<template v-if="is_menu_type(element)">

						<div class="row header">

							<div class="name">{{ display_menu_name(element.name) }}</div>

							<div class="button-block">
								<label class="label-checkbox" v-show="element.name != 'pending'">
									{{ translate('lang_settings_navigation_activate') }}
									<input class="active" type="checkbox" v-model="element.active" v-on:click="onclick_active(element)" />
								</label>
								<label class="label-checkbox" v-show="element.name != 'pending'">
									{{ translate('lang_del_button') }}:
									<input class="nuke" type="checkbox" v-model="element.nuke"  v-on:click="onclick_nuke(element)" />
								</label>
								
								<div class="up btn" v-on:click="onclick_menu_item_up(menu)" v-show="menu.name != 'pending'">
									{{ translate('lang_settings_navigation_up') }}
									<i class="fa fa-arrow-up"></i>
								</div>

								<div class="down btn" v-on:click="onclick_menu_item_down(menu)" v-show="menu.name != 'pending'">
									{{ translate('lang_settings_navigation_down') }}
									<i class="fa fa-arrow-down"></i>
								</div>
								
								<div class="delete btn" v-on:click="onclick_menu_item_delete(element)" v-show="element.name != 'pending'">
									{{ translate('lang_del_button') }}
									<i class="fa fa-trash"></i>
								</div>
							</div>
						</div>

						<div class="row">
							<expander type="toggle" v-bind:control_item="element" v-bind:open="is_menu_open(element)">
								<menu-list v-bind:menu="element" v-bind:open_menus="open_menus" v-bind:parent="menu"></menu-list>
							</expander>
						</div>
						
						<div class="row">
							<footer>
								<expander type="arrow" v-bind:control_item="dummy_control_item()" v-bind:open="false">
									<add-menu v-bind:parent="element"></add-menu>
								</expander>
								
								<!-- save-button></save-button -->
								
								<non-champion-button v-bind:parent="element"></non-champion-button>
							</footer>
						</div>
					</template>
					
					<template v-if="is_menu_item_type(element)">
						<menu-item v-bind:menu="menu" v-bind:item="element">
						</menu-item>
					</template>

				</div>
			</div>
		</template>
	</draggable>
	
</div>
<!-- end menu-list -->
`,

		/**
		 * watched properties
		 */
		watch: {
			
			//deep: true,
			//immediate: true,
			
			menu: function (new_val, old_val) {
				
				this.data_list = new_val.menu_items;
			}
		}
	}
);
