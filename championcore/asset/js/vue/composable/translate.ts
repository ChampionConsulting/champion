
import { computed } from "vue";

/**
 * translate a string. Expose as computed
 */
export function useTranslate () {
	
	return (
		(arg: string) : string  => {
	
			const result = window.championcore._(arg);
			
			return result;
		}
	);
}
