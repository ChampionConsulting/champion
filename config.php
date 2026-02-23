<?php
/* TOP_COMMENT_START
 * Copyright (C) 2022, Champion Consulting, LLC  dba ChampionCMS - All Rights Reserved
 *
 * This file is part of Champion Core. It may be used by individuals or organizations generating less than $400,000 USD per year in revenue, free-of-charge. Individuals or organizations generating over $400,000 in annual revenue who continue to use Champion Core after 90 days for non-evaluation and non-development use must purchase a paid license. 
 *
 * Proprietary
 * You may modify this source code for internal use. Resale or redistribution is prohibited.
 *
 * You can get the latest version at: https://cms.championconsulting.com/
 *
 * Dated June 2023
 *
TOP_COMMENT_END */


// **Champion CMS Version Number**
$champion_version   ='7.2.0' ;

// **Champion CMS Serial Number**
$champion_serial = "unregistered";

//**IMPORTANT**
//You don't need to edit this file anymore. This is used just for the initial load. Just upload Champion to your server, go to /admin and log in with the password "demo", navigate to settings and change all things from there, including the password. If you want to change in the future from outside of the Champion admin, go to championcore > storage > config.json and edit the information there. Otherwise, use the Champion Admin UI!


// GENERAL

// Tip: Copy only the contents of the "champion" folder into the site root, not the folder itself.
// If you want to install in a subfolder, you can, but it must be reflected in the path setting.
	
$path             ='/champion-core-230525'; // If installed in root, leave blank. If in "subfolder", use "/subfolder"
$admin            = 'admin'; // Admin folder name
$password         = '$2y$10$y1OxlJMDaxuPQGM.0fZy6.Sc2q19VR5i3CxJGz9d7ncUl9bqqtDdi'; // Admin login - "demo"
$autobackup       = true; // Turn on/off auto-backup feature
date_default_timezone_set('America/New_York'); // More: https://php.net/manual/en/timezones.php
$language         = 'english';
$anonymize_ip     = false;


// EDITOR

$wysiwyg          = true; // Toggle on/off WYSIWYG editor in blocks and blog
$allow            = array('txt','jpeg','gif','jpg','svg','png','pdf','zip','csv','xls','xlsx'); // File types allowed to be uploaded


// MEDIA

$jpeg_resampling_off = false; // Toggle on/off jpeg resampling
$jpeg_quality     = '85'; // Use 100 for full jpeg quality (larger files)
$jpeg_size        = '1200'; // Scale jpegs to a max pixel size (height)
$thumbnail_height     = '120';


// FORM

$mail_inputs      = (object)array('Name'=>'text','Email'=>'email','Phone'=>'text'); // Input fields
$lang_form_name   = 'Name'; // Must match "Name" input above
$lang_form_email  = 'Email'; // Must match "Email" input above
$mail_textarea    = (object)array('Comment'=>'7'); // 7 = Number of rows in comment textarea
$email_contact    = array('you@mail.com'); // Example: 'one@mail.com','two@mail.com'
$config_contact_form_auto_thank = true;

// Tip: To add more form fields, add to the $mail_inputs array
// Tip 2: For more complex forms, use the {{justforms:formid:height}} tag available to all Champion Premium users:  https://championcms.com/upgrade.php

// BLOG

$result_per_page  = 5; // Blog posts per page
$blog_url         = "http://example.com/blog";
$disqus_comments  = true; // Turn on/off blog comments (Disqus)
$disqus_shortname = "championconsulting"; // Your disqus account name
$date_format      = "M j, Y"; // More: https://php.net/manual/en/function.date.php

// RSS

$blog_title       = 'The Blog';
$blog_description = 'This is my blog.';
$rss_lang         = 'en-us';
$url_prefix   	  = 'blog'; // blog-1-post-title, if changed also edit htaccess

# ===========================================================================>
if (!\defined('NO_JSON_CONFIG_LOAD')) {
	/**
	 * wedge in the updated configs from championcore
	 */
	require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');
	
	\championcore\wedge\config\wedge_config();
	
	# bootstrap i18n
	\championcore\set_i18n(
		\championcore\wedge\config\get_json_configs()->json->date_default_timezone_set,
		\championcore\wedge\config\get_json_configs()->json->language
	);
}
# ===========================================================================>
