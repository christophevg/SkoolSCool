var __remote__ = (function() {
  var xmlhttp;
  
  function init() {
    xmlhttp = null;
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    } else if (window.ActiveXObject) {
      // code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    } else {
      notify("Your browser does not support XMLHTTP!");
    }
  }

  init();

  function get(id, callback) {
    xmlhttp.open( "GET", "./ajax.php?id="+id, true );
    xmlhttp.onreadystatechange = function() {
      if( xmlhttp.readyState  == 4 ) {
        if( xmlhttp.status == 200 ) {
          callback( xmlhttp.responseText );
        } else {
          callback();
        }
      }
    };
    xmlhttp.send(null);
  }

  function post(id, data, callback) {
    xmlhttp.open( "POST", "./ajax.php", true );
    xmlhttp.setRequestHeader( 'Content-Type',
                              'application/x-www-form-urlencoded' );
    xmlhttp.onreadystatechange = function() {
      if( xmlhttp.readyState  == 4 ) {
        if( xmlhttp.status == 200 ) {
          callback( xmlhttp.responseText );
        } else {
          callback();
        }
      }
    };
    xmlhttp.send("id=" + id + "&data=" + escape(data));
  }

  return {
    store: post,
    fetch: get
  };
})();
