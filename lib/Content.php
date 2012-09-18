<?php

// register a transient object cache store
Objects::addStore( 'transient', new SessionStore( 'ObjectCache' ) );

abstract class Content extends Object {
  var $author;
  var $children;
  var $tags;
  
  // this is bad, I know, but I prefer it to be in one place
  static $types = array( 'PageContent', 'NewsContent', 'HtmlContent' );

  // factory method to retrieve a content object.
  // depending on the option system prefix delegate it to a different helper
  static function get( $name = 'home', $alias = null ) {
    $object = strtolower(substr( $name, 0, 7 )) == 'system:' ? 
      Content::getSystem( substr( $name, 7 ) )
      :
      Content::getContent( $name );

    if( $object != null ) {
      // check if the current user is allowed to retrieve this content
      if( ! AuthorizationManager::getInstance()
              ->can( SessionManager::getInstance()->currentUser )
              ->read( $object ) )
      {
        // unathorized access ? reset the object
        $object = null;
      } else {
        // authorized access, prepare it for publication
        $object->replace( '{{id}}', $alias != null ? $alias : $object->id );
      }
    }

    return $object;
  }

  // get a system page by instantiating the class
  private static function getSystem( $name ) {
    $class = 'System'.ucfirst(strtolower($name));
    if( ! class_exists( $class ) ) { return null; }
    return new $class();
  }

  // gets actual content
  // try the persistent store first, if it is missing, maybe we're creating
  // new content and we have a draft in the transient store
  private static function getContent( $name = 'home', $alias = null ) {
    $object = Objects::getStore('persistent')->fetch( $name );
    if( $object == null ) {
      $object = Objects::getStore('transient')->fetch( $name );
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
    $this->children = isset( $data['children'] ) && is_array( $data['children'] ) ?
                      Objects::getStore('persistent')->fetch(split(',', $data['children'])) : array();
    $this->tags     = isset($data['tags']) ? 
                        split( ' ', $data['tags']) : array();
  }
  
  public function toHash() {
    $hash = parent::toHash();
    $children = array();
    foreach( $this->children as $child ) { 
      array_push( $children, $child->id );
    }
    $hash['children'] = join( ',', $children   );
    // toHash is used by ObjectStore to store objects
    // update access to tags is only available to admins
    // TODO: need better place for this, for now it protects us ;-)
    if( SessionManager::getInstance()->currentUser->isAdmin() ) {
      $hash['tags']     = join( ' ', $this->tags );
    }
    $hash['author']   = $this->author->id;
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
  
  function hasTag( $tag ) {
    return in_array( $tag, $this->tags );
  }

  public function replace($find, $replace) {}
  
  public function isHtml() { return false; }
  
  public function editor() {
    // all ContentTypes have tags, but only accessible to admins
    if( SessionManager::getInstance()->currentUser->isAdmin() ) {
      $tags = join( ' ', $this->tags );
      return <<<EOT
tags <input id="{$this->url}tags" class="tags" type="text" value="{$tags}"><br>
     <script> Editor.get( "{$this->url}" ).add( "tags" ); </script>
<br>
EOT;
    }
  }
  
  abstract public function render();
} 
