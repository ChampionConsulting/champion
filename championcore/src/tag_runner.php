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

namespace championcore\tag_runner;

/**
 * expand all tags in a string
 * @param string $tag The content to process
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function expand_single_tag (string $tag, array $tag_runner_context = []) : string {
	
	\championcore\pre_condition(      isset($tag) );
	\championcore\pre_condition( \is_string($tag) );
	\championcore\pre_condition(    \strlen($tag) > 0);
	
	$result = $tag;
	
	#extract tag contents
	$matches = array();
	
	$status = \preg_match("/{{(.*?)}}/", $result, $matches);
	
	\championcore\invariant( $status !== false);
		
	\championcore\invariant(  \is_array($matches) );
	\championcore\invariant(      isset($matches[1]) );
	\championcore\invariant( \is_string($matches[1]) );
	
	$result = $matches[1];
	
	#extract tag variables
	$variables = \explode(':', $result);
	
	\championcore\invariant( \is_array($variables) );
	\championcore\invariant(   \sizeof($variables) >= 1);
	
	$operator = $variables[0];
	
	#filter for safety
	$operator = \championcore\filter\item_url( $operator );
        
	\ob_start(); 
	if ($operator == 'template') {
		
		$GLOBALS['tag_var1'] = (!empty($variables[1])) ? $variables[1] : '';
		
		$GLOBALS['new_template'] = $GLOBALS['tag_var1']; #$tag_var1;
	} else { 
		
		#set vars up for loading block
		$GLOBALS['get_embed'] = $operator;
		
		$GLOBALS['tag_var1'] = (!empty($variables[1])) ? $variables[1] : '';
		$GLOBALS['tag_var2'] = (!empty($variables[2])) ? $variables[2] : '';
		$GLOBALS['tag_var3'] = (!empty($variables[3])) ? $variables[3] : '';
		$GLOBALS['tag_var4'] = (!empty($variables[4])) ? $variables[4] : '';
		$GLOBALS['tag_var5'] = (!empty($variables[5])) ? $variables[5] : '';
		$GLOBALS['tag_var6'] = (!empty($variables[6])) ? $variables[6] : '';
		$GLOBALS['tag_var7'] = (!empty($variables[7])) ? $variables[7] : '';
		
		require (CHAMPION_BASE_DIR . "/inc/tags/{$operator}.php");
		
		$new  = \ob_get_contents();
	}
	$result = str_replace($tag, $new, $tag ); //was $result
	ob_end_clean();
	
	return $result;
}

/**
 * expand all tags in a string
 * @param string $content The content to process
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function expand (string $content, array $tag_runner_context = []) : string {
	
	return \championcore\tag\runner\expand( $content, $tag_runner_context );
}

/**
 * OLD version
 * expand all tags in a string
 * @param string $content The content to process
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function expand_old (string $content, array $tag_runner_context = []) : string {
	
	\championcore\pre_condition(      isset($content) );
	\championcore\pre_condition( \is_string($content) );
	\championcore\pre_condition(    \strlen($content) >= 0);
	
	# process
	$result = $content;
	
	if (\strlen($content) > 0) {
		
		$matches = array();
		
		$status = \preg_match_all("/(\{\{.*?\}\})/", $result, $matches);
		
		if ($status !== false) {
			
			\championcore\invariant( \is_array($matches) );
			\championcore\invariant(     isset($matches[0]) );
			\championcore\invariant( \is_array($matches[0]) );
			
			$tag_list = $matches[0];
			
			foreach ($tag_list as $tag) {
				
				$tag = \trim($tag);
				
				$tmp = expand_single_tag($tag, $tag_runner_context );
				
				$result = str_replace($tag, $tmp, $result);
			}
		}
	}
	
	return $result;
}

/**
 * parse tags and return a list NB that the tags themselves need to be parsed
 * @param string $arg The string to parse
 * @return array of tags
 */
function parse (string $arg) : array {
	
	\championcore\pre_condition(         isset($arg) );
	\championcore\pre_condition(    \is_string($arg) );
	\championcore\pre_condition( \strlen(\trim($arg)) >= 0 );
	
	$result  = array();
	$matches = array();
	
	$arg = \trim($arg);
	
	$status = \preg_match_all("/\{\{([a-zA-Z0-9_\-]+)(.*)\}\}/", $arg, $matches );
	
	# unpack
	for ($k = 0; $k < \sizeof($matches[0]); $k++) {
		$result[$k] = (object)array(
			'data'    => $matches[2][$k],
			'name'    => $matches[1][$k], 
			'content' => $matches[0][$k]
		);
		
		if (\strlen(\trim($result[$k]->data)) > 0) {
			
			$data = array();
			
			$splitted = \explode(':', $result[$k]->data);
			
			foreach ($splitted as $value) {
				
				$value = \trim($value);
				$value = \trim($value, '"');
				
				if (\strlen($value) > 0) {
					$data[] = $value;
				}
			}
			
			$result[$k]->data = $data;
		}
	}
	
	return $result;
}
