<?php

/**
 * Logging
 */

class Logger implements EventHandler {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new Logger();
      self::$instance->init();
    }
    return self::$instance;
  }

  function init() {
    $this->fp = fopen( 'skoolscool.log', 'a' );
  }
  
  function __destruct() {
    fclose( $this->fp );
  }
  
  function handleEvent( $event ) {
    fwrite( $this->fp, (string)$event . "\n" );
  }
}

// register the logger with the eventbus (implicitly for ANY event)
// EventBus::getInstance()->subscribe( Logger::getInstance() );
