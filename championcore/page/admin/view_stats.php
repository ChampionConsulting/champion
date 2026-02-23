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

namespace championcore\page\admin;

/**
 * generate stats page for the admin
 */
class ViewStats extends Base {
	
	/**
	 * process the stats files
	 * @return stdClass
	 */
	protected function generate_stats () : \stdClass {
		
		$result = new \stdClass();
		
		$result->data = [];
		
		$result->daily = new \stdClass();
		$result->daily->bounce_rate = 0;
		$result->daily->visitors    = 0;
		$result->daily->page_views  = 0;
		
		$result->daily->ips  = [];
		
		$result->weekly = new \stdClass();
		$result->weekly->visitors = [];
		
		$result->browsers  = [];
		$result->countries = [];
		$result->devices   = [];
		$result->pages     = [];
		$result->referrers = [];
		$result->systems   = [];
		
		$stat_files = \glob( \championcore\get_configs()->dir_content . "/stats/*.txt");
		
		$last_week = new \DateTime();
		$last_week->sub( new \DateInterval('P7D') );
		$last_week = $last_week->format( 'Y-m-d' );
		
		foreach ($stat_files as $filename) {
			
			$datum_stat = new \championcore\store\stat\Item();
			$datum_stat->load( $filename );
			
			$file = \basename($filename,".txt");
			
			$when = \DateTime::createFromFormat('m.d.y', $file );
			$when = $when->format( 'Y-m-d' );
			
			$result->data[$when] = $datum_stat;
			
			foreach ($datum_stat->lines as $lll) {
				
				# daily
				if ($when == \date('Y-m-d')) {
					
					$result->daily->page_views  += 1; # count all lines
					
					$result->daily->ips[ $lll->ip ] = (isset($result->daily->ips[ $lll->ip ]) ? ($result->daily->ips[ $lll->ip ] + 1) : 1);
				}
				
				# weekly
				if (!isset($result->weekly->ips[ $when ])) {
					$result->weekly->ips[ $when ] = [];
				}
				$result->weekly->ips[ $when ][ $lll->ip ] = (isset($result->weekly->ips[ $when ][ $lll->ip ]) ? ($result->weekly->ips[ $when ][ $lll->ip ] + 1) : 1);
				
				# aggregate
				$result->browsers[  $lll->browser ] = (isset($result->browsers[ $lll->browser ])) ? ($result->browsers[ $lll->browser ] + 1) : 1;
				$result->countries[ $lll->country ] = (isset($result->country[  $lll->country ])) ? ($result->country[  $lll->country ] + 1) : 1;
				$result->devices[   $lll->device  ] = (isset($result->devices[  $lll->device  ])) ? ($result->devices[  $lll->device  ] + 1) : 1;
				$result->systems[   $lll->system  ] = (isset($result->systems[  $lll->system  ])) ? ($result->systems[  $lll->system  ] + 1) : 1;
				
				# pages
				$url = $lll->uri;
				
				if ((\strlen($url) !== 0) and !(\preg_match("/tracker.php/i", $url)) ) {
					
					$url = ((\stripos($url, 'javascript:') === false) ? $url : '#javascript-injection-attempt');
					
					$result->pages[ $url ] = isset($result->pages[ $url ]) ? ($result->pages[ $url ] + 1) : 1;
				}
				
				# referrers
				$url = $lll->referrer;
				
				if (    !(\preg_match("/google/i",    $url))
				    and !(\preg_match("/localhost/i", $url))
				    and !(\preg_match("/yahoo/i",     $url))
				    and !(\preg_match("/bing/i",      $url))
				    and !(\preg_match("/yandex/i",    $url))
				    and !(\preg_match("/yandex/i",    $url))
				    and (\strlen($url) !== 0)
				    and ($url != 'none')
				   ) {
				
					$url = ((\stripos($url, 'javascript:') === false) ? $url : '#');
					
					$url = \htmlspecialchars($url, \ENT_QUOTES, 'UTF-8');
					
					$url = \str_replace("http://",  "", $url);
					$url = \str_replace("https://", "", $url);
					$url = \str_replace("www.",     "", $url);
					
					$result->referrers[ $url ] = isset($result->referrers[ $url ]) ? ($result->referrers[ $url ] + 1) : 1;
				}
			}
			
			# weekly
			foreach ($result->weekly->ips as $day => $ips) {
				$result->weekly->visitors[ $day ] = \array_sum( $ips );
			}
		}
		
		# daily
		$result->daily->visitors = \sizeof( $result->daily->ips);
		
		$less_than_two_clicks = 0;
		foreach ($result->daily->ips as $ip => $counter) {
			$less_than_two_clicks += (($counter < 2) ? 1 : 0);
		}
		
		$result->daily->bounce_rate = (($result->daily->visitors == 0) ? 0 : round((floatval($less_than_two_clicks-1)/floatval($result->daily->visitors)) * 100.0 , 0))  .'%';
		
		# weekly
		
		# sort
		\arsort( $result->browsers );
		\arsort( $result->countries );
		\arsort( $result->devices );
		\arsort( $result->pages );
		\arsort( $result->referrers );
		\arsort( $result->systems );
		
		return $result;
	}
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/admin/view-stats.js' );
		
		$view_model = new \championcore\ViewModel();
		
		$view_model->stats = $this->generate_stats();
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/view-stats.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
