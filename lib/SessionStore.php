<?php

class SessionStore implements ObjectStore {
  private $name;

  public function __construct( $name ) {
    $this->name = $name;
  }
  
  public function fetch( $id ) {
    $set = SessionManager::getInstance()->{$this->name};
    return is_array( $set ) && array_key_exists($id, $set) ? $set[$id] : null;    
  }

  public function put( $object ) {
    $set = SessionManager::getInstance()->{$this->name};
    $set[$object->id] = $object;
    SessionManager::getInstance()->{$this->name} = $set;
  }
  
  // TODO:
  public function filter( $property, $value ) {
    return $this;
  }
  
  public function orderBy( $by, $desc ) {
    return $this;
  }
  
  public function retrieve( $limit ) {
    return array();
  }
}
