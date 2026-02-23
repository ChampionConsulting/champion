$(document).ready(function(){
	$('a.nav_toggle').on('click', function(e) {    
		e.preventDefault();    
		$('#nav').slideToggle(300);})		
});