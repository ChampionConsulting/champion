
import {defineComponent} from 'vue';

/**
 * modal component
 */
export default defineComponent(
	{
		/**
		 * component created callback
		 */
		created: function() {
			let self = this;
			
		},
		
		/**
		 * component state
		 */
		data: function() {
			return {
				flag: false
			};
		},
		
		/**
		 * methods in component
		 */
		methods: {
			/*
			 * event handler - close modal
			 */
			onclick_close: function(evnt: Event) {
				
				this.render(false);
			},
			
			/*
			 * show the modal
			 */
			render: function(flag: boolean) {
				
				let self = this;
				
				self.flag = flag;
				
				// open
				if (flag === true) {
					
					
				} else if (flag == false) {
					
				}
			}
			
		},
		
		/**
		 * component args
		 */
		 props: [
			'open'
		 ],
		
		/**
		 * component HTML
		 */
		template: `
<div class="championcore-vue-modal" v-show="flag">
	<div class="championcore-vue-modal-container">
		<header class="modal"><span class="close" v-on:click.stop.prevent="onclick_close"><i class="fa fa-times" aria-hidden="true"></i></span></header>
		<div class="modal-content">
			
			<slot></slot>
			
		</div>
		<footer class="modal"></footer>
	</div>
</div>
`,
		
		/**
		 * watch for component state or prop changes
		 */
		 watch: {
			 
			 open: function (new_state, old_state) {
				 
				 const self = this;
				 
				 // console.log( 'modal open', new_state, old_state);
				 
				 self.flag = new_state;
			 }
		 }
	}
);
