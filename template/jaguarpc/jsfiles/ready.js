$(function() {
    $("#controller").jFlow({
        slides: "#slides",
        width: "100%",
        height: "318px",
		easing: "jswing",
        duration: 300
    });
});
$(document).ready(function(){
	
	/* Basic Fancyboxes. -> */
	$("a.Basic-Box").fancybox({
		'zoomSpeedIn'			: 600,
		'zoomSpeedOut'			: 300,
		'frameWidth'			: 300,
		'frameHeight'			: 140,
		'easingIn'				: 'easeOutBack',
		'easingOut'				: 'easeInBack'
	});
	
});