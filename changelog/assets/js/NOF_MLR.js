/*jQuery.getScript("./assets/ui.tabs.js");
jQuery.getScript("./assets/jquery.accordion.js");
jQuery.getScript("./assets/sgbeal-togglepane.jquery.js");
jQuery.getScript("./assets/jquery.dimensions.js");
*/
jQuery.fn.MyTab = function(options) 
{
    this.find('ul > li > div').each(
        function() {
                    var _ulp = $(this).parent().parent().parent();
                    _ulp.append($(this).clone());
                    $(this).remove();
                });
    $(this).children('ul').addClass("nof-clearfix");             
    this.children('ul').tabs(options).find('li a').contents().wrap('<span/>');
	$(">ul", this).css("display", "block");
};

jQuery.fn.MyTabBottom = function(options) {
    this.find('ul > li > div').each(
        function() {
                    var _ulp = $(this).parent().parent().parent();
                    _ulp.prepend($(this).clone());
                    $(this).remove();
                });
    this.children('ul').tabs(options).find('li a').contents().wrap('<span/>');
	$(">ul", this).css("display", "block");
};

jQuery.fn.MyAcc = function(options) {
    //this.addClass("basic").find('ul li').each(
    this.find('ul > li').each(
        function() {
                    var _ulp = $(this).parent().parent();
                    //$(this).children('a').addClass('head');
                    $(this).children('a').replaceWith("<a class='head'>" + $(this).children('a').text() + "</a>");
                    _ulp.append($(this).html());
                });
    this.children('ul').remove();
    $(this).children('div').addClass("ui-accordion-panel");
	$(">ul", this).css("display", "block");
    return this.accordion(options);
};

jQuery.fn.MyWiz = function(options) {
    var myAccordion=this.MyAcc(options);
    var nxtBtn = "<INPUT class='next' type='button' value='next'/>";
    var prevBtn = "<INPUT class='previous' type='button' value='previous'/>";
    $(this).children('div').each(function(index) {
        //alert("id = " + this.id);
        var str;
        if (index == 0) 
        {
            //alert("next");
            $(this).append(nxtBtn);
        }else 
        {
            if ($(this).is(":last-child")) 
            {
                //alert("previous");
                $(this).append(prevBtn);
            }
            else
            {
                //alert("previous next");
                $(this).append(prevBtn + nxtBtn);
            }
        }
        //$(this).empty();
        //$(this).append(str);
        $(this)
        .children('input:button')
        .filter('.next, .previous')
        .click(function() {
            //alert(index + ($(this).is(".next") ? 1 : -1));
            $(this).parent().parent().activate(index + ($(this).is(".next") ? 1 : -1));
            return false;
	    });
    });
	$(">ul", this).css("display", "block");
    return myAccordion;
};

jQuery.fn.MyPane = function(options) 
{
    this.find('ul > li').each(
        function() {
                    var _a=$(this).children('a');
                    var _div=$(this).children('div').addClass('TogglePanePlainContent').html();
                    
                    _a.replaceWith("<div class='TogglePaneFancyHeader'>" + _a.text() + "</div>");
                    $(this).replaceWith("<div>" + $(this).html() + "</div>");
                });
    this.children('ul').replaceWith($(this).children('ul').html());
    this.initTogglePane(options);
	$(">ul", this).css("display", "block");
};