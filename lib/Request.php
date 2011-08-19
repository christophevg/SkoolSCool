<?php

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

  var $full;      // the request as it has been requested
  var $url;       // path + object
  var $path;      // URL path leading up to the $object
  var $object;    // Actually requested object == last part of the path
  var $name;      // object name, i.e. with spaces in stead of dashes
  var $style;     // e.g. Embedded

  // flags
  var $creation;  // the user wants the requested content to be created
  var $editor;    // the user wants the requested content as editor
  
  var $contentType; // the requested contentType
  
  function init() {
    $this->full = isset($_GET['id']) ? $_GET['id'] : 'home';

    // path = array
    $this->path = split( "/", $this->full );
    if( !is_array($this->path) ) { $this->path = array( $this->path ); }

    // url = path + object
    $this->url = $this->path;

    // object = last part of path
    $this->object = array_pop( $this->path );
    
    // name = object without dashes
    $this->name = str_replace( '-', ' ', $this->object );

    // style = embedded or show
    $this->style = isset( $_GET['embed'] ) ? "embed" : "show";
    
    // creation = true|false
    $this->creation = isset( $_GET['create'] );

    // edit = true|false
    $this->editor = isset( $_GET['mode'] ) ? $_GET['mode'] == 'edit' : false;
    
    // contentType = any of the Content::$types
    $this->contentType = isset( $_GET['type'] ) 
                         && in_array( $_GET['type'], Content::$types ) ?
                          $_GET['type'] : null;
  }
}
