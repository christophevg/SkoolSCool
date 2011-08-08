<?php

/**
 * ChangeLog
 * 
 * Subscribes to events and updates a ChangeLog page accordingly.
 */

class ChangeLog implements EventHandler {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new ChangeLog();
    }
    return self::$instance;
  }

  function handleEvent( $event ) {
    $changes = Content::get( 'changes' );
    if( ! $changes ) { $changes = $this->create(); }
    $changes->prepend( "* " . (string)$event ."\n" );
    $changes->persist();
  }
  
  private function create() {
    $changes = new PageContent( array( id     => 'changes', 
                                       author => User::get('system'),
                                       body   => "# Changelog\n\n" ) );
    return Objects::getStore('persistent')->put( $changes );
  }
}

// register the ChangeLog with the eventbus (implicitly for ANY event)
// TODO: this must be changed to only to update events (future implementation)
// EventBus::getInstance()->subscribe( ChangeLog::getInstance() );
