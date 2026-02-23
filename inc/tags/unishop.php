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

/**
 * unishop cart
 * 
 * @param array $tag_runner_context Extra content to provide to tags
 */
function tag_unishop (array $tag_runner_context = []) {
	
	$tag = new \championcore\tags\Unishop();
	
	$result = $tag->generate_html(
		[
			'business'        => ((isset($GLOBALS['tag_var1']) and (\strlen($GLOBALS['tag_var1']) > 0)) ? \trim($GLOBALS['tag_var1']) : 'paypal@domain.com' ),
			'currency_code'   => ((isset($GLOBALS['tag_var2']) and (\strlen($GLOBALS['tag_var2']) > 0)) ? \trim($GLOBALS['tag_var2']) : 'USD' ),
			'currency_symbol' => ((isset($GLOBALS['tag_var3']) and (\strlen($GLOBALS['tag_var3']) > 0)) ? \trim($GLOBALS['tag_var3']) : '$' ),
			'lc'              => ((isset($GLOBALS['tag_var4']) and (\strlen($GLOBALS['tag_var4']) > 0)) ? \trim($GLOBALS['tag_var4']) : 'US' ), # locale
			'no_shipping'     => ((isset($GLOBALS['tag_var5']) and (\strlen($GLOBALS['tag_var5']) > 0)) ? \trim($GLOBALS['tag_var5']) : '0'  ),
			'return_url'      => ((isset($GLOBALS['tag_var6']) and (\strlen($GLOBALS['tag_var6']) > 0)) ? \trim($GLOBALS['tag_var6']) : ''  ),
			'cancel_url'      => ((isset($GLOBALS['tag_var7']) and (\strlen($GLOBALS['tag_var7']) > 0)) ? \trim($GLOBALS['tag_var7']) : ''  )
		],
		$tag_runner_context,
		((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
	);
	
	return $result;
}

# call
echo tag_unishop( (isset($tag_runner_context) ? $tag_runner_context : []) );
