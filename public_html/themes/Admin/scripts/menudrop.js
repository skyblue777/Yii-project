
$(document).ready(function() {
	
	$('#menu ul > li > a').dblclick(function(e)
	{
		e.stopPropagation();
		window.location = this.href;
		return false;
	});

	$('#menu > ul > li > a').click(function(e)
	{
		var elem = this;
		if($(elem).parent().is('.open'))
		{
			$(elem.parentNode).removeClass('open');
			$(elem).parent().find('ul').css('display', 'block');
			$('embed').css('visibility', 'visible');
			return false;
		}


		if(document.topCurrentMenu) {
			$(document.topCurrentMenu).hide();
			$(document.topCurrentButton).removeClass('active-button');
			$('.control-panel-search-bar').show();
		}

		if(document.currentMenu) 
		{
			$(document.currentMenu.parentNode).removeClass('open');
			//$(document.currentMenu).parent().find('ul').css('display', 'none');
			$('embed').css('visibility', 'visible');
			/*if(document.currentMenu.parentNode.id == this.parentNode.id)
			{
				document.currentMenu = null;
				return false;
			}*/
		}
		document.currentMenu = this;

		offsetTop = offsetLeft = 0;
		var element = elem;
		do
		{
			offsetTop += element.offsetTop || 0;
			offsetLeft += element.offsetLeft || 0;
			element = element.offsetParent;
		} while(element && $(element).css('position') != 'relative');


		$(elem).parent().find('ul').css('visibility', 'hidden');
		if(navigator.userAgent.indexOf('MSIE') != -1) {
			$(elem).parent().find('ul').css('display', 'block');
		}
		else {
			$(elem).parent().find('ul').css('display', 'table');
		}

		var menuWidth = elem.parentNode.getElementsByTagName('ul')[0].offsetWidth;
		$(elem).parent().find('ul').css('width', menuWidth-2+'px');
		if(offsetLeft + menuWidth > $(window).width()) {
			$(elem).parent().find('ul').css('position', 'absolute');
			$(elem).parent().find('ul').css('left',  (offsetLeft-menuWidth+elem.offsetWidth-3)+'px');
		}
		else if(offsetLeft - menuWidth < $(window).width()) {
			$(elem).parent().find('ul').css('position', 'absolute');
			$(elem).parent().find('ul').css('left',  offsetLeft+'px');
		}
		$('embed').css('visibility', 'hidden');
		$('object').css('visibility', 'hidden');
		$(elem).parent().find('ul').css('visibility', 'visible');		
		$(elem).parent().addClass('over');
		/*$(elem).blur(function(event) {
			if(elem.parentNode.overmenu != true)
			{
				//$(elem.parentNode).removeClass('over');
				$(elem).parent().find('ul').css('display', 'none');
				$('embed').css('visibility', 'visible');
				$('object').css('visibility', 'visible');
			}
		});*/

		$(document).click(function(event) {
			/*if(elem.parentNode.overmenu != true)
			{
				$(elem.parentNode).removeClass('over');
				$(elem).parent().find('ul').css('display', 'none');
				$('embed').css('visibility', 'visible');
				$('object').css('visibility', 'visible');
				$("#menu ul li a").css('background-position','left 0');
				$("#menu ul li").css('background-position','right 0');
			}*/		
			$('#menu ul li.over ul').show();
		});
		return false;
	});
	$('#menu ul li ul li').mouseover(function() {
		this.parentNode.parentNode.overmenu = true;
		this.onmouseout = function(e) { this.parentNode.parentNode.overmenu = false;}
	});
	/*$('#menu ul li ul li').click(function() {
		$(this.parentNode).hide();
		$(this.parentNode.parentNode).removeClass('open');
	});*/	
	
	/*custom*/
	$("#menu ul li a").click(function() {
        var elem = $(this);
		if (elem.next('ul').length > 0)
        {
            window.location.href = elem.next('ul').find('li:eq(0) a').attr('href');
        }		
	});
    
    $('#menu ul li ul li').each(function(){
        if ($(this).hasClass('active'))
        {
            var elem = $(this).parent().prev();
            $("#menu ul li").removeClass("over");
            $("#menu ul li a span").removeClass("item-active");
            $("#menu ul ul").hide();        
            $("#menu ul li a").css('background-position','left 0');
            $("#menu ul li").css('background-position','right 0');        
            elem.parent().find('ul').show();
            elem.parent().css('background-position','right -27px');
            elem.css('background-position','left -27px').find('span').addClass('item-active');
            return false;
        }
    });
});

function closeMenu() {
	if(document.currentMenu) {
		$(document.currentMenu.parentNode).removeClass('open');
		$(document.currentMenu).parent().find('ul').css('display', 'none');
		$('embed').css('visibility', 'visible');
		$('object').css('visibility', 'visible');
	}
}

