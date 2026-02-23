jQuery(document).ready(
	function() {
		
		/**
		 * customise keyboard shortcuts
		 *
		const championcore_shortcuts = {
			'ctrl+s, meta+s': {
				api: 'plugin.champion_shortcuts_manager.save',
				args: ['arg1', 'arg2']
			}
		};
		*/
		/**
		 * handle the keyboard shortcuts
		 *
		$R.add(
			'plugin', 'champion_shortcuts_manager',
			{
				init: function (app) {
					this.app = app;
				},
				
				save: function (arg1, arg2) {
					console.log('shortcut Ctrl+S is triggered');
				}
			}
		);
		*/
		
		/**
		 * get redactor working
		 */
		$R(
			'#wysiwyg',
			{
				lang: (championcore.lang_short || "en"),
				
				pastePlainText: true,
				paragraphize: true,
				replaceDivs: false,
				autoresize: true,
				minHeight: '380px',
				//buttonSource: true,
				imageFigure: true,
				imageUpload: 'inc/editor_images.php',
				imageManagerJson: 'inc/data_json.php',
				fileUpload: 'inc/editor_files.php',
				fileManagerJson: 'inc/data_json.php',
				imageResizable: true,
				imagePosition: true,
				linkNewTab: true,
				plugins: [
						'alignment',
						'clips',
						'filemanager',
						'fontcolor',
						'fontsize',
						'fontfamily',
						//'fullscreen',
						
						//'imagemanager',
						'champion_redactor_imagemanager',
						//'champion_shortcuts_manager',
						
						'inlinestyle',
						'properties',
						'table',
						'textdirection',
						'video', 
						
						//'codemirror',
						//'snippets',
						
						'widget',
						'mail'
						],
				
				source: {
					codemirror: {
							lineNumbers: true,
							theme: "monokai",
							smartIndent: true,
							lineWrapping: true,
							indentUnit: 4
							
							//mode: 'xml',
					}
				},
				
				clips: [
					['More', '##more##']
				],
				
				//shortcutsAdd: championcore_shortcuts,
				
				styles: true,
				
				toolbarFixed: true,
				//toolbarFixedTarget: '#textfile',
				toolbarFixedTopOffset: 50,
				toolbarOverflow: true,
				focus: true,
				
				buttonsAddBefore: {
					before: 'format',
					buttons: ['undo', 'redo']
				},
				buttonsAddAfter: {
					after: 'deleted',
					buttons: ['underline']
				}
			}
		);
	}
);
