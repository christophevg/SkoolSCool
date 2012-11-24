<?php

/**
 * SkoolSCool - Ajax interface
 * A small and very specific CMS for an elementary school's website
 * 
 * This ajax interface accepts REST requests and requires each request to 
 * contain a "X-CSFR-Request" header containing the content of the 
 * same-named cookie, which is set by the interface itself. This is a valid
 * way to avoid Cross-Site-Forgery-Requests because although a malicious
 * website can construct requests, e.g. in a hidden iframe, to this API using
 * an active session, it can never obtain the content of the cookie to 
 * aditionally validate the actual request.
 * 
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 * 
 * TODO:
 * - GET & SEARCH methods support
 * - replace die() with sending correct HTTP responses (404, 500, 501,...)
 */

// include all functionality
include_once 'lib/SkoolSCool.php';

// bootstrap: setup caches, process login/out requests
include_once 'bootstrap.php';


class API {

  public static function handle_request() {
    $api = new API();
    switch( $_SERVER['REQUEST_METHOD'] ) {
      case 'GET'   : $api->handleGet();    break;
      case 'POST'  : $api->handlePost();   break;  
      case 'PUT'   : $api->handlePut();    break;  
      case 'DELETE': $api->handleDelete(); break;  
      case 'SEARCH': $api->handleSearch(); break;  
      default: $api->fail(405, 'unsupported method');
    }
  }
  
  private function __construct() {}

  private function handleGet() {
    $this->validateAccessRequest();
    list( $contentType, $id, $ts ) = $this->parseRequest();
    $this->get( $contentType, $id, $ts );
  }

  private function handlePost() {
    $this->validateModificationRequest();
    list( $contentType, $id, $ts ) = $this->parseRequest();
    // TODO clean POST data
    $this->create( $contentType, $_POST );
  }

  private function handlePut() {
    global $_PUT; // too bad I can't define a supreglobal ;-)
    $this->validateModificationRequest();
    list( $contentType, $id, $ts ) = $this->parseRequest();
    // TODO clean PUT data
    $this->update( $contentType, $id, $_PUT );
  }

  private function handleDelete() {
    $this->validateModificationRequest();
    list( $contentType, $id, $ts ) = $this->parseRequest();
    $this->delete( $contentType, $id );
  }

  private function handleSearch() {
    $this->validateAccessRequest();
    list( $contentType, $id, $ts ) = $this->parseRequest();
    // TODO: clean GET data
    $this->find($contentType, $_GET);
  }

  private function validateModificationRequest() {
    // we only allow authenticated users to modify objects
    if( SessionManager::getInstance()->currentUser->isAnonymous() ) {
      $this->fail(401, 'unauthenticated');
    }
    
    $this->validateAccessRequest();
  }
  
  private function validateAccessRequest() {
    // check that the request contains a xsfr value corresponding to the
    // one in the cookie
    $key = 'HTTP_X_' . str_replace( '-', '_', strtoupper(XSFR::$key) );
    if( ! isset( $_SERVER[$key] ) ) {
      $this->fail(401, 'missing request validator');
    }
    $xsfr = $_SERVER[$key];

    if( ! XSFR::validateRequest( $xsfr ) ) {
      $this->fail(401, 'invalid request validator');
    }
  }

  private function parseRequest() {
    // format request: api.php/<content-type>s
    //                 api.php/<content-type>s/<id>
    //                 api.php/<content-type>s/<id>/<ts>

    $parts = split( '/', $_SERVER['PATH_INFO'] );
    while(count($parts) > 0 && trim($parts[0]) == "") { array_shift($parts); }
    if( count($parts) == 0 ) { fail(400, 'missing object type'); }
    
    $contentType = substr( $parts[0], 0, -1 );      // strip of trailing s ;-)
    $id          = count($parts) > 1 ? $parts[1] : null;
    $ts          = count($parts) > 2 ? $parts[2] : null;
    
    return array( $contentType, $id, $ts );
  }
  
  // actual functional handlers
  
  private function create( $contentType, $data ) {
    switch($contentType) {
      case 'PageContent':
        $object = Objects::getStore('persistent')->fetch($data['id']);
        if( $object == null ) {
          $object = new PageContent($data);
          Objects::getStore('persistent')->put($object);
        } else {
          $this->fail( 409, 'Object with that ID already exist. Use PUT to update it. ' . $object->body );
        }
        break;
      default:
        $this->fail( 501, "create not implemented for requested contentType" );
    }
    $this->respond( 201, "created", $object );
  }

  private function update( $contentType, $id, $data ) {
    $object = Objects::getStore('persistent')->fetch($id);
    if( $object == null ) { $this->fail( 404, "unknown resource" ); }
    foreach( $data as $property => $value ) {
      // TODO: filter out "read-only" properties
      $object->$property = $value;
    }
    $object->persist();
    $this->respond( 201, "updated", $object );
  }

  private function get( $contentType, $id, $ts = false ) {
    // TODO: timestamp support
    if( ! $object = Objects::getStore('persistent')->fetch($id) ) {
      $this->fail( 404, "unknown resource" );
    }
    $this->respond( 200, "ok", $object );
  }

  private function delete( $contentType, $id = false, $ts = false ) {
    if( ! $object = Objects::getStore('persistent')->fetch($id) ) {
      $this->fail( 404, "unknown resource" );
    }
    $store = Objects::getStore('persistent')->filter( 'id', $id );
    if( $ts ) { $store->filter( 'ts', $ts ); }
    $store->remove();
    $this->respond( 200, "ok" );
  }

  private function find( $contentType, $constraints ) {
    $limit = isset($constraints['__limit']) ? $constraints['__limit'] : null;
    $start = isset($constraints['__start']) ? $constraints['__start'] : null;
    $store = Objects::getStore('persistent');
    foreach( $constraints as $property => $value ) {
      if( substr($property, 0, 2) != '__' ) {
        $store->filter( $property, $value );
      }
    }
    $this->respond( 200, "ok", $store->retrieve($limit, $start) );
  }
  
  // simple helper functions
  
  private function fail($code, $msg) {
    header($this->getProtocol() . ' ' . $code );
    die( json_encode( array( 'msg' => $msg ) ) );
  }

  private function respond($code, $msg, $data = null) {
    header($this->getProtocol() . ' ' . $code . ' ' . $msg );
    if( $data == null ) { $output = '{}'; }
    else {
      if( is_array($data) ) {
        $array = array();
        foreach( $data as $obj ) {
          array_push( $array, $obj->toHash() );
        }
        $output = json_encode($array);
      } else {
        $output = json_encode($data->toHash());
      }
    }

    die( $output );
  }
  
  private function getProtocol() {
    return isset($_SERVER['SERVER_PROTOCOL']) ?
      $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
  }

  private function toString($map) {
    ob_start();
    print_r($map);
    $string = ob_get_contents();
    ob_end_clean();
    return $string;
  }

}

API::handle_request();
