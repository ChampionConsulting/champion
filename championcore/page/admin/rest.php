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
 * manage admin REST interface
 */
class Rest extends Base {
	
	/**
	 * delete request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_delete (array $request_params, array $request_cookie) : string {
		
		\header('Content-type: application/json');
		return \json_encode( array('msg' => 'method not supported') );
	}
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(         isset($request_params['id']) );
		\championcore\pre_condition(    \is_string($request_params['id']) );
		\championcore\pre_condition( \strlen(\trim($request_params['id'])) > 0 );
		
		\championcore\pre_condition(         isset($request_params['type']) );
		\championcore\pre_condition(    \is_string($request_params['type']) );
		\championcore\pre_condition( \strlen(\trim($request_params['type'])) > 0 );
		
		# extract
		$id   = $request_params['id'];
		$type = $request_params['type'];
		
		# filter
		$id   = \championcore\filter\item_url(      $id );
		$type = \championcore\filter\variable_name( $type );
		
		$result =  array('msg' => 'type or id not supported for GET');
		
		switch ($type) {
			
			case 'block':
				$result = new \championcore\store\block\Item();
				$result->load( \championcore\get_configs()->dir_content . "/blocks/{$id}.txt" );
				break;
			
			case 'blog':
				$result = new \championcore\store\blog\Item();
				$result->load( \championcore\get_configs()->dir_content . "/blog/{$id}.txt" );
				break;
			
			case 'page':
				$result = new \championcore\store\page\Item();
				$result->load( \championcore\get_configs()->dir_content . "/pages/{$id}.txt" );
				break;
		}
		
		# replace {{show_var:"path"}} with the actual path
		$config_path = \championcore\wedge\config\get_json_configs()->json->path;
		
		$result->html = \str_replace( 'src="{{show_var:"path"}}', "src=\"{$config_path}", $result->html );
		
		# tx
		\header('Content-type: application/json');
		return \json_encode( $result );
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(         isset($request_params['id']) );
		\championcore\pre_condition(    \is_string($request_params['id']) );
		\championcore\pre_condition( \strlen(\trim($request_params['id'])) > 0 );
		
		\championcore\pre_condition(         isset($request_params['type']) );
		\championcore\pre_condition(    \is_string($request_params['type']) );
		\championcore\pre_condition( \strlen(\trim($request_params['type'])) > 0 );
		
		# extract
		$id   = $request_params['id'];
		$type = $request_params['type'];
		
		# filter
		$id   = \championcore\filter\item_url(      $id );
		$type = \championcore\filter\variable_name( $type );
		
		$result =  array('msg' => 'type or id not supported for POST');

		# $post_body = \file_get_contents('php://input');
		
		# cleanup path and convert to {{show_var:"path"}}
		$config_path = \championcore\wedge\config\get_json_configs()->json->path;
		
		foreach ($request_params as $key => $value) {
			if (\in_array($key, array('html', 'title'))) {
				$request_params[ $key ] = \str_replace( "src=\"{$config_path}", 'src="{{show_var:"path"}}', $value );
			}
		}
		
		# process
		switch ($type) {
			
			case 'block':
				$result = new \championcore\store\block\Item();
				$result->load( \championcore\get_configs()->dir_content . "/blocks/{$id}.txt" );
				
				foreach ($request_params as $key => $value) {
					if (\in_array($key, array('html', 'title'))) {
						$result->{$key} = $value;
					}
				}
				
				$result->save( \championcore\get_configs()->dir_content . "/blocks/{$id}.txt" );
				break;
			
			case 'blog':
				$result = new \championcore\store\blog\Item();
				$result->load( \championcore\get_configs()->dir_content . "/blog/{$id}.txt" );
				
				foreach ($request_params as $key => $value) {
					if (\in_array($key, array('author', 'date', 'description', 'html', 'tags', 'title'))) {
						$result->{$key} = $value;
					}
				}
				
				$result->save( \championcore\get_configs()->dir_content . "/blog/{$id}.txt" );
				break;
			
			case 'page':
				$result = new \championcore\store\page\Item();
				$result->load( \championcore\get_configs()->dir_content . "/pages/{$id}.txt" );
				
				foreach ($request_params as $key => $value) {
					if (\in_array($key, array('description', 'html', 'title'))) {
						$result->{$key} = $value;
					}
				}
				
				$result->save( \championcore\get_configs()->dir_content . "/pages/{$id}.txt" );
				break;
		}
		
		\header('Content-type: application/json');
		return \json_encode( array('msg' => 'OK') );
	}
	
	/**
	 * put request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_put (array $request_params, array $request_cookie) : string {
		
		\header('Content-type: application/json');
		return \json_encode( array('msg' => 'method not supported') );
	}
}
