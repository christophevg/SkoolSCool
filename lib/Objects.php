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
  var $created;
  var $updated;
  
  function __construct( $d ) {
    $this->id      = isset($d['id'])      ? $d['id']                 : null;
    $this->created = isset($d['created']) ? strtotime($d['created']) : null;
    $this->updated = isset($d['updated']) ? strtotime($d['updated']) : null;
  }
  
  function toHash() {
    return array( id => $this->id );
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
  /* public function find ( $criteria ); */
}
