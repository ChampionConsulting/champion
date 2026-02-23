// Begin MultiLayoutRegions
$(document).ready(
function() {
	$('#MultiLayoutRegion1LYR').MyAcc({active: 0, alwaysOpen: false, header: '.head', autoheight: false, animated: 'slide'});
}
);
// End MultiLayoutRegions

// Begin XHTML adjustment
$(document).ready(function(){
	if (jQuery.browser.msie && jQuery.browser.version.substr(0, 2) == "6.") {
		$(".nof-clearfix").each(function (i) {
			$(this).append("<div style='clear:both'/>");
			$(this).removeClass("nof-clearfix");
		});
	}
	if (jQuery.browser.safari){
		$(".nof-lyr>br:first").each(function () {
			$(this).replaceWith("<div style='height:0px'>&nbsp;</div>");
		});
	}
});

// End XHTML adjustment

// Begin Navigation Bars
var ButtonsImageMapping = [];
ButtonsImageMapping["NavigationBar1"] = {
	"NavigationButton1" : { image: "./assets/images/autogen/Home_Hhighlighted_1_1.gif", rollover: "./assets/images/autogen/Home_HRhighlightedRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton2" : { image: "./assets/images/autogen/Download_Nregular_1_1.gif", rollover: "./assets/images/autogen/Download_NRregularRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton3" : { image: "./assets/images/autogen/Purchase_Nregular_1_1.gif", rollover: "./assets/images/autogen/Purchase_NRregularRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton4" : { image: "./assets/images/autogen/Contact_Us_Nregular_1_1.gif", rollover: "./assets/images/autogen/Contact_Us_NRregularRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton5" : { image: "./assets/images/autogen/Free_Version_Nregular_1_1.gif", rollover: "./assets/images/autogen/Free_Version_NRregularRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton6" : { image: "./assets/images/autogen/Support_Nregular_1_1.gif", rollover: "./assets/images/autogen/Support_NRregularRollover_1_1.gif", w: 141, h: 25 },
	"NavigationButton7" : { image: "./assets/images/autogen/Search_Nregular_1.gif", rollover: "./assets/images/autogen/Search_NRregularRollover_1.gif", w: 141, h: 25 }
};

$(document).ready(function(){
	$.fn.nofNavBarOptions({ navBarId: "NavigationBar1", rollover: true, autoClose: false });
	$("#NavigationBar1").nofNavBar({isMain: true, orientation: "vertical" });
	$("#NavigationBar1 ul").hide();
});


// End Navigation Bars

