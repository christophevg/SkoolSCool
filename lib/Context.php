<?php

/**
 * Context
 * A central object representing the context in which a given request is
 * processed. It registers a lot of handlers with the EventBus to receive
 * information about what is happening within the application. This
 * information is correlated and the resulting contextual state is then
 * offered to the application for querying.
 */

class Context {
  static $singleton;
  
  var $items = array();
  
  function getInstance() {
    if( ! isset(self::$singleton) ) {
      self::$singleton = new Context();
      self::$singleton->init();
    }
    return self::$singleton;
  }

  private function __construct() {}
  
  function init() {
    foreach( array( "path" ) as $item ) {
      $builderName = ucfirst($item) . "Builder";
      $builder = new $builderName();
      $this->items[$item] = $builder;
    }
  }
  
  function refresh() {
    foreach( $this->items as $name => $builder ) {
      $builder->registerWith( EventBus::getInstance() );
    }
  }
  
  function __get($item) {
    return $this->items[$item];
  }
}

abstract class ContextBuilder implements EventHandler {
  function registerWith( $eventBus ) {
    foreach( $this->getEventTypes() as $type ) {
      $eventBus->subscribe( $this, $type );
    }
  }

  abstract function getEventTypes();
}

/**
 * PathBuilder looks at ContentRequest Events and builds a path leading up to
 * the currently rendered object.
 */
class PathBuilder extends ContextBuilder {
  
  function getEventTypes() {
    return array( EventType::NAVIGATION );
  }
  
  var $path = array();

  function handleEvent( $event ) {
    // clean up path up to the parent of the object/sender of the new content
    $this->findParent( $event->target );
    // add the new content on top
    array_push( $this->path, $event->target );
  }

  private function findParent( $child ) {
    if( count( $this->path ) < 1 ) { return; }
    $parent = $this->path[count($this->path)-1];
    if( ! $parent instanceof Content or ! $parent->hasSubContent( $child ) ) {
      array_pop( $this->path );
      return $this->findParent( $child );
    }
  }
  
  function getPath() {
    return $this->path;
  }
}
