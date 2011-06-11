<?php

/**
 * Logging
 */

include_once dirname(__FILE__) . '/Singleton.php';

class Logger extends Singleton implements EventHandler {
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
EventBus::getInstance()->subscribe( Logger::getInstance() );
