// Email Button
(function($R) {
	$R.add(
		'plugin',
		'mail',
		{
			init: function (app) {
				
				this.app = app;
				
				// service
				this.insertion = app.insertion;
				this.toolbar   = app.toolbar;
			},
			
			start: function () {
					
					const _button = {
						icon: '<i class="fa fa-envelope"></i>',
						title: championcore.translations.lang_redactor_mail_button,
						api:   'plugin.mail.showAlert'
					};
					
					var $button = this.toolbar.addButton( 'mail', _button );
					
					/*
					let button = this.button.add('mail', championcore.translations.lang_redactor_mail_button );
					this.button.addCallback(button, this.mail.showAlert);
					
					// Set icon
					this.button.setIcon(button, '<i class="fa fa-envelope"></i>');
					*/
			},
			showAlert: function (buttonName) {
				// this.insert.html('<a title="' + championcore.translations.lang_redactor_mail_link_title + '" href="' + championcore.base_url + '/contact" class="mailcrypt">name{{at}}provider.com</a>');
				
				let html = [
					'<a title="', championcore.translations.lang_redactor_mail_link_title, '" href="mailto:name{{at}}provider.com" class="mailcrypt">name{{at}}provider.com</a>'
				].join('');
				
				this.insertion.insertHtml( html );
			}
		}
	);
})(Redactor);
