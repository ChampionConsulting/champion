
import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus, {MenuAddEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';

/**
 * vue component
 */
export default defineComponent(
	{
		/**
		 * state
		 */
		data () {
			return {
				menu_item_new: ''
			}
		},
		
		/**
		 *
		 */
		methods: {
			
			/**
			 * add a menu item
			 */
			onclick_menu_item_add: function (evnt: Event) {
				
				message_bus.emit( 'menu-add', new MenuAddEvent(this.parent, this.menu_item_new) );
			}
		},
		
		/**
		 * props
		 */
		props: [
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
		 * html
		 */
		template: `
<!-- start add-menu -->
<form class="add-menu" method="post" action="#" v-on:submit.prevent.stop>
	
	<label><i class="fa fa-plus" aria-hidden="true"></i> {{ translate('lang_settings_navigation_add_menu') }}: </label>
	<input v-model="menu_item_new" v-bind:placeholder="translate('lang_settings_navigation_add_menu')" />
	
	<button class="btn" type="submit" name="add_menu_item" v-on:click.prevent.stop="onclick_menu_item_add">{{ translate('lang_save') }}</button>
</form>
<!-- end add-menu -->
`
	}
);
