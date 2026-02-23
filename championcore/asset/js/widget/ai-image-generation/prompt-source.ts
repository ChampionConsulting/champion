/**
 * AI image generation
 */

import {createApp} from 'vue';

import { GlobalMessageEventBase, useGlobalMessageBus } from 'ChampionCore/vue/composable/global_message_bus';
import ChampionCoreVueModal from 'ChampionCore/vue/component/modal';

import { AiImageGenerationEvent } from 'ChampionCore/widget/ai-image-generation/ai-image-generation-event';

import {useAiImageGenerationState} from 'ChampionCore/widget/ai-image-generation/state';

/**
 * message bus
 */
const {global_message_bus} = useGlobalMessageBus();

/**
 * state
 */
const {
	image_list,
	track_prompt,
	track_source,

	// message
	message,
	message_type,
	show
} = useAiImageGenerationState();

/**
 * method - click handler
 * images to select
*/
function on_click_generate () {

	// message
	show.value = 'progress';

	// form data
	const form_data = new FormData();
	form_data.set( 'prompt', track_prompt.value );
	form_data.set( 'source', track_source.value );

	// pick the url
	let url = window.championcore.admin_url + '/index.php?p=openai/image-generation';

	if (track_source.value == 'stable-diffusion') {

		url = window.championcore.admin_url + '/index.php?p=stable-diffusion/image-generation';
	}

	// poll
	window.fetch(
		url,
		{
			method: 'POST',
			body:   form_data
		}
	).then(
		(response) => {

			// detect error
			if (!response.ok) {
				throw new Error('Request has failed');
			}

			let result : any = {
				image_list:   [],
				message:      'An error has occurred',
				message_type: 'error',
				show:         'message'
			};

			try {

				result = response.json();

			} catch (error) {

				console.log( 'error picked up on', error);
			}

			return result;
		}
	).then(
		(data) => {
			
			image_list.value = data.image_list;

			// message
			message.value      = data.message;
			message_type.value = data.message_type;
			show.value         = ((data.message.length > 0) ? 'message' : '');
		}
	).catch(
		(error) => {
			// display an error
			image_list.value = [];

			message.value      = 'An error has occurred when loading the generated image';
			message_type.value = 'error';
			show.value         = 'message';
		}
	);
};

// start up
export default {

	/**
	 * components used in this widget/component
	 */
	components: {
		'championcore-vue-modal': ChampionCoreVueModal
	},

	/**
	 * props
	 */
	props: {
		prompt:      String,
		promptLabel: String,
		source:      String,
		sourceLabel: String
	},

	/**
	 * lifecycle method
	 */
	setup (props: any) {

		console.log( 'PromptSourceComponent -> props', props );

		// unpack props
		track_prompt.value = props.prompt;
		track_source.value = props.source;

		return {

			track_prompt,
			track_source,

			// message
			message,
			message_type,
			show,

			// methods
			on_click_generate
		};
	},

	/**
	 * html
	 */
	template: `
<div class="prompt-source">
	<div class="prompt">
		<label>{{ promptLabel }}</label>
		<input type="text" name="ai_prompt" v-model="track_prompt" />
	</div>
	<div class="source">
		<label>{{ sourceLabel }}</label>
		<select name="ai_source" v-model="track_source">
			<option value="openai">ChatGPT</option>
			<option value="stable-diffusion">Stable Diffusion</option>
		</select>
	</div>
	<div class="button" v-on:click="on_click_generate">Generate Images</div>
</div>
`
};
