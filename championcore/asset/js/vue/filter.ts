
/**
 * translate a string
 */
export function translate (arg: string) : string {
	
	const result = window.championcore._(arg);
	
	return result;
}
