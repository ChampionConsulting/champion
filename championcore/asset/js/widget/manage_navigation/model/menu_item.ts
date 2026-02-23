/**
 * imports
 */
// none

export default class MenuItem {

	/**
	 * @prop {string}
	 */
	public name: string;

	/**
	 * @prop {string}
	 */
	public name_changed: string;

	/**
	 * @prop {string}
	 */
	public open_in_new_tab: string;

	/**
	 * @prop {string}
	 */
	public url: string;

	/**
	 * @prop {boolean}
	 */
	public active: boolean;

	/**
	 * @prop {boolean}
	 */
	public nuke: boolean;

	/**
	 * menu item storage
	 * @param {string} name
	 * @param {string} url
	 * @param {boolean} active
	 * @param {boolean} nuke
	 * @param {string}  open_in_new_tab
	 */
	constructor (name: string, url: string, active: boolean, nuke: boolean, open_in_new_tab: string) {
		
		this.name            = name;
		this.open_in_new_tab = open_in_new_tab;
		this.url             = url;
		this.active          = active;
		this.nuke            = nuke;

		// not persisted
		this.name_changed = name;
	};

	/**
	 * comparison operator
	 * 
	 * @param {any} arg
	 * @return {boolean}
	 */
	public equals (arg: MenuItem) : boolean {
		
		let result = false;
		
		if (arg instanceof MenuItem) {
			
			result = ((this.name == arg.name) && (this.url == arg.url) && (this.open_in_new_tab == arg.open_in_new_tab));
		}
		
		return result;
	};

	/**
	 * pretty print the contents
	 * 
	 * @param {int} offset
	 * @return {string}
	 */
	public pretty_print (offset = 0) : string {
		
		const spacer = "=> ".repeat(offset);
		
		const result = spacer + "MenuItem " + JSON.stringify(this) + "\n";
		
		return result;
	}

	/**
	 * set/clear the active flag for menu
	 * 
	 * @param {boolean} flag
	 * @return {MenuItem} for fluent interface
	 */
	public set_active (flag: boolean) : MenuItem {
		
		this.active = flag;
		
		return this;
	}

	/**
	 * set/clear the deleted flag for menu
	 * 
	 * @param {boolean} flag
	 * @return {MenuItem} for fluent interface
	 */
	public set_nuked (flag: boolean) : MenuItem {
		
		this.nuke = flag;
		
		//console.log( 'set_nuked', this.name, this.nuke );
		
		return this;
	}
}
