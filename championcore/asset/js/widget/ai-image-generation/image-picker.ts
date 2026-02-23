/**
 * AI image generation
 */

import {createApp} from 'vue';

import { GlobalMessageEvent, GlobalMessageEventBase, useGlobalMessageBus } from 'ChampionCore/vue/composable/global_message_bus';
import ChampionCoreVueModal from 'ChampionCore/vue/component/modal';

import { AiImageGenerationEvent } from 'ChampionCore/widget/ai-image-generation/ai-image-generation-event';

import {useAiImageGenerationState} from 'ChampionCore/widget/ai-image-generation/state';

/**
 * expand GlobalMessageEvent
 *
type GlobalMessageEvent {
	'ai-image-generation': AiImageGenerationEvent
}
*/

/**
 * message bus
 */
const {global_message_bus} = useGlobalMessageBus();

/**
 * state
 */
const {
	image_list,
	picked
} = useAiImageGenerationState();

/**
 * method - click handler
 * select an image
 */
function on_click_image (item: any) {

	picked.value = item.url;

	console.log( 'AiImageGeneration -> ImagePicker -> on_click_image', item );

	global_message_bus.emit(
		'ai-image-generation',
		new AiImageGenerationEvent(
			item.url
		)
	);
}

// start up
export default {
	/**
	 * props
	 */
	props: [],

	/**
	 * lifecycle method
	 */
	setup (props: any) {

		return {

			image_list,
			picked,

			on_click_image
		};
	},

	/**
	 * html
	 */
	template: `
<div v-if="image_list.length > 0" class="image_list">
	<template v-for="(item, index) in image_list">
		<img alt="AI image" v-bind:src="item.url" v-on:click="on_click_image(item)" v-bind:class="{picked: (picked == item.url)}" />
	</template>
</div>
`
}
