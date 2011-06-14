<?php

// register a transient object cache store
Objects::addStore( 'transient', new SessionStore( 'ObjectCache' ) );

abstract class Content {
  // factory method to retrieve a content object.
  // first we look in the persisted objects, in the ObjectStore
  // then we look in the transient/session objects in the ObjectCache
  static function get( $name = 'home' ) {
    $object = Objects::getStore('persistent')->from( 'content' )->fetch( $name );
    if( ! $object ) {
      $object = Objects::getStore('transient') ->from( 'content' )->fetch( $name );
    }
    return $object;
  }
  
  // method to create a new content object
  // it gets stored in the transient/session ObjectCache, until persisted
  // in a "real" ObjectStore
  static function create( $type, $name ) {
    // try to fetch the named content, if we find it, return it, in stead of
    // creating a new one with the same name
    $content = Content::get($name);
    if( $content ) { return $content; }
    
    // else instantiate a fresh object and "store" it in the ObjectCache
    $content = new $type( $name );
    Objects::getStore( 'transient' )->in( 'content' )->put( $content );
    return $content;
  }
  
  private $store;

  function persist() {
    if( $this->store ) { $this->store->in( 'content' )->put( $this ); }
  }
  
  function setStore( $store ) {
    $this->store = $store;
    return $this;
  }
  
  public function __construct( $name, $data = array() ) {
    // common data
    $this->cid      = $name;
    $this->author   = isset( $data['author'] ) ? 
                      User::get( $data['author'] ) : 
                      SessionManager::getInstance()->currentUser;
    $this->time     = isset($data['time']) ? $data['time'] : time();
    $this->children = isset( $data['children'] ) ?
                      $data['children'] : array();
    // let specific type handle custom data
    if( isset($data['data']) ) { $this->setData( $data['data'] ); }
  }
  
  public function __toString() {
    return $this->render();
  }
  
  function __get( $name ) {
    if( method_exists( $this, $name ) ) {
      return $this->$name();
    }
  }
  
  function hasAuthor( $author ) {
    return $this->author == $author;
  }

  function hasSubContent( $subContent ) {
    if( ! $subContent ) { return false; }
    return in_array( $subContent->cid, $this->children );
  }
  
  public function setData( $data ) {
    $this->data = $data;
    return $this;
  }
  
  public function getData() {
    return $this->data;
  }
  
  abstract public function editor();
  abstract public function render();
} 
