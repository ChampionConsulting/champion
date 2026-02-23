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

namespace championcore\logic;

/**
 * country related logic
 */
class Country extends Base {

	/*
	 * ISO code to county name
	 * @param string $code 
	 * @return string NB empty if there is no match
	 */
	public static function lookup (string $code) : string {
		
		$result = '';
		
		$probe = \strtoupper( $code );
		
		$tmp = self::ISO_COUNTRY_CODES;
		
		if (isset($tmp[$probe])) {
			
			$result = $tmp[$probe];
		}
		
		return $result;
	}
	
	/*
	 * parse a given IP via http://freegeoip.net/
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['ip']) );
		\championcore\pre_condition( \is_string($arguments['ip']) );
		\championcore\pre_condition(    \strlen($arguments['ip']) > 0);
		
		# logic
		$logic_geoip_ipapi   = new GeoIpIpApi();
		$logic_geoip_ipstack = new GeoIpIpStack();
		
		# process
		$ip = \trim($arguments['ip']);
		
		switch (\championcore\wedge\config\get_json_configs()->json->geoip->service) {
			
			case 'freegeoip': # NB fallthrough
			case 'ipstack':
				$result = $logic_geoip_ipstack->process( array('ip' => $ip) );
				break;
				
			case 'ipapi':
				$result = $logic_geoip_ipapi->process( array('ip' => $ip) );
				break;
				
			default:
				\championcore\invariant( false, 'Unknown GeoIp service' );
		}
		
		return $result;
	}
	
	/*
	 * country codes
	 */
	const ISO_COUNTRY_CODES = array(
		"BD" => "Bangladesh",
		"BE" => "Belgium",
		"BF" => "Burkina Faso",
		"BG" => "Bulgaria",
		"BA" => "Bosnia and Herzegovina",
		"BB" => "Barbados",
		"WF" => "Wallis and Futuna",
		"BL" => "Saint Barthelemy",
		"BM" => "Bermuda",
		"BN" => "Brunei",
		"BO" => "Bolivia",
		"BH" => "Bahrain",
		"BI" => "Burundi",
		"BJ" => "Benin",
		"BT" => "Bhutan",
		"JM" => "Jamaica",
		"BV" => "Bouvet Island",
		"BW" => "Botswana",
		"WS" => "Samoa",
		"BQ" => "Bonaire, Saint Eustatius and Saba",
		"BR" => "Brazil",
		"BS" => "Bahamas",
		"JE" => "Jersey",
		"BY" => "Belarus",
		"BZ" => "Belize",
		"RU" => "Russia",
		"RW" => "Rwanda",
		"RS" => "Serbia",
		"TL" => "East Timor",
		"RE" => "Reunion",
		"TM" => "Turkmenistan",
		"TJ" => "Tajikistan",
		"RO" => "Romania",
		"TK" => "Tokelau",
		"GW" => "Guinea-Bissau",
		"GU" => "Guam",
		"GT" => "Guatemala",
		"GS" => "South Georgia and the South Sandwich Islands",
		"GR" => "Greece",
		"GQ" => "Equatorial Guinea",
		"GP" => "Guadeloupe",
		"JP" => "Japan",
		"GY" => "Guyana",
		"GG" => "Guernsey",
		"GF" => "French Guiana",
		"GE" => "Georgia",
		"GD" => "Grenada",
		"GB" => "United Kingdom",
		"GA" => "Gabon",
		"SV" => "El Salvador",
		"GN" => "Guinea",
		"GM" => "Gambia",
		"GL" => "Greenland",
		"GI" => "Gibraltar",
		"GH" => "Ghana",
		"OM" => "Oman",
		"TN" => "Tunisia",
		"JO" => "Jordan",
		"HR" => "Croatia",
		"HT" => "Haiti",
		"HU" => "Hungary",
		"HK" => "Hong Kong",
		"HN" => "Honduras",
		"HM" => "Heard Island and McDonald Islands",
		"VE" => "Venezuela",
		"PR" => "Puerto Rico",
		"PS" => "Palestinian Territory",
		"PW" => "Palau",
		"PT" => "Portugal",
		"SJ" => "Svalbard and Jan Mayen",
		"PY" => "Paraguay",
		"IQ" => "Iraq",
		"PA" => "Panama",
		"PF" => "French Polynesia",
		"PG" => "Papua New Guinea",
		"PE" => "Peru",
		"PK" => "Pakistan",
		"PH" => "Philippines",
		"PN" => "Pitcairn",
		"PL" => "Poland",
		"PM" => "Saint Pierre and Miquelon",
		"ZM" => "Zambia",
		"EH" => "Western Sahara",
		"EE" => "Estonia",
		"EG" => "Egypt",
		"ZA" => "South Africa",
		"EC" => "Ecuador",
		"IT" => "Italy",
		"VN" => "Vietnam",
		"SB" => "Solomon Islands",
		"ET" => "Ethiopia",
		"SO" => "Somalia",
		"ZW" => "Zimbabwe",
		"SA" => "Saudi Arabia",
		"ES" => "Spain",
		"ER" => "Eritrea",
		"ME" => "Montenegro",
		"MD" => "Moldova",
		"MG" => "Madagascar",
		"MF" => "Saint Martin",
		"MA" => "Morocco",
		"MC" => "Monaco",
		"UZ" => "Uzbekistan",
		"MM" => "Myanmar",
		"ML" => "Mali",
		"MO" => "Macao",
		"MN" => "Mongolia",
		"MH" => "Marshall Islands",
		"MK" => "Macedonia",
		"MU" => "Mauritius",
		"MT" => "Malta",
		"MW" => "Malawi",
		"MV" => "Maldives",
		"MQ" => "Martinique",
		"MP" => "Northern Mariana Islands",
		"MS" => "Montserrat",
		"MR" => "Mauritania",
		"IM" => "Isle of Man",
		"UG" => "Uganda",
		"TZ" => "Tanzania",
		"MY" => "Malaysia",
		"MX" => "Mexico",
		"IL" => "Israel",
		"FR" => "France",
		"IO" => "British Indian Ocean Territory",
		"SH" => "Saint Helena",
		"FI" => "Finland",
		"FJ" => "Fiji",
		"FK" => "Falkland Islands",
		"FM" => "Micronesia",
		"FO" => "Faroe Islands",
		"NI" => "Nicaragua",
		"NL" => "Netherlands",
		"NO" => "Norway",
		"NA" => "Namibia",
		"VU" => "Vanuatu",
		"NC" => "New Caledonia",
		"NE" => "Niger",
		"NF" => "Norfolk Island",
		"NG" => "Nigeria",
		"NZ" => "New Zealand",
		"NP" => "Nepal",
		"NR" => "Nauru",
		"NU" => "Niue",
		"CK" => "Cook Islands",
		"XK" => "Kosovo",
		"CI" => "Ivory Coast",
		"CH" => "Switzerland",
		"CO" => "Colombia",
		"CN" => "China",
		"CM" => "Cameroon",
		"CL" => "Chile",
		"CC" => "Cocos Islands",
		"CA" => "Canada",
		"CG" => "Republic of the Congo",
		"CF" => "Central African Republic",
		"CD" => "Democratic Republic of the Congo",
		"CZ" => "Czech Republic",
		"CY" => "Cyprus",
		"CX" => "Christmas Island",
		"CR" => "Costa Rica",
		"CW" => "Curacao",
		"CV" => "Cape Verde",
		"CU" => "Cuba",
		"SZ" => "Swaziland",
		"SY" => "Syria",
		"SX" => "Sint Maarten",
		"KG" => "Kyrgyzstan",
		"KE" => "Kenya",
		"SS" => "South Sudan",
		"SR" => "Suriname",
		"KI" => "Kiribati",
		"KH" => "Cambodia",
		"KN" => "Saint Kitts and Nevis",
		"KM" => "Comoros",
		"ST" => "Sao Tome and Principe",
		"SK" => "Slovakia",
		"KR" => "South Korea",
		"SI" => "Slovenia",
		"KP" => "North Korea",
		"KW" => "Kuwait",
		"SN" => "Senegal",
		"SM" => "San Marino",
		"SL" => "Sierra Leone",
		"SC" => "Seychelles",
		"KZ" => "Kazakhstan",
		"KY" => "Cayman Islands",
		"SG" => "Singapore",
		"SE" => "Sweden",
		"SD" => "Sudan",
		"DO" => "Dominican Republic",
		"DM" => "Dominica",
		"DJ" => "Djibouti",
		"DK" => "Denmark",
		"VG" => "British Virgin Islands",
		"DE" => "Germany",
		"YE" => "Yemen",
		"DZ" => "Algeria",
		"US" => "United States",
		"UY" => "Uruguay",
		"YT" => "Mayotte",
		"UM" => "United States Minor Outlying Islands",
		"LB" => "Lebanon",
		"LC" => "Saint Lucia",
		"LA" => "Laos",
		"TV" => "Tuvalu",
		"TW" => "Taiwan",
		"TT" => "Trinidad and Tobago",
		"TR" => "Turkey",
		"LK" => "Sri Lanka",
		"LI" => "Liechtenstein",
		"LV" => "Latvia",
		"TO" => "Tonga",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"LR" => "Liberia",
		"LS" => "Lesotho",
		"TH" => "Thailand",
		"TF" => "French Southern Territories",
		"TG" => "Togo",
		"TD" => "Chad",
		"TC" => "Turks and Caicos Islands",
		"LY" => "Libya",
		"VA" => "Vatican",
		"VC" => "Saint Vincent and the Grenadines",
		"AE" => "United Arab Emirates",
		"AD" => "Andorra",
		"AG" => "Antigua and Barbuda",
		"AF" => "Afghanistan",
		"AI" => "Anguilla",
		"VI" => "U.S. Virgin Islands",
		"IS" => "Iceland",
		"IR" => "Iran",
		"AM" => "Armenia",
		"AL" => "Albania",
		"AO" => "Angola",
		"AQ" => "Antarctica",
		"AS" => "American Samoa",
		"AR" => "Argentina",
		"AU" => "Australia",
		"AT" => "Austria",
		"AW" => "Aruba",
		"IN" => "India",
		"AX" => "Aland Islands",
		"AZ" => "Azerbaijan",
		"IE" => "Ireland",
		"ID" => "Indonesia",
		"UA" => "Ukraine",
		"QA" => "Qatar",
		"MZ" => "Mozambique"
	);
	
}
