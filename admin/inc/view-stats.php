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


require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#check editor permissions
\championcore\acl_role\is_editor_allowed();

/**
 * template view-stats handler
 */
function page_template_view_stats() {
	
	$page_handler = new \championcore\page\admin\ViewStats();
	echo $page_handler->process(
		$_GET,
		$_POST,
		$_COOKIE,
		(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get')
	);
}

# call
echo page_template_view_stats();

/*
<script type="text/javascript">
jQuery(function() {
     jQuery(".bar-container").css("height","0%").animate({height:"165px"},800);
});
</script>

<div class='breadcrumb'>
	<?php echo "<a href='$path/$admin'>$lang_nav_home</a>"; echo ' / '.$lang_nav_stats;?>
	<a href="#" class='rename' onClick="window.location.href=window.location.href"><?php echo $lang_stats_refresh; ?></a>
</div>

<div id="content">

<?php

require_once("login.php");

//calculating stats
$browsers  = array();
$countries = array();
$devices   = array();
$systems   = array();

$visitors  = array();
$referers  = array();
$pages     = array();
$day_list  = array();
$file_data = array();
$show_date = date('m.d.y');
$less_than_two_clicks = 0;
$all_vs = "";

$stat_files = \glob( \championcore\get_configs()->dir_content . "/stats/*.txt");

foreach ($stat_files as $filename) {
	
	$datum_stat = new \championcore\store\stat\Item();
	$datum_stat->load( $filename );
	
	$file    = basename($filename,".txt");
	$month   = substr($file, 0,2);
	$day     = substr($file, 3,2);
	$year    = substr($file, 6,2);
	
	$lines = \array_reverse( $datum_stat->lines );
	
	foreach ($lines as $line) {
		
		# referers 
		$referer_host = \parse_url($line->referrer, \PHP_URL_HOST);
		
		if (($referer_host != "") and ($referer_host != false)) {
		
			if (    (\stripos(\parse_url($line->referrer,\PHP_URL_HOST), $_SERVER["HTTP_HOST"]) === false)
			    and (\stripos($_SERVER["HTTP_HOST"], \parse_url($line->referrer,\PHP_URL_HOST)) === false)) {
				
				if (!isset($referers[$file][$line->referrer])) { 
					$referers[$file][$line->referrer] = 1;     
				} else { 
					$referers[$file][$line->referrer]++; 
				}
			}
		}
		
		# pages
		if (!isset($pages[$file][$line->uri])) { 
			$pages[$file][$line->uri] = 1; 
		} else {
			$pages[$file][$line->uri]++;
		}
		
		# visitors
		if (!isset($visitors[$file][$line->ip])) { 
			$visitors[$file][$line->ip] = 1; 
			
		} else {
			$visitors[$file][$line->ip]++;
		}
		
		# pageviews
		$page_views = \sizeof($datum_stat->lines) - 1;
		
		
		
		
		# browsers
		$browsers[ $line->browser ] = (isset($browsers[ $line->browser ]) ? ($browsers[ $line->browser ]++) : 1);
		
		# countries
		$countries[ $line->country ] = (isset($countries[ $line->country ]) ? ($countries[ $line->country ]++) : 1);
		
		# devices
		$devices[ $line->device ] = (isset($devices[ $line->device ]) ? ($devices[ $line->device ]++) : 1);
		
		# systems
		$systems[ $line->system ] = (isset($systems[ $line->system ]) ? ($systems[ $line->system ]++) : 1);
	}
	
	$all[$file] = array(
		'visitors'  => $visitors,
		'pageviews' => $page_views, 
		'refers'    => $referers, 
		'pages'     => $pages, 
		'date'      => $file,
		
		'browsers'  => $browsers,
		'countries' => $countries,
		'devices'   => $devices,
		'systems'   => $systems
	);
}

//#of unique visitors
$counter = ((isset($all[$show_date]) and isset($all[$show_date]['visitors'])) ? $all[$show_date]['visitors'] : array());
$counter = (isset($counter[$show_date]) ? $counter[$show_date] : null);
$counter = (isset($counter) ? (count($counter)-1) : "0");

//#pages 
$page_views = 0;
$page_views = isset($all[$show_date]['pageviews']) ? ($all[$show_date]['pageviews']) : "0";

//#average
$actions = 0;
$actions = ($counter >0) ? $actions = $page_views/$counter : $actions = 0;
$actions = round($actions, 1);

$show_date1  = $show_date;
if ($counter == 0){ 
   $show_date1 = $file; 
}

$big_array_visitors = $all[$show_date1]['visitors'];

if (!empty($big_array_visitors)) {

    foreach($big_array_visitors as $la => $val) {	
	    $lav[] = $la;
    }	

    for ($i = 0; $i < count($lav); $i++) {
	     $dat             = $lav[$i];
	     $each_day_unique = $all[$dat]['visitors'];
	     $each_day_unique = $each_day_unique[$dat];
	     $each_day_unique = isset($each_day_unique) ? count($each_day_unique)-1 : "0";	
	     $chart_data[]    = array($dat, $each_day_unique);
	     $all_vs         += $each_day_unique;
    }
	
    foreach($chart_data as $bardata){	
	     $perc[$bardata[0]] = (($all_vs > 0) ? ($bardata[1] / $all_vs * 100) : 0);
	     $max_array[]       = $bardata[1];
    }

     $ratio = (max($max_array) > 0) ? ($all_vs / max($max_array)) : 0;
     
     $bounce = $all[$dat]['visitors'];
     foreach ($bounce[$show_date1] as $value_bounce => $key_bounce) {
	     if ($key_bounce < 2) {
		     $less_than_two_clicks++ ;
	     }
     } 
}
//BOUNCE Rate
if($counter == 0){ $bounce_rate = 0;} if($counter != 0){ $bounce_rate = round((($less_than_two_clicks-1)/$counter) * 100 , 0)  .'%'; }

?>

<div class="stats-group first-one">
<p class="stat-title"><?php echo $lang_stats_thisweek; ?></p>

<?php

if (empty($big_array_visitors)) { 
    echo '<p style="color:#aaa">'.$lang_stats_nodata.'</p>'; 

} else {

    //bar chart
    foreach($perc as $perc_day => $perc_val) {
	     $month_per = substr($perc_day, 0,2);
	     $day_per   = substr($perc_day, 3,2);
	     $year_per  = substr($perc_day, 6,2);
	     $file_perc = mktime(0,0,0,$month_per, $day_per, $year_per);
	     $perc_day  = date('M d',$file_perc); 
?>
	
         <div class="bar-container">
	     <div title = "<?php echo $perc_val / 100 * $all_vs . ' - '. $perc_day; ?>" rel = "tooltip" class = "bar-fill <?php if ($perc_day == date('M d')){ echo 'blue-bar'; } ?>" style = "height:<?php echo $ratio * (round($perc_val,2))."%"; ?> ;">
	    </div>
        </div><?php
	
     }
}

?>

</div>

<?php

echo "<div class=\"stats-group group\">\n";
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_todays_stats), "</p>\n\n";

// Number of visitors
echo "<div class=\"black\">\n";
echo "<p class=\"num\">",  \htmlentities($counter), "</p>\n";
echo "<p class=\"desc\">", \htmlentities($lang_stats_today), "</p></div>\n\n";

//Number of page views
echo "<div class=\"black\">\n";
echo "<p class=\"num\">",  \htmlentities($page_views), "</p>\n";
echo "<p class=\"desc\">", \htmlentities($lang_stats_pageviews), "</p></div>\n\n";

//Number of page views per visit
echo "<div class=\"black\">\n";
echo "<p class=\"num\">",  \htmlentities($actions), "</p>\n";
echo "<p class=\"desc\">", \htmlentities($lang_stats_per_visit), "</p></div>\n\n";

//Number Bounce rate
echo "<div class=\"black\">\n";
echo "<p class=\"num\">",  \htmlentities($bounce_rate), "</p>\n";
echo "<p class=\"desc\">", \htmlentities($lang_stats_bounce_rate), "</p></div>\n\n";

echo "</div>\n\n";



//Top 10 pages 
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_pages), "</p>";
$pages    = ((isset($all[$show_date]) and isset($all[$show_date]['pages'])) ? $all[$show_date]['pages'] : array());
$pages    = (isset($pages[$show_date]) ? $pages[$show_date] : null);
$nb_pages = 10;

if (!is_array($pages)) { 
     $pages[] = $pages; 
}

asort($pages);
$pages = array_reverse($pages, true);

foreach ($pages as $key => $value ) {
	
		if ($nb_pages > 0) {
			
			if ($key != "") {
				
				if (!(preg_match("/tracker.php/i",$key))) {
					
					$url = ((\stripos($key, 'javascript:') === false) ? $key : '#javascript-injection-attempt');
					
					echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "<a target=\"_blank\" class=\"stats-link\" href='".htmlspecialchars($url, \ENT_QUOTES, 'UTF-8')."'>" . substr(htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'), 0,30) . "</a><br>\n"; 
					$nb_pages--;
				}
			}
		} else { break; }
}
echo "</div>\n\n";

// Top 10 referers
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_refers), "</p>";

if (!empty($referers)) {

	$referers    = (isset($all) and isset($all[$show_date]) and isset($all[$show_date]['refers'])) ? $all[$show_date]['refers'] : array();
	$referers    = (isset($referers) and isset($referers[$show_date])) ? $referers[$show_date] : array();
	$nb_referers = 10;
	
	if (!is_array($referers)) { 
			$referers[] = $referers;
	}
	
	asort($referers);
	$referers = array_reverse($referers, true);
	
	foreach ($referers as $key => $value) {
	
		if ($nb_referers > 0) {
		
			if ($key != "") {
			
				if (    !(\preg_match("/google/i",   $key))
						and !(\preg_match("/localhost/i",$key))
						and !(\preg_match("/yahoo/i",    $key))
						and !(\preg_match("/bing/i",     $key))
						and !(\preg_match("/yandex/i",   $key))) {
				
					$url = $key;
					$url = ((\stripos($url, 'javascript:') === false) ? $url : '#');
					
					$url = \htmlspecialchars($url, \ENT_QUOTES, 'UTF-8');
					
					$key = \str_replace("http://", "", $key);
					$key = \str_replace("www.",    "", $key);
					
					echo \htmlspecialchars($value, \ENT_QUOTES, 'UTF-8'), " &nbsp; ", "<a target=\"_blank\" class=\"stats-link\" href='",$url,"'>", substr(htmlspecialchars($key, \ENT_QUOTES, 'UTF-8'), 0,30), "</a><br>\n";
					$nb_referers--;
				 }
				}
			} else { break; }	
	}
}
echo "</div>\n\n";


#############
# top 10 browsers
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_browsers), "</p>";

if (!empty($browsers)) {

	$browsers    = $all[$file]['browsers'];
	$nb_browsers = 10;
	
	\asort($browsers);
	$browsers = \array_reverse($browsers, true);
	
	$sum_count        = \array_sum( $browsers );
	$sum_count_so_far = 0;
	
	foreach ($browsers as $key => $value) {
		
		if ($key == '') {
			continue;
		}
		
		if ($nb_browsers > 0) {
			$nb_browsers--;
		} else {
			break;
		}
		
		$sum_count_so_far += \intval($value);
		
		echo \htmlspecialchars($key, \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($value)/\floatval($sum_count)), "<br \>\n";
	}
	
	echo \htmlspecialchars('Other', \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($sum_count - $sum_count_so_far)/\floatval($sum_count)), "<br \>\n";
}
echo "</div>\n\n";

##################
# top 10 countries
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_countries), "</p>";

if (!empty($countries)) {

	$countries    = $all[$file]['countries'];
	$nb_countries = 10;
	
	\asort($countries);
	$countries = \array_reverse($countries, true);
	
	$sum_count        = \array_sum( $countries );
	$sum_count_so_far = 0;
	
	foreach ($countries as $key => $value) {
		
		if ($key == '') {
			continue;
		}
		
		if ($nb_countries > 0) {
			$nb_countries--;
		} else {
			break;
		}
		
		$sum_count_so_far += \intval($value);
		
		echo \htmlspecialchars($key, \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($value)/\floatval($sum_count)), "<br \>\n";
	}
	echo \htmlspecialchars('Other', \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($sum_count - $sum_count_so_far)/\floatval($sum_count)), "<br \>\n";
}
echo "</div>\n\n";

#############
# top 10 devices
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_devices), "</p>";

if (!empty($devices)) {

	$devices    = $all[$file]['devices'];
	$nb_devices = 10;
	
	\asort($devices);
	$devices = \array_reverse($devices, true);
	
	$sum_count        = \array_sum( $devices );
	$sum_count_so_far = 0;
	
	foreach ($devices as $key => $value) {
		
		if ($key == '') {
			continue;
		}
		
		if ($nb_devices > 0) {
			$nb_devices--;
		} else {
			break;
		}
		
		$sum_count_so_far += \intval($value);
		
		echo \htmlspecialchars($key, \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($value)/\floatval($sum_count)), "<br \>\n";
	}
	
	echo \htmlspecialchars('Other', \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($sum_count - $sum_count_so_far)/\floatval($sum_count)), "<br \>\n";
}
echo "</div>\n\n";

#############
# top 10 systems
echo '<div class="stats-group stats-list">';
echo "<p class=\"stat-title\">", \htmlentities($lang_stats_systems), "</p>";

if (!empty($systems)) {

	$systems    = $all[$file]['systems'];
	$nb_systems = 10;
	
	\asort($systems);
	$systems = \array_reverse($systems, true);
	
	$sum_count        = \array_sum( $systems );
	$sum_count_so_far = 0;
	
	foreach ($systems as $key => $value) {
		
		if ($key == '') {
			continue;
		}
		
		if ($nb_systems > 0) {
			$nb_systems--;
		} else {
			break;
		}
		
		$sum_count_so_far += \intval($value);
		
		echo \htmlspecialchars($key, \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($value)/\floatval($sum_count)), "<br \>\n";
	}
	
	echo \htmlspecialchars('Other', \ENT_QUOTES, 'UTF-8'), " &nbsp; ", \sprintf('%2d%%', 100.0*\floatval($sum_count - $sum_count_so_far)/\floatval($sum_count)), "<br \>\n";
}
echo "</div>\n\n";

?>

</div>

<script type="text/javascript">

jQuery(document).ready(
	function() {
		jQuery('#content').masonry(
			{
				// options
				itemSelector: '.stats-group',
				gutter: 0
			}
		);
	}
);
</script>
*/ ?>
