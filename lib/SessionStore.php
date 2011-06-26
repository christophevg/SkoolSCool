<?php

class SessionStore implements ObjectStore, DataStore {
  private $name;
  private $bucket;

  public function __construct( $name ) {
    $this->name = $name;
  }
  
  public function from( $bucket ) {
    $this->bucket = $bucket;
    return $this;
  }

  public function in( $bucket ) {
    $this->bucket = $bucket;
    return $this;
  }
  
  public function fetchData( $id ) {
    $bucket = SessionManager::getInstance()->{$this->name}[$this->bucket];
    return array_key_exists($id, $bucket) ? $bucket[$id] : null;
  }

  public function fetch( $id ) {
    if( $data = $this->fetchData($id) ) {
      $content = new $data['type']( $data['cid'], $data );
      return $content->setStore( $this );
    }
  }

  public function put( $object ) {
    $buckets = SessionManager::getInstance()->{$this->name};
    $buckets[$this->bucket][$object->cid] = array(
      'cid'      => $object->cid,
      'type'     => get_class( $object ),
      'author'   => $object->author->login,
      'time'     => $object->time,
      'data'     => $object->getData(),
      'children' => $object->children
    );
    SessionManager::getInstance()->{$this->name} = $buckets;
  }
}
