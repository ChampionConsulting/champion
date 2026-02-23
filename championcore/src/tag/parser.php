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

namespace championcore\tag;

/**
 * parse a string
 */
class Parser {

	/**
	 * content list
	 */
	protected $content = [];
	
	/**
	 * content tokens
	 */
	public $token_list =[];
	
	/**
	 * parsing rules
	 */
	protected $rules =[];
	
	/**
	 * constructor
	 */
	public function __construct () {
		
		$this->content =[];
		
		# rules
		$this->rules = array(
			new \championcore\tag\parser\rule\Filter(
				'simplification - remove whitespace between open braces and tag name',
				array(
					'\championcore\tag\lexer\token\Whitespace',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				array(
					'\championcore\tag\lexer\token\CurlyBraceClose'
				)
			),
			new \championcore\tag\parser\rule\Filter(
				'simplification - remove whitespace between open braces and tag name',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Whitespace',
					'\championcore\tag\lexer\token\TagName'
				), 
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName'
				)
			),
			new \championcore\tag\parser\rule\Filter(
				'simplification - remove whitespace between close braces and tag name',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Whitespace',
					'\championcore\tag\lexer\token\Slash'
				), 
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Slash'
				)
			),
			new \championcore\tag\parser\rule\Filter(
				'simplification - remove whitespace between close braces and tag name',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Slash',
					'\championcore\tag\lexer\token\Whitespace',
					'\championcore\tag\lexer\token\TagName'
				), 
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Slash',
					'\championcore\tag\lexer\token\TagName'
				)
			),
			
			# closing tag
			new \championcore\tag\parser\rule\Base(
				'closing tag',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\Slash',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\ClosingTag()
			),
			
			# opening tag
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 1',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 2',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 3',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 4',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 5',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 6',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			# opening tag - complex
			new \championcore\tag\parser\rule\LookAhead(
				'opening tag - complex - ta 7',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\OpeningTag(),
				array(
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceOpen',  'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\Slash',           'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\TagName',         'content' => '*'],
					(object)['type' => '\championcore\tag\lexer\token\CurlyBraceClose', 'content' => '*']
				)
			),
			
			new \championcore\tag\parser\rule\CompositeTag(
				'open and closing tag',
				array(
					'\championcore\tag\parser\token\OpeningTag',
					'\championcore\tag\parser\token\ClosingTag'
				), 
				new \championcore\tag\parser\token\CompositeTag()
			),
			new \championcore\tag\parser\rule\CompositeTag(
				'open and closing tag - with content',
				array(
					'\championcore\tag\parser\token\OpeningTag',
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\parser\token\ClosingTag'
				), 
				new \championcore\tag\parser\token\CompositeTag()
			),
			
			# simple tag
			new \championcore\tag\parser\rule\Base(
				'last rule - single tag',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\Tag()
			),
			new \championcore\tag\parser\rule\Base(
				'last rule - single tag - with attribute(s)',
				array(
					'\championcore\tag\lexer\token\CurlyBraceOpen',
					'\championcore\tag\lexer\token\TagName',
					'\championcore\tag\parser\token\TagAttributeList',
					'\championcore\tag\lexer\token\CurlyBraceClose'
				), 
				new \championcore\tag\parser\token\Tag()
			),
			
			new \championcore\tag\parser\rule\Base(
				'tag - attributes - list',
				array(
					'\championcore\tag\lexer\token\TagAttribute',
					'\championcore\tag\lexer\token\TagAttribute'
				), 
				new \championcore\tag\parser\token\TagAttributeList()
			),
			new \championcore\tag\parser\rule\Merge(
				'tag - attributes - list + tag attribute',
				array(
					'\championcore\tag\parser\token\TagAttributeList',
					'\championcore\tag\lexer\token\TagAttribute'
				), 
				new \championcore\tag\parser\token\TagAttributeList()
			),
			new \championcore\tag\parser\rule\Base(
				'tag - attributes - tag attribute -> list',
				array(
					'\championcore\tag\lexer\token\TagAttribute'
				), 
				new \championcore\tag\parser\token\TagAttributeList()
			),
			
			# content
			new \championcore\tag\parser\rule\Base(
				'simplification - content html - ws',
				array(
					'\championcore\tag\lexer\token\Whitespace',
				), 
				new \championcore\tag\parser\token\Content()
			),
			
			new \championcore\tag\parser\rule\Base(
				'simplification - content html - skip',
				array(
					'\championcore\tag\lexer\token\Skip'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Append(
				'simplification - content html content + skip',
				array(
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\lexer\token\Skip'
				), 
				new \championcore\tag\parser\token\Content()
			),
			
			
			new \championcore\tag\parser\rule\Append(
				'simplification - content html content + ws',
				array(
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\lexer\token\Whitespace'
				), 
				new \championcore\tag\parser\token\Content()
			),
			
			new \championcore\tag\parser\rule\Append(
				'simplification - content html content + composite tag',
				array(
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\parser\token\CompositeTag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			
			new \championcore\tag\parser\rule\Append(
				'simplification - content html content + tag',
				array(
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\parser\token\Tag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Merge(
				'simplification - content html content + content',
				array(
					'\championcore\tag\parser\token\Content',
					'\championcore\tag\parser\token\Content'
				), 
				new \championcore\tag\parser\token\Content()
			),
			
			new \championcore\tag\parser\rule\Base(
				'simplification - tag',
				array(
					'\championcore\tag\parser\token\Tag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Base(
				'simplification - tag + tag',
				array(
					'\championcore\tag\parser\token\Tag',
					'\championcore\tag\parser\token\Tag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Base(
				'simplification - compositetag + tag',
				array(
					'\championcore\tag\parser\token\CompositeTag',
					'\championcore\tag\parser\token\Tag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Base(
				'simplification - compositetag + tag',
				array(
					'\championcore\tag\parser\token\Tag',
					'\championcore\tag\parser\token\CompositeTag'
				), 
				new \championcore\tag\parser\token\Content()
			),
			new \championcore\tag\parser\rule\Base(
				'simplification - composite tag',
				array(
					'\championcore\tag\parser\token\CompositeTag'
				), 
				new \championcore\tag\parser\token\Content()
			)
		);
	}
	
	/**
	 * apply rules to a list of tokens until no more changes can be made to the front of the list
	 * @param array list of tokens
	 * @return array list of result tokens
	 */
	protected function apply_rules (array $token_list) : array {
		
		$change_flag = true;
		
		$working_list = $token_list;
		
		while ($change_flag == true) {
			
			$change_flag = false;
			
			foreach ($this->rules as $rule) {
				
				# skip if not enough tokens on the list
				if ($rule->get_input_size() > \sizeof($working_list)) {
					continue;
				}
				
				# can rule be applied
				$match_flag = $rule->is_match( $working_list );
				
				# rule matches
				if ($match_flag == true) {
					
					$working_list = $rule->apply( $working_list );
					
					# flag the change
					$change_flag = true;
					
					break;
				}
			}
		}
		
		return $working_list;
	}
	
	/**
	 * parse a string
	 * @param string $content text to consider
	 * @return array of parsed data
	 */
	public function parse (string $content) : array {
		
		# extract the tokens
		$lexer = new \championcore\tag\Lexer($content);
		
		$token = $lexer->consume();
		
		while ($token !== false) {
			
			$this->token_list[] = $token;
			
			$token = $lexer->consume();
		}
		
		# parse
		$working_list = $this->token_list;
		
		$change_flag = true;
		
		while ($change_flag == true) {
			
			$output_list =[];
			
			$hash_start = \md5( \print_r($working_list, true) );
			
			while (\sizeof($working_list) > 0) {
				
				$working_list = $this->apply_rules( $working_list );
				
				$output_list[] = \array_shift( $working_list );
			}
			
			$hash_end = \md5( \print_r($output_list, true) );
			
			$change_flag = (\strcmp($hash_start, $hash_end) != 0);
			
			$working_list = $output_list;
		}
		
		$this->token_list = $working_list;
		
		return $this->token_list;
	}
	
	/**
	 * pretty print
	 * @param int $depth Offset in tabs
	 * @param string $spacer string to use for spacing defaults to tab
	 * @return string
	 */
	public function pretty_print (int $depth = 1, string $spacer = "\t") : string {
		
		$top    = \str_repeat($spacer, $depth) . \get_class($this) . ' => <<';
		$bottom = \str_repeat($spacer, $depth) . '>>';
		
		$middle  = '';
		$counter = 0;
		
		foreach ($this->token_list as $value) { #"({$counter})"
			
			$middle .= (\str_repeat($spacer, $depth) . $value->pretty_print( $depth + 1, $spacer ) . "\n");
			
			$counter++;
		}
		
		$bottom = \str_pad( $bottom, $depth, $spacer, \STR_PAD_LEFT );
		
		$result = "\n{$top}\n{$middle}\n{$bottom}\n";
		
		return $result;
	}
}
