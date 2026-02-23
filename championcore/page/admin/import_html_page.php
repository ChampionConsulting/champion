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

namespace championcore\page\admin;

/**
 * import html into page
 */
class ImportHtmlPage extends Base {
	
	/**
	 * get request
	 * 
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();

		# page details
		$pile = new \championcore\store\page\Pile( \championcore\get_configs()->dir_content . '/pages' );

		$view_model->cur_page    = 1;
		$view_model->total_pages = \ceil(
			\floatval($pile->size())
			/
			\floatval(\championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page)
		);
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/import-html-page.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
	/*
	 * POST request adds a new custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$fields = $this->extract_fields( $request_params );

		# params
		$param_url  = $fields['url'];
		$param_text = $fields['text'];

		# filter
		$param_url  = \trim( $param_url  );
		$param_text = \trim( $param_text );
		
		#show the form?
		if ((\strlen($param_text) == 0) and (\strlen($param_url) == 0)) {
			
			\championcore\session\status_add( $GLOBALS['lang_import_html_page_no_input'], 'error' );
			
			\header( 'Location: ' . CHAMPION_ADMIN_URL . '/index.php?p=import-html-page' );
			exit;
		}

		# start loading
		$processor = new \championcore\import_html\Page();

		# import the text
		if (strlen($param_url) > 0) {

			$param_text = \championcore\html\import_file_from_url( $param_url );
		}

		# parse
		$parsed = $processor->parse( $param_text, $param_url );

		$page_item = $processor->generate( $parsed );

		# done
		\championcore\session\status_add( $GLOBALS['lang_import_html_page_success'], 'info' );

		\header( 'Location: ' . CHAMPION_ADMIN_URL . '/index.php?p=import-html-page' );
		exit;
	}
}
