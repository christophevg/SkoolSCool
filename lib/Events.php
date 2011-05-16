<?php

/**
 * Events
 * Publish/Subscriber system for internal event-message based inter-component
 * loose kind of communication.
 */

include_once dirname(__FILE__) . '/Singleton.php';

class EventType {
  const ANY = "ANY";

  const SECURITY = "SECURITY";
}

class Event {
  private $type;
  private $sender;
  private $msg;

  function __construct( $type, $sender, $msg ) {
    $this->type   = $type;
    $this->sender = $sender;
    $this->msg    = $msg;
  }

  function __get($prop) {
    switch($prop) {
      case 'type':
      case 'sender':
      case 'msg':
        return $this->$prop; 
        break;
      default:
        return null;
    }
  }
}

class EventBus extends Singleton {
  private $subscriptions;
  
  function init() {
    $this->subscriptions = array( EventType::ANY      => array(),
                                  EventType::SECURITY => array() );
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
    foreach( $this->subscriptions[$event->type] as $subscriber ) {
      if( ! in_array( $subscriber, $this->subscriptions[EventType::ANY] ) ) {
        $subscriber->handleEvent($event);
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
