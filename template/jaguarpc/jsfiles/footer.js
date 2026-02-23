
jQuery(window).bind("load", function() { 
       
       var footerHeight = 0,
           footerTop = 0,
           $footer = jQuery("#footercontainer");
           
       positionFooter();
       
       function positionFooter() {
       
                footerHeight = jQuery("#footercontainer").height();
                footerTop = (jQuery(window).scrollTop()+jQuery(window).height()-footerHeight)+"px";
                
               if ( (jQuery(document.body).height()+footerHeight) < jQuery(window).height()) 
               {
                   jQuery("#footercontainer").css({
                        position: "fixed"
                   })
               } else {
                   jQuery("#footercontainer").css({
                        position: "static"
                   })
               }
               
       }

      jQuery(window)
               .scroll(positionFooter)
               .resize(positionFooter)
               
});
