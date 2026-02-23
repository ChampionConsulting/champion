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

namespace championcore\import_html;

/**
 * extract the html for a given page
 */
class Page {

	/**
	 * generate the page from the parsed html
	 * 
	 * @param \stdClass $parsed
	 * @return \championcore\store\page\Item
	 */
	public function generate (\stdClass $parsed) : \championcore\store\page\Item {

		$page_name = \substr( sha1($parsed->html), 0, 6);

		$result = $this->generate_html( $parsed, $page_name );

		$result->save( \championcore\get_configs()->dir_content . "/pages/import-html-page-{$page_name}.txt" );

		return $result;
	}

	/**
	 * generate the page from the parsed html
	 * 
	 * @param \stdClass $parsed
	 * @return \championcore\store\page\Item
	 */
	public function generate_html (\stdClass $parsed, string $page_name) : \championcore\store\page\Item {

		$result = new \championcore\store\page\Item();

		# create the html
		\libxml_clear_errors();
		\libxml_use_internal_errors(true);

		$document = new \DOMDocument();
		$status = $document->loadHTML( $parsed->html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

		$document->normalizeDocument();

		# xpath
		$dom_xpath = new \DOMXPath( $document );

		# block folder
		$folder = \championcore\get_configs()->dir_content . "/blocks/{$page_name}";

		# cleanup existing folders
		if (\is_dir($folder)) {
			\championcore\dir_nuke( $folder );
		}

		# create folder
		if (!\is_dir($folder)) {
			\mkdir($folder);
		}

		# save blocks
		$counter = 0;

		foreach ($parsed->block_list as $value) {

			$xpath = $value->xpath;

			# expand counter
			$expanded = '';
			$probe_counter = $counter;
			while ($probe_counter > 0 ) {

				$expanded = chr( ($probe_counter % 26) + 97) . $expanded; # NB 65 is ascii uppercase A, 97 is lowercase a

				$probe_counter = \intval($probe_counter / 26);
			}
			$expanded = \str_pad( $expanded, 5, 'a', \STR_PAD_LEFT );

			# block
			$block = new \championcore\store\block\Item();
			$block->html     = $value->payload;
			$block->title    = 'import-page-block-' . \date('YmdHis') . '-' . $expanded . '-' . \substr( sha1($value->payload), 0, 6);
			
			$block->save( $folder . \DIRECTORY_SEPARATOR . $block->title . '.txt');

			# adjust document
			$node_list = $dom_xpath->query( $xpath );

			foreach ($node_list as $nnn) {

				$nnn->textContent = '{{block:"' . $page_name . '/' . $block->title . '"}}';
			}

			$counter++;
		}

		# update the img src
		$body_nodes = $dom_xpath->query( 'body' );

		foreach ($body_nodes as $nnn) {
			foreach (\championcore\html\get_elements_by_tag_name($nnn, 'img') as $node) {

				$probe = $node->attributes->getNamedItem('src');

				if (isset($probe) and isset($probe->nodeValue)) {

					$tmp_url = \championcore\html\expand_single_url( $probe->nodeValue, $parsed->url );

					#$probe->nodeValue = $tmp_url;

					$node->setAttributeNode( new \DOMAttr('src', $tmp_url) );
				}
			}
		}

		# inject header css/js
		$header_nodes = $dom_xpath->query( 'head' );

		foreach ($header_nodes as $nnn) {

			# convert to inline - css
			# foreach (\championcore\html\get_elements_by_tag_name($nnn, 'style') as $node) {
			# }
	
			# convert to inline - css - linked
			foreach (\championcore\html\get_elements_by_tag_name($nnn, 'link') as $node) {

				$probe = $node->attributes->getNamedItem('href');

				if (isset($probe) and isset($probe->nodeValue)) {

					$probe = $probe->nodeValue;

					foreach ($parsed->theme->header_css as $css) {
						if (isset($css->href) and isset($css->old_href) and isset($css->payload) and (\strcasecmp($css->old_href, $probe) == 0)) {

							if (\is_string($css->payload)) {
								$style_node = $document->createElement('style');
								$style_node->textContent = $css->payload;

								$nnn->appendChild( $style_node );

								$nnn->removeChild( $node );
							} else {
	
								$tmp_node = $node->attributes->getNamedItem('href');
								#$tmp_node->nodeValue = $css->href;
								#$tmp_node->value     = $css->href;

								$node->setAttributeNode( new \DOMAttr('href', $css->href) );
							}

							break;
						}
					}
				}
			}
	
			# js
			foreach (\championcore\html\get_elements_by_tag_name($nnn, 'script') as $node) {

				$probe = $node->attributes->getNamedItem('src');

				if (isset($probe) and isset($probe->nodeValue)) {

					$probe = $probe->nodeValue;

					foreach ($parsed->theme->header_js as $js) {

						if (isset($js->src) and isset($js->old_src) and isset($js->payload) and \is_string($js->src) and (\strcasecmp($js->old_src, $probe) == 0)) {

							if (\is_string($js->payload)) {
								$js_node = $document->createElement( 'script' );
								$js_node->textContent = $js->payload;

								$nnn->appendChild( $js_node );

								$nnn->removeChild( $node );
							} else {
								$tmp_node = $node->attributes->getNamedItem('src');
								#$tmp_node->nodeValue = $js->src;

								$node->setAttributeNode( new \DOMAttr('href', $js->src) );
							}

							break;
						}
					}
				}
			}
			
			# standard template css/js
			$nnn->appendChild( $document->createTextNode( '{{theme_css}}') );
			$nnn->appendChild( $document->createTextNode( '{{theme_js}}') );
			$nnn->appendChild( $document->createComment( ' OGP PLACEHOLDER ') );
			$nnn->appendChild( $document->createComment( ' GOOGLE ANALYTICS ') );
		}

		# inject footer css/js
		$body_nodes = $dom_xpath->query( 'body' );

		foreach ($body_nodes as $nnn) {

			# body class
			# $body_class = \championcore\get_context()->theme->body_tag->render( [''] );
			# $body_class = \str_replace( 'class="', '', $body_class );
			# $body_class = \str_replace(       '"', '', $body_class );
			# $nnn->setAttribute( 'class', $body_class . ' ' . $nnn->getAttribute('class') );

			# JS
			$nnn->appendChild( $document->createTextNode( '{{theme_js_body}}') );

			# header
			$nav_node = $document->createElement( 'championcms-header', '');

			$nav_node_header = $document->createElement( 'header', '{{navigation}}' );
			$nav_node_header->setAttributeNode( new \DOMAttr('class', 'header') );

			$nav_node->appendChild( $nav_node_header );

			$custom_element_js = $document->createElement( 'script' );
			$custom_element_js->setAttributeNode( new \DOMAttr('src', CHAMPION_BASE_URL . '/championcore/asset/dist/widget/championcms-header.js') );

			$nnn->appendChild( $custom_element_js );

			# $nav_node = $document->createElement('header', '{{navigation}}' );
			# $nav_node->setAttribute('class', 'header');

			$nnn->insertBefore( $nav_node, $nnn->firstChild );

			# footer
			$made_in_champion = \championcore\get_context()->theme->made_in_champion->render( ['badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/content/media/branding/logo.svg')] );
			
			$footer_node = $document->createDocumentFragment();
			$footer_node->appendXML(
<<<EOD
<div id="footer" class="group">
	{{ block:"copyright" }}
	{$made_in_champion}
</div>
EOD
			);

			$nnn->appendChild( $footer_node );

			# convert to inline - css - linked
			foreach (\championcore\html\get_elements_by_tag_name($nnn, 'link') as $node) {

				$probe = $node->attributes->getNamedItem('href');

				if (isset($probe) and isset($probe->nodeValue)) {

					$probe = $probe->nodeValue;

					foreach ($parsed->theme->body_css as $css) {
						if (isset($css->href) and isset($css->old_href) and isset($css->payload) and (\strcasecmp($css->old_href, $probe) == 0)) {

							if (\is_string($css->payload)) {
								$style_node = $document->createElement('style');
								$style_node->textContent = $css->payload;

								$nnn->appendChild( $style_node );

								$nnn->removeChild( $node );
							} else {
								$tmp_node = $node->attributes->getNamedItem('href');
								#$tmp_node->href = $css->href;

								$node->setAttributeNode( new \DOMAttr('href', $css->href) );
							}

							break;
						}
					}
				}
			}
	
			# js
			foreach (\championcore\html\get_elements_by_tag_name($nnn, 'script') as $node) {

				$probe = $node->attributes->getNamedItem('src');

				if (isset($probe) and isset($probe->nodeValue)) {

					$probe = $probe->nodeValue;

					foreach ($parsed->theme->body_js as $js) {

						if (isset($js->src) and isset($js->old_src) and \is_string($js->payload) and \is_string($js->src) and (\strcasecmp($js->old_src, $probe) == 0)) {

							if (\is_string($js->payload)) {
								$js_node = $document->createElement( 'script' );
								$js_node->textContent = $js->payload;

								$nnn->appendChild( $js_node );

								$nnn->removeChild( $node );
							} else {
								$tmp_node = $node->attributes->getNamedItem('src');
								#$tmp_node->nodeValue = $js->src;

								$node->setAttributeNode( new \DOMAttr('href', $js->src) );
							}

							break;
						}
					}
				}
			}
		}

		# set page state
		$result->page_template = 'skeleton';
		$result->html          = $document->saveHTML();

		return $result;
	}
	
	/**
	 * extract the html
	 * 
	 * @param string $html
	 * @return \stdClass
	 */
	public function parse (string $html, string $param_base_url) : \stdClass {

		$result = (object)[

			'url' => \rtrim($param_base_url, '/'),

			'html' => $html,

			'theme' => (object)[
				'header_css' => [],
				'header_js'  => [],
				'body_css'   => [],
				'body_js'    => []
			],
			'block_list' => []
		];

		\libxml_clear_errors();
		\libxml_use_internal_errors(true);

		$document = new \DOMDocument();
		$status = $document->loadHTML( $html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

		$document->normalizeDocument();

		# extract header
		$header_css = [];
		$header_js  = [];
		foreach ($document->getElementsByTagName('head') as $header_node) {

			# css
			foreach (\championcore\html\get_elements_by_tag_name($header_node, 'style') as $node) {

				$header_css[] = (object)['type' => 'style', 'payload' => $node->textContent];
			}

			# css - linked
			foreach (\championcore\html\get_elements_by_tag_name($header_node, 'link') as $node) {

				$probe = $node->attributes->getNamedItem('href');
				$rel   = $node->attributes->getNamedItem('rel');

				if (isset($probe) and isset($probe->nodeValue) and isset($rel) and isset($rel->nodeValue)) {

					$probe = $probe->nodeValue;
					$rel   = $rel->nodeValue;

					if (\strcasecmp($rel, 'stylesheet') == 0) {

						$tmp = (object)['type' => 'link', 'href' => $probe, 'old_href' => $probe, 'payload' => false];

						if (\stripos($probe, 'http') === 0) {

							$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

							$tmp->href = $probe;

							# $tmp->payload = \championcore\html\import_file_from_url( $probe );
						} else {
							$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

							$tmp->href = $probe;
						}

						$header_css[] = $tmp;
					}
				}
			}

			# js
			foreach (\championcore\html\get_elements_by_tag_name($header_node, 'script') as $node) {

				$probe = $node->attributes->getNamedItem('src');
				$type  = $node->attributes->getNamedItem('type') ?? (object)['nodeValue' => 'text/javascript'];

				if (isset($probe) and isset($probe->nodeValue) and isset($type->nodeValue)) {

					$probe = $probe->nodeValue;
					$type  = $type->nodeValue;

					$tmp = (object)['type' => $type, 'payload' => false, 'src' => $probe, 'old_src' => $probe];

					if (\stripos($probe, 'http') === 0) {

						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->src = $probe;

						# $tmp->payload = \championcore\html\import_file_from_url( $probe );
					} else {
						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->src = $probe;
					}

					$header_js[] = $tmp;
				} else {
					$header_js[] = (object)['type' => $type, 'payload' => $node->textContent, 'src' => false, 'old_src' => false];
				}
			}
			
		}

		# extract the body
		$body_css = [];
		$body_js  = [];

		foreach ($document->getElementsByTagName('body') as $body_node) {

			# css
			foreach (\championcore\html\get_elements_by_tag_name($body_node, 'style') as $node) {

				$body_css[] = (object)['type' => 'style', 'payload' => $node->textContent];
			}

			# css - linked
			foreach (\championcore\html\get_elements_by_tag_name($body_node, 'link') as $node) {

				$probe = $node->attributes->getNamedItem('href');

				if (isset($probe) and isset($probe->nodeValue)) {

					$probe = $probe->nodeValue;

					$tmp =  (object)['type' => 'link', 'href' => $probe, 'old_href' => $probe];

					if (\stripos($probe, 'http') === 0) {

						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->href = $probe;

						# $tmp->payload = \championcore\html\import_file_from_url( $probe );
					} else {
						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->href = $probe;
					}

					$body_css[] = $tmp;
				}
			}


			# js
			foreach (\championcore\html\get_elements_by_tag_name($body_node, 'script') as $node) {

				$probe = $node->attributes->getNamedItem('src');
				$type  = $node->attributes->getNamedItem('type') ?? (object)['nodeValue' => 'text/javascript'];

				if (isset($probe) and isset($probe->nodeValue) and isset($type->nodeValue)) {

					$probe = $probe->nodeValue;
					$type  = $type->nodeValue;

					$tmp = (object)['type' => $type, 'payload' => false, 'src' => $probe, 'old_src' => $probe];

					if (\stripos($probe, 'http') === 0) {

						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->src = $probe;

						# $tmp->payload = \championcore\html\import_file_from_url( $probe );
					} else {
						$probe = \championcore\html\expand_single_url( $probe, $param_base_url);

						$tmp->src = $probe;
					}

					$body_js[] = $tmp;
				} else {
					$body_js[] = (object)['type' => $type, 'payload' => $node->textContent, 'src' => false, 'old_src' => false];
				}
			}
		}

		# extract the blocks
		$body_block = [];

		foreach ($document->getElementsByTagName('body') as $body_node) {

			$text_containers = [
				'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
				'p',
				'dl', 'ol', 'ul',
				'img'
			];

			foreach ($text_containers as $tag) {

				$candidate_list = \championcore\html\get_elements_by_tag_name($body_node, $tag );

				foreach ($candidate_list as $node) {

					$parent_node = $node->parentNode;

					$xpath = $node->getNodePath();

					$parent_xpath = $parent_node->getNodePath();

					if (\strcasecmp('/html/body', $parent_xpath) == 0) {
						$body_block[] = new \championcore\import_html\parsed\Block($node->C14N(), $xpath, $node );

					} else {
						$body_block[] = new \championcore\import_html\parsed\Block($parent_node->C14N(), $parent_xpath, $parent_node );
					}
				}
			}
		}

		# reduce block list by removing blocks inside blocks
		$xpath_list = [];

		foreach ($body_block as $value) {
			$xpath_list[ $value->xpath ] = $value;
		}

		\ksort( $xpath_list );

		$tmp = [];

		while (\sizeof($xpath_list) > 0) {

			$probe = \array_shift( $xpath_list );

			$tmp[] = $probe;

			foreach ($xpath_list as $key => $value) {

				if (\strpos($value->xpath, $probe->xpath) !== false) {

					# echo $probe->xpath . ' ' . $value->xpath . ' ' . \strpos($value->xpath, $probe->xpath) . \PHP_EOL;

					unset( $xpath_list[$key] );
				}
			}
		}

		$body_block = $tmp;

		# sort the blocks
		# \ksort($body_block);

		# sort the blocks by order of appearance
		# \championcore\html\annotate_document_depth( $document );

		# echo \championcore\import_html\parsed\Block::pretty_print_array( $body_block ); exit;

		$tmp = [];
		foreach ($body_block as $value) {

			$depth = 0;

			/*
			if ($node->hasAttribute(\championcore\html\ANNOTATE_DOCUMENT_DEPTH_ATTRIBUTE_NAME)) {

				$depth = $node->getAttribute(\championcore\html\ANNOTATE_DOCUMENT_DEPTH_ATTRIBUTE_NAME);

				$depth = \intval( $depth );
			}
			*/

			$depth = $value->node->getLineNo();
			#$depth = \substr_count( $value->xpath, '/');
			
			if (!isset($tmp[$depth])) {
				$tmp[ $depth ] = [];
			}

			$tmp[ $depth ][] = $value;
		}

		\ksort( $tmp );

		#echo \championcore\import_html\parsed\Block::pretty_print_array( $tmp ); exit;

		$body_block = [];

		foreach ($tmp as $node_list) {

			foreach ($node_list as $value) {
				$body_block[] = $value;
			}
		}

		#echo \championcore\import_html\parsed\Block::pretty_print_array( $body_block ); exit;

		# pack result
		$result->theme->header_css = $header_css;
		$result->theme->header_js  = $header_js;
		$result->theme->body_css   = $body_css;
		$result->theme->body_js    = $body_js;

		$result->block_list = $body_block;

		###
		return $result;
	}
	
	/**
	 * fix links in the page for the  html export
	 * 
	 * @param string $html
	 * @param string $source_base_url
	 * @param string $target_base_url
	 * @return string
	 */
	public function link_fix (string $html, string $source_base_url, string $target_base_url) : string {
		
		\libxml_clear_errors();
		\libxml_use_internal_errors(true);

		$document = new \DOMDocument();
		$status = $document->loadHTML( $html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

		$document->normalizeDocument();

		// anchor elements
		foreach ($document->getElementsByTagName('a') as $node) {

			if (isset($node->attributes)) {

				$href = $node->attributes->getNamedItem('href');

				if (isset($href) and isset($href->nodeValue)) {

					$text = $href->nodeValue;

					if (\strlen($text) > 0) {

						if (\stripos($text, $source_base_url) > -1) {

							$relative_url = \championcore\html\relative_url( $source_base_url, $target_base_url );

							$clean = \str_replace( $source_base_url, $relative_url, $text); # target_base_url
							$clean = \str_replace( '//', '/', $clean );
							$clean = (\strlen($clean) == 0) ? 'index' : $clean;
							$clean = $clean . '.html';

							$href->nodeValue = $clean;
						}
					}
				}
			}
		}

		$result = $document->saveHTML();

		return $result;
	}

}
