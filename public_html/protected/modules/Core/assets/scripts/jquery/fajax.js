(function( $ ){

  var methods = {
    init : function( options ) {
        //TODO: configure the show/hide ajax loadding indicator
    },
    
    post : function(sid, data, callback, type) {
        if (! data) {
            data = {'SID': sid};
        } else if (typeof data === 'object') {
            data.SID = sid;
        } else {
            $.error ('Invalid data type');
        }
        
        $.post(
            baseUrl + '/index.php?r=Core/service/ajax',
            data,
            callback,
            type
        );
    },
    
    get : function(sid, data, callback, type) {
        if (! data) {
            data = {'SID': sid};
        } else if (typeof data === 'object') {
            data.SID = sid;
        } else {
            $.error ('Invalid data type');
        }
        
        $.get(
            baseUrl + '/index.php?r=Core/service/ajax',
            data,
            callback,
            type
        );
    },
    
    widget : function(wid, data, containerId, method) {
        if (! data) {
            data = {'WID': wid};
        } else if (typeof data === 'object') {
            data.WID = wid;
        } else {
            $.error ('Invalid data type');
        }
        
        if (method != 'get')
            $.post(
                baseUrl + '/index.php?r=Core/service/widget',
                data,
                function(html) {
                    $('#'+containerId).html(html);
                },
                'html'
            );
        else
            $.get(
                baseUrl + '/index.php?r=Core/service/widget',
                data,
                function(html) {
                    $('#'+containerId).html(html);
                },
                'html'
            );
    }
  };

  $.fajax = function( method ) {
    
    // Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.fajax' );
    }    
  
  };

})( jQuery );
