<?php

/**
 * SessionCache is a transparent/decorating implementation of the ObjectStore
 * interface. It accepts requests and delegates them to an actual ObjectStore.
 * It uses a SessionStore to store cached versions of the data exchanged with
 * the actual ObjectStore, thus caching the information in the session,
 * avoiding repetitive accesses.
 */

class SessionCache implements ObjectStore {
  private $store;
  private $cache;

  public function __construct( $store ) {
    $this->store = $store;
    $this->cache = new SessionStore( 'SessionCache' );
  }
  
  public function fetch( $id ) {
    if( $this->cache->has( $id ) ) {
      $object = $this->cache->fetch( $id );
    } else {
      $object = $this->store->fetch( $id );  // fetch it
      $this->cache->put( $object, ( $object == null ? $id : null ) ); // cache
    }
    return $object;
  }

  public function put( $object ) {
    $object = $this->store->put( $object );
    $this->cache->put( $object ); // update cache
    return $object;
  }
  
  public function filter( $property, $value ) {
    $this->store->filter( $property, $value );
    $this->cache->filter( $property, $value );
    return $this;
  }
  
  public function orderBy( $by, $desc ) {
    $this->store->orderBy( $by, $desc ); // just pass it on to the store
    return $this;
  }
  
  public function retrieve( $limit, $start = null ) {
    $objects = $this->store->retrieve( $limit, $start );
    // TODO: add caching here also ?
    return $objects;
  }
  
  public function remove() {
    $this->store->remove();
    $this->cache->remove();
    return $this;
  }
}
