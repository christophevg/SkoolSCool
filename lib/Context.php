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
  var $page;

  function handleEvent( $event ) {
    if( is_array( $event->sender ) ) {
      $this->path = $event->sender;
    } elseif( get_class( $event->sender ) == "PageContent" ) {
      $this->page = $event->sender;
    }
  }
  
  function asArray() {
    return $this->path;
  }

  function getRootID() {
    return str_replace( "-", " ", count($this->path) > 0 ? 
                        $this->path[0] : $this->page->cid );
  }
  
  function asString() {
    return join( "/", $this->asArray() ) . "/" . $this->page->cid;
  }
}
