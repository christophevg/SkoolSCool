<?php

class SessionStore implements ObjectStore {
  private $name;
  private $filters;

  public function __construct( $name ) {
    $this->name = $name;
    $this->clear();
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
    $this->filters[$property] = $value;
    return $this;
  }
  
  public function orderBy( $by, $desc ) {
    return $this;
  }
  
  public function retrieve( $limit, $start ) {
    return array();
  }
  
  public function remove() {
    $set = SessionManager::getInstance()->{$this->name};
    $hits = array();
    // determine filter hits
    foreach( $set as $id => $object ) {
      if( $object ) {
        foreach( $this->filters as $property => $value ) {
          if( $object->$property == $value ) {
            array_push($hits, $id);
          }
        }
      }
    }
    // delete objects that were hit
    foreach( $hits as $id ) {
      unset($set[$id]);
    }
    SessionManager::getInstance()->{$this->name} = $set;
    $this->clear();
    return $this;
  }
  
  private function clear() {
    $this->filters = array();
  }
}
