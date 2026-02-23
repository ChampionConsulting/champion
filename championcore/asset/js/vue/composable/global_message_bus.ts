
import mitt from 'mitt';

import { AiImageGenerationEvent } from 'ChampionCore/widget/ai-image-generation/ai-image-generation-event';

/**
 * component events
 */
export class GlobalMessageEventBase {
};

/**
 * events
 */
export type GlobalMessageEvent = {
	'ai-image-generation': AiImageGenerationEvent
};

// the event bus
const default_global_message_bus = mitt<GlobalMessageEvent>();

/**
 * global message bus
 */
export function useGlobalMessageBus () {

	if (!window.championcore.global_message_bus) {
		window.championcore.global_message_bus = default_global_message_bus;
	}

	const global_message_bus = window.championcore.global_message_bus;
	
	return { global_message_bus };
}
