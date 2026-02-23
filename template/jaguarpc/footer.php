<!-- footer -->
<style type="text/css">
<!--
.style1 {
	color: #FFFF99;
	font-weight: bold;
}
-->
</style>				
<link rel="stylesheet" href="<?php echo $path; ?>/template/jaguarpc/css/all.css">
      

<!-- /Bottom Blocks -->
<div id="footer">
<!-- company-name -->
<div class="company-name">
<strong class="company"> Champion Consulting - Your HomeTown Solution Provider - Providing Enterprise Web Hosting since 1995 </strong></div>

<!-- f-main -->
<div class="f-main">
	<div class="list-row">
		<div class="lr-1">
			<div class="lr-2">
				<div class="list-row-c">
				<!-- list-box -->
					<div class="list-box">
						<h4>Site Links</h4>
							<div class="columns-holder">
								<div class="column">
									<ul>
										<li><a href="contact"> Contact Us </a></li>
										<li><a href="overview"> About Us </a></li>
										<li><a href="support"> Support </a></li>
										<li><a href="blog"> Our Blog </a></li>
										<li><a href="datacenter"> Datacenter </a></li>
								  </ul>
				   		    </div>
					<div class="column">
							<ul>
								<li><a href="clientlogin"> Client Login </a></li>
								<li><a href="affiliate"> Affiliate Program </a></li>
								<li><a href="promotions"> Coupons </a></li>
								<li><a href="#"> Reviews </a></li>
                                <li><a href="site-map"> Site Map </a></li>
							</ul>
						</div>
					</div>
				</div>
						
<!-- list-box2 -->
<div class="list-box list-box2">
	<h4> Hosting Services</h4>
		<div class="columns-holder">
			<div class="column">
				<h5>Web hosting</h5>
					<ul>
						<li><a href="/web-hosting">Shared Hosting </a></li>
						<li><a href="/web-hosting"> Cloud Hosting </a></li>
						<li><a href="/vps-cloud-servers"> VPS Hosting </a></li>
						<li><a href="/reseller-hosting"> Reseller Hosting </a></li>
					</ul>
			</div>
						
			<div class="column">
				<h5>Cloud hosting</h5>
					<ul>
						<li><a href="/web-hosting"> Cloud Hosting </a></li>
						<li><a href="/vps-cloud-servers">Cloud VPS </a></li>
					</ul>
			</div>
						
			<div class="column">
				<h5>Virtual Servers</h5>
					<ul>
						<li><a href="/vps-cloud-servers"> VPS </a></li>
						<li><a href="/vps-cloud-servers"> Cloud Servers </a></li>
						<li><a href="/vps-cloud-servers"> VPS Reseller </a></li>
					</ul>
						
			<h5>Dedicated servers</h5>
					<ul>
						<li><a href="/dedicated-servers"> Managed Servers </a></li>
						<li><a href="/dedicated-servers"> Unmanaged Servers </a></li>
						<li><a href="/dedicated-servers"> Smart Servers </a></li>
					</ul>
				</div>
			</div>
		</div>

<!-- info-box -->
<div class="info-box">
	<div class="logo-box">
		<strong><a class="logo logo2" href="./index.html"></a></strong></div>
			<a href="#/"><p>&copy; Copyright 1995 - 
				<SCRIPT type=text/javascript> var currentDate = new Date() 
				var year = currentDate.getFullYear() 
				document.write(year) </SCRIPT> <BR /> Champion Consulting, LLC</p> <p>All rights reserved.</p>
				<div class="doc">
						<ul>
							<li><a href="/terms-of-service.shtml.html">Legal TOS</a></li>
						    <li><a href="/privacy-policy.shtml.html">Privacy Policy</a></li>
						</ul>
				</div><br />
					<DIV class=bottom><!--
<DIV class=client_login><A href="https://nixcore.championconsulting.com/">
<IMG style="FLOAT: left" alt="Client Login" src="<?php echo $path; ?>/template/jaguarpc/images/client.png" width=24 height=24> </A></DIV>
-->
<DIV class=facebook><A href="http://facebook.com/champion.consulting" target=_blank>
<IMG alt=facebook src="<?php echo $path; ?>/template/jaguarpc/images/facebook.png" width=82 height=17></A> </DIV>

<DIV class=twitter><A href="http://twitter.com/chmpconsulting" target=_blank>
<IMG alt=twitter src="<?php echo $path; ?>/template/jaguarpc/images/twitter.png" ></A> </DIV>

<DIV class=linkedin><A href="http://linkedin.com/company/champion-consulting-llc" target=_blank>
<IMG alt=linkedin src="<?php echo $path; ?>/template/jaguarpc/images/linked_in.png" width=66 height=17></A> </DIV><br />

				<div class="social-links">
						<ul>
							<li>											
								<a href="blog" onclick="_gaq.push(['_trackEvent', 'link', 'click', 'Blogger']);" class="blogger" id="ctl00_HyperLink1"><span class="hiddentext">Blogger</span></a>
							</li>

							<li>											
								<a href="rss.php" onclick="_gaq.push(['_trackEvent', 'link', 'click', 'RSS']);" class="rss" id="ctl00_HyperLink3">
								<span class="hiddentext">Rss</span></a>
							</li>
							<li>	
								<a href="chatsupport.php" onclick="_gaq.push(['_trackEvent', 'link', 'click', 'ChatSupport']);" class="chat" id="ctl00_HyperLink3"><span class="hiddentext">Chat</span></a>
							</li>
						</ul>						
 	<div class="copyright">
				<?php echo \championcore\get_context()->theme->made_in_champion->render( array('badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/content/media/branding/powered_by.png')) ); ?>
				{{ block:"copyright" }}
			</div>
		<?php 
		if (!empty($champion_serial)) { $check = str_split($champion_serial);}
	
		if (empty($champion_serial) 
		|| strlen($champion_serial) > 20 
		|| strlen($champion_serial) < 16
		|| count(array_unique($check)) < 4 ) { 
		
			echo '<a class="trial" href="http://cms.championconsulting.com/register">UNREGISTERED EDITION</a></p>'; 
		}	
		else {
			echo "<span class='serial'></span>"; 	
		}
		
		// if (extension_loaded('zip')==true) { include("includes/auto-backup.php"); }	
	?>

	<div class="social-icons">
			{{ block:"social-icons" }}
	</div>
</div>


					</div>		
				</div>
			</div>


<!--
<div class="floating_chat_side">
    <a href="JavaScript:newPopup('http://www.championconsulting.com/chatsupport.php');" id="slide">
    <img src="<?php echo $path; ?>/template/jaguarpc/images/chat.png" alt="chat" width="71" height="171"></a>
</div>

<script type="text/javascript">
    $(".close_button").click(function(){
        $(".floating_chat").css("display", "none");
    })
</script>
 </div> -->  