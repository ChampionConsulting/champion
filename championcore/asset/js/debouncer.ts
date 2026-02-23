
/**
 * payload function to debounce
 */
export type DebouncerPayloadFunction = () => void;

/**
 * debounce
 */
export class Debouncer {

	/**
	 * keep track of the timeout
	 */
	protected timer = 0;

	/**
	 * timeout in milliseconds
	 */
	protected timeout = 1000;

	/**
	 * constructor
	 */
	public constructor (timeout : number) {
		this.timeout = timeout;
	}

	/**
	 * debounce something
	 */
	public debounce (payload : DebouncerPayloadFunction) : void {

		// clear timer
		if (this.timer > 0) {
			window.clearTimeout(this.timer);
			this.timer = 0;
		}

		// start timer
		this.timer = window.setTimeout(
			() => {
				this.timer = 0;

				payload();
			},
			this.timeout
		);
	}
}
