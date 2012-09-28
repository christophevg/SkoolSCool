<?php

class SessionStore implements ObjectStore {
  private $name;

  public function __construct( $name ) {
    $this->name = $name;
  }
  
  public function fetch( $id ) {
    // applying strtolower because e.g. MySQLStore is case insensitive
    // FIXME: this should be enforced at Object level by cleaning up the id
    $id = strtolower($id);
    $set = SessionManager::getInstance()->{$this->name};
    return is_array( $set ) && array_key_exists($id, $set) ? $set[$id] : null;    
  }

  public function has( $id ) {
    // applying strtolower because e.g. MySQLStore is case insensitive
    // FIXME: this should be enforced at Object level by cleaning up the id
    $id = strtolower($id);
    $set = SessionManager::getInstance()->{$this->name};
    return is_array( $set ) && array_key_exists($id, $set);
  }

  public function put( $object, $alias = null ) {
    // applying strtolower because e.g. MySQLStore is case insensitive
    // FIXME: this should be enforced at Object level by cleaning up the id
    if( $object == null && $alias == null ) { return; }
    $id = $alias == null ? strtolower($object->id) : strtolower($alias);
    $set = SessionManager::getInstance()->{$this->name};
    $set[$id] = $object;
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
  
  public function remove() {
    return $this;
  }
}
