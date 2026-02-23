/**
 * AI image generation - centralise the state management
 */

import {computed, createApp, ref } from 'vue';
import type { Ref } from 'vue';

/**
 * build out the state
 */
const base_url = ref('');

const image_list = ref( [] );

const modal_open = ref( false );

const picked     = ref( '' );

const track_prompt = ref( '' );
const track_source = ref( '');

const open_settings_text = ref('');

const translation_error = ref('An error has occurred');

// messages
const show         = ref('');
const message      = ref('');
const message_type = ref('info');

// computed
const is_message_info  = computed( () => { return (message_type.value == 'info');  } );
const is_message_warn  = computed( () => { return (message_type.value == 'warn');  } );
const is_message_error = computed( () => { return (message_type.value == 'error'); } );

/**
 * the app state
 */
export function useAiImageGenerationState () {

	return {
		base_url,
		image_list,
		modal_open,
		picked,
		track_prompt,
		track_source,

		open_settings_text,
		translation_error,

		show,
		message,
		message_type,
		is_message_error,
		is_message_info,
		is_message_warn
	};
}
