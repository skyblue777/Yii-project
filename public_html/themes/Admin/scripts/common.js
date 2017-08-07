$(document).ready(function()
{
	$('.pop-down-menu-top').each(function() {
		$(this).click(function(e) {
			closeMenu();
			if(document.topCurrentmenu) {
				$(document.topCurrentButton).removeClass('active-button');
				$(document.topCurrentmenu).hide();
			}
			var id = this.id.replace(/Button$/, '');
			if(!('#'+id))
				return false;		
			var menu = $('#'+id);

			var obj = this;
			offsetTop = 0;
			offsetLeft = 0;
			overPositioned = false;
			pageOffsetLeft = 0;
			while(obj)
			{
				if(!overPositioned) {
					offsetLeft += obj.offsetLeft;
					offsetTop += obj.offsetTop;
				}
				pageOffsetLeft += obj.offsetLeft;
				obj = obj.offsetParent;
				if(obj && CurrentStyle(obj, 'position')) {
					var pos = CurrentStyle(obj, 'position');
					if(pos == "absolute" || pos == "relative") {
						overPositioned = true;
					}
				}
			}

			//$(this).addClass('active-button');

			// hide plugins like flash
			$('embed, object').css({ visibility: 'hidden' });

			$(menu).css('position', 'absolute');
			$(menu).css('visibility', 'hidden');
			$(menu).css('display', '');
			$(menu).addClass('pop-down-menu-container');

			// The Form Fields add field button
			if ($(this).hasClass('FormFieldsmenuButton')) {
				$(menu).css('top', offsetTop+this.offsetHeight+3+"px");
				this.blur();
				$(menu).css('left', offsetLeft+3 + "px");
			} else {
				//$(menu).css('top', offsetTop+this.offsetHeight+1+"px");
				$(menu).css('top', offsetTop+this.offsetHeight-2+"px");
				this.blur();
				var menuWidth = $(menu).get(0).offsetWidth;
				if(pageOffsetLeft + menuWidth > $(window).width()) {
					$(menu).css('left', (offsetLeft - menuWidth + $(this).get(0).offsetWidth + 2) + 'px');					
				}
				else {
					//$(menu).css('left', offsetLeft+2+ "px");
					$(menu).css('left', offsetLeft-1+ "px");
				}
			}

			$(menu).css('visibility', 'visible');
			$(menu).show();

			// show any plugins inside the actual menu dom which were hidden above, like swfupload elements as menu items
			$('embed, object', menu).css({ visibility: 'visible' });

			e.stopPropagation();
			$(document).click(function() {
				$(menu).hide(); $(document.topCurrentButton).removeClass('active-button');
				document.topCurrentmenu = '';
				$('.control-panel-search-bar').show();
				$('embed, object').css({ visibility: 'visible' });
			});
			document.topCurrentmenu = menu;
			document.topCurrentButton = this;
			return false;
		});
	});

});

function CurrentStyle(element, prop) {
	if(element.currentStyle) {
		return element.currentStyle[prop];
	}
	else if(document.defaultView && document.defaultView.getComputedStyle) {
		prop = prop.replace(/([A-Z])/g,"-$1");
		prop = prop.toLowerCase();
		return document.defaultView.getComputedStyle(element, "").getPropertyValue(prop);
	}
}

function openPopup(url, title)
{
	var l = screen.availWidth / 2 - 450;
	var t = screen.availHeight / 2 - 320;
	var win = window.open(url, title, 'width=800,height=650,left='+l+',top='+t+',scrollbars=1');
	return false;
}

$(document).ready(function() {
	// For IE, set the last child
	$('.menu-text a.menu-text:last-child').addClass('last ');
    
    $('.reset-filters').click(function(){
        $('.filters input, .filters select').val('');
        $('.filters input:first').trigger('change');
        return false;
    });
});
