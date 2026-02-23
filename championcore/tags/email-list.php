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


# these are set in the root index.php file so should'nt be needed here
#\error_reporting(\E_STRICT|\E_ALL);
#\session_start();

// edit the language variables for the form here

$thanks      = $GLOBALS['lang_newsletter_thanks'];
$try_again   = $GLOBALS['lang_newsletter_try_again'];
$placeholder = $GLOBALS['lang_newsletter_placeholder'];
$button_send = $GLOBALS['lang_newsletter_send'];

$no_good = false;
$success = false;

if (isset($_POST['submit'])
	&& ($_POST["send_token"] == $_SESSION["send_token"])) {

	if (filter_var($_POST['email'], \FILTER_VALIDATE_EMAIL)
	&& substr_count($_POST['email'],'@') == 1
	&& substr_count($_POST['email'],',') == 0 ) {
		
		$file_han = fopen(\championcore\get_configs()->dir_content . "/blocks/sb_email_list.txt","a");
		fwrite($file_han,$_POST['email']."\n");
		fclose($file_han);
		
		$success = true;
		unset($_SESSION["send_token"]);
		
	} else {
		$no_good = true;
	}
}

if ($success == true) { echo $thanks; }

if ($success == false || $no_good == true ) {
	
	if ($no_good == true) { $no_good = $try_again; }
	
	$_SESSION["send_token"] = md5(uniqid(rand(), TRUE));
	
?>

<form id="contact" class="championcore tag email-list" method="post" action="">
	<input id="email" name="email" placeholder="<?php echo $placeholder; ?>" type="email" value="<?php echo $no_good; ?>" >
	<input type="hidden" name="send_token" value="<?php echo $_SESSION["send_token"]; ?>">
	<button name="submit" type="submit"><?php echo $button_send; ?></button>
</form>

<?php } ?>
