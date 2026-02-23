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
 * duplicate content page
 */
class DuplicateContent extends Base {
	
	/**
	 * get request
	 * \param $request_params array of request parameters
	 * \param $request_cookie array of cookie parameters
	 * \return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(         isset($request_params['item_file']) );
		\championcore\pre_condition(    \is_string($request_params['item_file']) );
		\championcore\pre_condition( \strlen(\trim($request_params['item_file'])) > 0 );
		
		# extract
		$item_file = $request_params['item_file'];
		
		$item_type = \explode('/', $item_file);
		
		\championcore\invariant(     isset($item_type) );
		\championcore\invariant( \is_array($item_type) );
		\championcore\invariant(   \sizeof($item_type) > 0 );
		
		$item_type = \trim($item_type[0]);
		
		\championcore\invariant(      isset($item_type) );
		\championcore\invariant( \is_string($item_type) );
		\championcore\invariant(    \strlen($item_type) > 0 );
		
		$now = \date('Y_m_d_H_i_s');
		
		$filename = \championcore\get_configs()->dir_content . '/' . $item_file . '.txt';
		
		$new_filename = \championcore\get_configs()->dir_content . '/' . $item_file . '_' . $now . '.txt';
		
		switch ($item_type) {
			
			case 'blocks':
				$data = new \championcore\store\block\Item();
				$data->load( $filename );
				break;
				
			case 'pages':
				$data = new \championcore\store\page\Item();
				$data->load( $filename );
				break;
				
			default:
				\championcore\invariant( false );
		}
		
		# copy file
		$status = \copy( $filename, $new_filename );
		
		\championcore\invariant( $status === true, 'duplication failed' );
		
		# status message
		\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
		
		$result = '<script type="text/javascript">window.location="index.php?p=open&f=' . $item_file . '_' . $now . '&e=txt";</script>';
		
		return $result;
	}
}
