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

declare(strict_types=1);

require_once (CHAMPION_BASE_DIR . '/championcore/vendor/autoload.php');

/**
 * generate a random string 
 */
 
require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

# load configs
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');
\championcore\wedge\config\wedge_config();

$bytes = '';

for ($k = 0; $k < 16; $k++) {
	
	$bytes .= \chr( \mt_rand(0, 255) ); 
}

$bytes = \Base32\Base32::encode( $bytes );

echo ("random string:       " . \trim($bytes, '=') );
