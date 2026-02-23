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


// Checking for locale and storing in session
$locale = "";
// 1. url parameter
// if set, will overwrite stored locale in session
parse_str(parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY), $url_parameters);
if (isset($url_parameters["locale"]) ) {
	$locale = $url_parameters["locale"];
}
if ($locale && strlen($locale) > 1) {
    $locale = substr($locale, 0, 2);
}
// 2. session storage
// if no url parameter is given, check if there is already a stored locale in session
if (strlen($locale) == 0){
    if ($_SESSION["locale"] && strlen($_SESSION["locale"]) == 2){
        $locale = substr($_SESSION["locale"], 0, 2);
    }
}
// 3. browser
// if no locale was given in url parameter or session, take browser locale
if (strlen($locale) == 0){
    if ($_SERVER["HTTP_ACCEPT_LANGUAGE"]) {
        $locale = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
    }
}
// 4. fallback
// if no language was given by the browser, set language to en
if (strlen($locale) == 0) {
    $locale = "en";
}
$_SESSION["locale"] = $locale;

?>