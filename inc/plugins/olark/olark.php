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

	Script Name: Olark
	Description: A Champion CMS script to add the Olark (https://www.olark.com) live chat widget to your sites

 	* ====================================================================== */
	
if (!function_exists('makeOlark')){
	function makeOlark($id){
		$olarkCode = '<!-- begin olark code -->'.PHP_EOL;
		$olarkCode .= '<script type="text/javascript" async>'.PHP_EOL;
		$olarkCode .= ';(function(o,l,a,r,k,y){if(o.olark)return;'.PHP_EOL;
		$olarkCode .= 'r="script";y=l.createElement(r);r=l.getElementsByTagName(r)[0];'.PHP_EOL;
		$olarkCode .= 'y.async=1;y.src="//"+a;r.parentNode.insertBefore(y,r);'.PHP_EOL;
		$olarkCode .= 'y=o.olark=function(){k.s.push(arguments);k.t.push(+new Date)};'.PHP_EOL;
		$olarkCode .= 'y.extend=function(i,j){y("extend",i,j)};'.PHP_EOL;
		$olarkCode .= 'y.identify=function(i){y("identify",k.i=i)};'.PHP_EOL;
		$olarkCode .= 'y.configure=function(i,j){y("configure",i,j);k.c[i]=j};'.PHP_EOL;
		$olarkCode .= 'k=y._={s:[],t:[+new Date],c:{},l:a};'.PHP_EOL;
		$olarkCode .= '})(window,document,"static.olark.com/jsclient/loader.js");'.PHP_EOL;
		$olarkCode .= '/* Add configuration calls below this comment */'.PHP_EOL;
		$olarkCode .= "olark.identify('".$id."');</script>".PHP_EOL;
		$olarkCode .= '<!-- end olark code -->'.PHP_EOL;
		
		echo($olarkCode);
	}
}

$id = "3818-869-10-3935";

if (!isset($tag_var1)){ 
	$tag_var1 = 'id=3818-869-10-3935';
}
$asArray = explode(",",$tag_var1);
for ($c=0; $c < count($asArray); $c++){
	$thisPair = $asArray[$c];
	$pos = strrpos($thisPair,"=");
	
	if ($pos != false){
		$thisPairArray = explode("=",$thisPair);
		$thisLabel = $thisPairArray[0];
		$thisValue = $thisPairArray[1];
				
		if ($thisLabel == 'id'){
			$id = $thisValue;
		}
	}
}

if (isset($id)){
	makeOlark($id);
}
//clear the used variables
unset($id,$tag_var1,$asArray,$c,$thisPair,$pos,$thisPairArray,$thisLabel,$thisValue);
?>