this.tooltip = function(){	
		
	// -> Offset from the link.
		xOffset = 10;
		yOffset = 20;		
	
	// -> Go go go!
	$("a.ToolTip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
    });	
	$("a.ToolTip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};

// -> On page load, go ahead and generate tooltips.
$(document).ready(function(){
	tooltip();
});