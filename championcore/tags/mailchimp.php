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

$mc_user = $GLOBALS['tag_var1'];
$mc_id   = $GLOBALS['tag_var2'];
$mc_id2   = $GLOBALS['tag_var3'];
?>

<!--BEGIN mc_embed_signup-->
<link href="//cdn-images.mailchimp.com/embedcode/slim-10_7.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup">
<form action="https://<?php echo $mc_user; ?>.us20.list-manage.com/subscribe/post?u=<?php echo $mc_id; ?>&amp;id=<?php echo $mc_id2; ?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<div id="mc_embed_signup_scroll">
		<label for="mce-EMAIL"><?php echo $GLOBALS['lang_mailchimp_subscribe_label']; ?></label>
		<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="<?php echo $GLOBALS['lang_mailchimp_email_address']?>" required />
		<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		<div style="position: absolute; left: -5000px;" aria-hidden="true">
			<input type="text" name="<?php echo $mc_id; ?>" tabindex="-1" value="" />
		</div>
		<div class="clear">
			<input type="submit" value="<?php echo $GLOBALS['lang_mailchimp_subscribe']; ?>" name="subscribe" id="mc-embedded-subscribe" class="button" />
		</div>
	</div>
</form>
</div><!--End mc_embed_signup-->