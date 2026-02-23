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

declare(strict_types = 1);

namespace championcore\view\helper;

/**
 * Adds made in champion badges/etc to template
 */
class MadeInChampion extends Base {
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$params = \array_merge(
			array(
				'badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/template/img/powered_by.png')
			),
			$arguments
		);
		
		\championcore\pre_condition(      isset($params['badge_image']) );
		\championcore\pre_condition( \is_string($params['badge_image']) );
		\championcore\pre_condition(    \strlen($params['badge_image']) > 0 );
		
		$badge_image = $params['badge_image'];
		
		$result = '';
		
		if (\championcore\wedge\config\get_json_configs()->json->made_in_champion) {
			
			$result =<<<EOD
	<!-- Made in Champion CMS -->
		<!--<style>
		.madewithchampion-text {top: 11px!important;}   		
		.madewithchampion-badge {box-shadow: 0px 0px 25px 0px rgba(0, 0, 0, 0.04)!important;border: 1px solid #ececec!important;}  		
        </style>  		
		<style>
		.madewithchampion-text {top: 11px!important;}
		.madewithchampion-badge {box-shadow: 0px 0px 25px 0px rgba(0, 0, 0, 0.04)!important;border: 1px solid #ececec!important;}
		.back-to-top {bottom: 5.71428571em;}
        </style>     -->

		<link rel="stylesheet" href="./championcore/asset/css/madewithchampion.min.css" /> 		
		<script type="text/javascript" language="javascript" src="./championcore/asset/js/lytebox.js"></script>
		<link rel="stylesheet" href="./championcore/asset/css/lytebox.css" type="text/css" media="screen">

		<!--<img src="./championcore/asset/img/madewithchampiontext.svg" class="madewithchampion-text"> -->
		<a href="https://cms.championconsulting.com/?utm_campaign=madewithchampion" class="madewithchampion-badge">
		<img alt="made with champion" src="{$badge_image}" /></a>
			
		<br />
		<p><font size="-2" face="Verdana"><span>Version: <b>7.2.x</b> (1/May/2025) / Downloads: <b>2</b> / 
		
		<span style="color: rgb(255,255,255);">
		<a href="changelog/changelog.txt" onclick="window.open(this);return false" title="The Current Changelog" rel="lyteframe" rev="width: 620px; height: 400px; scrolling: yes;" >Changelog</a> / 
		
		<a href="changelog/roadmap.php" onclick="window.open(this);return false" title="The Current RoadMap" rel="lyteframe" rev="width: 620px; height: 400px; scrolling: yes;" >Roadmap</a></span>
		 </font></p>
<!--  -->

<!--// <a href="https://cms.championconsulting.com/" class="made_in_champion_badge"><img alt="made in champion" src="{$badge_image}" /></a> //-->
EOD;
		}
		
		return $result;
	}
	
}
