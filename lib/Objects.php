<?php

/**
 * Objects Store
 * Abstraction layer to the database
 */

class Objects {
  private static $stores = array();
  
  static function getStore( $name ) {
    return isset( self::$stores[$name] ) ? self::$stores[$name] : null;
  }
  
  static function addStore( $name, $store ) {
    self::$stores[$name] = $store;
  }
}

interface ObjectStore {
  public function from ( $bucket );
  public function fetch( $id     );

  public function in   ( $bucket );
  public function put  ( $object );
}

interface DataStore extends ObjectStore {
  public function fetchData( $id );
}
