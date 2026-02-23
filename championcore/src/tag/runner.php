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

namespace championcore\tag\runner;

/**
 * expand all tags in a string
 * @param string $content The content to process
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function expand (string $content, array $tag_runner_context = []) : string {
	
	\championcore\pre_condition(      isset($content) );
	\championcore\pre_condition( \is_string($content) );
	\championcore\pre_condition(    \strlen($content) >= 0);
	
	# process
	$result = '';
	
	if (\strlen($content) > 0) {
		
		$parser = new \championcore\tag\Parser();
		
		$parser->parse( $content );
		
		$result = expand_tokens( $parser->token_list, $tag_runner_context );
	}
	
	return $result;
}

/**
 * expand all tags in a list of tokens
 * @param array $token_list The content to process
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function expand_tokens (array $token_list, array $tag_runner_context = []) : string {
	
	# process
	$result = '';
	
	$working_list = \array_merge( [], $token_list);
	
	while (\sizeof($working_list) > 0) {
		
		$token = \array_shift($working_list);
		
		if (\is_string($token)) {
			# handle strings
			$result .= $token;
			
		} else if ($token instanceof \championcore\tag\lexer\token\Base) {
			# handle lexer tokens
			$result .= $token->content;
			
		} else if ($token instanceof \championcore\tag\parser\token\Tag) {
			# handle single tag
			
			$result .= handle_single_tag( $token, $tag_runner_context );
			
		} else if ($token instanceof \championcore\tag\parser\token\CompositeTag) {
			# handle composite tag
			
			$result .= handle_composite_tag( $token, $tag_runner_context );
			
		} else {
			
			# append the token content to the working list
			#foreach ($token->content as $value) {
			#	\array_unshift( $working_list, $value );
			#}
			$working_list = \array_merge( $token->content, $working_list );
		}
	}
	
	return $result;
}

/**
 * handle a composite tag
 * @param \championcore\tag\parser\token\CompositeTag $token
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function handle_composite_tag (\championcore\tag\parser\token\CompositeTag $token, array $tag_runner_context = []) : string {
	
	$operator   = $token->get_tag_name();
	$attributes = $token->get_attributes();
	
	# filter for safety
	$operator = \championcore\filter\item_url( $operator );
	
	# extract content (if present)
	$content = '';
	
	foreach ($token->content as $value) {
		
		if ($value instanceof \championcore\tag\parser\token\Content) {
			$content .= ((string)$value);
		}
	}
		
	\ob_start();
	
	#set vars up for loading block
	$GLOBALS['get_embed'] = $operator;
	
	$GLOBALS['tag_var1'] = (!empty($attributes[0])) ? $attributes[0] : '';
	$GLOBALS['tag_var2'] = (!empty($attributes[1])) ? $attributes[1] : '';
	$GLOBALS['tag_var3'] = (!empty($attributes[2])) ? $attributes[2] : '';
	$GLOBALS['tag_var4'] = (!empty($attributes[3])) ? $attributes[3] : '';
	$GLOBALS['tag_var5'] = (!empty($attributes[4])) ? $attributes[4] : '';
	$GLOBALS['tag_var6'] = (!empty($attributes[5])) ? $attributes[5] : '';
	$GLOBALS['tag_var7'] = (!empty($attributes[6])) ? $attributes[6] : '';
	
	$GLOBALS['tag_composite_content'] = $content;
	
	require (CHAMPION_BASE_DIR . "/inc/tags/{$operator}.php");
	
	$result  = \ob_get_contents();
	
	ob_end_clean();
	
	# reset global var so there are no knock on effects when parsing other tags with no composite content
	$GLOBALS['tag_composite_content'] = '';
	
	return $result;
}

/**
 * handle a single tag
 * @param \championcore\tag\parser\token\Tag $token
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function handle_single_tag (\championcore\tag\parser\token\Tag $token, array $tag_runner_context = []) : string {
	
	$result = '';
	
	$operator   = $token->get_tag_name();
	$attributes = $token->get_attributes();
	
	# filter for safety
	$operator = \championcore\filter\item_url( $operator );
		
	\ob_start(); 
	if ($operator == 'template') {
		
		$GLOBALS['tag_var1'] = (!empty($attributes[0])) ? $attributes[0] : '';
		
		$GLOBALS['new_template'] = $GLOBALS['tag_var1']; #$tag_var1;
	} else { 
		
		#set vars up for loading block
		$GLOBALS['get_embed'] = $operator;
		
		$GLOBALS['tag_var1'] = (!empty($attributes[0])) ? $attributes[0] : '';
		$GLOBALS['tag_var2'] = (!empty($attributes[1])) ? $attributes[1] : '';
		$GLOBALS['tag_var3'] = (!empty($attributes[2])) ? $attributes[2] : '';
		$GLOBALS['tag_var4'] = (!empty($attributes[3])) ? $attributes[3] : '';
		$GLOBALS['tag_var5'] = (!empty($attributes[4])) ? $attributes[4] : '';
		$GLOBALS['tag_var6'] = (!empty($attributes[5])) ? $attributes[5] : '';
		$GLOBALS['tag_var7'] = (!empty($attributes[6])) ? $attributes[6] : '';
		
		require (CHAMPION_BASE_DIR . "/inc/tags/{$operator}.php");
		
		$result  = \ob_get_contents();
	}
	ob_end_clean();
	
	return $result;
}
