<?php

/**
 * Singleton that acts as a PHP API to messages that can be displayed to the
 * web-client user.
 */

class Messages {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new Messages();
      self::$instance->init();
    }
    return self::$instance;
  }

  private $store;

  var $messages;

  function init() {
    $this->messages = array();
  }
  
  const INFO     = 'info';     // positive feedback
  const WARNING  = 'warning';  // problem, but not-fatal
  const CRITICAL = 'critical'; // fatal problem, operation failed
  
  function add( $message, $type = Messages::INFO ) {
    $this->messages[] = array( 'type' => $type, 'body' => $message );
  }
  
  function addInfo( $message ) {
    $this->add( $message );
  }

  function addWarning( $message ) {
    $this->add( $message, Messages::WARNING );
  }

  function addCritical( $message ) {
    $this->add( $message, Messages::CRITICAL );
  }
  
  function asHtml() {
    $script = "";
    foreach( $this->messages as $message ) {
      $type = ucfirst($message['type']);
      $body = $message['body'];
      $script .= "Messages.add{$type}('$body');\n";
    }
    return "<script>\n$script\n</script>\n";
  }
}
