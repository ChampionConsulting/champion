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

namespace championcore\page;

/**
 * web blog api
 */
class WebBlogApi extends Base {
	
	/**
	 * log all interactions with the blog client
	 */
	protected $log_flag = false;
	
	/**
	 * where to get the post body content
	 * NB this is raw
	 */
	protected $post_body_stream = '';
	
	/**
	 * constructor
	 * @param bool $log_flag
	 * @param string $post_body_stream string Where to find the XML post content
	 */
	function __construct (bool $log_flag = false, string $post_body_stream = 'php://input') {
		
		parent::__construct();
		
		\championcore\pre_condition( \strlen($post_body_stream) > 0 );
		
		$this->log_flag         = $log_flag;
		$this->post_body_stream = $post_body_stream;
	}
	
	/**
	 * rebuild the blog item / blog UUID cache
	 * @param bool $flag_force Set to true to force rebuilding of the cache
	 * @return stdClass The cached data
	 */
	public static function blog_item_uuid_cache_rebuild (bool $flag_force = false) : \stdClass {
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool(\championcore\cache\Manager::DAY_1 );
		$cache_key     = 'web_blog_api';
		
		# regenerate the blog and blog item cache
		$blog_item_uuid_cache = $cache_pool->get( $cache_key );
		
		if ($flag_force or ($blog_item_uuid_cache == false)) {
			$blog_item_uuid_cache = (object)array(
				'blogs' => (object)[],
				'items' => (object)[]
			);
		
			# first list all the blogs
			$blog_roll = new \championcore\store\blog\Roll( \championcore\get_configs()->dir_content . '/blog' );
			
			$sub_blogs = $blog_roll->sub_rolls();
			
			# uuids - top level blog
			$blog_item_uuid_cache->blogs->{ \championcore\generate_uuid( \print_r($blog_roll, true) ) } = '/blog/';
			
			foreach ($blog_roll->items( 1, $blog_roll->size()) as $item) {
				
				$blog_item_uuid_cache->items->{ \championcore\generate_uuid( \print_r($item, true)) } = $item->get_location();
			}
			
			# uuids - top level blog - corner case - no UUID for default blog
			$blog_item_uuid_cache->blogs->{'b'} = '/blog/';
			
			foreach ($blog_roll->items( 1, $blog_roll->size()) as $item) {
				
				$blog_item_uuid_cache->items->{ \championcore\generate_uuid( \print_r($item, true)) } = $item->get_location();
			}
			
			# uuids - sub-blogs
			foreach ($sub_blogs as $value) {
				
				$blog_item_uuid_cache->blogs->{ \championcore\generate_uuid( \print_r($value, true)) } = ('/blog/' . $value->get_name());
				
				foreach ($value->items( 1, $value->size()) as $item) {
					
					$blog_item_uuid_cache->items->{ \championcore\generate_uuid( \print_r($item, true)) } = $item->get_location();
				}
			}
			
			# cache save
			$cache_pool->set( $cache_key, $blog_item_uuid_cache, array('web_blog_api') );
		}
		
		return $blog_item_uuid_cache;
	}
	
	/**
	 * delete request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_delete (array $request_params, array $request_cookie) : string {
		return $this->handle_post($request_params, $request_cookie);
	}
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		return $this->handle_post($request_params, $request_cookie);
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		$param_xml = \file_get_contents( $this->post_body_stream );
		
		# log the request XML-RPC call for debugging
		if ($this->log_flag) {
			\error_log( "=========================================================" );
			\error_log( 'WebBlogApi::handle_post request' );
			\error_log( $param_xml );
		}
		
		$xml_rpc_server = new \PhpXmlRpc\Server(
			array(
				
				"blogger.deletePost" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_delete_post",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcBoolean,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcBoolean) ),
					"docstring" => "delete post",
				),
				
				"blogger.getUsersBlogs" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_get_users_blogs",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcArray,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString) ),
					"docstring" => "delete post",
				),
				
				"metaWeblog.editPost" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_edit_post",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcBoolean,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcStruct, \PhpXmlRpc\Value::$xmlrpcBoolean) ),
					"docstring" => "edit post",
				),
				
				"metaWeblog.getCategories" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_get_categories",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcStruct,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString) ),
					"docstring" => "get categories",
				),
				
				"metaWeblog.getPost" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_get_post",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcStruct,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString) ),
					"docstring" => "get a post",
				),
				
				"metaWeblog.getRecentPosts" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_get_recent_posts",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcArray,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcInt) ),
					"docstring" => "get recent posts",
				),
				
				"metaWeblog.newPost" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_new_post",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcString,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcStruct, \PhpXmlRpc\Value::$xmlrpcBoolean) ),
					"docstring" => "get categories",
				),
				
				"metaWeblog.newMediaObject" => array(
					"function"  => "\championcore\page\WebBlogApi::rpc_new_media_object",
					"signature" => array( array(\PhpXmlRpc\Value::$xmlrpcStruct,   \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcString, \PhpXmlRpc\Value::$xmlrpcStruct) ),
					"docstring" => "upload media object",
				),
			),
			false
		);
		
		# $xml_rpc_server->setDebug( 1 );
		
		$result = $xml_rpc_server->service( $param_xml, true );
		
		# log the request XML-RPC call for debugging
		if ($this->log_flag) {
			\error_log( 'WebBlogApi::handle_post response' );
			\error_log( $result );
			\error_log( "=========================================================" );
		}
		
		return $result;
	}
	
	/**
	 * put request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_put (array $request_params, array $request_cookie) : string {
		return $this->handle_post($request_params, $request_cookie);
	}
	
	/**
	 * delete a blog post
	 * @param PhpXmlRpc\Request $request
	 * @return object xmlrpcStruct
	 */
	public static function rpc_delete_post (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$api_key  = $request->params[0]->scalarval();
		$post_id  = $request->params[1]->scalarval();
		$username = $request->params[2]->scalarval();
		$password = $request->params[3]->scalarval();
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog item' );
		
		if (($username == 'administrator') or ($username == 'editor')) {
			
			if (     \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				  or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password)
				 ) {
				
				$param_post_id = \championcore\filter\uuid( $post_id );
				$param_post_id = $blog_item_uuid_cache->items->{$param_post_id};
				$param_post_id = \str_replace( 'blog/', '', $param_post_id );
				$param_post_id = \championcore\get_configs()->dir_content . '/blog/' . \championcore\filter\blog_item_id( $param_post_id ) . '.txt';
				
				$param_post_id = \str_replace( '/', DIRECTORY_SEPARATOR, $param_post_id ); # windows path fix
				
				\unlink( $param_post_id );
				
				# $encoder = new \PhpXmlRpc\Encoder();
				
				$packed = new \PhpXmlRpc\Value( true, \PhpXmlRpc\Value::$xmlrpcBoolean );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * edit a blog post
	 * @param PhpXmlRpc\Request $request  with the $blog_id, $username, $password, $payload
	 * @return object xmlrpcArray
	 */
	public static function rpc_edit_post (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# \error_log( print_r($blog_item_uuid_cache, true) );
		
		# unpack the params
		$post_id  = $request->params[0]->scalarval();
		$username = $request->params[1]->scalarval();
		$password = $request->params[2]->scalarval();
		$payload  = $request->params[3];
		$publish  = $request->params[4]->scalarval(); # ignore
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog item' );
		
		if (($username == 'administrator') or ($username == 'editor')) {
			
			if (     \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				  or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password) ) {
				
				$param_post_id = \championcore\filter\uuid( $post_id );
				
				if (isset($blog_item_uuid_cache->items->{ $param_post_id })) {
					$param_post_id = $blog_item_uuid_cache->items->{ $param_post_id };
					$param_post_id = \str_replace( 'blog/', '', $param_post_id );
					$param_post_id = \championcore\get_configs()->dir_content . '/blog/' . \championcore\filter\blog_item_id( $param_post_id ) . '.txt';
					
					$blog_item = new \championcore\store\blog\Item();
					$blog_item->load( $param_post_id );
					
					# unpack
					$categories = [];
					foreach ($payload['categories'] as $value) {
						$categories[] = \trim( $value->scalarval() );
					}
					$categories = \implode(',', $categories );
					
					$description = $payload['description']->scalarval();
					$title       = $payload['title'      ]->scalarval();
					
					$description = \trim( $description );
					$title       = \trim( $title );
					
					# save
					$blog_item->html  = $description;
					$blog_item->title = $title;
					$blog_item->tags  = $categories;
					
					$blog_item->save( $param_post_id );
					
					$packed = new \PhpXmlRpc\Value( true, \PhpXmlRpc\Value::$xmlrpcBoolean );
					
					$result = new \PhpXmlRpc\Response( $packed );
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * get categories/tags
	 * \param $request PhpXmlRpc\Request with the $blog_id, $username, $password
	 * \return object xmlrpcArray
	 */
	public static function rpc_get_categories (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$blog_id  = $request->params[0]->scalarval();
		$username = $request->params[1]->scalarval();
		$password = $request->params[2]->scalarval();
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog' );
		
		if (true or ($username == 'administrator') or ($username == 'editor')) {
			
			if (true
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password)
				) {
			
				$param_blog_id = \championcore\filter\uuid( $blog_id );
				$param_blog_id = $blog_item_uuid_cache->blogs->{ $param_blog_id };
				$param_blog_id = \str_replace( 'blog/', '', $param_blog_id );
				$param_blog_id = \championcore\get_configs()->dir_content . '/blog/' . \championcore\filter\blog_item_id( $param_blog_id );
				
				$blog_roll = new \championcore\store\blog\Roll( $param_blog_id );
				$items = $blog_roll->items( '1', $blog_roll->size() );
				
				$tags = [];
				
				foreach ($items as $value) {
					$tags = array_merge( $tags, $value->tags);
				}
				
				$tags = \array_unique( $tags );
				
				# pack output
				$encoder = new \PhpXmlRpc\Encoder();
				
				$payload = [];
				
				foreach ($tags as $value) {
					
					$payload[] = array(
						'categoryId'          => $value,
						'parentId'            => '',
						'categoryName'        => $value,
						'categoryDescription' => $value,
						'description'         => $value
					);
				}
				
				$packed = $encoder->encode( $payload );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * get a blog post
	 * \param $request PhpXmlRpc\Request with the $blog_id, $username, $password
	 * \return object xmlrpcStruct
	 */
	public static function rpc_get_post (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$post_id  = $request->params[0]->scalarval();
		$username = $request->params[1]->scalarval();
		$password = $request->params[2]->scalarval();
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog item' );
		
		if (true or ($username == 'administrator') or ($username == 'editor')) {
			
			if (
				true
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password)
				) {
				
				$param_post_id = \championcore\filter\uuid( $post_id );
				$param_post_id = $blog_item_uuid_cache->items->{ $param_post_id };
				$param_post_id = \str_replace( 'blog/', '', $param_post_id );
				$param_post_id = \championcore\get_configs()->dir_content . '/blog/' . \championcore\filter\blog_item_id( $param_post_id ) . '.txt';
				
				$blog_item = new \championcore\store\blog\Item();
				$blog_item->load( $param_post_id );
				
				$encoder = new \PhpXmlRpc\Encoder();
				
				$packed = $encoder->encode(
					array(
						'dateCreated' => $blog_item->date,
						'description' => $blog_item->html,
						'title'       => $blog_item->title,
						'postid'      => $param_post_id, #\str_replace('/blog/', '', $blog_item->location),
						
						'categories' => $blog_item->tags
					)
				);
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * edit a blog post
	 * \param $request PhpXmlRpc\Request with the  $blog_id, $username, $password, $number_of_posts
	 * \return string
	 */
	public static function rpc_get_recent_posts (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$blog_id         = $request->params[0]->scalarval();
		$username        = $request->params[1]->scalarval();
		$password        = $request->params[2]->scalarval();
		$number_of_posts = $request->params[3]->scalarval();
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog' );
		
		if (true or ($username == 'administrator') or ($username == 'editor')) {
			
			if (
				true
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password)
				) {
				
				$param_blog_id = \championcore\filter\uuid( $blog_id );
				$param_blog_id = $blog_item_uuid_cache->blogs->{ $param_blog_id };
				$param_blog_id = \str_replace( 'blog/', '', $param_blog_id );
				$param_blog_id = \championcore\get_configs()->dir_content . '/blog/' . \championcore\filter\blog_item_id( $param_blog_id );
				
				$blog = new \championcore\store\blog\Roll( $param_blog_id );
				
				$blog_size = $blog->size();
				
				$items = $blog->items( '1', $blog_size );
				
				$items = \array_reverse( $items );
				$items = \array_slice( $items, 0, \intval($number_of_posts) );
				
				$packed = [];
				
				foreach ($items as $value) {
					$packed[] = array(
						'dateCreated' => $value->date,
						'description' => $value->html,
						'title'       => $value->title,
						'postid'      => $param_blog_id, #\str_replace('/blog/', '', $value->location),
						
						'categories' => $value->tags
					);
				}
				
				$encoder = new \PhpXmlRpc\Encoder();
				
				$packed = $encoder->encode( $packed );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * get user blogs
	 * \param $request PhpXmlRpc\Request
	 * \return string
	 */
	public static function rpc_get_users_blogs (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$api_key         = $request->params[0]->scalarval();
		$username        = $request->params[1]->scalarval();
		$password        = $request->params[2]->scalarval();
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog' );
		
		if (true or ($username == 'administrator') or ($username == 'editor')) {
			
			if (
				true
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password)
				) {
				
				$isAdmin = \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password);
				
				$url_domain   = $_SERVER['HTTP_HOST'];
				$url_path     = \championcore\wedge\config\get_json_configs()->json->path;
				$url_protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
				
				$base_url = "{$url_protocol}://{$url_domain}{$url_path}";
				
				$blog_roll = new \championcore\store\blog\Roll( \championcore\get_configs()->dir_content . '/blog' );
				
				$packed = [];
				
				$packed[] = array(
					'isAdmin'  => $isAdmin,
					'url'      => "{$base_url}/blog",
					'blogid'   => \array_search( "/blog/", ((array)$blog_item_uuid_cache->blogs)), #'/blog/',
					'blogName' => 'blog',
					'xmlrpc'   => "{$base_url}/web-blog-api"
				);
				
				$sub_blogs = $blog_roll->sub_rolls();
				
				foreach ($sub_blogs as $value) {
					$packed[] = array(
						'isAdmin'  => $isAdmin,
						'url'      => "{$base_url}/blog/" . $value->get_name(),
						'blogid'   => \array_search( ('/blog/' . $value->get_name()), ((array)$blog_item_uuid_cache->blogs)), #'/blog/' . $value->get_name(),
						'blogName' => $value->get_name(),
						'xmlrpc'   => "{$base_url}/web-blog-api",
					);
				}
				
				error_log( print_r($packed, true) );########################################
				
				$encoder = new \PhpXmlRpc\Encoder();
				
				$packed = $encoder->encode( $packed );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * add a new blog post
	 * \param $request PhpXmlRpc\Request with the  $blog_id, $username, $password, $payload object
	 * \return string
	 */
	public static function rpc_new_post (\PhpXmlRpc\Request $request) {
		
		# load the UUID cache
		$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild();
		
		# unpack the params
		$blog_id  = $request->params[0]->scalarval();
		$username = $request->params[1]->scalarval();
		$password = $request->params[2]->scalarval();
		$payload  = $request->params[3];
		$publish  = $request->params[4]->scalarval(); # ignore
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog item' );
		
		if (($username == 'administrator') or ($username == 'editor')) {
			
			if (     \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				  or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password) ) {
				
				$param_blog_id = \championcore\filter\uuid( $blog_id );
				$param_blog_id = $blog_item_uuid_cache->blogs->{ $param_blog_id };
				$param_blog_id = \str_replace( 'blog/', '', $param_blog_id );
				$param_blog_id = \championcore\filter\blog_item_id( $param_blog_id );
				$param_blog_id = ($param_blog_id > 0) ? "{$param_blog_id}/" : '';
				
				$post_id = \championcore\store\blog\Roll::generate_clean_item_name();
				
				$param_blog_id = \championcore\get_configs()->dir_content . "/blog/{$param_blog_id}{$post_id}.txt";
				
				$blog_item = new \championcore\store\blog\Item();
				
				# unpack
				$categories = [];
				foreach ($payload['categories'] as $value) {
					$categories[] = \trim( $value->scalarval() );
				}
				$categories = \implode(',', $categories );
				
				$description = $payload['description']->scalarval();
				$title       = $payload['title'      ]->scalarval();
				
				$description = \trim( $description );
				$title       = \trim( $title );
				
				# save
				$blog_item->html  = $description;
				$blog_item->title = $title;
				
				$blog_item->tags  = $categories;
				
				$blog_item->save( $param_blog_id );
				
				# reload the UUID cache
				$blog_item_uuid_cache = \championcore\page\WebBlogApi::blog_item_uuid_cache_rebuild( true );
				
				$probe = "{$blog_id}{$post_id}";
				$probe = \ltrim($probe, '/');
				
				$uuid = \array_search( $probe, ((array)$blog_item_uuid_cache->items));
				
				# generate output
				$encoder = new \PhpXmlRpc\Encoder();
				
				$packed = $encoder->encode( $uuid );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
	
	/**
	 * edit a blog post
	 * \param $request PhpXmlRpc\Request with the  $blog_id, $username, $password, $payload object
	 * \return string
	 */
	public static function rpc_new_media_object (\PhpXmlRpc\Request $request) {
		
		# unpack the params
		$blog_id  = $request->params[0]->scalarval();
		$username = $request->params[1]->scalarval();
		$password = $request->params[2]->scalarval();
		$payload  = $request->params[3];
		
		$result = new \PhpXmlRpc\Response( 0, \PhpXmlRpc\PhpXmlRpc::$xmlrpcerruser, 'Unknown blog item' );
		
		if (($username == 'administrator') or ($username == 'editor')) {
			
			if (     \password_verify($password, \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				  or \password_verify($password, \championcore\wedge\config\get_json_configs()->json->password) ) {
				
				$upload_name = $payload['name']->scalarval();
				$upload_type = $payload['type']->scalarval();
				$upload_bits = $payload['bits']->scalarval(); # NB base64 decoded already
				
				$upload_name = \championcore\filter\file_name( $upload_name );
				$upload_type = \championcore\filter\file_name( $upload_type );
				
				$destination = \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'gallery1' . DIRECTORY_SEPARATOR . $upload_name;
				
				$status = \file_put_contents( $destination, $upload_bits );
				
				$packed = array(
					'url' => ('http://' . \championcore\get_configs()->domain . \championcore\wedge\config\get_json_configs()->json->path . "/content/media/gallery1/{$upload_name}")
				);
				
				$encoder = new \PhpXmlRpc\Encoder();
				$packed = $encoder->encode( $packed );
				
				$result = new \PhpXmlRpc\Response( $packed );
			}
		}
		
		return $result;
	}
}
