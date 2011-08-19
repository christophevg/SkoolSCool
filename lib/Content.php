<?php

// register a transient object cache store
Objects::addStore( 'transient', new SessionStore( 'ObjectCache' ) );

abstract class Content extends Object {
  var $author;
  var $children;
  
  // this is bad, I know, but I prefer it to be in one place
  static $types = array( 'PageContent', 'NewsContent', 'HtmlContent' );

  // factory method to retrieve a content object.
  // first we look in the persisted objects, in the ObjectStore
  // then we look in the transient/session objects in the ObjectCache
  static function get( $name = 'home', $alias = null ) {
    $object = Objects::getStore('persistent')->fetch( $name );
    if( $object == null ) {
      $object = Objects::getStore('transient')->fetch( $name );
    }
    if( $object != null ) {
      $object->replace( '{{id}}', $alias != null ? $alias : $object->id );
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
    
    // else instantiate a fresh object and "store" it in the Transient Store
    $content = new $type( array( id => $name ) );
    Objects::getStore( 'transient' )->put( $content );
    return $content;
  }
  
  public function __construct( $data = array() ) {
    parent::__construct( $data );
    
    $this->author   = isset( $data['author'] ) ? 
                      User::get( $data['author'] ) : 
                      SessionManager::getInstance()->currentUser;
    $this->children = isset( $data['children'] ) ?
                      Objects::getStore('persistent')->fetch(split(',', $data['children'])) : array();
  }
  
  public function toHash() {
    $hash = parent::toHash();
    $hash['author'] = $this->author->id;
    $children = array();
    foreach( $this->children as $child ) { 
      array_push( $children, $child->id );
    }
    $hash['children'] = join( ',', $children );
    return $hash;
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
    return in_array( $subContent->id, $this->children );
  }
  
  public function addChild( $childContent ) {
    $this->children[] = $childContent;
    $this->persist();
  }
  
  public function replace($find, $replace) {}
  
  public function isHtml() { return false; }
  
  abstract public function editor();
  abstract public function render();
} 
