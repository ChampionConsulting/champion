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

namespace championcore;

/**
 * the classes and filenames to autoload from
 */
return [
	
	# cache
	'championcore\cache\Manager' => CHAMPION_BASE_DIR . '/championcore/src/cache/manager.php',
	'championcore\cache\Meta'    => CHAMPION_BASE_DIR . '/championcore/src/cache/meta.php',
	'championcore\cache\Pool'    => CHAMPION_BASE_DIR . '/championcore/src/cache/pool.php',

	# export HTML
	'championcore\export_html\Page'   => CHAMPION_BASE_DIR . '/championcore/src/export_html/page.php',
	'championcore\export_html\Spider' => CHAMPION_BASE_DIR . '/championcore/src/export_html/spider.php',

	# import HTML
	'championcore\import_html\Page' => CHAMPION_BASE_DIR . '/championcore/src/import_html/page.php',

	'championcore\import_html\parsed\Block' => CHAMPION_BASE_DIR . '/championcore/src/import_html/parsed/block.php',
	
	# install
	'championcore\installer\Base'              => CHAMPION_BASE_DIR . '/championcore/src/installer/base.php',
	'championcore\installer\BaseCollection'    => CHAMPION_BASE_DIR . '/championcore/src/installer/base_collection.php',
	'championcore\installer\FileMetaData'      => CHAMPION_BASE_DIR . '/championcore/src/installer/file_meta_data.php',
	'championcore\installer\DirectoryMetaData' => CHAMPION_BASE_DIR . '/championcore/src/installer/directory_meta_data.php',
	'championcore\installer\MetaData'          => CHAMPION_BASE_DIR . '/championcore/src/installer/meta_data.php',
	'championcore\installer\Update'            => CHAMPION_BASE_DIR . '/championcore/src/installer/update.php',
	
	'championcore\installer\change\Base'       => CHAMPION_BASE_DIR . '/championcore/src/installer/change/base.php',
	'championcore\installer\change\ConfigJson' => CHAMPION_BASE_DIR . '/championcore/src/installer/change/config_json.php',
	'championcore\installer\change\ConfigPhp'  => CHAMPION_BASE_DIR . '/championcore/src/installer/change/config_php.php',
	'championcore\installer\change\Htaccess'   => CHAMPION_BASE_DIR . '/championcore/src/installer/change/htaccess.php',
	'championcore\installer\change\File'       => CHAMPION_BASE_DIR . '/championcore/src/installer/change/file.php',
	
	# logic
	'championcore\logic\Base'           => CHAMPION_BASE_DIR . '/championcore/src/logic/base.php',
	'championcore\logic\Country'        => CHAMPION_BASE_DIR . '/championcore/src/logic/country.php',
	'championcore\logic\FindImage'      => CHAMPION_BASE_DIR . '/championcore/src/logic/find_image.php',
	'championcore\logic\FeaturedImage'  => CHAMPION_BASE_DIR . '/championcore/src/logic/featured_image.php',
	'championcore\logic\GeoIpFreeGeoIp' => CHAMPION_BASE_DIR . '/championcore/src/logic/geoip_freegeoip.php',
	'championcore\logic\GeoIpIpApi'     => CHAMPION_BASE_DIR . '/championcore/src/logic/geoip_ipapi.php',
	'championcore\logic\GeoIpIpStack'   => CHAMPION_BASE_DIR . '/championcore/src/logic/geoip_ipstack.php',
	'championcore\logic\Search'         => CHAMPION_BASE_DIR . '/championcore/src/logic/search.php',
	'championcore\logic\SearchBase'     => CHAMPION_BASE_DIR . '/championcore/src/logic/search_base.php',
	'championcore\logic\SearchBlock'    => CHAMPION_BASE_DIR . '/championcore/src/logic/search_block.php',
	'championcore\logic\SearchBlog'     => CHAMPION_BASE_DIR . '/championcore/src/logic/search_blog.php',
	'championcore\logic\SearchGallery'  => CHAMPION_BASE_DIR . '/championcore/src/logic/search_gallery.php',
	'championcore\logic\SearchPage'     => CHAMPION_BASE_DIR . '/championcore/src/logic/search_page.php',
	'championcore\logic\UserAgent'      => CHAMPION_BASE_DIR . '/championcore/src/logic/user_agent.php',
	
	# champion page handlers - admin
	'championcore\page\admin\Avatar'                   => CHAMPION_BASE_DIR . '/championcore/page/admin/avatar.php',
	'championcore\page\admin\Base'                     => CHAMPION_BASE_DIR . '/championcore/page/admin/base.php',
	'championcore\page\admin\BlogImportFromRss'        => CHAMPION_BASE_DIR . '/championcore/page/admin/blog_import_from_rss.php',
	'championcore\page\admin\CreateFolder'             => CHAMPION_BASE_DIR . '/championcore/page/admin/create_folder.php',
	'championcore\page\admin\CustomPostType'           => CHAMPION_BASE_DIR . '/championcore/page/admin/custom_post_type.php',
	'championcore\page\admin\CustomPostTypeDefinition' => CHAMPION_BASE_DIR . '/championcore/page/admin/custom_post_type_definition.php',
	'championcore\page\admin\Dashboard'                => CHAMPION_BASE_DIR . '/championcore/page/admin/dashboard.php',
	'championcore\page\admin\DebugInfo'                => CHAMPION_BASE_DIR . '/championcore/page/admin/debug_info.php',
	'championcore\page\admin\Delete'                   => CHAMPION_BASE_DIR . '/championcore/page/admin/delete.php',
	'championcore\page\admin\DuplicateContent'         => CHAMPION_BASE_DIR . '/championcore/page/admin/duplicate_content.php',
	'championcore\page\admin\ExportHtmlWebsite'        => CHAMPION_BASE_DIR . '/championcore/page/admin/export_html_website.php',
	'championcore\page\admin\GalSort'                  => CHAMPION_BASE_DIR . '/championcore/page/admin/gal_sort.php',
	'championcore\page\admin\GalleryOrder'             => CHAMPION_BASE_DIR . '/championcore/page/admin/gallery_order.php',
	'championcore\page\admin\ImportHtmlPage'           => CHAMPION_BASE_DIR . '/championcore/page/admin/import_html_page.php',
	'championcore\page\admin\LogViewer'                => CHAMPION_BASE_DIR . '/championcore/page/admin/log_viewer.php',
	'championcore\page\admin\ManageNavigation'         => CHAMPION_BASE_DIR . '/championcore/page/admin/manage_navigation.php',
	'championcore\page\admin\ManageTags'               => CHAMPION_BASE_DIR . '/championcore/page/admin/manage_tags.php',
	'championcore\page\admin\ManageUserList'           => CHAMPION_BASE_DIR . '/championcore/page/admin/manage_user_list.php',
	'championcore\page\admin\ManageUserGroupList'      => CHAMPION_BASE_DIR . '/championcore/page/admin/manage_user_group_list.php',
	'championcore\page\admin\MediaUploadHandler'       => CHAMPION_BASE_DIR . '/championcore/page/admin/media_upload_handler.php',
	'championcore\page\admin\PluginUploadHandler'      => CHAMPION_BASE_DIR . '/championcore/page/admin/plugin_upload_handler.php',
	'championcore\page\admin\PluginsTags'              => CHAMPION_BASE_DIR . '/championcore/page/admin/plugins_tags.php',
	'championcore\page\admin\Rest'                     => CHAMPION_BASE_DIR . '/championcore/page/admin/rest.php',
	'championcore\page\admin\TemplateUploadHandler'    => CHAMPION_BASE_DIR . '/championcore/page/admin/template_upload_handler.php',
	'championcore\page\admin\Update'                   => CHAMPION_BASE_DIR . '/championcore/page/admin/update.php',
	'championcore\page\admin\ViewStats'                => CHAMPION_BASE_DIR . '/championcore/page/admin/view_stats.php',
	'championcore\page\admin\Unishop'                  => CHAMPION_BASE_DIR . '/championcore/page/admin/unishop.php',
	'championcore\page\admin\UnishopEditor'            => CHAMPION_BASE_DIR . '/championcore/page/admin/unishop_editor.php',
	
	'championcore\page\admin\create\Base'       => CHAMPION_BASE_DIR . '/championcore/page/admin/create/base.php',
	'championcore\page\admin\create\BlogFolder' => CHAMPION_BASE_DIR . '/championcore/page/admin/create/blog_folder.php',
	'championcore\page\admin\create\BlogItem'   => CHAMPION_BASE_DIR . '/championcore/page/admin/create/blog_item.php',
	
	'championcore\page\admin\open\AudioVideo' => CHAMPION_BASE_DIR . '/championcore/page/admin/open/audio_video.php',
	'championcore\page\admin\open\Base'       => CHAMPION_BASE_DIR . '/championcore/page/admin/open/base.php',
	'championcore\page\admin\open\Block'      => CHAMPION_BASE_DIR . '/championcore/page/admin/open/block.php',
	'championcore\page\admin\open\Blog'       => CHAMPION_BASE_DIR . '/championcore/page/admin/open/blog.php',
	'championcore\page\admin\open\Image'      => CHAMPION_BASE_DIR . '/championcore/page/admin/open/image.php',
	'championcore\page\admin\open\Page'       => CHAMPION_BASE_DIR . '/championcore/page/admin/open/page.php',
	
	'championcore\page\admin\openai\Base'            => CHAMPION_BASE_DIR . '/championcore/page/admin/openai/base.php',
	'championcore\page\admin\openai\ImageGeneration' => CHAMPION_BASE_DIR . '/championcore/page/admin/openai/image_generation.php',

	'championcore\page\admin\stable_diffusion\Base'            => CHAMPION_BASE_DIR . '/championcore/page/admin/stable-diffusion/base.php',
	'championcore\page\admin\stable_diffusion\ImageGeneration' => CHAMPION_BASE_DIR . '/championcore/page/admin/stable-diffusion/image_generation.php',
	
	'championcore\page\admin\update\Base'     => CHAMPION_BASE_DIR . '/championcore/page/admin/update/base.php',
	'championcore\page\admin\update\Done'     => CHAMPION_BASE_DIR . '/championcore/page/admin/update/done.php',
	'championcore\page\admin\update\Download' => CHAMPION_BASE_DIR . '/championcore/page/admin/update/download.php',
	'championcore\page\admin\update\Prepare'  => CHAMPION_BASE_DIR . '/championcore/page/admin/update/prepare.php',
	'championcore\page\admin\update\Results'  => CHAMPION_BASE_DIR . '/championcore/page/admin/update/results.php',
	'championcore\page\admin\update\Start'    => CHAMPION_BASE_DIR . '/championcore/page/admin/update/start.php',
	'championcore\page\admin\update\Status'   => CHAMPION_BASE_DIR . '/championcore/page/admin/update/status.php',
	'championcore\page\admin\update\Updating' => CHAMPION_BASE_DIR . '/championcore/page/admin/update/updating.php',
	
	# champion page handlers
	'championcore\page\Base'                  => CHAMPION_BASE_DIR . '/championcore/page/base.php',
	'championcore\page\DropzoneUploadHandler' => CHAMPION_BASE_DIR . '/championcore/page/dropzone_upload_handler.php',
	'championcore\page\WebBlogApi'            => CHAMPION_BASE_DIR . '/championcore/page/web_blog_api.php',
	'championcore\page\rss\Blog'              => CHAMPION_BASE_DIR . '/championcore/page/rss_blog.php',
	
	# routes for URL dispatching
	'championcore\route\Base'     => CHAMPION_BASE_DIR . '/championcore/src/route/base.php',
	'championcore\route\BlogItem' => CHAMPION_BASE_DIR . '/championcore/src/route/blog_item.php',
	
	'championcore\route\admin\Base'               => CHAMPION_BASE_DIR . '/championcore/src/route/admin/base.php',
	'championcore\route\admin\ExportHtmlWebsite'  => CHAMPION_BASE_DIR . '/championcore/src/route/admin/export_html_website.php',
	'championcore\route\admin\ImportHtmlPage'     => CHAMPION_BASE_DIR . '/championcore/src/route/admin/import_html_page.php',
	'championcore\route\admin\MediaUploadHandler' => CHAMPION_BASE_DIR . '/championcore/src/route/admin/media_upload_handler.php',

	'championcore\route\admin\openai\Base'            => CHAMPION_BASE_DIR . '/championcore/src/route/admin/openai/base.php',
	'championcore\route\admin\openai\ImageGeneration' => CHAMPION_BASE_DIR . '/championcore/src/route/admin/openai/image_generation.php',

	'championcore\route\admin\stable_diffusion\Base'            => CHAMPION_BASE_DIR . '/championcore/src/route/admin/stable-diffusion/base.php',
	'championcore\route\admin\stable_diffusion\ImageGeneration' => CHAMPION_BASE_DIR . '/championcore/src/route/admin/stable-diffusion/image_generation.php',
	
	'championcore\route\admin\update\Base'     => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/base.php',
	'championcore\route\admin\update\Done'     => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/done.php',
	'championcore\route\admin\update\Download' => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/download.php',
	'championcore\route\admin\update\Prepare'  => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/prepare.php',
	'championcore\route\admin\update\Results'  => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/results.php',
	'championcore\route\admin\update\Start'    => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/start.php',
	'championcore\route\admin\update\Status'   => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/status.php',
	'championcore\route\admin\update\Updating' => CHAMPION_BASE_DIR . '/championcore/src/route/admin/update/updating.php',
	
	'championcore\route\editor\Base'          => CHAMPION_BASE_DIR . '/championcore/src/route/editor/base.php',
	'championcore\route\editor\Rest'          => CHAMPION_BASE_DIR . '/championcore/src/route/editor/rest.php',
	'championcore\route\editor\UnishopEditor' => CHAMPION_BASE_DIR . '/championcore/src/route/editor/unishop_editor.php',
	
	# storage - base
	'championcore\store\Base'       => CHAMPION_BASE_DIR . '/championcore/src/store/base.php',
	'championcore\store\Item'       => CHAMPION_BASE_DIR . '/championcore/src/store/item.php',
	
	# storage - block
	'championcore\store\block\Base' => CHAMPION_BASE_DIR . '/championcore/src/store/block/base.php',
	'championcore\store\block\Item' => CHAMPION_BASE_DIR . '/championcore/src/store/block/item.php',
	'championcore\store\block\Pile' => CHAMPION_BASE_DIR . '/championcore/src/store/block/pile.php',
	
	# storage - blog
	'championcore\store\blog\Base'  => CHAMPION_BASE_DIR . '/championcore/src/store/blog/base.php',
	'championcore\store\blog\Item'  => CHAMPION_BASE_DIR . '/championcore/src/store/blog/item.php',
	'championcore\store\blog\Roll'  => CHAMPION_BASE_DIR . '/championcore/src/store/blog/roll.php',
	
	# storage - gallery
	'championcore\store\gallery\Base'  => CHAMPION_BASE_DIR . '/championcore/src/store/gallery/base.php',
	'championcore\store\gallery\Item'  => CHAMPION_BASE_DIR . '/championcore/src/store/gallery/item.php',
	'championcore\store\gallery\Image' => CHAMPION_BASE_DIR . '/championcore/src/store/gallery/image.php',
	'championcore\store\gallery\Pile'  => CHAMPION_BASE_DIR . '/championcore/src/store/gallery/pile.php',
	
	# storage - page
	'championcore\store\page\Base' => CHAMPION_BASE_DIR . '/championcore/src/store/page/base.php',
	'championcore\store\page\Item' => CHAMPION_BASE_DIR . '/championcore/src/store/page/item.php',
	'championcore\store\page\Pile' => CHAMPION_BASE_DIR . '/championcore/src/store/page/pile.php',
	
	# storage - stats
	'championcore\store\stat\Base' => CHAMPION_BASE_DIR . '/championcore/src/store/stat/base.php',
	'championcore\store\stat\Item' => CHAMPION_BASE_DIR . '/championcore/src/store/stat/item.php',
	'championcore\store\stat\Line' => CHAMPION_BASE_DIR . '/championcore/src/store/stat/line.php',
	
	# tag lexer
	'championcore\tag\Lexer' => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer.php',
	
	'championcore\tag\lexer\token\Base'            => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/base.php',
	'championcore\tag\lexer\token\Colon'           => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/colon.php',
	'championcore\tag\lexer\token\CurlyBraceClose' => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/curly_brace_close.php',
	'championcore\tag\lexer\token\CurlyBraceOpen'  => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/curly_brace_open.php',
	'championcore\tag\lexer\token\DoubleQuote'     => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/double_quote.php',
	'championcore\tag\lexer\token\Skip'            => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/skip.php',
	'championcore\tag\lexer\token\Slash'           => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/slash.php',
	'championcore\tag\lexer\token\TagAttribute'    => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/tag_attribute.php',
	'championcore\tag\lexer\token\TagName'         => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/tag_name.php',
	'championcore\tag\lexer\token\Whitespace'      => CHAMPION_BASE_DIR . '/championcore/src/tag/lexer/token/whitespace.php',
	
	# tag parser
	'championcore\tag\Parser' => CHAMPION_BASE_DIR . '/championcore/src/tag/parser.php',
	
	'championcore\tag\parser\token\Base'             => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/base.php',
	'championcore\tag\parser\token\ClosingTag'       => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/closing_tag.php',
	'championcore\tag\parser\token\CompositeTag'     => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/composite_tag.php',
	'championcore\tag\parser\token\Content'          => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/content.php',
	'championcore\tag\parser\token\OpeningTag'       => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/opening_tag.php',
	'championcore\tag\parser\token\Tag'              => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/tag.php',
	'championcore\tag\parser\token\TagAttributeList' => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/token/tag_attribute_list.php',
	
	'championcore\tag\parser\rule\Append'       => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/append.php',
	'championcore\tag\parser\rule\Base'         => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/base.php',
	'championcore\tag\parser\rule\CompositeTag' => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/composite_tag.php',
	'championcore\tag\parser\rule\Filter'       => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/filter.php',
	'championcore\tag\parser\rule\LookAhead'    => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/look_ahead.php',
	'championcore\tag\parser\rule\Merge'        => CHAMPION_BASE_DIR . '/championcore/src/tag/parser/rule/merge.php',
	
	# tags
	'championcore\tags\Base'                  => CHAMPION_BASE_DIR . '/championcore/tags/base.php',
	'championcore\tags\BasePage'              => CHAMPION_BASE_DIR . '/championcore/tags/base_page.php',
	'championcore\tags\Block'                 => CHAMPION_BASE_DIR . '/championcore/tags/block.php',
	'championcore\tags\BlockLoop'             => CHAMPION_BASE_DIR . '/championcore/tags/block_loop.php',
	'championcore\tags\Blog'                  => CHAMPION_BASE_DIR . '/championcore/tags/blog.php',
	'championcore\tags\BlogItemAuthor'        => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_author.php',
	'championcore\tags\BlogItemContent'       => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_content.php',
	'championcore\tags\BlogItemDate'          => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_date.php',
	'championcore\tags\BlogItemFeaturedImage' => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_featured_image.php',
	'championcore\tags\BlogItemTag'           => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_tag.php',
	'championcore\tags\BlogItemTitle'         => CHAMPION_BASE_DIR . '/championcore/tags/blog_item_title.php',
	'championcore\tags\BlogContentLoop'       => CHAMPION_BASE_DIR . '/championcore/tags/blog_content_loop.php',
	'championcore\tags\BlogList'              => CHAMPION_BASE_DIR . '/championcore/tags/blog_list.php',
	'championcore\tags\BlogTags'              => CHAMPION_BASE_DIR . '/championcore/tags/blog_tags.php',
	'championcore\tags\BodyTags'              => CHAMPION_BASE_DIR . '/championcore/tags/body_tags.php',
	'championcore\tags\Breadcrumb'            => CHAMPION_BASE_DIR . '/championcore/tags/breadcrumb.php',
	'championcore\tags\CookieConsent'         => CHAMPION_BASE_DIR . '/championcore/tags/cookie_consent.php',
	'championcore\tags\CustomPostType'        => CHAMPION_BASE_DIR . '/championcore/tags/custom_post_type.php',
	'championcore\tags\Domain'                => CHAMPION_BASE_DIR . '/championcore/tags/domain.php',
	'championcore\tags\Dropzone'              => CHAMPION_BASE_DIR . '/championcore/tags/dropzone.php',
	'championcore\tags\Gal'                   => CHAMPION_BASE_DIR . '/championcore/tags/gal.php',
	'championcore\tags\GalMasonry'            => CHAMPION_BASE_DIR . '/championcore/tags/gal_masonry.php',
	'championcore\tags\Gdpr'                  => CHAMPION_BASE_DIR . '/championcore/tags/gdpr.php',
	'championcore\tags\GoogleCalendar'        => CHAMPION_BASE_DIR . '/championcore/tags/google_calendar.php',
	'championcore\tags\Lang'                  => CHAMPION_BASE_DIR . '/championcore/tags/lang.php',
	'championcore\tags\Link'                  => CHAMPION_BASE_DIR . '/championcore/tags/link.php',
	'championcore\tags\MainContent'           => CHAMPION_BASE_DIR . '/championcore/tags/main_content.php',
	'championcore\tags\MediaPlayer'           => CHAMPION_BASE_DIR . '/championcore/tags/media_player.php',
	'championcore\tags\Navigation'            => CHAMPION_BASE_DIR . '/championcore/tags/navigation.php',
	'championcore\tags\NavigationLoggedIn'    => CHAMPION_BASE_DIR . '/championcore/tags/navigation_logged_in.php',
	'championcore\tags\Olark'                 => CHAMPION_BASE_DIR . '/championcore/tags/olark.php',
	'championcore\tags\PageDesc'              => CHAMPION_BASE_DIR . '/championcore/tags/page_desc.php',
	'championcore\tags\PageList'              => CHAMPION_BASE_DIR . '/championcore/tags/page_list.php',
	'championcore\tags\PageTitle'             => CHAMPION_BASE_DIR . '/championcore/tags/page_title.php',
	'championcore\tags\Path'                  => CHAMPION_BASE_DIR . '/championcore/tags/path.php',
	'championcore\tags\Picture'               => CHAMPION_BASE_DIR . '/championcore/tags/picture.php',
	'championcore\tags\RecentPosts'           => CHAMPION_BASE_DIR . '/championcore/tags/recentposts.php',
	'championcore\tags\RecentPostsVisual'     => CHAMPION_BASE_DIR . '/championcore/tags/recentposts_visual.php',
	'championcore\tags\SB_Block'              => CHAMPION_BASE_DIR . '/championcore/tags/sb_block.php',
	'championcore\tags\Search'                => CHAMPION_BASE_DIR . '/championcore/tags/search.php',
	'championcore\tags\ShowVar'               => CHAMPION_BASE_DIR . '/championcore/tags/show_var.php',
	'championcore\tags\Slide'                 => CHAMPION_BASE_DIR . '/championcore/tags/slide.php',
	'championcore\tags\SocialExposure'        => CHAMPION_BASE_DIR . '/championcore/tags/social_exposure.php',
	'championcore\tags\TeaserImage'           => CHAMPION_BASE_DIR . '/championcore/tags/teaser_image.php',
	'championcore\tags\TemplateFolder'        => CHAMPION_BASE_DIR . '/championcore/tags/template_folder.php',
	'championcore\tags\ThemeCss'              => CHAMPION_BASE_DIR . '/championcore/tags/theme_css.php',
	'championcore\tags\ThemeJs'               => CHAMPION_BASE_DIR . '/championcore/tags/theme_js.php',
	'championcore\tags\ThemeJsBody'           => CHAMPION_BASE_DIR . '/championcore/tags/theme_js_body.php',
	'championcore\tags\Thumbs'                => CHAMPION_BASE_DIR . '/championcore/tags/thumbs.php',
	'championcore\tags\TrackingCode'          => CHAMPION_BASE_DIR . '/championcore/tags/tracking_code.php',
	'championcore\tags\Unishop'               => CHAMPION_BASE_DIR . '/championcore/tags/unishop.php',
	'championcore\tags\WebsitePath'           => CHAMPION_BASE_DIR . '/championcore/tags/website_path.php',
	
	# view
	'championcore\View'      => CHAMPION_BASE_DIR . '/championcore/src/view.php',
	'championcore\ViewModel' => CHAMPION_BASE_DIR . '/championcore/src/view_model.php',
	
	# view helpers
	'championcore\view\helper\ActiveNav'         => CHAMPION_BASE_DIR . '/championcore/src/view/helper/active_nav.php',
	'championcore\view\helper\Base'              => CHAMPION_BASE_DIR . '/championcore/src/view/helper/base.php',
	'championcore\view\helper\BodyTag'           => CHAMPION_BASE_DIR . '/championcore/src/view/helper/body_tag.php',
	'championcore\view\helper\Css'               => CHAMPION_BASE_DIR . '/championcore/src/view/helper/css.php',
	'championcore\view\helper\Escape'            => CHAMPION_BASE_DIR . '/championcore/src/view/helper/escape.php',
	'championcore\view\helper\InlineEdit'        => CHAMPION_BASE_DIR . '/championcore/src/view/helper/inline_edit.php',
	'championcore\view\helper\Js'                => CHAMPION_BASE_DIR . '/championcore/src/view/helper/js.php',
	'championcore\view\helper\JsBody'            => CHAMPION_BASE_DIR . '/championcore/src/view/helper/js_body.php',
	'championcore\view\helper\JsModule'          => CHAMPION_BASE_DIR . '/championcore/src/view/helper/js_module.php',
	'championcore\view\helper\Language'          => CHAMPION_BASE_DIR . '/championcore/src/view/helper/language.php',
	'championcore\view\helper\LastModified'      => CHAMPION_BASE_DIR . '/championcore/src/view/helper/last_modified.php',
	'championcore\view\helper\MadeInChampion'       => CHAMPION_BASE_DIR . '/championcore/src/view/helper/made_in_champion.php',
	'championcore\view\helper\Meta'              => CHAMPION_BASE_DIR . '/championcore/src/view/helper/meta.php',
	'championcore\view\helper\NavigationSubMenu' => CHAMPION_BASE_DIR . '/championcore/src/view/helper/navigation_sub_menu.php',
	'championcore\view\helper\Pagination'        => CHAMPION_BASE_DIR . '/championcore/src/view/helper/pagination.php',
	'championcore\view\helper\Resource'          => CHAMPION_BASE_DIR . '/championcore/src/view/helper/resource.php',
	'championcore\view\helper\Translations'      => CHAMPION_BASE_DIR . '/championcore/src/view/helper/translations.php'
	
];
