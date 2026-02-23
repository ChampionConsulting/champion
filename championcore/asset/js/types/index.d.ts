/// <reference types="jquery"/>

export {}

/**
 * window options
 */
export interface ChampionCore {

	admin_url: string;
	base_url:  string;

	config?: Record<string, any>,

	global_message_bus: any,

	lang_short: string,

	translations: Record<string, string>;

	//
	_: any,

	// widgets
	ai_image_generation?: any,
	inline_edit?:         any,
	manage_navigation?:   any,
	championcms_header?:     any
}

/**
 * expand the window
 */
declare global {

	interface Window {

		championcore: ChampionCore;

		$R: any // redactor

		RedactorX: any // redactorX
	}
}
