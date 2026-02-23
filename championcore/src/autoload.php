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
 * autoload PHP classes
 * @param string $classname classname to load
 */
function autoloader (string $classname) : void {
	
	# import the class map
	static $classmap = false;
	
	if ($classmap === false) {
		$classmap = require_once (CHAMPION_BASE_DIR . '/championcore/src/autoload_classmap.php');
	}
	
	# autoload the class name
	if (isset($classmap[$classname])) {
		require_once ($classmap[$classname]);
	} else {
		#throw new \LogicException( "Cannot autoload {$classname}" );
	}
}

# register the autoloader
\spl_autoload_register( '\championcore\autoloader' );

/**
 * bootstrap the extra code for champion cms - autoload vendor library
 */
require_once (CHAMPION_BASE_DIR . '/championcore/vendor/autoload.php');
