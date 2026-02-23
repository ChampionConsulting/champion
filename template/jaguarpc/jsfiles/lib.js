
    
$(document).ready(function(){
    $('#accordion a.item').click(function () {
        $('#accordion li').children('ul').slideUp('fast');	
        $('#accordion a.item').each(function () {
            if ($(this).attr('rel')!='') {
                $(this).removeClass($(this).attr('rel') + 'Over');	
            }
        });	
        $(this).siblings('ul').slideDown('fast');
        $(this).addClass($(this).attr('rel') + 'Over');			
        return false;
    });
        
    $(".login").mouseover(function(){ 
        $(".login_box").css("display", 'block')
    });
    $(".login").mouseout(function(){ 
        $(".login_box").mouseover(function(){
            $(".login_box").css("display", 'block') 
        });
        $(".login_box").mouseout(function(){
            $(".login_box").css("display", 'none') 
        })
        $(".login_box").css("display", 'none')
    });
    $(".menu li").mouseover(function(){
        $(".menu_li").removeClass("hover"); 
        $(this).addClass("hover"); 
        var submenu_id = $(this).attr("ID");
        $("."+submenu_id).css({
            display:"block"
        });
                                
    });
    
    
    //Drop Down Menu Display
    $(".menu li").mouseout(function(){
        var ele = $(this);
        var submenu_id = $(this).attr("ID");
        $("."+submenu_id).mouseover(function()
        {
            $(ele).addClass("hover"); 
            $("."+submenu_id).css({
                display:"block"
            });
        });
        $("."+submenu_id).mouseout(function()
        {
            $(ele).removeClass("hover"); 
            $("."+submenu_id).css({
                display:"none"
            });
        });
        $("."+submenu_id).css({
            display:"none"
        });
        $(this).removeClass("hover"); 
                         
    });
    
    
    //Read more
    $(".read_more").mouseover(function(){
        $(this).addClass("read_more_hover");
    })
    $(".read_more").mouseout(function(){
        $(this).removeClass("read_more_hover");
    })
      
    $(".try").mouseover(function(){
        $(this).addClass("try_hover");
    })
    $(".try").mouseout(function(){
        $(this).removeClass("try_hover");
    })  
    $(".get_custom").mouseover(function(){
        $(this).addClass("get_custom_hover");
    })
    $(".get_custom").mouseout(function(){
        $(this).removeClass("get_custom_hover");
    }) 
        
    $(".close_button").click(function(){
        $(".floating_chat").css("display", "none");
    })
});