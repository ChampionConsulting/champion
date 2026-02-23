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
 * export site as static html
 */
class ExportHtmlWebsite extends Base {
	
	/**
	 * get request
	 * 
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# extract
		$param_url = $request_params['url'] ?? '';
		
		# filter
		$param_url = \championcore\filter\url( $param_url );

		# page details
		$pile = new \championcore\store\page\Pile( \championcore\get_configs()->dir_content . '/pages' );

		$view_model->cur_page    = 1;
		$view_model->total_pages = \ceil(
			\floatval($pile->size())
			/
			\floatval(\championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page)
		);

		# crawler
		$spider = new \championcore\export_html\Spider();

		$target_dir = \championcore\wedge\config\get_json_configs()->json->export_html->path;
		$target_dir = empty($target_dir) ? (CHAMPION_BASE_DIR . '/championcore/storage/export_html') : $target_dir;
		
		$url_to_crawl = (\strlen($param_url) == 0) ? CHAMPION_BASE_URL : $param_url;
		$url_limit    = (\strlen($param_url) == 0) ? 1000 : 1;

		# var_dump( $param_url . ' ' . $url_to_crawl . ' ' . $url_limit ); exit;

		$spider->crawl( $url_to_crawl, CHAMPION_BASE_URL, $target_dir, $url_limit );

		# view model
		$view_model->export_folder = $target_dir;
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/export_html_website.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
}
