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

namespace championcore\tags;

/**
 * unishop tag
 */
class Unishop extends Base {
	
	/**
	 * generate html
	 * 
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge(
			[
				
				'business'        => 'paypal@domain.com',
				'currency_code'   => 'USD',
				'currency_symbol' => '$',
				'lc'              => 'US', # locale
				'no_shipping'     => '0',
				'return_url'      => '',
				'cancel_url'      => ''
			],
			$params
		);
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$view_model->business        = \trim($arguments['business']);
		$view_model->currency_code   = \trim($arguments['currency_code']);
		$view_model->currency_symbol = \trim($arguments['currency_symbol']);
		$view_model->lc              = \trim($arguments['lc']);
		$view_model->no_shipping     = \trim($arguments['no_shipping']);
		$view_model->return_url      = \trim($arguments['return_url']);
		$view_model->cancel_url      = \trim($arguments['cancel_url']);
		
		$view_model->business        = \trim($view_model->business,        '"');
		$view_model->currency_code   = \trim($view_model->currency_code,   '"');
		$view_model->currency_symbol = \trim($view_model->currency_symbol, '"');
		$view_model->lc              = \trim($view_model->lc,              '"');
		$view_model->no_shipping     = \trim($view_model->no_shipping,     '"');
		$view_model->return_url      = \trim($view_model->return_url,      '"');
		$view_model->cancel_url      = \trim($view_model->cancel_url,      '"');
		
		$view_model->business        = \trim($view_model->business);
		$view_model->currency_code   = \trim($view_model->currency_code);
		$view_model->currency_symbol = \trim($view_model->currency_symbol);
		$view_model->lc              = \trim($view_model->lc);
		$view_model->no_shipping     = \intval( \trim($view_model->no_shipping) );
		$view_model->return_url      = \trim($view_model->return_url);
		$view_model->cancel_url      = \trim($view_model->cancel_url);
		
		\championcore\pre_condition(         isset($view_model->business) );
		\championcore\pre_condition(    \is_string($view_model->business) );
		\championcore\pre_condition( \strlen(\trim($view_model->business)) > 0 );
		
		\championcore\pre_condition(         isset($view_model->currency_code) );
		\championcore\pre_condition(    \is_string($view_model->currency_code) );
		\championcore\pre_condition( \strlen(\trim($view_model->currency_code)) > 0 );
		
		\championcore\pre_condition(         isset($view_model->currency_symbol) );
		\championcore\pre_condition(    \is_string($view_model->currency_symbol) );
		\championcore\pre_condition( \strlen(\trim($view_model->currency_symbol)) > 0 );
		
		\championcore\pre_condition(         isset($view_model->lc) );
		\championcore\pre_condition(    \is_string($view_model->lc) );
		\championcore\pre_condition( \strlen(\trim($view_model->lc)) > 0 );
		
		\championcore\pre_condition(       isset($view_model->no_shipping) );
		\championcore\pre_condition( \is_numeric($view_model->no_shipping) );
		
		\championcore\pre_condition(         isset($view_model->return_url) );
		\championcore\pre_condition(    \is_string($view_model->return_url) );
		\championcore\pre_condition( \strlen(\trim($view_model->return_url)) > 0 );
		
		\championcore\pre_condition(         isset($view_model->cancel_url) );
		\championcore\pre_condition(    \is_string($view_model->cancel_url) );
		\championcore\pre_condition( \strlen(\trim($view_model->cancel_url)) > 0 );
		
		# add css/js
		\championcore\get_context()->theme->css->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/css/normalize.css" );
		\championcore\get_context()->theme->css->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/css/unifilter.css" );
		\championcore\get_context()->theme->css->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/css/unishop.css", [], 'unishop' );
		# \championcore\get_context()->theme->css->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/css/style.css"     );
		
		# \championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/js/jquery-3.3.1.min.js" );
		\championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/js/jquery.easing.1.3.js" );
		\championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/js/masonry.min.js" );
		\championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/js/jquery.unifilter.min.js" );
		\championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/unishop/js/jquery.unishop.min.js", [], 'unishop' );
		
		$path = \championcore\wedge\config\get_json_configs()->json->path;
		
		$inline_css =<<<EOD
.unishop-modal-options > .unishop-modal-option {
	position: relative;
	padding: 1px;
}
.unishop-modal-options > .unishop-modal-option .unishop-select-wrap::after {
	display: block;
	position: absolute;
	top:   20px;
	right: 60px;
	left: auto;
}
EOD;
		\championcore\get_context()->theme->css->add_inline( 'unishop_inline', $inline_css,  array('unishop') );
		
		$inline_js =<<<EOD
jQuery(document).ready(
	function() {
		jQuery('#shop').unishop(
			{
				shopXML: '{$path}/inc/plugins/unishop/shop.xml.php',
				
				currencySymbol: '{$view_model->currency_symbol}',  
				currencyName:   '{$view_model->currency_code}',
				
				shopFilters: {
					animationType: 'scale',
					filters: 'category',
					range: 'price', 
					search: 'name',
					sort: 'rating, price'
				},
				
				paypal: {
					'business':      '{$view_model->business}',
					'currency_code': '{$view_model->currency_code}',
					'lc':            '{$view_model->lc}',
					'no_shipping':   {$view_model->no_shipping},
					'return':        '{$view_model->return_url}',
					'cancel_return': '{$view_model->cancel_url}'  
				}
			}
		)
	}
);
EOD;
		\championcore\get_context()->theme->js_body->add_inline( 'unishop_inline', $inline_js,  array('unishop') );
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/unishop.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
