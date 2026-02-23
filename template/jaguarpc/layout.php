<!DOCTYPE html>
<html>
<head>	
	<title><?php echo $page_title; ?></title>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo $page_desc; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?php echo $path; ?>/template/jaguarpc/images/favicon.ico" />
	
	<!--<link rel="canonical" href="<?php echo $path; ?>/" />-->
	<!--
	<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/base.css">
	<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/master.css">	
	-->
		<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/base.css">
	<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/master.css">
	
	<!-- CSS files -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap-grid.min.css" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap-reboot.min.css" />
	<!-- link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" / -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700|Droid+Serif:700" />
	
	<link rel="stylesheet" href='http://fonts.googleapis.com/css?family=Roboto+Slab:300,400'>
	<link rel="stylesheet" href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300,400'>
	
	<link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css" />

	<!-- Stylesheets -->
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/hz.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/cssfiles/main.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/style.css" media="screen" /> 
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/cssfiles/jquery.fancybox.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/cssfiles/jqueryslidemenu.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/cssfiles/new-home.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/cssfiles/slide02.css" media="screen" />
	
	<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/style-new.css" />
	<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/responsive.css" />

	{{theme_css}}
	
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/typeface.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/dakota_regular.typeface.js"></script>
	
	<script src="<?php echo $path; ?>/template/jaguarpc/jsfiles/contentslider.js" type="text/javascript"></script>
	<script src="<?php echo $path; ?>/template/jaguarpc/js/sequencejs-options.apple-style.js" type="text/javascript"></script>
    
	{{theme_js}}
	
	<!-- Fonts -->
	
    <!-- Java Scripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/easing.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/jquery.flow.1.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/fancybox.js"></script>
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/ready.js"></script>
			
	<script language="javascript" type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jquery.equalheights.js"></script>
	
	
	<script type="text/javascript" src="<?php echo $path; ?>/template/jaguarpc/jsfiles/jtip.js"></script>
		
	<script type="text/javascript">

    // Can also be used with $(document).ready()
            $(window).load(function() {
                if (($.browser.msie) && ($.browser.version < 10)) {
                    $('.flexslider').flexslider({
                        animation: "slide",
                        slideshow: true,
                        directionNav: false,
                        slideshowSpeed: 7000,
                        animationDuration: 600
                    });
                }
                else
                {
                    $('.flexslider').flexslider({
                        animation: "fade",
                        slideshow: true,
                        slideshowSpeed: 7000,
                        animationDuration: 600
                    });
                }
            });
        </script>
	
    <!-- Header and tabs begin. -->  
	<SCRIPT language=javascript type=text/javascript>
	var rotateSpeed = 5500; // Milliseconds to wait until switching tabs.
	var currentTab = 0; // Set to a different number to start on a different tab.
	var numTabs; // These two variables are set on document ready.
	var autoRotate;
	
	function openTab(clickedTab) {
	var thisTab = $(".tabbed-box .tabs a").index(clickedTab);
	$(".tabbed-box .tabs li a").removeClass("active");
	$(".tabbed-box .tabs li a:eq("+thisTab+")").addClass("active");
	$(".tabbed-box .tabbed-content").hide();
	$(".tabbed-box .tabbed-content:eq("+thisTab+")").show();
	currentTab = thisTab;
	}
	
	function rotateTabs() {
	var nextTab = (currentTab == (numTabs - 1)) ? 0 : currentTab + 1;
	openTab($(".tabbed-box .tabs li a:eq("+nextTab+")"));
	}
	
	$(document).ready(function() {
	$(".tabbed-content").equalHeights();
	numTabs = $(".tabbed-box .tabs li a").length;
	$(".tabbed-box .tabs li a").click(function() { 
		openTab($(this)); return false; 
	});
	$(".tabs").mouseover(function(){clearInterval(autoRotate)})
	.mouseout(function(){autoRotate = setInterval("rotateTabs()", rotateSpeed)});
	
	$(".tabbed-box .tabs li a:eq("+currentTab+")").click()
	$(".tabs").mouseout();
	
});
</SCRIPT>

<!-- Header and tabs begin. -->  
<script type="text/javascript">		
	function init(){		
	var stretchers = document.getElementsByClassName('stretcher'); //div that stretches
	var toggles = document.getElementsByClassName('tab'); //h3s where I click on
	
	//accordion effect
	var myAccordion = new fx.Accordion(
		toggles, stretchers, {opacity: true, height: true, duration: 400}
	);
	//hash functions
	var found = false;
		toggles.each(function(h3, i){
		var div = Element.find(h3, 'nextSibling'); //element.find is located in prototype.lite
		if (window.location.href.indexOf(h3.title) > 0) {
		myAccordion.showThisHideOpen(div);
		found = true;
	}
});
	if (!found) myAccordion.showThisHideOpen(stretchers[0]);
	}
</script>

<!-- Stylesheets -->
<script type="text/javascript"> 
// Popup window code
function newPopup(url) {
	popupWindow = window.open(
		url,'popUpWindow','height=375,width=508,left=10,top=10,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes')
}

</script>

</head>

<!-- words we dropped
	Title: Virtual Private Hosting, Virtual Dedicated Server,
	Description: quality, non-oversold, reliable hosting solutions at an affordable price
	Misc: Affordable VPS Hosting and Virtual Private Hosting Solutions	virtual private server (VPS) Virtual Private Server Hosting plans  managed dedicated servers reseller hosting server, virtual hosting solutions
	#-->
	
<!-- ClickTale Top part -->

<script type="text/javascript">
var WRInitTime=(new Date()).getTime();
</script>

	
<?php include("header.php"); ?>
<body <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> >
	
	{{navigation_logged_in}}

<!-- /Page Header -->

<!-- Navigation -->
<div id="nav" >
		{{navigation}}
</div>
<!-- /Navigation -->

  <div class="Clear-Both"></div>
</div>
<!-- /Page Header -->

<!-- Begin Content Wrapper -->

<!-- // BANNER -->

<!-- Content Block -->
<?php //echo $parsedown->text($content); ?>
	<section class="main-content">
		<div class="container inner group">
			<div class="row">
				<div class="col">
					<!-- Main Content -->
					<?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
				</div>
			</div>
		</div>
	</section>

<!-- /Bottom Blocks -->
<div class="Clear-Both"></div>
<!-- /Content Wrapper -->

<!-- /Content Wrapper --></div>

<div class="Clear-Both"></div>

<?php include("footer.php"); ?>

<!-- /Content Wrapper -->

<!-- /Footer -->

</body>

<!-- /Body -->

<?php
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$gu_siteid="bqwkxhe";
	$gu_param = "st=".$gu_siteid."&ref=".(isset($_SERVER['HTTP_REFERER']) ? urlencode($_SERVER['HTTP_REFERER']) : ''). "&vip=".$_SERVER['REMOTE_ADDR']."&ua=".urlencode($_SERVER['HTTP_USER_AGENT']). "&cur=".urlencode($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."&b=5";
	@readfile($protocol."counter.goingup.com/phptrack.php?".$gu_param); ?>
	
	<script src="/<?php echo $champion_dir; ?>/includes/tracker.php?uri=<?php echo $_SERVER['REQUEST_URI']; ?>&ref=<?php echo $_SERVER['HTTP_REFERER'];?>">
</script>
<!--
<script type="text/javascript" charset="utf-8">var ju_num="26966343-6053-49D4-9A20-FF612C5DFB50";var asset_host=(("https:"==document.location.protocol)?"https":"http")+'://d2j3qa5nc37287.cloudfront.net/';(function() {var s=document.createElement('script');s.type='text/javascript';s.async=true;s.src=asset_host+'coupon_code1.js';var x=document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);})();</script>
-->

</html>