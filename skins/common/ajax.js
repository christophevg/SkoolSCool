/**
 * ajax.js
 * lightweight remote object invocation client implementation
 * adheres to the generic objectstore REST api
 */

var __OS__ = (function() {
  var base = "api.php";
  
  function createXmlHttp() {
    var xmlhttp;
    
    function init() {
      xmlhttp = null;
      if( window.XMLHttpRequest ) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
      } else if( window.ActiveXObject ) {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } else {
        notify("Your browser does not support XMLHTTP!");
      }
      return xmlhttp;
    }
    
    init();

    function open( method, path, args ) {
      query = [];
      if( typeof args != "undefined" ) {
        for( key in args ) {
          if( args[key] != null ) {
            query.push( key + "=" + args[key] );
          } else {
            query.push( key );
          }
        }
      }
      query = query.length > 0 ? "?" + query.join("&") : "";
      xmlhttp.open( method, base + "/" + path + query, true );
      return this;
    }

    function onReady( callback ) {
      xmlhttp.onreadystatechange = function() {
        if( xmlhttp.readyState  == 4 ) {
          callback( (xmlhttp.responseText != "" ? 
                      JSON.parse(xmlhttp.responseText) : ""),
                    (""+xmlhttp.status).substring(0,1) == 2 ); // 2xx success codes
        }
      };
      return this;
    }

    function prepareForData() {
      xmlhttp.setRequestHeader( 'Content-Type',
      'application/x-www-form-urlencoded' );
      return this;
    }

    function getCookie(name) {
      var nameEQ = name + "=";
      var ca     = document.cookie.split(";");
      for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while(c.charAt(0) == ' ') { c = c.substring(1, c.length); }
        if( c.indexOf(nameEQ) == 0 ) {
          return c.substring(nameEQ.length, c.length);
        }
      }
      return null;
    }

    function send(data) {
      if( data != null ) { prepareForData(); }
      if( typeof data != "object" ) { data = {}; }

      stringParts = [];
      for( var key in data ) {
        if(typeof data[key]["push"] != "undefined") {
          for( var item in data[key] ) {
            stringParts.push( key + "[]=" + encodeURIComponent(data[key][item]) );
          }
        } else {
          stringParts.push( key + "=" + encodeURIComponent(data[key]) );
        }
      }
      data = stringParts.join("&");

      // add CSFR info from cookies
      setHeader("CSFR-Request", getCookie("CSFR-Request"));
      xmlhttp.send(data);
      return this;
    }

    function setHeader(name, value) {
      xmlhttp.setRequestHeader( "X-" + name, value );
      return this;
    }
    
    return {
      "open"    : open,
      "onReady" : onReady,
      "send"    : send
    }
  }
  
  // ObjectStore exposed functionality
	function setBase(newBase) {
		base = newBase;
	}

  function post(contentType, data, callback) {
    createXmlHttp().open( "POST", contentType )
                   .onReady(callback)
                   .send(data);
  }

  function put(contentType, id, data, callback) {
    createXmlHttp().open( "PUT", contentType + "/" + id )
                   .onReady(callback)
                   .send(data);
  }

  function get(contentType, id, ts, callback) {
    path = contentType + "/" + (ts != null ? id + "/" + ts : id);
    createXmlHttp().open( "GET", path )
                   .onReady(callback)
                   .send();
  }


  function del(contentType, id, ts, callback) {
    path = contentType + "/" + id + ( ts != null ? "/" + ts : "" );
    createXmlHttp().open( "DELETE", path )
                   .onReady(callback)
                   .send();
  }
  
  function search( contentType, constraints, limit, start, callback ) {
    if( limit != null ) { constraints["__limit"] = limit; }
    if( start != null ) { constraints["__start"] = start; }
    createXmlHttp().open( "SEARCH", contentType, constraints )
                   .onReady(callback)
                   .send();
  }

  return {
    "use"		 : setBase,
    // REST operation -> HTTP method
    "create" : post,
    "update" : put,
    "fetch"  : get,
    "remove" : del,
    "find"   : search
  };
})();
