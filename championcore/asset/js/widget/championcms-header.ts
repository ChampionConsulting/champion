
/**
 * custom component
 */
class ChampionCmsHeader extends HTMLElement {

	/**
	 * attach shadow root
	 */
	constructor () {

		super();

		this.attachShadow( {mode: 'open'} );
	}

	/**
	 * list of component attributes
	 */
	static get observedAttributes () {
		return [
		];
	}

	/**
	 * handle updates in attributes
	 */
	attributeChangedCallback (attribute_name: string, old_value: any, new_value: any) {
	}


	/**
	 * wire up
	 */
	connectedCallback () {

		this.render();
	}

	/**
	 * render the component
	 */
	protected render () : void {
		
		if (this.shadowRoot) {
			
			this.shadowRoot.innerHTML = `
<!-- -->
<!-- -->
<!-- -->
<style>
	.championcms-header {
		position: absolute;

		top: 0;
		left: 0;
		width: 95%;

		z-index: 10000;
	}
</style>

<div class="championcms-header">
	<slot></slot>
</div>
<!-- -->
<!-- -->
<!-- -->
`;

			// Add in the outer CSS
			const outer_css = `
#nav {
	text-align: right;
	box-sizing: border-box;

	background-color: rgba(220, 220, 220, 0.7);

	padding-right: 3em;
}
#menu-button { display: none; }
#nav a {
	display: block;
	padding: 1em 0;
	color: #202e39;
	text-decoration: none;
	text-transform: capitalize;
}
#nav li:hover > a,
#nav a:hover,
#nav a.active {
	color: #1EC1C3;
}
#nav > ul {
	margin: 0;
	padding: 0;
	z-index: 999;
}
#nav > ul > li {
	display: inline-block;
	position: relative;
}
#nav > ul > li:not(:last-child) {
	margin-right: 25px;
}
#nav > ul ul {
	position: absolute;
	left: -9999px;
	top: calc(100% + 15px);
	width: 200px;
	visibility: hidden;
	opacity: 0;
	transition: opacity .2s, top .2s;
	background: #fff;
	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
	padding: 0;
	list-style: none;
	text-align: left;
}
#nav > ul > li:hover ul {
	left: 0;
	top: 100%;
	visibility: visible;
	opacity: 1;
	z-index: 99999;
}
#nav > ul ul li {
	border-bottom: 1px solid rgb(235, 235, 235);
}
#nav > ul ul li a {
	padding: 10px 15px;
}

#nav > ul > li:hover ul > li:hover ul {
	
	position: relative;
	left: -30px;
	
	background-color: #ccc;
}
`;

			const css_node = document.createElement( 'style' );

			css_node.innerHTML = outer_css;

			document.querySelector('championcms-header')?.appendChild( css_node );
		}
	}
}

/**
 * wire into page
 */
customElements.define( 'championcms-header', ChampionCmsHeader );
