<?php

/**
 * Events
 * Publish/Subscriber system for internal event-message based inter-component
 * loose kind of communication.
 */

include_once dirname(__FILE__) . '/Singleton.php';

class EventType {
  const ANY        = "ANY";
  const SECURITY   = "SECURITY";
  const NAVIGATION = "NAVIGATION";
}

class Event {
  // private to disallow external "tampering" ;-) getter below provides read
  // access
  private $time;
  private $type;
  private $sender;
  private $msg;
  private $target;

  function __construct( $type, $sender, $msg, $target = null ) {
    $this->time   = time();
    $this->type   = $type;
    $this->sender = $sender;
    $this->msg    = $msg;
    $this->target = $target;
  }

  function __get($prop) {
    switch($prop) {
      case 'time':
      case 'type':
      case 'sender':
      case 'msg':
      case 'target':
        return $this->$prop; 
        break;
      default:
        return null;
    }
  }
  
  function __toString() {
    return  date("d/m/Y H:i:s", $this->time ) . " " . 
            $this->type . " " . 
            ( is_null($this->sender) ? "app" : $this->sender ) . " " . 
            $this->msg .
            ( is_null($this->target) ? "" : " " . $this->target );
  }
}

class EventBus extends Singleton {
  private $subscriptions;
  
  function init() {
    $this->subscriptions = array( EventType::ANY        => array(),
                                  EventType::SECURITY   => array(),
                                  EventType::NAVIGATION => array() );
  }
  
  function subscribe( $handler, $eventType = EventType::ANY ) {
    array_push( $this->subscriptions[$eventType], $handler );
  }
  
  function publish( $event ) {
    // notify those interested in "all"
    foreach( $this->subscriptions[EventType::ANY] as $subscriber ) {
      $subscriber->handleEvent($event);
    }
    // notify those specific interested in this type of event, unless they
    // where already notified based on any
    if( isset($this->subscriptions[$event->type]) ) {
      foreach( $this->subscriptions[$event->type] as $subscriber ) {
        if( !in_array( $subscriber, $this->subscriptions[EventType::ANY] ) ) {
          $subscriber->handleEvent($event);
        }
      }
    }
  }
}

interface EventHandler {
  public function handleEvent( $event );
}

interface EventPublisher {
  public function __toString(); 
}
