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

        /* 02: Back to top button
        ==============================================*/

        var $backToTopBtn = $('.back-to-top');

        if ($backToTopBtn.length) {
            var scrollTrigger = 400, // px
            backToTop = function () {
                var scrollTop = $(window).scrollTop();
                if (scrollTop > scrollTrigger) {
                    $backToTopBtn.addClass('show');
                } else {
                    $backToTopBtn.removeClass('show');
                }
            };

            backToTop();

            $(window).on('scroll', function () {
                backToTop();
            });

            $backToTopBtn.on('click', function (e) {
                e.preventDefault();
                $('html,body').animate({
                    scrollTop: 0
                }, 700);
            });
        }
        
    });

})(jQuery);