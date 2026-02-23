(function($) {
    "use strict";
    $(function(){

        /* 01: Main menu
        ==============================================*/

        var subMenu = $('#nav ul ul');

        if(subMenu.length){
            subMenu.parent().addClass('has-submenu');
            subMenu.parent().prepend('<span class="menu-caret"></span>');
        }

        var menuButton = $('#menu-button'),
            menuCaret = $('.menu-caret');

        menuButton.on('click', function(){
            $(this).next('ul').slideToggle();
        });

        menuCaret.on('click', function(){
            $(this).siblings('ul').slideToggle();
        });

        
    });

})(jQuery);