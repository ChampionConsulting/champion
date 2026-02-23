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

# random string for security token. Token is used to validate calls and changes for EACH one
\define( 'CHAMPION_RANDOM', 'GUZSjMU8qzcPsDZ12XvisNVgmWCO4jEsYcHqOWuu' );

# where the xml file is
\define( 'CHAMPION_SHOP_FILENAME', (CHAMPION_BASE_DIR . '/inc/plugins/unishop/shop.xml') );

/**
 * unishop management for the I/O
 */
class UnishopEditor extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$mtime = $this->last_changed();
		
		$result = (object)array(
			'status'  => 'OK',
			'now'     => $mtime,
			'content' => \file_get_contents( CHAMPION_SHOP_FILENAME ),
			
			'token'   => $this->token( $mtime )
		);
		
		$result = \json_encode( $result );
		
		\header('Content-type: application/json');
		return $result;
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		$param_content = isset($request_params['content']) ? $request_params['content'] : '';
		$param_token   = isset($request_params['token'])   ? $request_params['token']   : '';
		
		$param_content = \trim( $param_content );
		$param_token   = \trim( $param_token );
		
		if (\strlen($param_content) > 0) {
			
			# verify the token
			$mtime = $this->last_changed();
			
			$next_token = $this->token($mtime);
			
			if ($next_token == $param_token) {
				
				$now = \date('YmdHis');
				
				# backup
				\copy(
					CHAMPION_SHOP_FILENAME,
					( \championcore\get_configs()->dir_storage . "/shop_{$now}.xml")
				);
				
				# save
				\file_put_contents( CHAMPION_SHOP_FILENAME, $request_params['content']);
			}
		}
		
		# make sure this settles first
		\sleep( 1 );
		
		return $this->handle_get($request_params, $request_cookie);
	}
	
	/**
	 * last time the shop.xml file changed
	 * @return string
	 */
	protected function last_changed () : string {
		
		$last_changed = \filemtime( CHAMPION_SHOP_FILENAME );
		$last_changed = \date('Y-m-d H:i:s', $last_changed);
		
		return $last_changed;
	}
	
	/**
	 * generate the token
	 * @param string $mtime
	 * @return string
	 */
	protected function token (string $mtime) : string {
		
		$result = \sha1( ($mtime . CHAMPION_RANDOM) );
		
		return $result;
	}
}
