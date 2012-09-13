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

  var $string;      // the request as it has been requested, as a string
  var $url;         // array of all parts of the $string
  var $path;        // array of parts of path leading up to the $object
  var $object;      // Actually requested object == last part of the path
  var $id;          // name of the object, without dashes
  var $context;     // name of the context, without dashes
  var $style;       // e.g. Embedded

  var $contentType; // the requested contentType

  // flags
  var $requiredCreation;  // the requested content needs to be created

  function init() {
    // the string as it has been requested
    $this->string = isset($_GET['id']) ? $_GET['id'] : 'home';

    // url = array version of $string
    $this->url = split( '/', $this->string );
    if( !is_array($this->url) ) { $this->url = array( $this->url ); }

    // path = url - object
    $this->path = $this->url;

    // object = last part of path, dashed become spaces
    $this->object = array_pop( $this->path );

    // id = object without dashes
    $this->id = str_replace( '-', ' ', $this->object );
    
    // context = (first) path part without dashes = reference to section
    $this->context = str_replace( "-", " ", $this->url[0] );
    
    // style = embedded or show
    $this->style = isset( $_GET['embed'] ) ? 'embed' : 'show';
    
    // contentType = any of the Content::$types
    $this->contentType = isset( $_GET['type'] ) 
                         && in_array( $_GET['type'], Content::$types ) ?
                          $_GET['type'] : null;

    // creation = true|false
    $this->requiresCreation = isset( $_GET['create'] );
  }
}
