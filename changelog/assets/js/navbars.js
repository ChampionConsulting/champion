(function($){

	var settings = {},
		defaults = {
			// for each subnavbar
			orientation: "horizontal",
			opening: "Bottom",
			offsetX: 0,
			offsetY: 0,
			// for the entirea navigation bar
			rollover: false,
			autoClose: true,
			spacing: 0,
			// others
			delay: 0,
			speed: 1000
		};

	$.fn.nofNavBarOptions = function(options) {
		settings = $.extend(defaults, options);
	}

	$.fn.nofNavBar = function(options) {
		$(this).addClass("NavBar_ul");

		var oldVis = $(this).css("visibility");
		$(this).css("visibility", "hidden");
		
		$(this).css("display", "block");

		// resolve settings
		settings = $.extend(defaults, settings);
		settings = $.extend(settings, options);
		currentWidth  = settings.spacing;
		currentHeight = settings.spacing;
		maxWidth = 0;
		maxHeight = 0;
		
		if (settings.isMain && !settings.autoClose) {
			var cMenuId = $(this).attr("id");
			var closeCurrentMenu = function (e) {
				var mMenu = $("#" + e.data.id); 
				mMenu.find("a").removeClass("mouseKeeper");

				// hide all UL children
				var ids = [];
				$("#" + e.data.id + " ul").each( function (i) {
					ids.push(this.id);
					$(this).removeClass("autoClose");
					$(this).removeClass("dontClose");
				});
				ids.reverse();
				for (var i=0; i<ids.length; i++){
					$("#" + ids[i]).hide();
				}
			}
			$(document).bind("click", {id: cMenuId}, closeCurrentMenu);
			$(window).bind("blur", {id: cMenuId}, closeCurrentMenu);
		}

		var zindex = parseInt($(this).css("z-index"));
		// proceed with every LI tag
		$("> li", this).each( function(i) {
			var li = $(this);
			var a = $("> a", this); 

			a.addClass("NavBar_a");
			var title = alt = a.attr("title");
			if (!title)
				title = alt = "";

			li.addClass("NavBar_li");
			var listItemId = $(this).attr("id");

			var width, height;
			var padWidth = 0, padHeight = 0;
			
			var hasCssClass = true;
			var cssClass = settings.cssClass;

			if (listItemId)
			{
				var cBIM = ButtonsImageMapping[settings.navBarId][listItemId];
				if (settings.isMain)
				{
					settings.opening = cBIM.opening;
					settings.offsetX = cBIM.offsetX;
					settings.offsetY = cBIM.offsetY;
				}

				var buttonCssClass = cBIM.cssClass;
				if (buttonCssClass)
				{
					cssClass = buttonCssClass;
					hasCssClass = true;
				}
				else
				{
					var image, rollover;
					image = cBIM.image;
					if (settings.rollover)
						rollover = cBIM.rollover;

					a.empty();
					a.append('<img alt="' + alt + '" title="' + title + '" src="' + image + '"></img>');
					var img = $("img", a);
					img.attr("border", "0");
					if (settings.rollover) 
					{
						img.mouseover( function() {
							img.attr("src", rollover);
						});

						img.mouseout( function() {
							img.attr("src", image);
						});
					}

					width = cBIM.w;
					height = cBIM.h;
					
					hasCssClass = false;
				}
			}

			if (hasCssClass)
			{
				a.addClass(cssClass);
				if (settings.rollover)
				{
					a.hover(
						function () {
							a.removeClass(cssClass);
							a.addClass(cssClass + "_hover");
						}, 
						function () {
							a.removeClass(cssClass + "_hover");
							a.addClass(cssClass);
						}
					);
				}

				height = a.height();
				width = a.width();

				var pl = a.css("padding-left");
				pl = pl.substring(0, pl.length - 2);
				var pr = a.css("padding-right");
				pr = pr.substring(0, pr.length - 2);
				padWidth = parseInt(pl) + parseInt(pr);
				/* if ( pl + pr != 0)
						a.css("width", width - pl - pr); */

				var pt = a.css("padding-top");
				pt = pt.substring(0, pt.length - 2);
				var pb = a.css("padding-bottom");
				pb = pb.substring(0, pb.length - 2);
				padHeight = parseInt(pt) + parseInt(pb);
				/* if ( pb != 0)
					a.css("height", height - pt - pb); */
			}
			
			// positioning LI
			if (settings.orientation == "horizontal") {
				li.css({ left: currentWidth + "px", top: currentHeight + "px" });
				currentWidth += width + padWidth + settings.spacing;
				if (height > maxHeight)
					maxHeight = height;
			}
			else {
				li.css({ left: currentWidth + "px", top: currentHeight + "px" });
				currentHeight += height + padHeight + settings.spacing;
				if (width > maxWidth)
					maxWidth = width;
			}
			li.css("z-index", zindex + 1);

			// do we have a submenu ?
			var ul = $("> ul", this);
			if (ul.size() > 0) {
				var offsetX = settings.offsetX;
				var offsetY = settings.offsetY - height;
				if (settings.opening.toLowerCase().match("left"))
					offsetX = -offsetX - settings.spacing * (ul.children().length + 1);
				if (settings.opening.toLowerCase().match("top"))
					offsetY = -offsetY - settings.spacing * (ul.children().length + 1);

				ul.css({ top: offsetY + "px", left: offsetX + "px"});
				ul.css("z-index", zindex + 1);
				ul.css("display", "block");
				this.opening = settings.opening;
				this.autoClose = settings.autoClose;
			} // UL (sub-menu)
				
			a.mouseover( function() { 
				if (this.timer)
					clearTimeout(this.timer);

				$(this).addClass("mouseIn");
				$(this).addClass("mouseKeeper");
				
				var parentUL = a.parent().parent();
				if (parentUL.hasClass("autoClose"))
					parentUL.removeClass("autoClose");
				
				if (!ul.size()) return;
				
				if (!ul.hasClass("autoClose"))
				{
					// hide all UL children
					var ids = [];
					$("#" + parentUL.attr("id") + " ul").each( function (i) {
						ids.push(this.id);
						$(this).removeClass("autoClose");
						$(this).removeClass("dontClose");
					});
					ids.reverse();
					for (var i=0; i<ids.length; i++){
						$("#" + ids[i]).hide();
					}
					ul.addClass("autoClose");
				}
				
				li.css("z-index", zindex + 2);
				switch (this.opening) {
					case "slideTop":
					case "slideLeft":
						ul.slideDown(settings.speed);
						break;
					case "slideBottom":
					case "slideRight":
						ul.slideUp(settings.speed);
						break;
					case "fadeTop":
					case "fadeLeft":
					case "fadeBottom":
					case "fadeRight":
						ul.fadeIn(settings.speed);
						break;
					default:
						ul.show();
				};
				
			});
			
			a.mouseout( function() {
					var thisObj = this;
					if ($(this).hasClass("mouseIn")) $(this).removeClass("mouseIn");
					this.timer = setTimeout(function() {
						$(thisObj).removeClass("mouseKeeper");
								
						if (settings.autoClose) {
							var ln = $(thisObj.parentNode).find("a.mouseKeeper").length;
							if (!ln) {
								if (ul.size()) ul.hide();
							}
							var pa = $(thisObj.parentNode.parentNode.parentNode).find(">a");
							if (pa.size()) {
								if (!pa.hasClass("mouseIn"))
									pa.trigger("mouseout");
							}
						}
						else {
							if (ul.hasClass("autoClose")) {
								ul.removeClass("autoClose");
								ul.hide();
							}
						}
					}, settings.delay);
			});
				
		}); // LI

		if (settings.orientation == "horizontal") {
			currentHeight += maxHeight + settings.spacing;
		}
		else {
			currentWidth += maxWidth + settings.spacing;
		}
		
		$(this).width(currentWidth);
		$(this).height(currentHeight);
		$(this).css("visibility", oldVis);
	}; // nofNavBar

})(jQuery);