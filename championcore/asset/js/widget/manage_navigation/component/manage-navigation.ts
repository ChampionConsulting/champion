
import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus from 'ChampionCore/widget/manage_navigation/message-bus';
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
				
				parent: false
			}
		},
		
		/**
		 * methods
		 */
		methods: {
		},
		
		/**
		 * props
		 */
		props: [
			'menu',
			'open_menus'
		],

		/**
		 * lifecycle hook
		 */
		setup () {

			const translate = useTranslate();

			return { translate };
		},
		
		template: `
<!-- start manage-navigation -->
<form name="textfile"
      class="manage-navigation"
	  method="post"
	  action="index.php?p=manage_navigation&method=put"
	  v-on:submit.prevent.stop>
	  
	<h4>{{ translate('lang_settings_navigation_menus') }}</h4>

	<p>{{ translate('lang_settings_navigation_text') }}</p>

	<hr />
	<div class="row right-justify">
		<save-button></save-button>
	</div>
	<hr />
	
	<menu-list v-bind:menu="menu" v-bind:open_menus="open_menus" v-bind:parent="parent"></menu-list>
	
	<add-menu></add-menu>

</form>
<!-- end manage-navigation -->
`
	}
);
