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

$jf_id     = $GLOBALS['tag_var1'];
$jf_height = $GLOBALS['tag_var2'];
//$jf_theme  = $GLOBALS['tag_var3'];
?>

<!-- Just Forms: https://championforms.com -->
<div id="c<?php echo $jf_id; ?>"><!-- option -->
	Fill out my <a href="https://championforms.com/app/app/form?id=<?php echo $jf_id; ?>">online form</a>.<!-- option -->
</div>
<script type="text/javascript">
	(function(d, t) {
			var s = d.createElement(t), options = {
					'id': <?php echo $jf_id; ?>,<!-- option -->
					//'theme': <?php /*echo $jf_theme;*/ ?>,<!-- option -->
					'container': 'c<?php echo $jf_id; ?>',<!-- option -->
					'height': '<?php echo $jf_height; ?>px',<!-- option -->
					'form': '//championforms.com/app/app/embed'
			};
			s.type= 'text/javascript';
			s.src = 'https://championforms.com/app/static_files/js/form.widget.js';
			s.onload = s.onreadystatechange = function() {
					var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
					try { (new EasyForms()).initialize(options).display() } catch (e) { }
			};
			var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
	})(document, 'script');
</script>
<!-- End Just Forms -->
