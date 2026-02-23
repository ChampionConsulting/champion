/**
 * add folder support to the redactor image manager
 */

// redactor X
/*
(function($R)
{
    $R.add('plugin', 'champion_redactor_imagemanager', {
        translations: {
    		en: {
    			"choose": "Choose"
    		}
        },
        init: function(app)
        {
            this.app = app;
            this.lang = app.lang;
            this.opts = app.opts;
			
			this.champion_selected_folder = '/';
        },
        // messages
        onmodal: {
            image: {
                open: function($modal, $form)
                {
                    if (!this.opts.imageManagerJson) return;
                    this._load($modal)
                }
            }
        },

		// private
		_load: function($modal)
		{
			var $body = $modal.getBody();
			
			this.$box = $R.dom('<div>');
			this.$box.attr('data-title', this.lang.get('choose'));
			this.$box.addClass('redactor-modal-tab');
			this.$box.hide();
			this.$box.css({
				overflow: 'auto',
				height: '300px',
				'line-height': 1
			});
			
			$body.append(this.$box);
			
			this.champion_load_data();
			
		},
		_parse: function (data) {
			
			var self = this;
			
			// clear content
			self.$box.children().remove();
			
			// populate
			for (var key in data) {
			
				var obj = data[key];
				
				if (typeof obj !== 'object') {
					continue;
				}
				
				// folder
				if (obj.champion_type == 'folder') {
					
					var $div = $R.dom('<div>');
					var url = (obj.thumb) ? obj.thumb : obj.url;
					
					$div.html( obj.name );
					
					$div.attr('data-champion_type', 'folder');
					$div.attr('data-params', encodeURI(JSON.stringify(obj)));
					$div.css(
						{
							width: '96px',
							height: '72px',
							margin: '0 4px 2px 0',
							cursor: 'pointer',
							
							display: 'inline-block',
							lineHeight: '122px',
							backgroundImage: 'url(' + championcore.admin_url + '/img/icon-folder.svg)',
							backgroundRepeat: 'no-repeat',
							backgroundPosition: 'center center',
							fontSize: 'smaller',
							textAlign:  'center',
							
							verticalAlign: 'top'
						}
					);
					
					$div.on(
						'click',
						function (evnt) {
							
							var $el = $R.dom(evnt.target);
							var data = JSON.parse(decodeURI($el.attr('data-params')));
							
							self.champion_selected_folder = data.champion_folder;
							
							self.champion_load_data();
						}
					);
					
					this.$box.append($div);
				}
				
				// image
				if (obj.champion_type == 'img') {
					
					var $img = $R.dom('<img>');
					var url = (obj.thumb) ? obj.thumb : obj.url;

					$img.attr('src', url);
					$img.attr('data-champion_type', 'img');
					$img.attr('data-params', encodeURI(JSON.stringify(obj)));
					$img.css(
						{
							width: '96px',
							height: '72px',
							margin: '0 4px 2px 0',
							cursor: 'pointer'
						}
					);
					
					$img.on('click', this._insert.bind(this));
					
					this.$box.append($img);
				}
			}
		},
		_insert: function(e)
		{
    		e.preventDefault();

			var $el = $R.dom(e.target);
			var data = JSON.parse(decodeURI($el.attr('data-params')));

			this.app.api('module.image.insert', { image: data });
		},
		
		/**
		 * champion - load data
		 * /
		champion_load_data: function () {
			
			const self = this;
			
			$R.ajax.get(
				{
					url: this.opts.imageManagerJson + '?filter=' + window.encodeURI(self.champion_selected_folder),
					
					success: function (data) {
						self._parse(data);
					}
				}
			);
		}
    });
})(RedactorX);
*/

/*
// old redactor
(function($R)
{
    $R.add('plugin', 'champion_redactor_imagemanager', {
        translations: {
    		en: {
    			"choose": "Choose"
    		}
        },
        init: function(app)
        {
            this.app = app;
            this.lang = app.lang;
            this.opts = app.opts;
			
			this.champion_selected_folder = '/';
        },
        // messages
        onmodal: {
            image: {
                open: function($modal, $form)
                {
                    if (!this.opts.imageManagerJson) return;
                    this._load($modal)
                }
            }
        },

		// private
		_load: function($modal)
		{
			var $body = $modal.getBody();
			
			this.$box = $R.dom('<div>');
			this.$box.attr('data-title', this.lang.get('choose'));
			this.$box.addClass('redactor-modal-tab');
			this.$box.hide();
			this.$box.css({
				overflow: 'auto',
				height: '300px',
				'line-height': 1
			});
			
			$body.append(this.$box);
			
			this.champion_load_data();
			
		},
		_parse: function (data) {
			
			var self = this;
			
			// clear content
			self.$box.children().remove();
			
			// populate
			for (var key in data) {
			
				var obj = data[key];
				
				if (typeof obj !== 'object') {
					continue;
				}
				
				// folder
				if (obj.champion_type == 'folder') {
					
					var $div = $R.dom('<div>');
					var url = (obj.thumb) ? obj.thumb : obj.url;
					
					$div.html( obj.name );
					
					$div.attr('data-champion_type', 'folder');
					$div.attr('data-params', encodeURI(JSON.stringify(obj)));
					$div.css(
						{
							width: '96px',
							height: '72px',
							margin: '0 4px 2px 0',
							cursor: 'pointer',
							
							display: 'inline-block',
							lineHeight: '122px',
							backgroundImage: 'url(' + championcore.admin_url + '/img/icon-folder.svg)',
							backgroundRepeat: 'no-repeat',
							backgroundPosition: 'center center',
							fontSize: 'smaller',
							textAlign:  'center',
							
							verticalAlign: 'top'
						}
					);
					
					$div.on(
						'click',
						function (evnt) {
							
							var $el = $R.dom(evnt.target);
							var data = JSON.parse(decodeURI($el.attr('data-params')));
							
							self.champion_selected_folder = data.champion_folder;
							
							self.champion_load_data();
						}
					);
					
					this.$box.append($div);
				}
				
				// image
				if (obj.champion_type == 'img') {
					
					var $img = $R.dom('<img>');
					var url = (obj.thumb) ? obj.thumb : obj.url;

					$img.attr('src', url);
					$img.attr('data-champion_type', 'img');
					$img.attr('data-params', encodeURI(JSON.stringify(obj)));
					$img.css(
						{
							width: '96px',
							height: '72px',
							margin: '0 4px 2px 0',
							cursor: 'pointer'
						}
					);
					
					$img.on('click', this._insert.bind(this));
					
					this.$box.append($img);
				}
			}
		},
		_insert: function(e)
		{
    		e.preventDefault();

			var $el = $R.dom(e.target);
			var data = JSON.parse(decodeURI($el.attr('data-params')));

			this.app.api('module.image.insert', { image: data });
		},
		
		/**
		 * champion - load data
		 * /
		champion_load_data: function () {
			
			const self = this;
			
			$R.ajax.get(
				{
					url: this.opts.imageManagerJson + '?filter=' + window.encodeURI(self.champion_selected_folder),
					
					success: function (data) {
						self._parse(data);
					}
				}
			);
		}
    });
})(RedactorX);
*/
