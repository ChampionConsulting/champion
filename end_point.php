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


require_once (__DIR__ . '/symlink_safe.php');

require_once (CHAMPION_BASE_DIR . '/config.php');
require_once (CHAMPION_BASE_DIR . '/inc/plugins/parsedown.php');
require_once (CHAMPION_BASE_DIR . '/championcore/page/end_point.php');

#set up the globals
$parsedown = new \Parsedown();

#set up the globals - language
require_once (CHAMPION_BASE_DIR . "/{$admin}/inc/lang/english.php");
if (!empty($language) and \file_exists(CHAMPION_BASE_DIR . "/{$admin}/inc/lang/{$language}.php")) {
	require_once (CHAMPION_BASE_DIR . "/{$admin}/inc/lang/{$language}.php");
}

/**
 * end point for embedding things in non-champion pages
 */
\header( 'Access-Control-Allow-Origin: *' );
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		
		<title>Champion CMS</title>
	</head>
	<body>
		<?php echo \championcore\page\end_point\process( $_GET, $_POST, $_COOKIE ); ?>
		
		<script type="text/javascript">
			document.addEventListener(
				'readystatechange',
				function() {
					
					// receiver
					function receiver_handler(evnt) {
						
						var message = evnt.data; 
						var source  = evnt.source;
						
						if (message.msg && (message.msg == 'poll')) {
							
							//var content_height = document.body.scrollHeight;
							
							var content_height = Math.max(
								document.body.scrollHeight, document.documentElement.scrollHeight,
								document.body.offsetHeight, document.documentElement.offsetHeight,
								document.body.clientHeight, document.documentElement.clientHeight
							);
							
							source.postMessage( {msg: 'content height', height: content_height}, '*' );
						}
					};
					
					window.addEventListener( 'message', receiver_handler, false );
				},
				false
			);
		</script>
	</body>
</html>
 