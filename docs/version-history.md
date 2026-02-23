## Pulse 5.4.0
*CURRENTLY IN BETA* 

*NEW*
- Alpha release of Grand Escape theme.
- Major improvements with PulseCMS updater. This makes it easier to upgrade going forward.
https://forum.pulsecms.com/t/call-for-feature-ideas/1941/60

*IMPROVEMENTS*
- After this version, you can pick and choose what files to update during an install.
- On update, files will be scanned for changes with a checksum. This lets you avoid overwriting files you have customized.
- Progress bar fixes during an update.
- Templates: Added $template_folder variable to allow templates to be more flexible and "pastable".
- Navigation tag allows sending custom classes to make templating easier.

*FIXES*
- Fixed "No content found" bug when trying to update. Added various fixes to updater compatibility.
- Fixed blog search. Some templates have weak UX, but blog search functionally does work.
- Minor fix which affected some blog images, so they will show properly.


## Pulse 5.3.12
*IMPROVEMENTS*
- Navigation labels now allow multiple words. (Add a dash "-" for multi-word navigation.)
https://forum.pulsecms.com/t/two-words-in-navigation-title/1929
https://forum.pulsecms.com/t/call-for-feature-ideas/1941
- More flexible blog routing code. Allows renaming of blogs without breaking old links. (Lookup by ID or title slug.)
https://forum.pulsecms.com/t/blog-url-renaming-not-working/1928/17
- Default blog URLs are cleaner/shorter.

*FIXES*
- Blog routing fix. Resolved issue which would cause some blog entries not to route correctly. (Especially old ones.)
https://forum.pulsecms.com/t/blog-url-renaming-not-working/1928/17
- Case-insensitive comparisons for blog URL routing to avoid typos.



## Pulse 5.3.11
*IMPROVEMENTS*
- Redactor 3.4.1 Added
https://imperavi.com/redactor/log/
Including better pasting of text, line breaks, inserting and processing code in tags, lists and many more updates
- Latest Vue.js added which includes improvements for Inline Editing with RapidWeaver 
- Editors can now delete items (pages/blog posts/etc)
- Editors can now also upload Media items

*FIXES*
- Fix to syntax error in the Forms if a form field not filled in correctly 
- Fix for the Manage User Group setting sometimes showing a "Undefined" error
https://github.com/yuzoolcode/pulsecms-tracking/issues/143
- Fix for image links in OGP images adding Base URL twice
- The Gal, Masonry, Slider and Thumb tags now take the same arguments
- Lightbox fixes for Masonry, Thumb and Slider tags (Thanks John Robertson for reporting these!)
- Editor user now can create and edit Blog posts without errors



## Pulse 5.3.10
*NEW*
- Just Forms now moved to its own domain. Please use this from now on to create new forms and update your sites to 5.3.10 to keep current forms working with the new tag. Access the builder from the Pulse dashboard
- Text Editor now includes an option to open links in a new window
- CodeMirror now integrated for line numbers and easier to read HTML if editing content from the code view

*IMPROVEMENTS*
- On install it sets up the default menu better so it's a little simpler for the demo first site Navigation
- Pulse installs take up under 50Mb (more with templates and content) but should be small enough for majority of web hosts, but we've added a check on the install just in case
https://forum.pulsecms.com/t/fatal-error-allowed-memory-size/1808
- Updated Redactor to 3.3.3
https://imperavi.com/redactor/log/

*FIXES*
- PDF files are now visible in sub folders
https://forum.pulsecms.com/t/pdf-files-in-sub-folders-not-visible/1789
- {{blog-show:blog}} is fixed to run correctly
https://forum.pulsecms.com/t/blog-links-to-details-dont-work/1761
- Logout as an Editor issue fixed
https://forum.pulsecms.com/t/one-more-time-can-not-log-out-as-an-editor/1793
- Plugged in a fix to stop the inline Redactor from firing up when blog items are edited. The only way to edit them now is using the modal
- Template tag processing bug fixed



## Pulse 5.3.9
*NEW*
- Olark Tag - you can now easily add an Olark Chat widget to your site using the {{olark:id=xxx-xxx-}} tag - thanks @armen!
- New Navigation Settings Widget! Better drag and drop, easier to make sub menus and nested menus, deleting is more intuitive, 3 levels deep sub menus are easy to make, non live items are in a new Pending column


*IMPROVEMENTS*
- Users now stay on Navigation page after saving
- Improvements to Locked page login screen for client portals - takes branding from Media folder
- Improvement for Stats links that were truncated. The cutoff is now 60 characters instead of 30
- The WYSIWYG setting now controls also the Editor for Blog posts
- Added some error trapping when uploading Pulse core updates that exceed the server upload limit. This happens when the upload is larger than the POST MAX SIZE limit. So uncommenting that in the .htaccess fixes this.
- The blog page wraps the blog data in its own inline edit widget
- The ##more## tag now doesn't appear for logged-in users when inline editing 


*FIXES*
- Bug fix for deleting all items in the Navigation Menu (as well as general deleting) 
- Added support for percentage % characters in attribute strings for {{block_loop}} tag
https://github.com/yuzoolcode/pulsecms-tracking/issues/135
- Updates to MailChimp Forms integration 
- Embed titles missing on blog posts embed command is fixed
- Fix for special characters in blog and media
https://github.com/yuzoolcode/pulsecms-tracking/issues/133
- Shop items now appearing in Ecommerce store and the JS errors have gone
- Inline editing now saves correctly 
- Fix for Dropzone uploader tag PDF not working
- Fix for some users having a white screen after uploading 5.3.8. It is also recommended to use PHP 7.2 or higher as this is the current version getting security fixes: https://www.php.net/supported-versions.php
- Fix for selecting images from sub folders in inline editing mode 
- Fix for blog editing working in inline edit - opens in its own modal with all the different fields
- Fix for blog item filtering removing dashes "-"
- Fix for the blog routing that was removing the string "blog", so subblogs containing the name "blog" were mangled



## Pulse 5.3.8
*NEW*
- Added a Plugins & Tags Scanner page to the Account dropdown. This allows users to quickly see what plugins & tags are installed and a quick reference on how to use them. Any uploaded custom Tags will also appear here
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/25


*IMPROVEMENTS*
- Added note in .htaccess about for working locally without SSL installed
- Added Redactor Editor 3.3.2 
https://imperavi.com/redactor/log/
- More clear inline editing boxes
- There are some new translations in the language files for {{form}} tag on multi-lingual sites, including reCAPTCHA. The translations are used for the form labels if the user has not changed them in the admin settings. 
https://github.com/yuzoolcode/pulsecms-tracking/issues/115
https://forum.pulsecms.com/t/how-can-i-add-fields-in-the-form/1611
- Duplicate Block button now redirects to the Block just created
- Local Font Awesome now running the latest v5 version and it loads everywhere so can now be easily accessed in Templates using the HTML calls without loading it again
- Plugged in a settings option for showing/hiding the Blog teaser image
https://forum.pulsecms.com/t/how-to-get-rid-of-the-blog-picture-teaser-image/1600
https://forum.pulsecms.com/t/blog-teaser-picture/1608
- Allow for spaces and dashes in navigation urls and labels
- Added more possibilities to user group management. Added the picture element so access to images can be controlled via the user groups. This is limited to logged in users only though. The picture element has these attributes: source, alt tag, width and height. The media player now checks the user group access as well. Blog access can be controlled by setting the access rights on the blog page. On things like the admin stats page, that’s limited by the editor users. 
https://forum.pulsecms.com/t/user-groups-more-possibilities/1654
- Inline editing Block in Block save improvement of icons and edit boxes
- Navigation front-end tag menu can now display nested sub-menus
Thanks @annett for this request


*FIXES*
- Error with € in Ecommerce tag. This now fixed. HTML entities are also supported for the currency symbol now.
- Dropzone Icon now definitely appearing
https://github.com/yuzoolcode/pulsecms-tracking/issues/125
- PDFs and other file types now re-appearing in Media Folders
https://github.com/yuzoolcode/pulsecms-tracking/issues/128
- Improvement to Disqus comments embed in Blog posts
https://forum.pulsecms.com/t/disqus-javascript/1716/2
- All form fields are now in the message body 
https://forum.pulsecms.com/t/how-can-i-add-fields-in-the-form/1611 
- CTRL+S (for Mac+PC) now saves updates in the WYSIWYG editor again
https://github.com/yuzoolcode/pulsecms-tracking/issues/120
- Blog Featured Images selector can now access image folders
https://github.com/yuzoolcode/pulsecms-tracking/issues/113
- Tag descriptions in Blogs not being deleted when updating between versions
https://github.com/yuzoolcode/pulsecms-tracking/issues/117
- Blog Description Label is back in META drawer
- Blog Post Featured Image now has the correct URL
https://github.com/yuzoolcode/pulsecms-tracking/issues/118
https://forum.pulsecms.com/t/blog-featured-image-media-selector/1676/2
https://forum.pulsecms.com/t/5-3-6-blog-featured-image-wrong-url-solution/1661
https://forum.pulsecms.com/t/featured-image-problem/1724
- Preview in Pulse now goes to correct place if installed with RapidWeaver
https://github.com/yuzoolcode/pulsecms-tracking/issues/75
https://forum.pulsecms.com/t/preview-in-admin-panel-goes-to-wrong-folder/1231/4
- Broken link in media sub folder galleries now fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/130 
- Fix for Non-Pulse URLs in Sub menus now handled properly
https://forum.pulsecms.com/t/non-pulse-items-dont-work-as-sub-menu-in-navigation/1725
- Fix for hover bug in the admin navigation edit page
https://forum.pulsecms.com/t/impossible-to-add-more-than-1o-items-to-navigation/1726
- Fix for Navigation Logged in Admin user Toolbar no longer going to double // when clicking the logo



## Pulse 5.3.7
*NEW*
- Added Stripe Checkout Tag for quick buy now items on any page or block
{{stripe:publishable-key:sku:success-url:cancel-url:text}}
- Added PayPal Tag for quick buy now items also
{{paypal:email:currency-code:success-url:cancel-url::text:price:description}}
- It's easy to make Pulse addons - see here for details
https://github.com/yuzoolcode/pulsecms-plugins


*IMPROVEMENTS*
- Various UI and UX improvements
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/5
- Moved the Managed Tags setting to under Blogs in General
- Moved Ecommerce and Users panes out of settings Extend and into the main side navigation
- There's also an option under Settings > Extend to disable Ecommerce and it will also disappear from the sidebar (and from Editors). It's on by default.
- Hyperlinked to Editor user from main Users page 
- Linked to Manage User Groups from Users page
- Moved Update and Debug into the Account dropdown menu and only show for site Admin
- Moved Avatar Upload to General under Admin username
- Removed Navigation title from main sidebar
- Added website Navigation link to the main sidebar and out of General settings - and this only appears for Admin
- Pages icon improved in sidebar and alignment of icons on iPad
- Removed spacing at top of sidebar
- Integrate RapidWeaver option moved to Extend Plugins pane and improved translations


*FIXES*
- Preview link in settings dropdown fixed again
- Stats layout Blocks not overlapping anymore on width



## Pulse 5.3.6
*NEW*
- Pulse is now PHP 7.0 or higher compatible only! This improves security and allows more powerful features for the future! Please update your server to PHP7 before proceeding...
https://forum.pulsecms.com/t/can-we-drop-php-5-6/1551


*IMPROVEMENTS*
- Extended image selector to now included featured images for Blogs
https://forum.pulsecms.com/t/5-3-4-update-thoughts/1582
- Separated out blogs/media from open page
- Updated Redactor Editor to 3.3.0
https://imperavi.com/redactor/log/
- Update (install) function now blocks updating content, template and pulsecore/storage directories 
https://forum.pulsecms.com/t/5-3-4-update-thoughts/1582
- Pulse Builder exporter improved 
https://forum.pulsecms.com/t/pulse-builder-export-problem/1575/3
- Ships now with version history text


*FIXES*
- Old blog tag fixed and is still supported. 
- Sitemaps now fully support SSL as default 
- Fix for the preview button which was pointing to the wrong place in RapidWeaver installs
- Fix for additional text appearing after Blog loop
- Blog link fix for blog posts on the second page or more
- Sub-blogs variety of fixes including {{blog-show}} tag fixes 



## Pulse 5.3.5 Patch fix
- Date string formatting switches to strftime if intl module is not available on the server



## Pulse 5.3.5
*NEW*
- Videos and Audio in Media now appear as embed pages without autoplaying
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/11
- New Visual Sitemap tag so you can have a list of all pages for accessibility or if you want a visual sitemap for your pages (like a contents page of a book)
{{page-list}} will trigger this
- Tick boxes to make pages and blocks go live or in draft mode for much better usability. Thanks to @ezchile for this idea
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/4
(Blog posts already have a date set feature for this)


*IMPROVEMENTS*
-  Navigation menu builder has had some improvements.The entries appear with the mouse over and the menu uses the first entry for the link target of the menu name.
https://forum.pulsecms.com/t/suggestions-for-5-3-2/1392/24
- Blog URLs from 5.3.3 now switched on as default!


*FIXES*
- Masonry gallery update with Flexbox
https://github.com/yuzoolcode/pulsecms-tracking/issues/109




## Pulse 5.3.4
*NEW*
- Sites now force SSL as default. To switch this off uncomment in htaccess file
- Allow folders in Image Selector to be selectable from Media popup in the post editor
- Added "read more" link to {{recentposts_visual}} and this is now limited to 50 words


*IMPROVEMENTS*
- Updated Redactor Editor to 3.2.0
https://imperavi.com/redactor/log/


*FIXES*
- Tags parsing fix for Shop, Google Maps and Blog categories
https://forum.pulsecms.com/t/not-working-tags/1558
- Blog dates fixed for some locales including Hungarian eg 27. jún 2019
- Admin dashboard has a date format fix



## Pulse 5.3.3 Patch fix
- PHP 5 method / function signature fix
https://forum.pulsecms.com/t/pulse-5-3-3-is-here/1549



## Pulse 5.3.3
*NEW*
- Better way to add Blog featured images with image popup and selector
https://github.com/yuzoolcode/pulsecms-tracking/issues/103
https://forum.pulsecms.com/t/recent-post-visual/1458/7
- Re-ordering of images in media can now be dragged and dropped (great for when lots of images exist) with drop targets for next/previous pages
https://github.com/yuzoolcode/pulsecms-tracking/issues/104
- URLs of the form blog/A-Very-Nice-Blog-Post or blog/subblog/A-Very-Nice-Blog-Post are now supported
https://github.com/yuzoolcode/pulsecms-tracking/issues/102
- Extended Blog loop to make it more flexible. The current Blog tags all work but if wanted to separate the template fro the code use something like: {{blog-loop-start}} ...my own layout with divs, cards, whatever {{blog-title}} {{blog-content}} etc. {{blog-loop-end}}


*IMPROVEMENTS*
- German language updates for Email newsletter copy
https://forum.pulsecms.com/t/styling-the-newsletter-signup-email-list/1455/8
- Changed "Home" in the breadcrumb admin to "Home page" to represent the breadcrumb hierarchy as: Home page > admin > pages 
- Made the installer clearer with new Administrator username requirement
https://forum.pulsecms.com/t/default-credentials/1477
- Improved usability handling after uploading an image
https://github.com/yuzoolcode/pulsecms-tracking/issues/101
- Truncation is now based on number of words
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/6
- Also with {{recentposts_visual}} tag it now strips pulse tags from a string - also removes ##more##
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/6
- Reduced ZIP install size again! Compressed images, cut down demo images and made video assets remote. And cut down core files such as unused Font Awesome and other dependencies.
https://forum.pulsecms.com/t/updater-not-working-from-5-22-5-3/1377/6
- Featured image in Blog with social share - improvement to only show image OGP meta tags if there is a featured image or image within a blog post
https://forum.pulsecms.com/t/featured-image-in-blog/1457


*FIXES*
- Dropzone upload icon back for Font Awesome 5
https://github.com/yuzoolcode/pulsecms-tracking/issues/106
- Fix for Sweet alert error X image
- Hide bullets in Pages META
- Ecommerce / Shop table headers fixed to one line
- Better handles leading slashes in image URLs for {{recentposts_visual}}
- Fixed the Media Gallery sub folder sorting bug
https://forum.pulsecms.com/t/suggestions-for-5-3-2/1392
- Plugged in a fix for showing audio/media files in the media folder
https://forum.pulsecms.com/t/work-started-on-5-3-3/1481/3
- CSS fix to ensure bullets appear for unordered lists in Editor mode 
- Fix for editor adding blocks now adds the block to the editor block ACL list



## Pulse 5.3.2
*NEW*
- Allow option for input on login username instead of dropdown - the select is now an input with a datalist
https://github.com/yuzoolcode/pulsecms-tracking/issues/94
- Custom Post Types can now have multiple tags and basic audio/video/image support 
https://forum.pulsecms.com/t/new-types-of-content-for-custom-posts-types/1334
https://forum.pulsecms.com/t/multiple-custom-post-type-tags-on-one-page/1367
- Added a blog-item-title tag to the Blog loop layout, so that can be moved around. That means the blog tag now needs parameters if the layout needs to change. The blog tag has become:
{{blog:"blog":"[[featured-image]] [[blog-content-loop(<<blog-item-author>> <<blog-item-date>> <<blog-item-featured-image>> <<blog-item-title>> <<blog-item-content>>)]]"}}
https://forum.pulsecms.com/t/set-the-title-of-a-blog-post-below-the-featured-image/1368


*IMPROVEMENTS*
- Cleaned up the Ecommerce Shop UI and added a currency drop down
https://forum.pulsecms.com/t/pulse-ecommerce-final-release/1370/21
- Blog post saving now handles save paths with more than one "content" sub string
https://forum.pulsecms.com/t/blog-post-won-t-save-fatal-error/1408
- Improved error handling with user agent processing
- Gallery/Masonry - initialise and load baguetteBox more efficiently
https://forum.pulsecms.com/t/5-3-2-is-getting-ready/1434
- Redactor updated to 3.1.8 with various improvements and fixes
https://imperavi.com/redactor/log/
- Turned logging on for desktop blogging to help reverse engineer any possible MarsEdit issues
https://forum.pulsecms.com/t/marsedit-configuration/1386/10
- Adding d.m.Y and free form option to the date settings..turned the date field into a textbox and added a datalist. The old select has been removed
https://github.com/yuzoolcode/pulsecms-tracking/issues/93
- German dates in dashboard improved for Windows with new dependency to help compatibility and rendering 
https://github.com/yuzoolcode/pulsecms-tracking/issues/97
https://forum.pulsecms.com/t/after-update-to-5-3/1373/3
- Custom post type tag has the new css class and html cleanup - thanks @ezchile
- Usergroup - access control is now only for logged in users on block content
- Update composer installed packages (PHPMailer specifically)


*FIXES*
- {{recentposts_visual}} - Issues with title link and image path now fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/99
https://forum.pulsecms.com/t/pulse-5-3-1-recentposts-visual-issues/1388
- {{recentposts_visual}} bug fix for images in the blog
- Link images on sample page for Onepage-creative template are now correct as a sub template
https://forum.pulsecms.com/t/pulse-5-3-1-is-here/1387/6
- Preview error when blog is in draft mode has been fixed
- The blog search no longer shows content from the admin area (blocks not in use and draft entries)
https://github.com/yuzoolcode/pulsecms-tracking/issues/95
- Fix for the page not showing after been added for some user groups
https://github.com/yuzoolcode/pulsecms-tracking/issues/100
https://forum.pulsecms.com/t/cant-get-users-groups-to-work/1412
- Fixes for the OGP featured images. Pulse now uses featured images for blog items (if they exist), the body tag has been refactored and there is a open graph image loading fix (per page)
https://forum.pulsecms.com/t/ogp-featured-images/1378/16
- Small bug fix for social exposure tag regarding sharing 
https://forum.pulsecms.com/t/suggestions-for-5-3-2/1392
- User roles - editor access checking. Bug fix for handling missing GET parameters more gracefully
- Admin - breadcrumbs helper - handle change of admin folder name properly
- Recent posts no date parameter fixed. Eg {{recentposts_visual:"5":"blog":"false":"200"}} will omit the date



## Pulse 5.3.1 
*NEW*
- Language/locale setting for HTML HEAD tag. Allow to vary per page. NB the default is the language setting in the configs. Thanks to @norm from Blocs app for this idea
- New tag: {{recentposts_visual}} - this adds more control over the {{recentposts}} blog loop including auto image scanning, preview text and more
https://forum.pulsecms.com/t/a-new-type-of-visual-recent-post-tag/1357
- New social_exposure feature replaces the deprecated {{ogp}} tag and pulls in images from a dedicated media folder - with options all in settings for the default and more social media integration
https://forum.pulsecms.com/t/ogp-featured-images/1378/11


*IMPROVEMENTS*
- LinkedIn button added to {{social}} Share tag and replaces the now defunct Google+ 
- Backup has been extended to also include tags, plugins and other custom things in the “inc” folder added by users
- The OGP is now using /media/branding/pulse5_banner.jpg as the default
https://github.com/yuzoolcode/pulsecms-tracking/issues/96
- Store / Shop page example added to Pages folder with tag inserted as an example store
- Added ip-api.com for non-commercial sites as another Geo-IP option 
https://forum.pulsecms.com/t/please-help-with-geoip-data-for-site-statistics/1318
- Tweak to btn css selectors to be more specific so there is less chance of a clash with integrations like custom Themes or Blocs app - thanks to @norm for the suggestion 
https://forum.pulsecms.com/t/blocsapp-und-pulse-button-problem/1372/5
- Pulse commerce is now updated and improved:
  * You can select another currency like EUR and all the currency symbols should now work
  * The products editing screen has been improved for editing
  * PayPal checkout page is working
  * You can now add individual shipping amounts per item
https://forum.pulsecms.com/t/pulse-ecommerce-final-release/1370/12
  * The tag has been updated to: {{unishop:"test@this.that":"USD":"$":"US":"0":"/paypal_ok":"/paypal_cancel"}}


*FIXES*
- Search now works correctly with Pulse installations in subfolders 
https://github.com/yuzoolcode/pulsecms-tracking/issues/92
-  Check back button solution doesn't always go to root (from page 2 of Blog for example). Use referrer if it exists
https://github.com/yuzoolcode/pulsecms-tracking/issues/90
- Tracker fixes - user agent processing handle missing headers more gracefully
https://forum.pulsecms.com/t/blog-tag-search-not-showing-all-results/1366/5
- Renaming Media issue has been fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/91
- Blog pagination fix for different blog url prefixes
https://forum.pulsecms.com/t/blog-tag-search-not-showing-all-results/1366/7
- Update {{recentposts}} to work with multi-blogs
https://forum.pulsecms.com/t/two-different-blogs/1349/7
- Fixed {{cookieconsent}} tag errors 
https://forum.pulsecms.com/t/limit-blog-content-loop-help/1361/4



## Pulse 5.3
*NEW* 
- Pulse Commerce released! Add a quick shopping cart of products to any Pulse site and edit the products from the Pulse dashboard. No install nor database needed.
- Use the new Pulse Tag: {{unishop:"test@this.that":"USD":"US":"0":"/paypal_ok":"/paypal_cancel"}}
The arguments are: PayPal email, currency, locale, no_shipping, return url and the cancel url.
- Manage your Products by going to Settings > Extend > Manage Store



## Pulse 5.2.3

*NEW*
- Two new amazing Bootstrap templates from StartBootstrap.com: Creative and Coming Soon. Select them right from within Pulse - thanks to @sharif for these!
- Breadcrumbs are now localised
https://github.com/yuzoolcode/pulsecms-tracking/issues/82
- Added Mailchimp lang settings and other tags
https://github.com/yuzoolcode/pulsecms-tracking/issues/78
- Developer: Theme selected has an added array for CSS, Saas, font, assets etc
https://github.com/yuzoolcode/pulsecms-tracking/issues/84
- Option to cancel Blog heading link if want a single page Blog
https://forum.pulsecms.com/t/cancel-blog-heading-link/1254
- Navigation updated to allow for Classes if needed
For the navigation tag you can use {{navigation}} still or something like {{navigation:"all":"css classes"}} 
The first parameter is the label parameter which is used to select a menu. The default is "all".
https://forum.pulsecms.com/t/pulse-tag-navigation/1286/6
https://github.com/yuzoolcode/pulsecms-tracking/issues/88
- Allow other pages than home to be used via Settings in Admin
https://github.com/yuzoolcode/pulsecms-tracking/issues/83


*IMPROVEMENTS*
- Redactor updated to 3.1.7 with fixes to Vimeo embed, RTL text, ImageFigure and others
https://www.imperavi.com/redactor/log/
- Changed order undo/redo and underline in WYSIWYG toolbar
- The filtering for submenu names has been improved. We've updated the filters to allow for UTF8 letters and digits. 
https://github.com/yuzoolcode/pulsecms-tracking/issues/77
https://forum.pulsecms.com/t/no-special-characters-in-the-menu-items/1241/7
https://forum.pulsecms.com/t/german-umlauts-and-spaces-disappear-in-the-navigation/1304/8
- UTF-8 now set in the backup emails
https://github.com/yuzoolcode/pulsecms-tracking/issues/81
- Updated Font Awesome so now in sync. Backend and Default demo now both run off 5.2.0 with admin using local SVGs added also
https://github.com/yuzoolcode/pulsecms-tracking/issues/79
- Added error message now shown in user management. The status messages now have an optional level
https://forum.pulsecms.com/t/winter-2019-update-pulse-5-2-2-is-here/1246/3
- When Editor is switched off it also won't appear on the login
https://github.com/yuzoolcode/pulsecms-tracking/issues/85#issuecomment-454619823
https://github.com/yuzoolcode/pulsecms-tracking/issues/85#issuecomment-454619823
- Z-index of inline edit toolbar is higher as it was under elements on some page setups
- RSS importer improvements for WordPress feeds
https://forum.pulsecms.com/t/blog-import-error-for-simplexml-load-string/1316/9
- The featured images are now used in the OGP tag. That can be changed by embedding an {{ogp:image}} in the blog item content.
https://forum.pulsecms.com/t/images-not-showing-up-in-social-media-shares/1289/4
- The gal tag now handles sub folders properly. Added a third tag (values "yes" or "no", default "no" ) for showing all of the images in a folder or not.
https://forum.pulsecms.com/t/images-in-the-media-subfolder-are-not-displayed/1309/9
- Redactor image chooser now including images in sub folders
https://forum.pulsecms.com/t/images-in-the-media-subfolder-are-not-displayed/1309


*FIXES*
- Redundant text in the menus - submenu items have folder name removed
https://forum.pulsecms.com/t/no-special-characters-in-the-menu-items/1241/7
- Fix for OTP password encoding in “forgot your password?” emails



## Pulse 5.2.2

*IMPROVEMENTS*
- Detect missing .txt on files and handle them better
https://github.com/yuzoolcode/pulsecms-tracking/issues/71
- Sample.htaccess to mirror included htaccess for file upload parameters as a prompt if needed
- Fixed cache clearing on the open pages as a precaution 
https://github.com/yuzoolcode/pulsecms-tracking/issues/73
- Admin - edit items handle no user group more gracefully
https://forum.pulsecms.com/t/5-2-adding-users-and-groups/1226
- Added error message for the no group case and disabled the redirect to the groups page (when there are no groups)
https://forum.pulsecms.com/t/5-2-adding-users-and-groups/1226
- User management improvement - consolidate per page and group resource access. The group access list is now the ground truth. The page/block group setting now adjusts this too.
- User management improvements - renames/deletes now update the user group permissions
https://forum.pulsecms.com/t/php-errors-when-saving-content-or-creating-user/1211/5
- Added graceful notifications to Pulsecore updates! Thanks @jdlouden for the idea (shows error messages and the upload size allowed)
https://forum.pulsecms.com/t/pulse-5-2-is-here/1210/3
- Updated Redactor to 3.1.6
https://imperavi.com/redactor/log/
- Handle Open Live Writer editing Blog posts more gracefully
https://forum.pulsecms.com/t/open-live-write-mobile-editing-offline-editing/1222
- Add an option in {{recentposts}} to not show date. Add a third parameter for tag, anything other than an empty string is taken to turn the date display off (e.g. 1)
https://forum.pulsecms.com/t/time-stamp-in-recentposts-tag/1197/2
- Redactor Editor screen now has a minimum height of 380px so that it is not too small when opening a Block for the first time with no content inside
- Pulse now checks the permissions of content/backups and content/stats in the installer and skip daily backups if no content/backups and log an error message - thanks to @norm from Blocs app for this suggestion
- Added some error trapping for the admin navigation management page for non UTF-8 characters


*FIXES*
- Logout too quick - bug fix and regenerate session ID for logged in users
https://github.com/yuzoolcode/pulsecms-tracking/issues/74
- RecentPosts tag no longer show draft items
https://github.com/yuzoolcode/pulsecms-tracking/issues/68
- When change “admin” path in settings it no longer breaks the site
https://github.com/yuzoolcode/pulsecms-tracking/issues/72
- Fix for Backup download error on some environments 
- Page rename filtering fixed for pages with non UTF-8 characters


## Pulse 5.2.1

*FIXES*
- Errors when creating users 
https://forum.pulsecms.com/t/php-errors-when-saving-content-or-creating-user/1211/2


## Pulse 5.2BETA5

*NEW*
- Unlimited user management added! It's now possible to add as many users as you'd wish via the Admin settings and assign them Admin, Editor or Guest roles. You'll now be able to add more contributors to big sites like schools and universities or for membership sites for private communities. The block/page editor can set which groups are allowed access.
- Allow users to change the avatar from the Admin Settings (rather than just Media folder)
- Redactor updated from 3.1.4 to 3.1.5
https://imperavi.com/redactor/log/

*IMPROVEMENT*
- Added a failsafe check for Media folder/images when users add them via FTP and not the admin uploader - so the Media folder and Admin view is in sync
https://github.com/yuzoolcode/pulsecms-tracking/issues/62
- php mailer now moved to vendor directory
- Added some logic to stop the GEO IP call if the key is missing (for the services that need them).
https://github.com/yuzoolcode/pulsecms-tracking/issues/64
- Removed the old default freegeoip for the stats since it's now deprecated
- Better country detection for GeoIP
- Improved the Blog Api for integration with desktop blogging apps like OpenLiveWriter. The blog cache might need to be cleared in storage/cache to see the fix
- Improved RSS importer for RapidWeaver 8 and Armadillo in particular
https://forum.pulsecms.com/t/how-to-import-rapidweaver-rss-blog-into-pulse-5-cms/1139

*FIXES*
- Added a new safety check to raise an error if the old config.json cant be backed up or doesn't exist
https://forum.pulsecms.com/t/pulse-5-2beta4-release/1181/9
- Regenerate the json config if needed
- Selecting/unselecting delete and activate in Navigation Menu Settings now sets the menu items too. Save works correctly
- Drag and drop images order in Media is now working correctly
- Fixed Footer showing JSON on install. The fix is to ensure the template parses footer blocks instead of just including them. So use {{block:copyright}} instead of including it.
- Installer Bug fix
https://github.com/yuzoolcode/pulsecms-tracking/issues/63
- Plugged in an update for the redactor alignment bug. The fix is to allow figures in the output (imageFigure setting for redactor).
https://github.com/yuzoolcode/pulsecms-tracking/issues/65
- Pulse now applies the Page Description correctly. The "Page Description" is now used if "Custom META description" is empty.
https://forum.pulsecms.com/t/pulse-ignores-page-desc/1143


## Pulse 5.2BETA4

*NEW*
- Redactor from 3.1.1 to 3.1.4
https://imperavi.com/redactor/log/
- Added a “cookie consent” tag! 
Eg {{cookieconsent:#000:#f1d600:edgeless:bottom:www.site.com}}
Variables are: popup background, button background, themes, position, link
https://forum.pulsecms.com/t/need-help-for-gdpr/1148

*IMPROVEMENT*
- Switched over some code to non jQuery
- Date of last revision added to policy/T&C tag
NB this defaults to the last time the file was changed if the third tag parameter is not present
- Added an intermediate step to the Pulse install updates process that lists the files that will be overwritten. The .htaccess and anything in the content directory are flagged
- PHPMailer updated to the latest version

*FIXES*
- Backspace/delete not working in Tables within Redactor WYSIWYG Blocks
https://github.com/yuzoolcode/pulsecms-tracking/issues/60
- Removed snoopi service from geoip options
https://forum.pulsecms.com/t/pulse-5-2-beta2-is-out-new-theme-smaller-footprint-and-offline-blogging/1128/11
- Sub folders under pages - bug fix for names ending with periods
- Updating error when Pulse is in a sub folder is now fixed
https://forum.pulsecms.com/t/error-message-using-built-in-update-option/1156
- Plugged in fix for error with blog loading in some cases. Thanks to @jprezleon


## Pulse 5.2BETA3

*NEW*
- Added css classes to the pagination view helper so pagination links can be styled or hidden by adding a class to the Pulse template
https://forum.pulsecms.com/t/upgrading-from-pulse-3-to-pulse-5-on-non-template-site/1043/10
- Reverse blog option in settings, so newest posts appear last. Useful for an events page or needing oldest first ordering
https://forum.pulsecms.com/t/reverse-blog-function/1037
- Added few more buttons to backend Redactor toolbar: Underline, Undo, Redo. So can now undo easily content changes!
- Added an option in Form Settings to hide the GDPR consent box in forms (for users who don't need it)
https://forum.pulsecms.com/t/gdpr-input-field-on-forms/1088
- Added a page cookie agree pop up tag for GDPR! {{gdpr}}
- Global Saving added to Inline Editing! So 1-click save can also clearly save all the edits to make it even easier to use
- Non-Pulse links in the Navigation can now also open in New Window

*IMPROVEMENT*
- Changed the {{recentposts}} link so the date is not in highlight; just the title next to it. Thanks @RHKay
- Switched off <figure> wrapping for images added via Redactor uploader, so can target <img> tags directly for lightboxes or anything else
https://forum.pulsecms.com/t/how-to-add-a-single-image-lightbox/950/9
https://forum.pulsecms.com/t/images-are-reduced-to-50-in-size/1084
- Auto generate the blog url slugs. The user can change this but by pre-filling it saves time in making a blog post with less clicks.
- {{sb_login}} tag now allows for blocks to have different passwords
- {{sb_login}} tag also now has better redirecting on Locked page if password is wrong. There's a customisable error message and the user stays on the page. sb_login uses "Your Password is Incorrect!" for the error message from the language strings. And can also be amended in the tag:
{{sb_login:"password":"otp":"block_name":"error message"}}
- {{sb_login}} with an empty block name leads to locking the page instead of the block. 
- Made Drag and Drop with Navigation a little more stable, especially in Chrome on PC
https://forum.pulsecms.com/t/adding-sub-menu/1073/5
- Inline Editing has been improved again! Save button is now green and the color button is added to the toolbar. 
https://forum.pulsecms.com/t/bring-back-green-edit-boxes/1113/6
- Top level Navigation link now allows for external links - not just in-site links
https://forum.pulsecms.com/t/problems-with-the-frontend-while-i-am-working-in-the-backend/1106/17


*FIXES*
- {{recentposts}} tag now works with multi-blogs (sub-blogs). The tag takes two parameters (both optional). The first is the number of posts to show and the second is the blog to use. eg {{recentposts:"10":"blog/another"}} for a sub blog.
- Bug fix for the image chooser in Redactor and selecting images from sub folders
https://forum.pulsecms.com/t/problems-with-the-frontend-while-i-am-working-in-the-backend/1106/19
- Fixed the bug also in the admin direct image upload.
- Also removed icons, branding and thumbnails from the Redactor image chooser
- Plugged in a fix for the media block on the admin dashboard page. The media block had thumbnails and the gallery file.
- Plugged in a fix for a GeoIP bug. 
- JSON error in the top of the page
- Blog {{social}} is now posting complete URL on single posts
https://forum.pulsecms.com/t/blog-social-tags-issue/1083
- Plugged in a fix for Blog META appearing in first line of Blog content. 
- HTML bug fix for media list with bullets on the dashboard


## Pulse 5.2BETA2

*NEW*
- Brought back Inline-Editing boxes and IDs so easier to edit inline and know what you are doing and improved colours so more subtle 
https://forum.pulsecms.com/t/bring-back-green-edit-boxes/1113/

*IMPROVEMENT*
- Improvements to default theme: logo a little smaller, Responsive menu improvements and a margin added to the back button in single post page.

*FIXES*
- Inline editing and a few other things missing from Default template now fixed (tags added again)


## Pulse 5.2BETA1

*NEW*
- Offline Blogging Desktop Capabilities! Archive, write, preview, and publish your Pulse Blog posts with MarsEdit on your Mac or Open Live Writer for the PC. Make it even easier to edit your blog without needing a browser!
- Brand new Default Template using Bootstrap4! Previous template moved to “default-2” folder, as an example of a child template
- Select the service to use for the GeoIP settings in the stats. Select from: freegeoip, ipstack, snoopi. Enable in Settings and input the corresponding API key 
- Added pre text on blog tags such as "posted in" - can be changed in language files
https://forum.pulsecms.com/t/upgrading-from-pulse-3-to-pulse-5-on-non-template-site/1043/13
- New Dropdown styles added to default-2 template including responsive drawer menu on mobiles (new template also has beautiful mobile responsive sub-menu items!)

*IMPROVEMENT*
- Reduced File Size and number of Files of Pulsecore in order to speed up SFTP transfers on install! 83.57% file item size crushing! Only 17MB to upload!
- Added date to {{recentposts}} tag - thanks @rie05
- Reduced gap before inline edit boxes so doesn't jump down the page when visited as logged in user
- Added more space between Blog Tags
- Moved "Back" button in Blog to bottom of post (from top)
https://forum.pulsecms.com/t/styling-pulse-cms-blog-pages/1061
- Redactor from 3.0.11 to 3.1.1
https://imperavi.com/redactor/log/
- Improved deep links so Pulse installs on some server setups (which put all sites in the root) now work seamlessly with multi-site installs on the same server
https://github.com/yuzoolcode/pulsecms-tracking/issues/59
- Editor user dashboard improvements: Block list showing for editors in the dashboard, download backups no longer showing and the sidebar shows pages that the editor can edit
https://forum.pulsecms.com/t/need-editor-capability-clarification/1063/2
- Pages List in the Admin now shows the page title as well as the filename
- Login Admin Template welcome message now editable from the Admin Settings page. The old translations are used as the default if nothing entered
https://forum.pulsecms.com/t/personalising-admin-login-screen/1092
- Navigation CSS moved from Pulsecore CSS file into the template CSS files so much cleaner and easier to work with. All custom templates need to include menu css in the their own assets.

*FIXES*
- Dashboard Notes Block Unordered List fix
- Policy Tag fix for the displayed font
- Fix styles issue for Locked pages using {{sb_login}} tag
https://forums.realmacsoftware.com/t/pulse-5-1-password-protection/20940
- Renaming Blocks with conflicting directory names (eg Contact Form) is now fixed
- Editor login bug - session cleanup and typo fix
https://github.com/yuzoolcode/pulsecms-tracking/issues/53
- Moving images now rebuilds the gallery.txt files and thumbs
- Related Gallery txt issue fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/56
- Navigation Page Titles in the site navigation can now contain spaces eg "About Me"
- Fixed when admin is logged-out the policy page was broken and clashed with some theme CSS styles


## Pulse 5.1BETA5

*NEW*
- Nós falamos Português do Brasil! Pulse5 now ships with Brazilian Portuguese as standard in the admin
- Мы говорим по-русски! Pulse5 now ships with Russian as standard in the admin
- Added checkbox to contact form for agreeing t&c for GDPR. It's also recommended to use SMTP and SSL servers to receive email.
- Log viewer added in settings > extended area. Download the logs to debug/error test or viewer directly inside the Admin settings
- New things ready for RapidWeaver integration: Inline Editing, lightbox styling, statistics and various bug fixes

*IMPROVEMENTS*
- Updated to Redactor 3.0.11 (from 3.0.9)
https://imperavi.com/redactor/log/
- The Masonry Gal and Blog tags (blog-list, blog, blog-tags) now use Flexbox!
- Sub blog embed tag added {{blog-show:subblog}} to embed helper view
- {{blog-show:blogname}} now shows all blog items without pagination for a simplified sub-blog. Sub-blogs allow you to show more than one blog on the same website! To get its categories use: {{blog-tags:blog/[subblogname]}}

*FIXES*
- Gallery image delete and gallery.txt not properly updated issue has been fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/47
- Fix for Breadcrumb in admin for HTTPs servers
- Fix for links in Footer Blocks. This depends on prefixing the site urls with {{show_var:path}} e.g. <a href="{{show_var:path}}/privacy">Privacy</a>
https://github.com/yuzoolcode/pulsecms-tracking/issues/50
- RSS dates fixed and not showing dates wildly in the future!
- Bug fixes for upload added
https://github.com/yuzoolcode/pulsecms-tracking/issues/48
- Renaming Admin username now correctly changes the directory naming
https://forum.pulsecms.com/t/some-troubles-in-pulse-admin/997
- Fix for Editor user uploading files bug
https://github.com/yuzoolcode/pulsecms-tracking/issues/52
- Fix for running on 1und1 hosting and uploading
- Fix for renaming folders error in Blog folders
https://github.com/yuzoolcode/pulsecms-tracking/issues/49


## Pulse 5.1BETA4

*NEW* 
- Nosotros hablamos Español! Pulse5 now ships with Spanish as standard in the admin.
- A new dedicated loop tag. The usage is {{block_loop:"block folder name":"width of block including units or %"}} 
  eg {{block_loop:"contact":"30%"}} . This will show the blocks in the content/blocks/contact and each block will be 30% of the container width. 
https://forum.pulsecms.com/t/looping-through-a-folder-of-blocks/952

*IMPROVEMENTS*
- Updated to Redactor 3.0.9 (from 3.0.7)
https://imperavi.com/redactor/log/
- Menu items in settings drag and drop now capitalised
- csv, xls, xlxs (Excel) spreadsheet data can be uploaded as a default setting
- The Gal tag now uses Flexbox

*FIXES*
- Plugged in a fix for the password reset email
- Editor username now showing on blog posts


## Pulse 5.1BETA3

*FIXES*
- Update to the Blog “more” link 
https://github.com/yuzoolcode/pulsecms-tracking/issues/43
- Config fix - default value for url_prefix (blog) added so Blog from Navigation links correctly on default install


## Pulse 5.1BETA2

*NEW*
-  Blog posts can now be timestamped for future release!
Future dating a blog post moves it into draft mode. Everyday, posts scheduled for that day will be auto unlocked and released!
- Gallery search support added!
https://forum.pulsecms.com/t/image-gallery-search/966
- There's now a RSS feed per blog! Accessed at /[blogname]/rss
- Blocks and Pages can be flagged to appear in Search results or not! 
https://forum.pulsecms.com/t/search-tag-goes-to-deep/943

*IMPROVEMENTS* 
- Policy Tag improved to add example Data protection policy in accordance with the EU General Data Protection Regulation (GDPR)
- Blog tag list now sorts into alphabetical order on the blog page
https://forum.pulsecms.com/t/blog-tags-order/971/2
- Layout change for templates! Now using Block tags for embedding CSS and JS in the layout - rather than more complex lines. See the manual and demo theme layout.php for what to do with {{theme_css}}, {{theme_js}} and {{theme_js_body}}
- Dutch language for Redactor Plugins improved and checked 
- Handle empty paths in navigation more elegantly
- Allow for multiple Search Tags on a page
- Updated to latest Redactor 3.0.7
https://imperavi.com/redactor/log/
- Allow zip files uploaded via Media or Redactor to be downloaded
- Allow for bigger sized files to be uploaded. This can be changed in the root. htaccess file
https://forum.pulsecms.com/t/big-file-upload-unsuccessful/982/2

*FIXES*
- Update of Redactor plugins translation for some languages
https://github.com/yuzoolcode/pulsecms-tracking/issues/36
- Insert file in Redactor now definitely inserts and links the file (great for adding PDFs to sites)
- Fix for Email link in Redactor not working when set in a Footer Block of site. 
- Fix for renaming image files and folders in galleries and Media panel
https://github.com/yuzoolcode/pulsecms-tracking/issues/29
- Fix for install timezone check error on some hosts
- Fix for alternative template selection in Page META not working. The tag was working, but not from the admin dashboard.
- Redactor Languages now loading correctly
https://github.com/yuzoolcode/pulsecms-tracking/issues/36
- Added a fix for missing /slash in menu paths. 
- For the Editor user the admin area is now showing the selected blocks/pages now and also not showing all Blocks as administered by Admin user
https://github.com/yuzoolcode/pulsecms-tracking/issues/35
- Media ordering for Gallery and Masonry tags is now working again (drag and drop ordering in Media)
- Plugged in fixes for the @ tag insertion of Email Button in Redactor
- Blog URL prefix change in Settings now handled correctly 
- Sub Blog URL linking problem from titles is fixed
https://forum.pulsecms.com/t/sub-blog-problem/947
- Fixes for Navigation bugs in PHP7.2.3
https://github.com/yuzoolcode/pulsecms-tracking/issues/37
https://forum.pulsecms.com/t/bug-in-demo-menu-problem/981
- Search blog cleanup for snippets
https://github.com/yuzoolcode/pulsecms-tracking/issues/40


## Pulse 5.1BETA1

*NEW*
* NEW! Redactor 3 added! Better design and much easier to use.
https://imperavi.com/redactor/log/
* NEW! Streamlined Inline Editing! No longer a popup. Just click anywhere on the live page as a logged in Editor or Admin and edit the content - like magic! Click and edit!
* NEW! Quick edit mode on front end. Air toolbar pop up shows essential edit attributes such as text styles and images. For more detailed editing and Meta data etc, login to the backend admin panel to refine those changes
* NEW! RapidWeaver integration: this streamlined click-to-edit is also working in RapidWeaver built Pulse sites
* New! Update a Pulse install! Admins can upload the latest Pulse zip file to auto update their Pulse core files install without having to go into the server files. Download the latest from Pulse Dashboard and upload the ZIP. Pulse auto backups up and uploads the latest Pulse build (always recommended to take another pre-backup too of course!)
* NEW! Allow users to add snippets, embed code, tweets etc. as a widget from the WYSIWYG
* NEW! Full formatting is available inside the tables for WYSIWYG. Many people asked about this.
* NEW Tag!! We are able to add new pages to the navigation - good.
But, what if you have a page not in the navigation and want to link to it? {{link:page/animal/hippo}} or {{link:blog/1}} will create links to pages in a Pulse site.
* NEW! Added a Blog “back” button
https://github.com/yuzoolcode/pulsecms-tracking/issues/25
* NEW! Template now includes the language setting. See the Manual around page 38 on how to adapt the <!DOCTYPE html> of the template to auto-insert the page language setting.
* NEW! Blog now shows time of creation (The settings is handled via the date_format setting)
https://forum.pulsecms.com/t/add-publishing-time-in-a-pulse-blog/916/
* NEW! Multi-navigations are now possible for different page layouts. “All" is the default navigation category option and more can be made depending on your site layout needs
https://forum.pulsecms.com/t/use-different-navigation-menus-for-each-layout/895
* NEW! Debug testing included. This allows smoother updates and test cases for confirmed bugs to trap regressions. Export Debug settings from Admin Settings Extend Panel.
* NEW Language - Romanian added!

*IMPROVEMENTS* 
* Improved the Template importer so that it's ready to import Pulse Builder made templates.
* Backend edit mode now scrolls with a floating edit toolbar - great for Blocks and Pages with long content. Saves you scrolling back to top to get edit options.
* RapidWeaver integration: Poster Stack layout settings for blog
* Added additional CSS classes to the blog, also standard RapidWeaver classes, for more blog layouts and more advanced RapidWeaver blog integration
* Blocs app integration: Pulse 5.1 now works with Blocs app for Mac! Make Pulse templates without any code
* Redactor Edit Toolbar now appears as separate squares and not scrolling - looks better on Windows with Internet Explorer
* Navigations also get CSS class for easier styling
* In edit mode, on page load the cursor appears in the edit box at the beginning to help with usability
* Drag and drop improvements for navigation menu settings
* Allow for navigation tag to use sub menus
* Blog page button now works for tagged blog display
https://github.com/yuzoolcode/pulsecms-tracking/issues/28
* Blog “back button” is now aware of blog tags
https://github.com/yuzoolcode/pulsecms-tracking/issues/27
* Blog “back button” is now a relative link rather than JS to avoid “confirm reissue of form” warnings in the browser
https://github.com/yuzoolcode/pulsecms-tracking/issues/25
* Frontend parsedown switched off so it's now possible to edit Markdown easily with inline editing
* Added more blog posts to the demo site
* Admin.css cleaned up and added some more semantic elements
* Admin Dashboard now powered by Flexbox and not Masonry for greater flexibility across screen dimensions!
* Upgrade vue.js to latest build
* Autofocus taken off search input for better usability on mobile

*FIXES*
* Fix for missing SK and CS languages in Redactor settings
* Fix for tracker script - now has the correct content-type header
https://github.com/yuzoolcode/pulsecms-tracking/issues/23
* Fixes for missing Sweetalert errors in admin
https://github.com/yuzoolcode/pulsecms-tracking/issues/24
* Blog tags translation strings that were missing have been added
https://github.com/yuzoolcode/pulsecms-tracking/issues/26
* Deprecated warnings for Localizer in PHP 7.2 fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/30
* Localizer fixed to work inside layout.php
https://github.com/yuzoolcode/pulsecms-tracking/issues/32
* Fix for item path normalisation when embedding in non-Pulse sites
* Fix for the navigation non-pulse urls and to get the sub-menus working better
* Fix for paths being broken in sub folder installs with navigation going to the wrong pages
* Fix for Navigation not appearing on site install without “saving” the navigation in settings to get it to appear
* Fix for linking to files inside the Media Folder with the RapidWeaver integration
https://github.com/yuzoolcode/pulsecms-tracking/issues/31
* Fix for codemirror not working correctly with line breaks - back to standard Redactor 3 code view inside Blocks WYSIWYG
https://forum.pulsecms.com/t/no-broken-source-code-since-pulse-5/946/2
* Fix for Redactor not showing language differences in inline edit mode
* Fix for Redactor issue when https changes to http, and http adds to the email when inserting a link.
https://github.com/yuzoolcode/pulsecms-tracking/issues/17
* The media area now shows images with a dot in the filename
https://github.com/yuzoolcode/pulsecms-tracking/issues/33
* Error with dates in German in the backend Admin now fixed (e.g. March). Setlocal is now improved so all locale aware functions (and not just date/time functions) are using the selected locale.
* Blog username is now being printed on blog posts




## Pulse 5.0.3

*UPDATES*
* Installer - allow for editing the autodetected base path
https://github.com/yuzoolcode/pulsecms-tracking/issues/16
* Installer - adds error logging for better testing if something goes wrong
https://forum.pulsecms.com/t/changing-permissions-breaks-install/910
* Fix for skipping a slash in the URL for sub-menus
https://github.com/yuzoolcode/pulsecms-tracking/issues/20
* Fix for Blog page showing a dispatcher error
https://github.com/yuzoolcode/pulsecms-tracking/issues/19


## Pulse 5.0.2:

**FIX + ENHANCEMENTS**

*USER EXPERIENCE*
* Delete paths - e.g. delete blog no longer takes you to dashboard
* When creating a new folder and renaming, the redirection takes you back to what was being edited, and not the dashboard
* After uploading a theme, redirected back to settings for theme selection 
* Empty blocks no longer killing the site (client protection): https://forum.pulsecms.com/t/empty-block-results-in-blank-page-pulse-5/856/4
* Front end navigation no longer shows extra spaces in the menu item
* Editors now longer see the Page content
* Admin create page - no longer accessible to editors
* Sweetalert popups now have options in the settings for time delay and deactivation 
https://forum.pulsecms.com/t/saved-popup-box/857
* Blog date format now a select (dropdown) on the settings page

*NAVIGATION*
* NEW - allow for adding non Pulse pages to navigation so can link to external pages with drag and drop
* Fix for navigation config setup by installer - no longer need to edit navigation and save to show menu on first install
https://forum.pulsecms.com/t/after-editing-the-menu-fatal-error/859
* Make sure to use the un-tick the "active" checkbox to hide pages in the navigation. Delete will remove this completely, but any page that exists will be added back next time the manage navigation page is saved.Delete should be used for pages which have been removed.
* Sub-menu re-ordering bug fixed 
https://forum.pulsecms.com/t/navigation-not-showing-on-fresh-install-need-to-save-once-in-manage-navigation/871/9

*BLOG*
* Author meta tag is now controlled in the settings - with language settings
https://forum.pulsecms.com/t/remove-pulsecms-from-author-meta-tag/867
* New Tag!
{{blog-list:blog:title,author,date:5}} 
If you want to add further control to this tag. The second parameter is the lines to show author/date/title and the last is the number of items on a page.
https://forum.pulsecms.com/t/blog-list-tag-issues/866
* Blog dates in the past can now be selected
https://forum.pulsecms.com/t/blog-post-prior-date-not-permitted/862
* Blog posts now appear in reverse (“correct”) order
https://github.com/yuzoolcode/pulsecms-tracking/issues/11
* Order is now controlled by date and then by the ID/filename
So for blog posts you make a blog post and Pulse gives you a random string like 201809018898.txt? You can change the name of that post and the URL if you want. When you change the date of the post this affects the order.
* Blog tag descriptions and titles can now be added. It's linked off the "extend" part of the settings page. And can be viewed when viewing a tag.
* URLs issue fixed. It's not necessary to change the .htaccess file for blog item pages now.
https://forum.pulsecms.com/t/blog-urls-pulse-5/863/2
* Blog Draft mode is now fixed. Add "draft" before blog file name to put it in draft mode so "draft1". Delete "draft" to put it live! No more dashes.
https://github.com/yuzoolcode/pulsecms-tracking/issues/4

*LANGUAGE* 
* Folder new page/block language setting added #3
* More unicode allowed
* Updated language strings for all 8 included languages

*POLICY TAG* 
* Now show country and location correctly: 
https://forum.pulsecms.com/t/policy-tag-not-working-correctly/874

*SOCIAL TAG*
* Social tags url bug fixed
https://github.com/yuzoolcode/pulsecms-tracking/issues/7
https://forum.pulsecms.com/t/pulse-5-social-tags-not-working-as-they-should/842

*SEARCH* 
* Removed JSON from search results:
https://github.com/yuzoolcode/pulsecms-tracking/issues/8

*THEMES*
* The fix handles the case where the theme files are in the root of the zip file. Previously, the theme files had to be in a folder (with the same name as the theme) in the zip archive.
https://forum.pulsecms.com/t/templating-questions/851

*WYSIWYG*
* PDFs can be added using the WYSIWYG editor. But if they don't appear PDF embedding can be done using the object tag eg:
<object width="400" height="400" data="[base url]/content/media/testpdf.pdf"></object>

*SPEED*
* Some code optimisations

*FORM TAG*
* Form now using the labels from the settings
https://github.com/yuzoolcode/pulsecms-tracking/issues/6
https://forum.pulsecms.com/t/form-languages-am-i-missing-something/873

*JUSTFORMS*
* Justforms tag embed fixed
* Forms now submit better on mobile devices
* Forms send notifications with reply-to from the user's account for less confusion
* Forms can now be duplicated
* New WYSIWYG editor 
* Better export functionality (export to Excel, CSV etc)


## Pulse 5.0.1:

NEW

* Added version number in footer of Admin Settings, along with a link to the version history
* Added links in admin/install.php to /admin and front end site for easy bookmarking and next steps userflow
* Also default password added to admin/install.php so clearer how to log in before changing the default password in settings (short term access)

FIX

* Redactor update to 2.12
* Navigation in dashboard is now no longer hard coded and responding to lang files
* Added Czech, Slovak and updated Polish, Hungarian to Redactor languages
* Fixed language selection bug for Redactor
* More width to inline editing so easier to edit
* Fixed links so images appear in Redactor in demo site
* German language file update
* German fixes for special characters in backend and inline editing


## Pulse 5.0:

* Config.php now replaced by JSON storage in a folder called ~~XENO~~ pulsecore
* JSON will open up _lots_ of possibilities for 3rd Party Developers to get into :)
* JSON also means password is now no longer able to be seen in plain text.  The password is hashed and salted and no longer printed naked in config file - using bcrypt. Is now as strong as the server and difficult to hack.
* New settings page - control all from settings. No longer need to edit config.php when install just upload and go to /admin/ (password is "demo"). If want to edit on the code level pulsecore > storage > config.json / but shouldn't be needed. Config.php is just needed for the initial login.
* Latest Redactor I added with video button. Will move to II or III soon.
* Redactor II added. Sharp and sleek icons in the toolbar and more plugin compatibility and bug fixes in the editor
* Forgot password added. Sends email to user registered in settings.
* Customise login stylesheet added in admin / admin.css so designers can easily style the login page
* 2 step authentication added - this is **_cool_** - so can lock down with smartphone verification for extra security with a secret key. Use Google Authenticator app to access Pulse admin, along with usual password.
* Settings now has dropdown option for all time zones
* All language files will be included in drop down on install. German and English currently but all baked in soon
* Easy to flip front end home page from home > blog - if want to land on blog
* Can flip admin landing page to dashboard, blog, stats etc for more customised login
* Can use Redactor Editor on Pages or just HTML. No longer client scary!
* New user editor added. This user cannot see settings page and only edit content. Good for client user. Designer (admin) can change their password (which is also hashed) and add 2 step authentication. This also includes secure forgot password emailing backup. 
* Designer can also restrict what pages / blocks editor can edit. For example, just want to create a blog for a client, you can do that now. Or We definitely do not want editors going into the pages section. They already find it hard to understand the concept.  
* Form now has SMTP settings in the settings page
* Themes chooser - you can add more themes in the template folder in sub-folders and select them from settings dropdown for easy template changing on the site.
* Themes can also be set per page too for multi-theme sites.
* Blog now has tags in the META drawer, which can be seen on the front end too and when clicked show all posts in that tag. Blogs posts have the list of tags at the end of the post e.g. "#tag1 #tag2". If no tags are selected then the tag list at the end of the blog post does not appear.
* There is a tag widget for editing tags!
* Each blog post now has individual title + description meta tags - different to the blog page also which has it's own (SEO upgrade).
* Sitemaps auto-generated and placed in root as sitemap.xml including blog posts (thanks to Tim again for the idea!)
* New page or block creation now has a radio button for "page" or "folder" and auto adds .txt to pages and blocks so more user-friendly.
* Embed individual blocks on pages like Pulse v3. Embed URLs are now included for blog, blocks and galleries to add to non Pulse template pages that are in the same folder (gives the option of template driven like in v4 and non-template like v3). Two options: relative & site specific - to cover both use cases. So you can also embed on same site other sites so in effect, controlling many sites from 1 Pulse install is also possible!
* Security fix - https://cxsecurity.com/issue/WLB-2016020228
* Markdown now in Blocks
* Various RSS & Blog error fixes 
* Error reporting is now always on for developers
* Added "MORE" to Redactor toolbar
* htaccess/index.php sanitisation for private directories and turning off access to private urls. Adding rules to the .htaccess to block access too.
* Custom post types added and a new Pulse tag. The idea is that a site selling books (for example) would have fields for ISBN, cover image, date published, price, availability etc. These can be created and managed from the settings panel. Then using the new tag, for example {{custom_post_type:book/hercules}} the custom post data will appear on the front end. Useful for listing data or create e-commerce sites.
* Upload images can now be resampled or original quality. Good for creating an upload repository for photographers for example.
* Gallery outline bug fixed
* New Feature - Drag and drop navigation UI found under settings. Much easier to create navigation over sb_nav.txt for users who don't want to edit HTML links.
* Bug fix - Text position focus moves when doing a "save"
* RSS now works with http or https + tag content now appears in rss.php
* Pulse tags now work when website is located in a sub folder
* Top referrers in Stats links now fixed
* Link to switch to a textarea (and back) for the case where low-level edits are needed on a per block basis (not just in settings) - works pages and blog entries too 
* New tag to add One Time Password to any page on front-end - not just password plugin using: {{sb_login:test:1234567890:testblock}}
* Latest Redactor Editor includes many bug fixes, better mobile use, support for powerful codemirror in HTML mode, classes/ids on images for things like animation or lightboxes, added snippets (such as "more" for blog posts), can paste as plain text automatically and many more...
* Ordered lists and un-ordered lists now appear in editor
* Draft blog feature! Add "draft-" before blog file name to put it in draft mode so "draft-1.txt" etc. Delete "draft-" to put it live!
* Migrate / Import blog feature! Import your Wordpress, Tumblr, Blogger, RapidWeaver or any blog using an RSS / atom feed and migrate to Pulse without manually porting it over! Text, videos and images included.
* Blog import even more in-depth and powerful! Options added for the page extension name and max number of pages for a deeper and wider RSS crawl. It's now a more flexible approach to different kinds of sources and will loop through even long and heavy WordPress blogs over multiple pages with 100s of blog posts!
* Default timezone now JP baby!
* Reqs information now included in admin/install.php for easier server checking after uploading (diag.php now install.php)
* Recommend in most cases run the admin/install.php page firstly to auto-set the path, no need to set it and will run super smoothly!
* Anyone still using less than PHP 5.6 gets redirected to install.php to make them realise they have to upgrade - even PHP 5.3 ended security support in August 2014! Minimum now PHP 5.6
* Auto focus on input fields when page loads for easier user experience
* Latest jQuery included! (3.x.x)
* Security fix for Contact Form - latest version of PHP Mailer added
* Stats UI improvements in some languages & more variables added
* More language variables added for Bounce rate in stats, Blog UI meta data, title, description and the embed instruction
* Forms, Gallery, Slider tags better compatibility with Pulse in sub folders
* Contact form errors when email sent are now fixed
* Blog page can now use an alternative template. In previous versions this wasn't possible and would result in a linking error
* Updated to magnified popup 1.10 for the gallery
* htaccess and index.html files added in some directories to stop content and stats being reached from an absolute URL - more security features
* htaccess fix to allow get parameter passing in rewritten urls
* Stats fonts now fit better on the page
* Fix of allow array 
* UTF-8 in all blocks so non English characters now render correctly
* Form fix to block page reload sending the email again
* Pulse Cloud - now one license for unlimited sites. Join the Pulse Cloud today to get unlimited sites, updates, premium addons, form builder app (to make multi-page interactive forms and surveys with form logic and stats), Pulse 5 when it drops, and many more exclusive things: https://app.moonclerk.com/pay/kgu2jwx07dw
* New Just Forms Tag included in the Pulse Core. Just add {{justforms}} to any page followed by options for form ID and form height and you can easily embed your forms e.g {{justforms:formid:height}} so {{justforms:1:300}} / Requires a Pulse Cloud subscription to make forms
* Gorgeous new default theme! Colourful, bright and simple - shows the power of Pulse and finally modernises the default site. Uses two alternate templates too, to show that in action
* Standard Contact form auto responder for the {{form}} tag - any contact form submissions will automatically receive a thank you email from the admin address in config.php. You can change the message in the language files
* Blog single posts now have "back" button to return to blog top page
* Multilingual out of the box! Use the {{localizer:block}} tag to translate your content into any language you want. And use links like <a href="?locale=de">Deutsch</a> to change the language.
* Dashboard added! Now get quick shortcuts to all pages including last worked on blocks, blogs, backups quick links, last media added along with dates and file size. // (leaves space in future to be updated with notes spaces, down time, stats etc) - thanks to Tim Plumb of http://www.pulse-style.com/
* "Branding" folder in Content>Media now includes all images to change branding. Logo, favicon, Apple Touch Icon and more in future. This is for easy replacement and also future images can go in here for easy changes by designer and user.
* Login has it's own CSS file login.css to make it easy to customise the login screen.
* Added favicon + apple icon to login screen
* Gorgeous login UI added based on Justin from http://www.pulse-fusion.net and his popular Aero login addon. Image, icon can be changed in the "Branding" folder and colours easy to change in the admin.css
* Modern, minimal and more user-friendly admin UI added! Beautiful responsive admin UI with a constant fixed sidebar and dropdown on mobile so all menu items, settings and help docs are just one-click away.
* The media image edit now shows the alt tag text block. The captions editing/adding code has also been hugely reduced.
* Language variables added to signup / email list block
* Pulsecore added to template.php to style core elements needed like buttons or blog tags, but designers can leave out or override with their stylesheets should they wish
* The media image edit now shows the ALT tag text block. Images / gallery / slider images now all have the ALT tag along with Title tag for better SEO and accessibility purposes
* Blog preview button has the proper single post blog link!
* "Add image" in the editor now allows images to be selected from galleries and folders
* There is now a "body tag helper". This adds classes to the body tag depending on the context of the page so that CSS can be targeting styles more closely. E.g style just the About page or just the Contact page with a separate stylesheet or styles
* reCAPTCHA added to the built-in contact {{form}} tag
* Config / settings edit option for contact form subject line has been added
* Option added to easily redirect {{form}} tag to a page upon successful submission. More than just a success message this can take users to a "thank you" page.
* Form bug fix, no longer refreshing the page sends the contact form twice!
* Social media sharing icons built-in using the new {{social}} tag! Facebook, Twitter, Email, Pinterest and Google+ all included with auto URL building. No JS, lightweight and fast and no tracking or signup needed.
* There is now a blog and page "duplicate" button to make it easier to add more of the same content
* Some great updates to the Blog engine. Now there are blog-related tags: {{blog:"":"[[author]] [[date]] [[featured-image]] [[blog-content-loop]]"}} So you can layout if you want your blog page to show author (taken from admin or editor names) date, featured images (add in media folder "featured_images" and the main content.  (The old {blog}} tag will still work. The first parameter to the blog tag is an optional prefix so passing an empty string keeps that working. Any of the square bracket tags can be left off, although blog-content-loop is needed to show the items)
* The above was extended to: {{blog:"":"[[featured-image]] [[blog-content-loop(<<blog-item-author>> <<blog-item-date>> <<blog-item-featured-image>> <<blog-item-content>>)]]"}}
So can now move around featured image, author and date and items independently....
* This includes featured images for blog posts!
* Now add {{blog-tags}} (new tag) {{blog-tags:"blog"}} and prints all blog tags so can select what want to read - with numbers for amount of posts in each, and links!
* Pagination is now also added for blog - so no longer just "next" and "previous" but numbers also for quicker access to bigger blogs
* Pagination inside admin is also improved. No longer just 10 blocks and "next" and "previous" but also numbers for exact selection and also the amount of blocks to be shown can be set in the settings. This is all part of the new gorgeous UI.
* Offsite emailed backup to admin's email so a backup is kept on the server and off the server. This can still be switched off in the settings.
* Theres now a {{breadcrumb}} tag which puts some breadcrumbs where the tag appears on the front-end templates.
* Mailchimp integration! Use the {{mailchimp}} tag to embed sign up lists that go straight to your Mailchimp made newsletters
* Recent Posts tag {{recentposts}} now in the core! Just add it to any block, blog or page to see your recent blog posts. Thanks to Tim Plumb for this!
* Googlemaps tag added! {{googlemaps:address=brooklyn children's museum-new york-usa}} Thanks again to Tim for this! You can also optionally add in width and height values to specify a fixed or percentage size for the map as well as zoom for the zoom level;
{{googlemaps:address=brooklyn children's museum-new york-usa,width=600,height=300,zoom=12}}
* Redactor Font colour, Font family and Font size options added! 
* Redactor back to nice icon options!
* Can now select media from sub-folders when editing blocks and pages - not just media root folder
* PHP7 compatibility! Upgrade to PHP7 to get a really fast, secure and powerful website
* When creating a blog post just hit "create" - no need to label "filename.txt" anymore - auto assigns an ID so quicker to get going and less complicated! And goes straight to blog post to start typing. No need to see the .txt and get confused!
* Filenames of blogs hidden from list of blogs page so less confusing for users
* Pages and Blocks creation - no need to type .txt! And if file or folder select from the radio buttons
* Gorgeous date picker added for blog posts!
* The blog meta data for the date now uses the date format set in the config. The blog-item-date tag is responsible for formatting the date for display. Easier to setup and localisation!
* The locale and timezone are now set in one place in settings
* the date is now locale sensitive so dates showing in language and locale!
* The settings have a blog date format which can be adjusted to include the day of week or time. The actual date still needs to be formatted here
* The .txt extension is hidden on the admin blog/block/page list pages and dashboard and doesn't need to be shown during creation
* Blocks can now move folders! There is a select below the text editor.
* Extra Meta data added for blog posts and pages. Checkboxes for nofollow/index and support for custom meta to fine-tune your SEO settings
* Add OGP data to pages and blog posts with new tag {{ogp:title:Lorem Ipsum}}
{{ogp:description:Lorem ipsum dolor sit amet, consectetur adipiscing elit}}
{{ogp:type:article}}
{{ogp:image:content/media/example.jpg}}
With a default image in the settings page.
Also, in the template, use this:
<!-- OGP PLACEHOLDER -->
And will appear in there..
* Blogs now have a url meta field!
* RSS blog import converts input to UTF-8
* Blog layout now has Masonry option!! This can be found in the settings
* Blog list tag added for listing short version of the blog list items {{blog-list:"blog"}}. This shows the title/author/date for a blog
* Blog ordering is done! Blog articles are now ordered by the published date in the calendar UI pickaday, and not the .txt string
* Have more than 1 blog per Pulse site!
tag - "blog-show" for showing any blog
First parameter is the blog name, second is the prefix, third is the layout
{{blog-show:"meep":"blog":"[[featured-image]] [[blog-content-loop(<<blog-item-author>> <<blog-item-date>> <<blog-item-featured-image>> <<blog-item-content>> <<blog-item-tag>>)]]"}}
Then Meep is the folder name and all blog articles in the Meep folder will appear here, as part of the Meep blog
For the blogs, sub-folders in the blog directory hold the blog data. Its exactly like Pages/Blocks
* Cache added! So front end content appears faster and more reliable in the browser to speed up your Pulse sites even more!
* There is a new domain tag {{domain}}    \pulsecore\get_configs()->domain will also work in PHP code
This will show the domain - useful for some tags or blocks
* The domain tag now has an optional argument for prepending a string. For the social content I tried {{domain:"//"}} which seems to work.
* There is now a google analytics placeholder replacement tag. Add this to template.php to be replaced by Google Analytics added to the settings in admin ui
* The path is now auto detected - so install is just deploy to server and visit /admin - login with "demo" then go to settings and change what you need! 
* The gal tag now accepts the number of images in the second parameter eg {{gal:"gallery1":"all"}} or {{gal:"gallery1":"5"}} for last 5 images...The size default is all, so that can be left off (with or without inverted "" commas - they get stripped out)
* New lightbox gallery in the {{gal}} tag. Much better touch and responsive, modern gallery. The caption is set by the link title and image alt tags for better SEO
* SweetAlerts added to user admin! Admin now gives alert messages of success, rather than just reloading to the dashboard. More intuitive. 
* New Masonry tag {{masonry:gallery1}} has been added! Image galleries can be auto-formatted into a responsive masonry grid!
(the format for the masonry tag is {{masonry:gallery1:all}} The all parameter can be left off since its the default.)
* New Slider framework latest flexslider version and upgraded to new gal format so {{slide:gallery1}}. The slide is {{slide:gallery1}} and shows all images The slide uses the image alt and the link title attributes.
* New tag {{dropzone}} - allows you to collect files from clients into any media folder. E.g. {{dropzone:"media/gallery1"}} Safety measures in place for only docs, images, videos and zips
* Active links added to navigation menu! User can see what page they are on
* Upload templates now possible from the admin! No need to SFTP to the server. Just upload a theme zip of the contents then select from the theme drawer in settings!
* Plugins can now also be updated! Drop the Zip into settings then start using the Tag in your blocks and pages!
* Images can now take links in the media page. The gallery.txt now can store a link url. This is done in the media item page where the caption and alt tags are set
* New tag {{thumbs:"gallery"}} is now added. This is using the new galley load/save code. This allows you to make thumbnail links to galleries for quick portfolio sites with multiple galleries
* The admin gallery page now has an order button. This orders the galley items by upload/modification date
* If logged in, the top bar shows for logged in users on the front end (matching the settings) for short cut entries!
* The per page inline css/js and the general inline css/js in the settings page are now loaded for each page so you can overwrite the CSS/JS without looking in the template files on the server!
* {{navigation}} tag added and it's autogenerated on load! The first run sets the menu up. This also includes sub-menu support and organisation (drag and drop sub too!)
* New Stats! Browsers, countries, devices and Systems added to stats package.
* The number of Tag variables (for developers) is now 6
* New Tag! Audio + Video Player. This is cool because means can play any content from the web or your Pulse Media folder:
{{media_player:"//domain.com/content/media/CheersThemeTune.mp3":"audio/mp3":"audio"}}

{{media_player:"//domain.com/content/media/rotate_set.mov":"video/mp4":"video":400":300"}}
* New - there is now a cache layer on the front end output so sites are much faster as the server doesn't have to render the plug-ins or do any file handling etc. so good for performance. Exists for 24 hours and can be switched on/off in settings
* New notes for clients on dashboard. Add Block dashboard-notes.txt (dashnotes) and anything you type in there will appear on the front after login. It's a space the developer can add notes such as shortcodes needed for site, add own contact details for contact etc..
* Pulse no longer requires a serial number! Just install on as many sites as you want to make. 
* You can now include blocks outside of the main Pulse page area and still process Pulse tags within them. E.g in template <?php include("path/block.txt"); ?> and tags inside block.txt will be processed
* Crop tool added so easily upload photos and then crop them to size without needing to use an external tool
* Order images button in Media. Order images to latest uploaded. Handy if you want to show your latest images in a gallery / portfolio first (automatically). But want to show your best first? Re-order with drag and drop.
* New {{search}} tag! Add this to your page to create a search engine/box for your site!
* {{policy}} tag. Generate a simple Terms of Service and Privacy Policy statement for your website (English only) and substitute location and business name.
{{policy:company:location}}   
e.g.  {{policy:PulseCMS:Osaka, Japan}}
* Replace media link added - so can replace images in Media folder without having to delete, re-upload and then re-paste into Blocks. Overwrite with same filename
* Upload media now using drag and drop for multiple files and ease of use
