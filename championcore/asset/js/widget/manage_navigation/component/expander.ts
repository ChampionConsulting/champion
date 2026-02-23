
import {defineComponent} from 'vue';

import {useTranslate} from 'ChampionCore/vue/composable/translate';

import message_bus, {TrackUiMenuCloseEvent, TrackUiMenuOpenEvent} from 'ChampionCore/widget/manage_navigation/message-bus';
import storage     from 'ChampionCore/widget/manage_navigation/storage';

import Menu     from 'ChampionCore/widget/manage_navigation/model/menu';
import MenuItem from 'ChampionCore/widget/manage_navigation/model/menu_item';

/**
 * show hide content
 */
export default defineComponent(
	{
		/**
		 * state
		 */
		data () {
			return {
				show: this.open
			}
		},
		
		/**
		 *
		 */
		methods: {
			
			/**
			 * add a menu item
			 */
			onclick_show: function (evnt: Event) {
				
				this.show = !this.show;
				
				// keep track of whats open/closed so re-renders keep open menus open
				if (this.control_item) {

					if (this.show) {
						message_bus.emit( 'track-ui-menu-open',  new TrackUiMenuOpenEvent( (this.control_item as Menu) ) );
					} else {
						message_bus.emit( 'track-ui-menu-close', new TrackUiMenuCloseEvent( (this.control_item as Menu) ) );
					}
				}
			}
		},
		
		/**
		 * props
		 */
		props: {
			'control_item': Object,
			'type': String,
			'open': Boolean
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
<!-- start expander -->
<div class="expander">
	
	<div class="expander-toggle" v-on:click.stop.prevent="onclick_show">
		<template v-if="type == 'arrow'">
			<i class="fa fa-arrow-circle-down"  v-if="show"></i>
			<i class="fa fa-arrow-circle-right" v-if="!show"></i>
		</template>
		
		<template v-if="type == 'toggle'">
			<i class="fa fa-toggle-on"  v-if="show"></i>
			<i class="fa fa-toggle-off" v-if="!show"></i>

			<template v-if="show">
				&emsp; {{ translate('lang_settings_navigation_expander_collapse') }}
			</template>
			<template v-if="!show">
				&emsp; {{ translate('lang_settings_navigation_expander_expand') }}
			</template>
		</template>
	</div>
	
	<div class="expander-content" v-show="show">
		<slot></slot>
	</div>
	
</div>
<!-- end expander -->
`,

		/**
		 * watched properties
		 */
		watch: {
			
			//deep: true,
			//immediate: true,
			
			open: function (new_val, old_val) {
				
				if (this.control_item) {
					//console.log( 'watch open', this.control_item.name, new_val );
				}
				
				this.show = new_val;
			}
		}
	}
);

