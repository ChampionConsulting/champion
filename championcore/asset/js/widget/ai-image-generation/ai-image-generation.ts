/**
 * AI image generation
 */

import {createApp, ref} from 'vue';

import { GlobalMessageEventBase, useGlobalMessageBus } from 'ChampionCore/vue/composable/global_message_bus';
import ChampionCoreVueModal from 'ChampionCore/vue/component/modal';

import { AiImageGenerationEvent } from 'ChampionCore/widget/ai-image-generation/ai-image-generation-event';

import ImagePickerComponent  from 'ChampionCore/widget/ai-image-generation/image-picker';
import PromptSourceComponent from 'ChampionCore/widget/ai-image-generation/prompt-source';

import {useAiImageGenerationState} from 'ChampionCore/widget/ai-image-generation/state';

/**
 * message bus
 */
const {global_message_bus} = useGlobalMessageBus();

/**
 * state
 */
// state
const {
	base_url,

	image_list,
	modal_open,
	picked,
	track_prompt,
	track_source,

	open_settings_text,

	translation_error,

	// message
	show,
	message,
	message_type,
	is_message_error,
	is_message_info,
	is_message_warn
} = useAiImageGenerationState();

const header      = ref(window.championcore.config?.ai_image_generation.header      ?? 'OpenAI / ChatGPT image generation');
const promptLabel = ref(window.championcore.config?.ai_image_generation.promptLabel ?? 'Prompt');
const sourceLabel = ref(window.championcore.config?.ai_image_generation.sourceLabel ?? 'Source');

// start up
const app = createApp(
	{
		/**
		 * components used in this widget/component
		 */
		components: {
			'championcore-vue-modal': ChampionCoreVueModal,

			'image-picker':  ImagePickerComponent,
			'prompt-source': PromptSourceComponent
		},

		/**
		 * lifecycle method
		 */
		mounted () {

			// unpack for props in child components
			window.setTimeout(
				() => {
					if (window.championcore.config) {

						this.base_url = window.championcore.config.ai_image_generation.base_url;
						
						this.header = window.championcore.config.ai_image_generation.header;

						this.track_prompt = window.championcore.config.ai_image_generation.prompt;
						this.track_source = window.championcore.config.ai_image_generation.source;

						this.promptLabel = window.championcore.config.ai_image_generation.promptLabel;
						this.sourceLabel = window.championcore.config.ai_image_generation.sourceLabel;

						this.open_settings_text = window.championcore.config.ai_image_generation.open_settings_text;

						this.translation_error  = window.championcore.config.ai_image_generation.translation_error;
					}
				},
				1000
			);
		},

		/**
		 * lifecycle method
		 */
		setup () {

			return {

				base_url,

				header,

				track_prompt,
				track_source,

				promptLabel,
				sourceLabel,

				open_settings_text,

				translation_error,

				// message
				show,
				message,
				message_type,
				is_message_error,
				is_message_info,
				is_message_warn
			};
		},

		/**
		 * html
		 */
		template: `
<div class="championcore-ai-image-generation">

	<div class="championcore-ai-image-generation--header">
		<h3>{{header}}</h3>

		<a class="btn" v-bind:href="base_url + '/admin/index.php?p=settings'" target="_blank">
			{{open_settings_text}}
		</a>
	</div>

	<prompt-source
		v-bind:prompt = "track_prompt"
		v-bind:promptLabel = "promptLabel"
		v-bind:source = "track_source"
		v-bind:sourceLabel = "sourceLabel"
	></prompt-source>

	<image-picker></image-picker>

	<template v-if="show == 'progress'">
		<div class="progress">
			<div class="bar"></div>
		</div>
	</template>

	<template v-if="show == 'message'">
		<p v-bind:class="{info: is_message_info, warn: is_message_warn, error: is_message_error}">
			{{ message }}
		</p>
	</template>
</div>
`
	}
);

app.mount( '#ai-image-generation' );

/**
 * exports
 */
window.championcore.ai_image_generation = {app: app};

//export default app;
