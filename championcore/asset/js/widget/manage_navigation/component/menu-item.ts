
import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import { Debouncer, DebouncerPayloadFunction } from 'ChampionCore/debouncer';

import message_bus, {MeMi, MenuItemActivateToggleEvent, MenuItemDeleteEvent, MenuItemDeleteToggleEvent, MenuItemMoveDownEvent, MenuItemMoveUpEvent, MenuItemNameChangedEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * vue component
 */
export default defineComponent(
	{

		/**
		 * computed
		 */
		computed: {

			/**
			 * name or name_changed
			 */
			nomen: function () {

				let result = '';

				if (this.item) {

					result = (this.item.name == this.item.name_changed) ? this.item.name : this.item.name_changed;
				}

				return result;
			}
		},
		
		/**
		 * state
		 */
		data () {
			return {

				name_debouncer : false as (false | Debouncer)
			}
		},
		
		/**
		 *
		 */
		methods: {
			
			/**
			 * toggle activation
			 */
			onclick_active: function () {
				
				if (this.menu && this.item) {
					message_bus.emit( 'menu-item-activate-toggle', new MenuItemActivateToggleEvent( this.menu, this.item) );
				}
			},
			
			/**
			 * delete a menu item
			 */
			onclick_nuke: function () {
				
				if (this.menu && this.item) {
					message_bus.emit( 'menu-item-delete-toggle', new MenuItemDeleteToggleEvent( this.menu, this.item ) );
				}
			},
			
			/**
			 * delete a menu item
			 */
			onclick_menu_item_delete: function () {
				
				if (this.menu && this.item) {
					message_bus.emit( 'menu-item-delete', new MenuItemDeleteEvent( this.menu, this.item ) );
				}
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
			 * pick up on name change in the contenteditable
			 */
			on_input_name: function (evnt: Event, item: MeMi) {
				
				// debounce this
				if (this.name_debouncer === false) {
					this.name_debouncer = new Debouncer(3000);
				}

				this.name_debouncer.debounce(
					() => {
						console.log( 'contenteditable input change', item , evnt);

						const node = evnt.target as HTMLElement;

						const text = node.innerText;

						console.log( 'contenteditable input change -> text', text);

						(item as MenuItem).name_changed = text;

						message_bus.emit( 'menu-item-name-changed', new MenuItemNameChangedEvent( item ) );
					}
				);
			}
		},
		
		/**
		 * props
		 */
		props: {
			'item': MenuItem,
			'menu': Menu
		},

		/**
		 * lifecycle hook
		 */
		setup () {

			const translate = useTranslate();

			return { translate };
		},
		
		/**
		 * html
		 */
		template: `
<!-- start menu-item -->
<div class="menu-item">
	<div class="menu-item-container">
		<div class="row">
			<div class="name" contenteditable="true" v-on:input="on_input_name($event, item)">{{ nomen }}</div>
			
			<div class="url">{{ item.url }}</div>
			
			<div class="button-block">
				<label class="label-checkbox" v-show="menu.name != 'pending'">
					{{ translate('lang_settings_navigation_activate') }}
					<input class="active" type="checkbox" v-model="item.active" v-on:click="onclick_active" />
				</label>
				<label class="label-checkbox" v-show="menu.name != 'pending'">
					{{ translate('lang_del_button') }}:
					<input class="nuke" type="checkbox" v-model="item.nuke"  v-on:click="onclick_nuke" />
				</label>

				<div class="up btn" v-on:click="onclick_menu_item_up(item)">
					{{ translate('lang_settings_navigation_up') }}
					<i class="fa fa-arrow-up"></i>
				</div>

				<div class="down btn" v-on:click="onclick_menu_item_down(item)">
					{{ translate('lang_settings_navigation_down') }}
					<i class="fa fa-arrow-down"></i>
					</div>
				
				<div class="delete btn" v-on:click="onclick_menu_item_delete" v-show="menu.name != 'pending'">
					{{ translate('lang_del_button') }}
					<i class="fa fa-trash"></i>
				</div>
			</div>
		</div>
	</div>
	
</div>
<!-- end menu-item -->
`,

		/**
		 * watched properties
		 */
		watch: {
			
			item: {
				deep: true,
				immediate: true,
				
				handler: function (new_val, old_val) {
					
					// console.log( 'item watched', new_val );
				}
			}
		}
	}
);
