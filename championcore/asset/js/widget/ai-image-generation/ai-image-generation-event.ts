/**
 * the global message AI event
 */

import { GlobalMessageEventBase} from 'ChampionCore/vue/composable/global_message_bus';

export class AiImageGenerationEvent extends GlobalMessageEventBase {

	public url: string = '';

	/**
	 * constructor
	 */
	constructor (url: string) {

		super();

		this.url = url;
	}
}