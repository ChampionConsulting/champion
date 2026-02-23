(function($, undefined){

	"use strict";
	
	
	/***********************************************
	*
	*  "UniShop" Class
	*
	************************************************/
	
	function UniShop(container, options){
	
		// properties
		this.container = $(container);
		this.options = options;
		this.xml = null;
		this.cart = {};
		this.cart_item = null;
		
		// load shop xml
		if(this.options.shopXML) {
			$.ajax({
				url: this.options.shopXML,
				data: '',
				dataType: 'xml',
				context: this,
				success: function(xml){
					this.xml = $(xml);
					this.init();
				},
				error: function(err){
					window.console && console.log('UniShop: XML ' + err.statusText);
				}
			});
		} else {
			window.console && console.log('UniShop: Shop XML file is not set!');
		}
		
	}
	
	
	UniShop.prototype = {
	
		/***********************************************
		*
		*	Init
		*
		************************************************/
		
		init: function(){
	
			// ref to "UniShop" object
			var that = this;
			
			// add shop markup
			this.addShopWrap();
			
			// init filters
			this.addFiltersMenu();
			
			// check if Masonry plugin is availabel and use it
			var is_masonry = this.options.masonry && (typeof $.fn.masonry === "function");
			
			// preload images and init Masonry
			if(is_masonry) {
				this.list.masonry({
		  			itemSelector: '.unishop-item:not(.uf-clone)',
		  			percentPosition: true,
		  			columnWidth: '.unishop-sizer'
		  		});
			}
			
			// preload grid thumbs
			this.items.find('.unishop-item-thumbs').addClass('unishop-thumbs-loading');
			this.preloadImages(this.items, function(item){
				item.find('.unishop-item-thumbs').removeClass('unishop-thumbs-loading');
				that.adjustThumbs(item);
				if(is_masonry) {
					that.list.masonry('layout');
				}
			});
			
			// run onReady() callback
			if(this.options.onReady && typeof this.options.onReady === 'function') {
				this.options.onReady.call(this.wrap);
			}
			
			// adjust thumbs height during window resize
			$(window).on('resize.unishop orientationchange.unishop', function(){
	
				// adjust each thumb height
				$.each(that.items, function(){ 
					that.adjustThumbs(this);
				});
				
				// reset modal scroll position on every resize
				if(that.wrap.hasClass('unishop-show-modal')) {
					that.wrap.find('.unishop-modal-content').css('top', '');
				}
			});
				
		}, 
		
		
		/***********************************************
		*
		*	Method to preload images in grid
		*
		************************************************/
		
		preloadImages: function(list, progress, complete) {
	
			// ref to "UniShop" object
			var that = this;
			
			// total items in list
			var list_total = list.length;
			
			// loop list
			$.each(list, function(){
			
				var item = $(this), imgs = item.find('img');
				var item_total = imgs.length;
				
				// check if image is already loaded otherwise attach "load" event
				$.each(imgs, function(){
					if(this.complete && this.naturalWidth) {
						if(--item_total === 0 && typeof progress === 'function') {
							progress.call(that, item);
							if(--list_total === 0 && typeof complete === 'function') {
								complete.call(that);
							}
						}
					} else {
						$(this).one('load', function(){
							if(--item_total === 0 && typeof progress === 'function') {
								progress.call(that, item);
								if(--list_total === 0 && typeof complete === 'function') {
									complete.call(that);
								}
							}
						});
					}
				});
			
			});
		}, 


		/***********************************************
		*
		*	Method to adjust item thumbs height 
		*	on resize to match the smallest one
		*
		************************************************/
	
		adjustThumbs: function(item){
			var thumbs = $(item).find('.unishop-item-thumbs > a');
			if(thumbs.length > 1) {
				var h1 = thumbs.eq(0).find('img').height(), h2 = thumbs.eq(1).find('img').height();
				if(h1 !== h2) {
					thumbs.height(h1 > h2 ? h2 : h1);
					thumbs.eq(h1 > h2 ? 0 : 1).css('top', -0.5 * Math.abs(h1 - h2));
				}
			}
		}, 
		
		
		/***********************************************
		*
		*	Method to add shop wrap element with 
		*	all markup
		*
		***********************************************/
		
		addShopWrap: function(){
			
			// create shop wrap
			this.wrap = $('<div id="' + this.options.shopID + '" class="unishop"></div>');
			
			// add shop menu markup
			this.addShopMenu();
			
			// add shop grid markup
			this.addShopGrid();
			
			// add shop modal markup
			this.addShopModal();
					
			// add wrap to container
			this.container.append(this.wrap);
		
		},
		
		
		/***********************************************
		*
		*	Method to add menu with filters and cart 
		*	icons
		*
		***********************************************/
		
		addShopMenu: function(){
	
			// ref to "UniShop" object
			var that = this;
			
			// add menu markup
			this.wrap.prepend('<div class="unishop-menu">' + 
				(function(){
					if(that.options.shopFilters && typeof that.options.shopFilters === 'object') {
						return '<a href="#filters" class="unishop-filters"></a>';
					}
					return '';
				})() + 
				'<a href="#cart" class="unishop-cart"></a>' +
				'<div class="unishop-cart-wrap">' +
					'<div class="unishop-empty-message">Your cart is empty</div>' +
					'<div class="unishop-cart-buttons">' +
						'<a class="unishop-button-secondary unishop-keep-shopping" href="#">Keep Shopping</a>' +
						'<a class="unishop-button unishop-paypal-checkout" href="#">Proceed to Checkout</a>' +
					'</div>' +
				'</div>' +
			'</div>');
			
			// add click event to open filters menu
			this.wrap.find('.unishop-filters').click(function(e){
				
				// add class to open filters
				that.wrap.toggleClass('unishop-show-filters');
				
				// click on list will close menu
				that.list.one('click', function(){
					that.wrap.removeClass('unishop-show-filters');
				});
				
				// close cart if open
				that.wrap.removeClass('unishop-show-cart');
				
				// prevent defaults
				e.preventDefault();
			});
			
			// add click event to open cart & display its contents
			this.wrap.find('.unishop-cart').click(function(e){
				// add cart contents
				that.updateCartContents();
				// show cart
				that.wrap.toggleClass('unishop-show-cart');
				
				// click on list will close menu
				that.list.one('click', function(){
					that.wrap.removeClass('unishop-show-cart');
				});
								
				e.preventDefault();
			});
			
			
			// click handler to start checkout via Paypal
			this.wrap.find('.unishop-menu .unishop-paypal-checkout').on('click', function(e){
				that.paypalCheckout();
				e.preventDefault();
			});
			
			// check if we need to restore cart from browser's localStorage
			if(this.options.localStorage) {
				this.restoreCartContents();
				// update items count in cart
				if(Object.keys(that.cart).length) {
				this.wrap.find('.unishop-menu').find('.unishop-cart').html('<span>' + 
					Object.keys(that.cart).length + '</span>');
				}
			}
						
		},
		
		
		/***********************************************
		*
		*	Method to add shop grid with items
		*
		***********************************************/
		
		addShopGrid: function(){
			
			// ref to "UniShop" object
			var that = this;
			
			// shop columns
			var shop_columns; switch(this.options.shopColumns) {
				case 1: { shop_columns = 'unishop-col-one'; break; }
				case 2: { shop_columns = 'unishop-col-two'; break; }
				case 3: { shop_columns = 'unishop-col-three'; break; }
				case 4: { shop_columns = 'unishop-col-four'; }
			}
				
			// shop UL list w/ grid sizer
			var list_markup = $('<ul class="unishop-list"></ul>');
			
			// add grid sizer
			list_markup.append('<li class="unishop-sizer ' +  shop_columns + '"></li>');
		
			// add list items
			$.each(this.xml.find('item'), function(){
				var item = $(this);
				var item_data = {
					sku: item.find('sku').text(),
					name: item.find('name').text(),
					price: item.find('price').text(),
					sale: item.find('price').attr('sale'),
					rating: item.find('rating').text(),
					thumbs: item.find('thumbs').text().split(/\s*,\s*/)
				};							
								
				// calculate rating
				var rating_markup = '';
				for(var i = 0; i < parseInt(item_data.rating, 10); i++) {
					rating_markup += '<span class="unishop-star"></span>';
				}
				if(item_data.rating % 1 !== 0) {
					rating_markup += '<span class="unishop-halfstar"></span>';
				}
										
				// create item markup
				var item_markup = $('<li class="unishop-item ' + shop_columns + '"></li>');
				
				// add "data-sku" attr to the item for further reference
				item_markup.attr('data-sku', item_data.sku);
				
				// add item thumb & overlay
				item_markup.append('<div class="unishop-item-photo">' + 
						'<div class="unishop-item-thumbs' + 
							(item_data.thumbs.length > 1 ? ' unishop-thumbs-multiple' : '') + '">' +
							(function(){
								var thumbs_markup = '';
								for(var k = 0; k < item_data.thumbs.length; k++) {
									thumbs_markup += '<a class="unishop-modal-link" href="#"><img src="' + 
										item_data.thumbs[k] + '" alt="' + item_data.name + '"/></a>';
								}
								return thumbs_markup;
							})() + '</div>'+
							(function(){
								if(item_data.sale !== undefined) {
									return '<div class="unishop-sale-discount">' + 
										Math.round(100 - 100 * item_data.price/item_data.sale) + '% off</div>';
								} else {
									return '';
								}
							})() + '</div>');
				
				// add item details
				item_markup.append('<div class="unishop-item-info">' +
					'<div class="unishop-item-name"><a class="unishop-modal-link" href="#">' + item_data.name + '</a></div>' +
					'<div class="unishop-item-price unishop-item-sale">' + 
						(function(){
							if(item_data.sale !== undefined) {
								return '<span>' + that.options.currencySymbol + item_data.sale + '</span>';	
							} else {
								return '';
							}
						})() +
						that.options.currencySymbol + item_data.price + '</div>' +
					'<div class="unishop-rating">' + rating_markup + '</div>' +
					'</div>');
				
				// add items to the list
				list_markup.append(item_markup);
				
			});		
						
			// save ref to DOM in "UniShop" object
			this.list = list_markup;
			this.items = this.list.find('.unishop-item');
			
			// add markup to shop wrap
			this.wrap.append(this.list);
			
			// add hover/swipe events to thumbs
			this.swipeThumbs();
						
		}, 
		
		
		
		/***********************************************
		*
		*	Method to add hover & swipe events to
		*	item thumbs
		*
		***********************************************/
		
		swipeThumbs: function(){
		
			// get thumbs container
			var thumbs_multiple = this.wrap.find('.unishop-thumbs-multiple');			
			
			if(thumbs_multiple.length > 0) {
					
				// if there is 2 thumbs add pagination
				thumbs_multiple.prepend('<div class="unishop-thumbs-pagination"><span></span><span></span></div>');
					
				// add touch/swipe support
				var touch = -1;
				this.wrap.find('.unishop-thumbs-multiple').on('touchstart', function(e){
					var evt = e.originalEvent;
					touch = evt.touches[0].pageX;
				}).on('touchmove', function(e){
					if(touch > 0) {
						var evt = e.originalEvent;
						var delta = evt.changedTouches[0].pageX - touch;
						if(Math.abs(delta) > 3) {
							$(this).toggleClass('unishop-thumbs-hover');
						}
					}
					touch = -1;
				});
				
				// add mouse hover support
				this.wrap.find('.unishop-thumbs-multiple').on('mouseenter', function(){
					$(this).addClass('unishop-thumbs-hover');
				}).on('mouseleave', function(){
					$(this).removeClass('unishop-thumbs-hover');
				});
			
			}
		
		},
		
		
		/***********************************************
		*
		*	Method to add modal box for item description
		*
		***********************************************/
		
		addShopModal: function(){
	
			// ref to "UniShop" object
			var that = this;
		
			// add modal box markup for item details
			var modal_box = $('<div class="unishop-modal"></div>');
			var modal_wrap = $('<div class="unishop-modal-wrap">' + 
					'<a class="unishop-modal-close" href="#" title="Close Details"></a>' + 
					'<div class="unishop-modal-content">' +
						'<div class="unishop-modal-slider"></div>' + 
						'<div class="unishop-modal-item"></div>' +
					'</div>' +
					'<div class="unishop-scroll-path"><a href="#" class="unishop-scroll-handle"></a></div>' +
				'</div>' + 
			'</div>');
			var modal_content = modal_wrap.find('.unishop-modal-content');
			
			// add markup to shop wrap
			this.wrap.append(modal_box.append(modal_wrap));
							
			// add click event to open modal
			this.wrap.find('.unishop-modal-link').click(function(e){
			
				// get item sku
				var sku = $(this).parents('.unishop-item').data('sku');
				
				// look for item by sku number
				$.each(that.xml.find('item'), function(){
					if($(this).find('sku').text() === sku) {
										
						// add item content
						that.addModalContent(this);
						
						// reset content & handle position 
						modal_box.find('.unishop-modal-content, .unishop-scroll-handle').css('top', '');

						// add classes to show modalbox with transition
						that.wrap.addClass('unishop-show-modal unishop-show-wrap');
					}
				});
				
				// prevent default
				e.preventDefault();
				
			});
			
			// hide modal when close icon is clicked
			this.wrap.on('click', '.unishop-modal-close, .unishop-keep-shopping', function(e){
				that.wrap.removeClass('unishop-show-wrap');
				$('body').css('overflow', '');
				window.setTimeout(function(){
					that.wrap.removeClass('unishop-show-modal');
					that.wrap.find('.unishop-add-cart').text('Add to Cart');
				}, 1000);
				// reset item summary
				that.cart_item = null;
				// close cart
				that.wrap.removeClass('unishop-show-cart');
				e.preventDefault();
			});
						
			// hide modal when "ESC" key is pressed
			$(window).on('keydown.unishop', function(e){
				if(that.wrap.hasClass('unishop-show-modal')) {
					if(e.keyCode === 27) {
						modal_box.find('.unishop-modal-close').trigger('click');
					}
				}
			});
			
			// add mouse wheel support
			this.addWheelSupport(modal_wrap, modal_content);
			
			// add touch scroll support
			this.addTouchScroll(modal_wrap, modal_content);
						
		},
		
		
		
		
		/***********************************************
		*
		*	Method to add modal content & related events 
		*	(accepts item XML as argument)
		*
		***********************************************/
		
		addModalContent: function(item){
				
			// ref to "UniShop" object
			var that = this;
					
			// modal DOM elements
			var modal_item = that.wrap.find('.unishop-modal-item');
			var modal_slider = that.wrap.find('.unishop-modal-slider');

			// wrap item xml into jQ object
			item = $(item);
			
			// get item data
			var item_data = {
				name: item.find('name').text(),
				photos: item.find('photos').text().split(/\s*,\s*/),
				price: item.find('price').text(),
				description: item.find('description').text(),
				rating: item.find('rating').text(),
				options: item.find('options'),
				quantity: item.find('quantity')
			};
						
			// add photos
			modal_slider.empty();
			for(var k = 0; k < item_data.photos.length; k++) {
				modal_slider.append('<img src="' + item_data.photos[k] + '" alt="' + item_data.name + '"/>');
			}
					
			// add description
			modal_item.empty().append('<div class="unishop-modal-name">' + item_data.name + '</div>' +
				
				'<p>' + item_data.description + '</p>' +
				
				'<div class="unishop-modal-separator">' +
					
					'<div class="unishop-modal-price">' + that.options.currencyName + ' ' + 
					that.options.currencySymbol + item_data.price + '</div>' +
					
					'<div class="unishop-modal-rating unishop-rating">' + 
					
						(function(){
							var rating = '';
							for(var i = 0; i < parseInt(item_data.rating, 10); i++) {
								rating += '<span class="unishop-star"></span>';
							}
							if(item_data.rating % 1 !== 0) {
								rating += '<span class="unishop-halfstar"></span>';
							}
							return rating;
						
						})() + 
						
					'</div>' +
				'</div>');
				
			// add quantity and options
			modal_item.append((function(){
			
				return 	'<div id="unishop-item-quantity" class="unishop-modal-quantity">' +
							'<strong class="unishop-modal-label">Quantity:</strong>' +
							'<div class="unishop-quantity-wrap">' +
								'<a href="#subtract"></a>' +
								'<input type="text" name="unishop-item-quantity"/>' +
								'<a href="#add"></a>' +
							'</div>' +
						'</div>';
			})() + 
			
			'<div class="unishop-modal-options">' + (function(){
			
				// return if no options
				if(item_data.options.length < 0) { 
					return '';
				}
			
				// markup for all options
				var all_options = '';
				
				// only one priced option is allowed, all other 
				// threat as simple options
				var priced_count = 1;
				
				// loop options if any
				$.each(item_data.options.children('option'), function(k){
				
					// get option name and generate its ID
					var option_name = $(this).attr('name');
					var option_id = 'unishop-option-'  + option_name.toLowerCase().replace(/[^\w]+/g, '-');
					
					// check if this is priced option (look if first entry has a "price" attribute)
					var is_priced = $(this).find('entry:first').attr('price') && priced_count--;
					
					// set option class
					var option_class = 'unishop-modal-option';
					option_class += (is_priced ? ' unishop-priced-option' : '');
											
					// container for options
					var option = '<div id="' + option_id + '" class="' + option_class + '">' +
						'<strong class="unishop-modal-label">' + option_name + ': </strong>' +
						'<div class="unishop-select-wrap" title="' + option_name + 
						'"><select name="' + option_id +'">';
										
					// add select options
					$.each($(this).children('entry'), function(){
						option += '<option value="' + $(this).attr('value') + '"' + 
						(is_priced ? ' price="' + $(this).attr('price') + '"' : '') +
						'>' + $(this).text() + '</option>';
					});
					
					// add closing tag
					option += '</select></div></div>';
					all_options += option;
					
				});
				
				// return all options
				return all_options;
			
			})()+ '</div>');
			
				
			// add reset & item summary
			modal_item.append('<div class="unishop-modal-reset">' + 
				'<a href="#" title="Reset all options">Reset</a></div>' +  
				'<div class="unishop-modal-summary">' +
					'<div class="unishop-modal-subtotal"></div>' +
					'<div class="unishop-modal-shipping"></div>' +
					'<div class="unishop-modal-total"></div>' +
			'</div>' +
			
			// add buttons
			'<div class="unishop-modal-buttons">' +
				'<a class="unishop-button-secondary unishop-keep-shopping" href="#">Keep Shopping</a>' +
				'<a class="unishop-button unishop-add-cart" href="#">Add to Cart</a>' +
			'</div>');
			
			
			// on click reset quantity & options
			modal_item.find('.unishop-modal-reset a').click(function(e){
				modal_item.find('.unishop-modal-price').text(that.options.currencyName + 
				' ' + that.options.currencySymbol + item_data.price);
				modal_item.find('.unishop-modal-quantity input').val(item_data.quantity.text());
				modal_item.find('.unishop-modal-option select').prop('selectedIndex', 0);
				that.updateModalSummary(item);
				e.preventDefault();
			});
			
			// on update change item price and recalculate summary
			modal_item.find('.unishop-modal-options').on('change', 'select', function(){
				var price = $(this).find('option:selected').attr('price');
				if(!isNaN(price)) {
					modal_item.find('.unishop-modal-price').text(that.options.currencyName + 
					' ' + that.options.currencySymbol + price);
				} 
				that.updateModalSummary(item);
			});
						
			// add item quantity select	
			(function(){
				
				var quantity_select = modal_item.find('.unishop-modal-quantity');
				var quantity = item_data.quantity;
				
				// quantity value & min, max range
				var val = parseInt(quantity.text(), 10) || 1,
					max = parseInt(quantity.attr('max'), 10), 
					min = parseInt(quantity.attr('min'), 10);
										
				// set initial value
				quantity_select.find('input').val(val);
			
				// add click & change events
				quantity_select.on('click', 'a', function(e){
					e.preventDefault();
					var value = parseInt(quantity_select.find('input').val(), 10) || val;
					if($(this).attr('href').indexOf('add') !== -1) {
						value = Math.min(value + 1, max);
					} else {
						value = Math.max(value - 1, min);
					}
					quantity_select.find('input').val(value);
					that.updateModalSummary(item);
				}).find('input').change(function(){
					var value = $(this).val() || val;
					$(this).val(Math.max(Math.min(value, max), min));
					that.updateModalSummary(item);
				});
			})();	
			
			// click on 'Add to Cart' button adds item to the cart
			modal_item.find('.unishop-add-cart').click(function(e){
				var sku = that.cart_item.sku;
				// check if this item is already in the cart
				if(typeof that.cart[sku] === 'object'){
					$(this).text('Already in cart');
				} else {
					that.cart[sku] = that.cart_item;
					$(this).text('Done');
					// run onCart() callback 
					if(that.options.onCart && typeof that.options.onCart === 'function') {
						var callback_result = that.options.onCart.call(that.wrap, that.cart_item);
						if(callback_result && typeof callback_result === 'object') {
							that.cart[sku] = callback_result;
						}
					}
				}
				// close modal with 1s delay
				window.setTimeout(function(){
					modal_item.find('.unishop-keep-shopping').trigger('click');
				}, 500);
				// update items count in cart
				that.wrap.find('.unishop-menu').find('.unishop-cart').html('<span>' + 
					Object.keys(that.cart).length + '</span>');
				
				// save cart contents to browser's localStorage 
				that.saveCartContents();
				
				// prevent click
				e.preventDefault();
			});
			
			// calculate summary when modal opens
			this.updateModalSummary(item);
		
		},	
		
		
		/***********************************************
		*
		*	Method to add contents to the cart
		*
		***********************************************/
		
		updateCartContents: function(){
		
			// ref to "UniShop" object 
			var that = this;
			
			// cart wrap element
			var cart_wrap = this.wrap.find('.unishop-cart-wrap');
					
			// display cart empty message
			if(Object.keys(this.cart).length === 0) {
				cart_wrap.addClass('unishop-cart-empty');
				this.wrap.find('.unishop-menu .unishop-cart').empty();
			} else {
				cart_wrap.removeClass('unishop-cart-empty');
			}	
		
			// add list with orders
			var cart_orders = $('<ul class="unishop-cart-orders"></ul>');
			
			// loop items in "this.cart"
			$.each(this.cart, function(){	
				cart_orders.append('<li>'+
					'<div class="unishop-order-name">' + this.name  + '<span>(' + this.sku + ')</span></div>' +
					'<div class="unishop-order-thumb" style="background-image: url(' + this.thumb + ');"></div>' + 
					'<div class="unishop-order-desc">' +  
						'<div class="unishop-order-price">'+
							'<div><span>Price:</span>' + that.options.currencySymbol + this.price + '</div>' +
							'<div><span>Qty:</span>' + this.quantity + '</div>' + 
							'<div><span>Shipping:</span>' + 
								(this.shipping === 0 ? 'FREE' : that.options.currencySymbol + this.shipping) + 
							'</div>' +
						'</div>' + 
							(function(cart_item){
								if(cart_item.options.length) {
									var opt_list = '<div class="unishop-order-options">';
									for(var k = 0; k < cart_item.options.length; k++) {
										var opt = cart_item.options[k];
										opt_list += '<span>' + opt.name + ': ' + opt.value + '</span>, ';
									} 
									return opt_list.slice(0, -2) + '</div>'; 
								} else {
									return '';
								}
							})(this) +
						'<div class="unishop-order-subtotal">' +
							'<span>' + that.options.currencyName + ' ' + 
								that.options.currencySymbol + (this.price * this.quantity) + '</span>' +
								'<span> + ' + 
								(this.shipping === 0 ? 'FREE' : that.options.currencySymbol + this.shipping) + 
								'</span>' +
						'</div>' + 
					'</div>' +
					'<a class="unishop-order-remove" href="#' + this.sku + '" title="Remove"></a>' +
				'</li>');
			});
			
			
			// add remove event to remove items from cart
			cart_orders.find('.unishop-order-remove').one('click', function(e){
				var remove_sku = $(this).attr('href').substr(1);				
				// loop for cart items matching sku
				for(var sku in that.cart) {
					if(sku === remove_sku) {	
						delete(that.cart[sku]);
						// update items count in cart
						that.wrap.find('.unishop-menu .unishop-cart').html('<span>' + 
							Object.keys(that.cart).length + '</span>');
						// update cart contents
						that.updateCartContents();
						// save cart using browser's localStorage
						that.saveCartContents();
					}
				}
				e.preventDefault();
			});
			
			
			// replace cart items with new set
			cart_wrap.find('.unishop-cart-orders').remove();
			cart_wrap.find('.unishop-cart-buttons').before(cart_orders);
			
			// calculate cart subtotal, shipping and total
			cart_wrap.find('.unishop-cart-total').remove();
			
			if(Object.keys(this.cart).length > 0) {
				var cart_total = $('<div class="unishop-cart-total"></div>');
				var subtotal = 0, shipping = 0, total = 0;
				for(var sku in this.cart) {
					subtotal += this.cart[sku].subtotal;
					shipping += this.cart[sku].shipping;
					total += this.cart[sku].total;
				}
				$('<span>Subtotal:&nbsp;' + 
					this.options.currencySymbol + subtotal + '</span>').appendTo(cart_total);
				$('<span>Shipping:&nbsp;' + 
					this.options.currencySymbol + shipping + '</span>').appendTo(cart_total);
				$('<span><strong>TOTAL:&nbsp;' + 
					this.options.currencySymbol + total + '</strong></span>').appendTo(cart_total);
				
				cart_wrap.find('.unishop-cart-buttons').before(cart_total);
			
			}
												
		},
		
		
		/***********************************************
		*
		*	Method to save cart content in 
		*	browser's localStorage
		*
		***********************************************/
		
		saveCartContents: function(){
			if(this.options.localStorage && typeof JSON.stringify === 'function') {
				if(Object.keys(this.cart).length) {
					window.localStorage.setItem(this.options.localStorage, JSON.stringify(this.cart));
				} else {
					window.localStorage.removeItem(this.options.localStorage);
				}
			}
		},
		
		
		/***********************************************
		*
		*	Method to restore cart content from 
		*	localStorage
		*
		***********************************************/
		
		restoreCartContents: function(){
					
			// check if localStorate option is enabled
			if(this.options.localStorage && typeof JSON.parse === 'function') {
				// get saved cart
				var saved_cart = window.localStorage.getItem(this.options.localStorage);
				if(saved_cart) {
					this.cart = JSON.parse(saved_cart);
				}
			}						
						
		},
		
		
		/***********************************************
		*
		*	Method to start checkout via PayPal
		*
		***********************************************/
		
		paypalCheckout: function(){
		
			// ref to "UniShop" object
			var that = this;
			
			// create a <FORM> for post request
            var form = $('<form />');
            form.attr('action', 'https://www.paypal.com/cgi-bin/webscr');
            form.attr('method', 'post');
            form.attr('target', '_blank'); 
            
            // paypal cart global variables
		 	var paypal_params = $.extend(this.options.paypal, {
            	cmd: '_cart',
            	upload: 1
            });
            
   			// process cart
			var item_params = {}, index = 1;
			for(var sku in this.cart) {
				var cart_item = this.cart[sku];
				item_params['item_number_' + index] = sku;
				item_params['item_name_' + index] = cart_item.name;
				item_params['amount_' + index] = cart_item.price;
				item_params['shipping_' + index] = cart_item.shipping;
				item_params['quantity_' + index] = cart_item.quantity;
				
				// process options
				for(var k = 0; k < cart_item.options.length; k++) {
					var opt = cart_item.options[k];
					item_params['on' + k + '_' + index] = opt.name;
					item_params['os' + k + '_' + index] = opt.value;
					// for priced option add two more fields
					if(!isNaN(opt.price)) {
						item_params['option_index_' + index] = k;
						item_params['option_select' + k + '_' + index] = opt.value;
						item_params['option_amount' + k + '_' + index] = opt.price;
					}
				}

				// increase index
				index++;
			}
						
			// add everything to the form_params object
			var form_params = $.extend({}, paypal_params, item_params);
			
			// run onCheckout() callback
			if(this.options.onCheckout && typeof this.options.onCheckout === 'function') {
				var callback_result = this.options.onCheckout.call(this.wrap, form_params);
				if(callback_result && typeof callback_result === 'object') {
					form_params = callback_result;
				}
			}
						
			 // add params to the form
			for(var key in form_params) {
				var param = $('<input type="hidden" />');
				param.attr('name', key).attr('value', form_params[key]);
				form.append(param);
			}
						
			// add <FORM> to the document 
			form.hide().appendTo(this.wrap);
						
			// change Checkout button text to 'Processing'
			this.wrap.find('.unishop-paypal-checkout').text('Please wait..');						
						
			// wait 0.5s and radirect to paypal.com
			window.setTimeout(function(){
				form.submit();
				// clear cart after checkout and restore button text
				window.setTimeout(function(){
					that.cart = {};
					form.remove();
					that.updateCartContents();
					that.wrap.find('.unishop-paypal-checkout').text('Proceed to Checkout');
					that.saveCartContents();
				}, 2500);
			}, 1000);
			
		},
		
		
		/***********************************************
		*
		*	Method to calculate & update 
		*	modal summary when user selects item options
		* 	and quantity (item XML as argument)
		*
		***********************************************/
		
		updateModalSummary: function(item){
										
			// get elements
			var modal_item = this.wrap.find('.unishop-modal-item');
			var modal_subtotal = modal_item.find('.unishop-modal-subtotal');
			var modal_shipping = modal_item.find('.unishop-modal-shipping');
			var modal_total = modal_item.find('.unishop-modal-total');
			
			// get quantity
			var quantity = modal_item.find('#unishop-item-quantity input').val();
			quantity = parseInt(quantity, 10) || 1;
			
			// get price
			var price = parseFloat(item.find('price').text());
			if(modal_item.find('.unishop-priced-option').length > 0) {
				var priced_option = modal_item.find('.unishop-priced-option').eq(0);
				var price_override = priced_option.find('select option:selected').attr('price');
				price = parseFloat(price_override) || price;
			}
			
			// calculate and update subtotal
			modal_subtotal.html('<span>Subtotal:</span> ' + this.options.currencySymbol + price * quantity +
			' (' + quantity  + (quantity > 1 ? ' items' : ' item') + ')');
							
			// calculate and update shipping
			var shipping_price = parseFloat(item.find('shipping').text()) || 0;
			modal_shipping.html('<span>Shipping:</span> ' +
			(shipping_price > 0 ? this.options.currencySymbol + shipping_price : 'FREE'));
			
			// calculate and update total
			modal_total.html('<span>TOTAL:</span> ' + this.options.currencySymbol + (price * quantity + shipping_price));
			
			// get selected options values
			var options = [];
			$.each(modal_item.find('.unishop-modal-option'), function(){
				options.push({
					'name': $(this).find('.unishop-select-wrap').attr('title'), 
					'value': $(this).find('select option:selected').attr('value'),
					'price': $(this).find('select option:selected').attr('price'),
					'index': $(this).find('select').prop('selectedIndex')
				});				
			});
																		
			// save info about item in global variable
			this.cart_item = {
				'sku': item.find('sku').text(),
				'price': price,
				'name': item.find('name').text(),
				'quantity': quantity,
				'subtotal': quantity * price,
				'shipping': shipping_price,
				'total': quantity * price + shipping_price,
				'thumb': item.find('thumbs').text().split(/\s*,\s*/)[0],
				'options': options
			};
					
		},	
		
		
		/***********************************************
		*
		*	Method to add mouse wheel support to 
		*	scroll item description
		*
		***********************************************/
		
		
		addWheelSupport: function(wrap, inner) {
		
			// helper function to scroll handle along with the content
			function _scroll_handle(amount){
				amount = Math.min(1, Math.max(0, amount));
				var path = wrap.find('.unishop-scroll-path');
				var handle = wrap.find('.unishop-scroll-handle');
				var top = amount * (path.height()  - handle.height());
				handle.css('top', top);
			}
		
			// scroll item description with mousewheel
			wrap.on('mousewheel DOMMouseScroll', function(e){
			
				var item_top = parseInt(inner.css('top'), 10) || 0;
				var evt = e.originalEvent;	
				var delta = evt.detail ? evt.detail * -1 : evt.wheelDelta / 40;
				var max_scroll = wrap.height() - inner.outerHeight();
	    		    					
				// set content scroll limits and scroll item description
				item_top = Math.min(0, Math.max(item_top + delta * 10, max_scroll));
				inner.css('top', item_top);
							
				// scroll handle also
				_scroll_handle(item_top / max_scroll);
										
				// stop <body> from scrolling
				e.preventDefault();
				e.stopPropagation();
			}).on('MozMousePixelScroll', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
		
		},
		
		
		/***********************************************
		*
		*	Method to add touch/grad support to 
		*	scroll item description
		*
		***********************************************/
		
		addTouchScroll: function(wrap, inner){
		
				// some variables
				var touch_y = -1, drag = false,
					max_scroll, time, inner_top;
		
				// helper function to scroll hanle by % amount
				function _scroll_handle(amount){
					amount = Math.min(1, Math.max(0, amount));
					var scroll_path = wrap.find('.unishop-scroll-path');
					var scroll_handle = wrap.find('.unishop-scroll-handle');
					var handle_top = amount * (scroll_path.height()  - scroll_handle.height());
					scroll_handle.css('top', handle_top);
				}
				
				// add "easeOutQuad" easing to jQuery
				$.extend($.easing, {
					easeOutQuad: function (x, t, b, c, d) {
						return -c *(t/=d)*(t-2) + b;
					}
				});
	
				// register touch events
				wrap.on('touchstart', function(e){
					
					// get touch "Y" position
					var evt = e.originalEvent;
					touch_y = evt.touches[0].pageY;
					inner_top = parseInt(inner.css('top'), 10) || 0;
					max_scroll = wrap.height() - inner.outerHeight();
					// stop any runnnig animation
					inner.stop();
					// save time of touch
					time = new Date().getTime();
									
				}).on('touchmove', function(e){
					if(touch_y > 0) {
					
						// get move delta
						var evt = e.originalEvent;
						var delta = evt.changedTouches[0].pageY - touch_y;
						
						// we need at least 5px drag
						if(Math.abs(delta) > 5) {
							drag = true;
						}
											
						// set content scroll limits and scroll item description
						var inner_offset = Math.min(0, Math.max(inner_top + delta, max_scroll));
						inner.css('top', inner_offset);
										
						// scroll handle also
						_scroll_handle(inner_offset / max_scroll);
						
						// prevent <body> from scrolling
						e.preventDefault();
					}
				}).on('touchend', function(e){
					// measure time between touch start & touch end
					var time_dif = new Date().getTime() - time;
					if(drag && time_dif < 300) {
						var evt = e.originalEvent;
						var swipe_distance = 100;
						// get swipe direction and position of content after dragging 
						var dir = evt.changedTouches[0].pageY - touch_y > 0 ? 1 : -1;
						var inner_top = parseInt(inner.css('top'), 10) || 0;
						// set limits of swipe offset
						var inner_offset = Math.min(0, Math.max(inner_top + dir * swipe_distance, max_scroll));
						// start swipe
						inner.stop().animate({top: inner_offset + 'px'}, {
							duration: 300,
							easing: 'easeOutQuad',
							progress: function(animation, progress){
								_scroll_handle((inner_top + dir * swipe_distance * progress) / max_scroll);
							} 	
						});
					}
					
					// reset
					touch_y = -1;
					drag = false;
				});
		},
		
		
		/***********************************************
		*
		*	Method to add shop filters 
		*   via "UniFilter" plugin
		*
		***********************************************/
		
		addFiltersMenu: function(){
		
			// ref to "UniShop" object
			var that = this;
			
			// check if filters are enabled
			if(!(this.options.shopFilters && typeof this.options.shopFilters === 'object')) {
				return;
			}
			
			// check if "UniFilter" plugin is available
			if(typeof $.fn.unifilter !== 'function') { 
				window.console && console.log('UniShop: UniFilter plugin is required!');
				return; 
			}
			
			// display filters if at least one range, search or sort is present
			if(!(this.options.shopFilters.filters ||
				 this.options.shopFilters.range ||
				 this.options.shopFilters.search ||
				 this.options.shopFilters.sort)) { 
				return; 
			}
			
			// default options
			var options = {
				selector: '.unishop-item:not(.unishop-sizer)'
			};
			
			// extend default options
			options = $.extend(options, this.options.shopFilters);
						
			// loop filters
			if(options.filters) {
				var filters = options.filters.trim().split(/\s*,\s*/);
				options.filters = {};
				for(var k = 0; k < filters.length; k++) {
					options.filters[filters[k]] = {
						tooltip: true,
						title: filters[k].charAt(0).toUpperCase() + filters[k].substr(1),
						multiple: false,
						getFilterData: function(name){
							var sku = $(this).data('sku'), data = [];
							$.each(that.xml.find('item'), function(){
								if(sku === $(this).find('sku').text()) {
									data = $(this).find(name).text().trim().split(/\s*,\s*/);
									return;
								}
							});
							return data;
						}
					};
				}			
			}
			
			// loop ranges
			if(options.range) {
				var range = options.range.trim().split(/\s*,\s*/);
				// get items min/max price
				var min_price, max_price;
				$.each(this.xml.find('item'), function(n){
					var price = $(this).find('price').text();
					if(n > 0) {
						min_price = Math.min(min_price, price);
						max_price = Math.max(max_price, price);
					} else {
						min_price = max_price = price;
					}
				});
				options.range = {};
				for(var k = 0; k < range.length; k++) {
					options.range[range[k]] = {
						title: range[k].charAt(0).toUpperCase() + range[k].substr(1),
						scale: min_price + '-' + max_price,
						precision: 0,
						prefix: that.options.currencySymbol,
						getRangeData: function(name){
							var sku = $(this).data('sku'), data;
							$.each(that.xml.find('item'), function(){
								if(sku === $(this).find('sku').text()) {
									data = $(this).find(name).text().trim();
									return;
								}
							});
							return data;
						}
					};
				}
			}
			
			// loop search
			if(options.search) {
				var search = options.search.trim().split(/\s*,\s*/);
				options.search = {};
				for(var k = 0; k < search.length; k++){
					options.search[search[k]] = {
						title: 'Search by ' + search[k].charAt(0).toUpperCase() + search[k].substr(1),
						placeholder: 'Type here..',
						getSearchData: function(name){
							var sku = $(this).data('sku'), data = [];
							$.each(that.xml.find('item'), function(){
								if(sku === $(this).find('sku').text()) {
									data = $(this).find(name).text().trim().split(/\s*,\s*/);
									return;
								}
							});
							return data;
						}
					};
				}
			}
			
			// loop sort
			if(options.sort) {
				var sort = options.sort.trim().split(/\s*,\s*/);
				var sort_options = {};
				for(var k = 0; k < sort.length; k++){
					sort_options[sort[k]] = {
						label: sort[k].charAt(0).toUpperCase() + sort[k].substr(1),
						getSortData: function(name){
							var sku = $(this).data('sku'), data;
							$.each(that.xml.find('item'), function(){
								if(sku === $(this).find('sku').text()) {
									data = $(this).find(name).text().trim();
									data = isNaN(data) ? data : parseFloat(data);
									return;
								}
							});
							return data;
						}
					};
				}
				options.sort = {
					'title': 'Sort',
					'options': sort_options
				};
			}
																		
			// init "UniFilter" plugin
			this.wrap.find('.unishop-menu').after('<div class="unishop-unifilter"></div>');
			$('.unishop-unifilter', this.wrap).unifilter(this.list, options).on('onListUpdate', function(){
				that.list.masonry('destroy').masonry({
		  			itemSelector: '.unishop-item:not(.uf-clone)',
		  			percentPosition: true,
		  			columnWidth: '.unishop-sizer'
		  		});
	  		});
		
		}
		
	
	}; // "UniShop" prototype chain
	

	/***********************************************
	*
	*	Add "$.unifilter()" jquery pugin
	*
	***********************************************/
	
	$.fn.unishop = function(options){
					
		// default options
		var default_options = {
			shopXML: 'shop.xml',								// shop XML file. Default "./shop.xml"
			shopID: 'myshop',									// your shop #ID
			masonry: true,										// use Masonry plugin for grid layout
			shopColumns: 3,										// grid columns 1,2,3. Default 3
			currencySymbol: '$',								// shop currency symbol, e.g '$'
			currencyName: 'USD',								// shop currency name, e.g. 'USD'
			localStorage: 'unishop',							// name of local storage to save cart contents 
			shopFilters: {										// shop filters configuration or false
				filters: null,									// filters XML elements, e.g. 'color, size'
				search: null,									// search XML elements, e.g. 'name'
				sort: null,										// sort XML elements, e.g. 'price, rating'
				range: null,									// range XML elements, e.g. 'price'
				order: 'filters, range, search, sort',			// order to display filters, range, search and sort
				animationTime: 500,								// animation duration, ms
				animationType: 'opacity'						// filtering animation - 
																// 'none|opacity|scale|rotate|translate'
			},
			paypal: {
				'business': 'paypal@domain.com',				// your PayPal business account
	            'currency_code': 'USD',							// your shop currency
	            'lc': 'US',										// your shop locale
	            'no_shipping': 0,								// request shipping address on checkout
	            'return': 'http://www.domain.com/shop/',		// URL to return after successful checkout
	            'cancel_return': 'http://www.domain.com/shop/'	// URL to return when checkout is canceled
			},
			onReady: null,										// DOM ready callback
			onCart: null,										// new cart item added callback
			onCheckout: null 									// paypal checkout callback
			
		};
		
		// extend "shopFilters" option
		options.shopFilters = $.extend(default_options.shopFilters, options.shopFilters);
		
		// extend "paypal" option
		options.paypal = $.extend(default_options.paypal, options.paypal);
		
		// extend options with defaults
		options = $.extend(default_options, options);
				
		// create "UniShop" object
		return $.each(this, function(){
			new UniShop(this, options);
		});
		
	
	};


})(jQuery);