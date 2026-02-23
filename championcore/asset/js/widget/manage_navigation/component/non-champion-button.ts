
import swal from "sweetalert";

import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus, {MenuAddNonChampionEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
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
			onclick: function () {
				
				const self = this;
				
				const form = document.createElement('div');
				
				form.innerHTML = `
<div>
	<label for="#name" style="text-align: left;">${window.championcore.translations.lang_settings_navigation_non_champion_name}</label>
	<input style="width: 90%;" id="name" type="text" name="name" value="" placeholder="${window.championcore.translations.lang_settings_navigation_non_champion_name}" />
</div>
<div>
	<label for="#url" style="text-align: left;">${window.championcore.translations.lang_settings_navigation_non_champion_url}</label>
	<input style="width: 90%;" id="url" type="text" name="url"  value="" placeholder="${window.championcore.translations.lang_settings_navigation_non_champion_url}" />
</div>
<div>
	<label for="#open_in_new_tab" style="text-align: left;">
		<input id="url"  type="checkbox" name="open_in_new_tab" value="1" checked />
		${window.championcore.translations.lang_settings_navigation_non_champion_open_in_new_tab}
	</label>
</div>
`;

				const fe : HTMLElement = form;
				
				const deferred = swal(
					{
						title: window.championcore.translations.lang_settings_navigation_non_champion_page,
						text:  window.championcore.translations.lang_settings_navigation_non_champion_page,
						content: {
							element: fe
						},
						buttons: {
							cancel: {
								closeModal: true
							},
							confirm: {
								closeModal: true
							}
						}
					}
				);
				
				deferred.then(
					function (inputValue: string | boolean) {
						
						const dom_open_in_new_tab = form.querySelector( 'input[name="open_in_new_tab"]' ) as HTMLInputElement;
						const dom_name            = form.querySelector( 'input[name="name"]' )            as HTMLInputElement;
						const dom_url             = form.querySelector( 'input[name="url"]'  )            as HTMLInputElement;

						const open_in_new_tab = (dom_open_in_new_tab) ? dom_open_in_new_tab.value : '0';
						const name            = (dom_name)            ? dom_name.value            : '';
						const url             = (dom_url)             ? dom_url.value             : '';
						
						// close
						if (inputValue === false) {
							return false;
						}
						
						if (self.parent) {
							message_bus.emit( 'menu-add-non-champion', new MenuAddNonChampionEvent(self.parent, name, url, open_in_new_tab) );
						}

						return true;
					}
				);
			}
		},
		
		/**
		 * props
		 */
		props: {
			parent: Menu
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
<!-- start non-champion-button -->
<div class="non-champion-button btn" v-on:click.stop.prevent="onclick">
	<i class="fa fa-plus"></i>
	{{ translate('lang_settings_navigation_non_champion_page') }}
</div>
<!-- end non-champion-button -->
`,

		/**
		 * watched properties
		 */
		watch: {
			
			item: {
				deep: true,
				immediate: true,
				
				handler: function (new_val, old_val) {
					
					console.log( 'item watched', new_val );
				}
			}
		}
	}
);
