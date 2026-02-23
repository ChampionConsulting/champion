
import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus, {StateSaveEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

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
			}
		},
		
		/**
		 *
		 */
		methods: {
			
			/**
			 * toggle activation
			 */
			onclick_save: function () {
				
				message_bus.emit( 'state-save', new StateSaveEvent() );
			}
		},
		
		/**
		 * props
		 */
		props: {
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
<!-- start save-button -->
<div class="save-button btn" v-on:click.stop.prevent="onclick_save">
	<i class="fa fa-save"></i>
	{{ translate('lang_save') }}
</div>
<!-- end save-button -->
`
	}
);
