<?php

/**
 * Context
 * A central static object representing the context in which a given request 
 * is processed.
 * It contains a request object that can be queried for information about
 * the current request.
 */

class Context {
  static $request;     
  static $currentUser;
}

// populate context
Context::$request     = Request::getInstance();
Context::$currentUser = SessionManager::getInstance()->currentUser;

/**
 * content requests are passed through the 'id' (content id) get parameter
 * example: http://skoolscool.org/index.php?id=somePage
 * at server level this can be rewritten
 * example: http://skoolscool.org/somePage
 * By default we show the home-page.
 *
 * A request can contain a path and an object identifier:
 * http://skoolscool.org/someSection/someSubSection/somePage
 * An object can be called through many different paths, which just creates
 * a context.
 */
class Request {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new Request();
      self::$instance->init();
    }
    return self::$instance;
  }

  var $url;       // path + object
  var $path;      // URL path leading up to the $object
  var $object;    // Actually requested object == last part of the path
  var $style;     // e.g. Embedded
  
  function init() {
    $request = isset($_GET['id']) ? $_GET['id'] : 'home';

    // path = array
    $this->path = split( "/", $request );
    if( !is_array($this->path) ) { $this->path = array( $this->path ); }

    // url = path + object
    $this->url = $this->path;

    // object = last part of path
    $this->object = array_pop( $this->path );

    // style = embedded or show
    $this->style = isset( $_GET['embed'] ) ? "embed" : "show";
  }
}
