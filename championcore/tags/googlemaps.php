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


/* ======================================================================

	Plugin Name: Google Maps
	Plugin URI: http://www.plumb-design.com/plugins
	Description: A Champion CMS plug-in to add Google Maps to your sites.
	Version: 1.0
	Author: Tim Plumb
	Author URI: http://www.plumb-design.com
	License: GNU General Public License v2.0
	License URI: http://www.opensource.org/licenses/gpl-license.php
	
 * ====================================================================== */
 
if (!function_exists('makeGoogleMap')){
	function makeGoogleMap($address,$width,$height,$zoom,$language){
		$flex = "";
		$iframeSizeCode = '';
		
		if ($width == "" && $height == ""){
			$flex = " flexmap";
			
			//add the responsive CSS styles
			if (!function_exists('googleMapStylesAdded')){
				//add the css
				echo("<style type=\"text/css\">".PHP_EOL);
				echo(".flexmap { position: relative; padding-bottom: 60%; height: 0; overflow: hidden; width:100%; }".PHP_EOL);
				echo(".googlemaps iframe { position: absolute; top: 0; left:0; width:100% !important; height:100% !important; }".PHP_EOL);
				echo("</style>".PHP_EOL);
				
				function googleMapStylesAdded(){}
			}
		} else {
			//fixed size
			if ($width != ""){
				$iframeSizeCode .= ' width="'.$width.'"';
			}
			if ($height != ""){
				$iframeSizeCode .= ' height="'.$height.'"';
			}
		}
		
		$iframeCode = '<div class="googlemaps'.$flex.'">'.PHP_EOL;
		$iframeCode .= '<iframe src="https://maps.google.com/maps?q='. rawurlencode($address) .'&amp;ie=UTF8&amp;t=m&amp;z='.$zoom.'&amp;hl='.$language.'&amp;iwloc=near&amp;num=1&amp;output=embed" frameborder="0"';
		$iframeCode .= $iframeSizeCode;
		$iframeCode .= '></iframe>'.PHP_EOL;
		$iframeCode .= '</div>'.PHP_EOL;
		
		echo($iframeCode);
	}
}

$address = "New York New York";
$width = "";
$height = "";
$zoom = 15;
$language = "en";

if (!isset($GLOBALS['tag_var1'])){ return; }
$asArray = explode(",",$GLOBALS['tag_var1']);
for ($c=0; $c < count($asArray); $c++){
	$thisPair = $asArray[$c];
	$pos = strrpos($thisPair,"=");
	
	if ($pos != false){
		$thisPairArray = explode("=",$thisPair);
		$thisLabel = $thisPairArray[0];
		$thisValue = $thisPairArray[1];
				
		if ($thisLabel == 'address'){
			$address = $thisValue;
		}
		if ($thisLabel == 'width'){
			$width = $thisValue;
		}
		if ($thisLabel == 'height'){
			$height = $thisValue;
		}
		if ($thisLabel == 'zoom'){
			$zoom = $thisValue;
		}
		if ($thisLabel == 'language'){
			$language = $thisValue;
		}
	}
}

if (isset($address)){
	makeGoogleMap($address,$width,$height,$zoom,$language);
}
//clear the used variables
unset($address,$width,$height,$zoom,$language,$GLOBALS['tag_var1'], $GLOBALS['tag_var2'], $GLOBALS['tag_var3'], $asArray,$c,$thisPair,$pos,$thisPairArray,$thisLabel,$thisValue);
?>