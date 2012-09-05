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
    $html = "";
    $id = 0;
    foreach( $this->messages as $message ) {
      $id++;
      $html .= <<<EOT
<div id="message-$id" 
     class="message {$message['type']}"
     onclick="javascript:this.style.display='none';">
{$message['body']}
</div>
EOT;
    }
    return <<<EOT
<div id="all-messages" class="messages">
  $html
</div>
<script>
  window.setTimeout( function() { 
    document.getElementById('all-messages').style.display = "none";
  }, 5000 );
</script>
EOT;
  }
}
