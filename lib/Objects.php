<?php

/**
 * Objects Store
 * Very Basic Data-to-Object Abstraction layer
 */

class Objects {
  private static $stores = array();
  
  static function addStore( $name, $store ) {
    self::$stores[$name] = $store;
    return $store;
  }

  static function getStore( $name ) {
    return isset( self::$stores[$name] ) ? self::$stores[$name] : null;
  }
}

abstract class Object {
  var $id;
  var $url;
  var $created;
  var $updated;
  
  function __construct( $d ) {
    $this->id      = isset($d['id'])      ? $d['id'] : $this->generateID();
    $this->url     = str_replace( ' ', '-', $this->id );
    $this->created = isset($d['created']) ? strtotime($d['created']) : null;
    $this->updated = isset($d['updated']) ? strtotime($d['updated']) : null;
  }
  
  private function generateID() {
    $type = get_class($this);
    $num = rand();
    return "{$type}{$num}";
  } 
  
  function toHash() {
    return array( 'id' => $this->id );
  }

  function toSensitiveHash() {
    return $this->toHash();
  }
  
  public function timeLabel() {
    return strftime( "%e %B %Y - %H:%m", $this->updated );
  }

  private $store;
  
  function persist() {
    if( $this->store ) { $this->store->put( $this ); }
  }
  
  function setStore( $store ) {
    $this->store = $store;
    return $this;
  }
}

interface ObjectStore {
  public function fetch( $id       );
  public function put  ( $object   );

  public function filter( $property, $value );
  public function orderBy( $by, $desc );
  public function retrieve( $limit, $start );
  
  public function remove();
}
