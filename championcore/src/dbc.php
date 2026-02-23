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

namespace championcore;

/**
 * basic dessign-by-contract stuff - invariant
 * @param bool $test
 * @param string $message
 * @return void
 * @throw LogicException
 */
function invariant (bool $test, string $message = 'An invariant has failed') : void {
	
	if (false === $test) {
		
		$eee = new \LogicException($message);
		
		throw $eee;
	}
}

/**
 * log an exception
 * @param \Exception $eee The exception
 * @return void
 */
function log_exception (\Exception $eee): void {
	
	\error_log( "exception raised: " . $eee->getMessage() );
	\error_log( $eee->getTraceAsString() );
}

/**
 * basic dessign-by-contract stuff - post condition
 * @param bool $test
 * @param string $message
 * @return void
 * @throw LogicException
 */
function post_condition (bool $test, string $message = 'A post-condition has failed') : void {
	
	invariant( $test, $message );
}

/**
 * basic dessign-by-contract stuff - pre condition
 * @param bool $test
 * @param string $message
 * @return void
 * @throw LogicException
 */
function pre_condition (bool $test, string $message = 'A pre-condition has failed') : void {
	
	invariant( $test, $message );
}
