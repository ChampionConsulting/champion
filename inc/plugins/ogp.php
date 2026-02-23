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

/**
 * Created by PhpStorm.
 * User: hishikawa
 * Date: 2016/10/17
 * Time: 21:43
 */
class Ogp
{
    protected static $tags = array();

    public static function add($property, $content)
    {
        static::$tags[$property] = $content;
    }

    public static function render()
    {
        global $page, $page_title, $page_desc, $get_id, $url_title, $blog_prefix, $ogp_default_image;
        
        foreach (static::$tags as $property => $content) {
            if ($property == 'image') {
            	$content = static::getBaseURL(). $content;
            }
            static::print_tag($property, $content);
        }

        if (!static::exists('url')) {
            if (!(empty($get_id)) && is_numeric($get_id) && $url_title && $blog_prefix) {
                $url = static::getBaseURL() . $blog_prefix . '-' . strtolower($url_title);
            } elseif ($page == 'home') {
                $url = static::getBaseURL();
            } else {
                $url = static::getBaseURL() . $page;
            }
            static::print_tag('url', $url);
        }

        if (!static::exists('type')) {
            static::print_tag('type', 'article');
        }

        if (!static::exists('title')) {
            static::print_tag('title', $page_title);
        }

        if (!static::exists('description')) {
            static::print_tag('description', $page_desc);
        }

        if (!static::exists('image') && is_string($ogp_default_image) && !empty($ogp_default_image)) {
        	
					if (($page == 'blog') and \is_numeric($get_id)) {
						
						# logic
						$logic_featured_image = new \championcore\logic\FeaturedImage();
						
						$detected_fi = $logic_featured_image->process( array('blog_id' => $get_id) );
						
						if ($detected_fi->url !== false) {
							static::print_tag('image', $detected_fi->url);
						} else {
							static::print_tag('image', static::getBaseURL() . $ogp_default_image);
						}
						
					} else {
						static::print_tag('image', static::getBaseURL() . $ogp_default_image);
					}
        }
    }

    private static function print_tag($property, $content)
    {
        $property = htmlspecialchars($property, ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        echo sprintf('<meta property="og:%s" content="%s" />' . PHP_EOL, $property, $content);
    }

    private static function exists($property)
    {
        return array_key_exists($property, static::$tags);
    }

    private static function isSecure()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] and strtolower($_SERVER['HTTPS']) == 'on') {
            $isSecure = true;
        }
        return $isSecure;
    }

    private static function getScheme()
    {
        return (static::isSecure()) ? 'https' : 'http';
    }

    private static function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    private static function getBaseURL()
    {
        global $ogp_base_url, $path;
        return (isset($ogp_base_url)) ? $ogp_base_url : static::getScheme() . '://' . static::getHost() . $path . '/';
    }

}

function ogp_render() {
    Ogp::render();
}