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

namespace championcore\store\stat;

/**
 * base class for stats storage backend
 */
abstract class Base extends \championcore\store\Base {
	
	/*
	 * remove stats files older than a set number of days
	 */
	public static function clean_stats_files (int $days) : void {

		\championcore\pre_condition( $days > 0 );
		
		$date_line = new \DateTime();
		$date_line = $date_line->sub( new \DateInterval( "P{$days}D" ) );
		$date_line = $date_line->getTimestamp();
		
		$stats_files = \championcore\get_configs()->dir_content . "/stats/*.txt";
		
		$stats_files = \glob($stats_files);
		
		\championcore\invariant( $stats_files !== false );
		
		foreach ($stats_files as $fff) {
		
			$basename  = \basename($fff, ".txt");
			
			$month     = \substr($basename, 0, 2);
			$day       = \substr($basename, 3, 2);
			$year      = \substr($basename, 6, 2);

			$month = \intval( $month );
			$day   = \intval( $day );
			$year  = \intval( $year );

			$file_date = \mktime(0,0,0, $month, $day, $year);
			
			if ($file_date < $date_line) {
				
				$filename = \championcore\get_configs()->dir_content . "/stats/{$basename}.txt";
				$status   = \unlink($filename);
				
				\championcore\invariant( $status === true );
			}
		}
	}
}
