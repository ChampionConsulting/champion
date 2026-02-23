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

namespace championcore\import_html\parsed;

/**
 * parsed data: block
 */
class Block {

	/**
	 * the html
	 */
	public string $payload = '';

	/**
	 * the DOM node
	 */
	public \DOMNode $node;

	/**
	 * the xpath
	 */
	public string $xpath = '';

	/**
	 * constructor
	 */
	public function __construct (string $payload, string $xpath, \DOMNode $node) {
		
		$this->payload = $payload;
		$this->node    = $node;
		$this->xpath   = $xpath;
	}

	/**
	 * magic method
	 */
	public function __toString () {

		$hash = \md5( $this->payload );

		$result = "XPATH: {$this->xpath}, md5_hash: {$hash}";
		
		return $result;
	}

	/**
	 * __toString on an array of blocks
	 */
	public static function pretty_print_array (array $arg, int $level = 0) : string {

		$collect = '';

		$indentation = \str_pad('', $level, ' ');

		foreach ($arg as $key => $value) {

			if ($value instanceof \championcore\import_html\parsed\Block) {
				$collect .= $indentation;
				$collect .= "[{$key}] ";
				$collect .= (string)$value;
				$collect .= \PHP_EOL;

			} else if (\is_array($value)) {
				$collect .= $indentation;
				$collect .= "[{$key}] ";
				$collect .= ' (';
				$collect .= \championcore\import_html\parsed\Block::pretty_print_array( $value, $level + 1);
				$collect .= $indentation . ' )';
				$collect .= \PHP_EOL;
			}
		}

		return $collect;
	}
}
