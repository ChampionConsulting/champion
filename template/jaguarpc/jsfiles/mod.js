function loadMenuJs(){
	if( $('ul#nav-main') != null ) {
		$('ul#nav-main li').each(function(){
			$(this).hover(function(){

			$(this).find('div.subnav').show();

			//hides the subnav when the user hovers out from it
			$(this).find('div.subnav').hover(function(){},
			function(){
				$(this).hide();
			});

			//highlights the hovered item from the subnav list
			$(this).find('div.subnav div ul li').hover(function(){
				$(this).css('background','#9cd0eb');
				$(this).css('cursor','pointer');
			},function(){
				$(this).css('cursor','default');
			});

			},function(){
				$(this).find('div.subnav').hide();
				$(".subnav li").css('background','none');
			}
		);

		});

		//redirects to the home page when the home icon is clicked
		$('ul#nav-main li.home-icon').click(function(){
			var href = '/';
			window.location = href;
		});

		$('div.subnav div ul li').click(function(e){
			 var href = $(this).children('a').attr('href');
			 window.location = $('#basehrefurl').val() + href;
			 e.stopPropagation();
		});

		$('ul#nav-main li.main-menu').click(function(f){
			var href_main = $(this).children('a.main-menu-a').attr('href');
			window.location = $('#basehrefurl').val() + href_main;
		});
	}
}

// Making the nav menu stick to top when scrolling
$(function() {
 
    // grab the initial top offset of the navigation
    var sticky_navigation_offset_top = $('ul#nav-main').offset().top;
     
    // our function that decides weather the navigation bar should have "fixed" css position or not.
    var sticky_navigation = function(){
        var scroll_top = $(window).scrollTop(); // our current vertical position from the top
         
        // if we've scrolled more than the navigation, change its position to fixed to stick to top,
        // otherwise change it back to relative
        if (scroll_top > sticky_navigation_offset_top) {
            $('ul#nav-main').css({ 'position': 'fixed', 'top':0, 'margin-top':0, 'border-radius':'0' });
        } else {
            $('ul#nav-main').css({ 'position': 'relative', 'margin-top':'17px', 'border-radius':'4px 4px 0 0' });
        }  
    };
     
    // run our function on load
    sticky_navigation();
     
    // and run it again every time you scroll
    $(window).scroll(function() {
         sticky_navigation();
    });
 
});
