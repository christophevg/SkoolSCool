<?php

/**
 * ChangeLog
 * 
 * Subscribes to events and updates a ChangeLog page accordingly.
 */

include_once dirname(__FILE__) . '/Singleton.php';

class ChangeLog extends Singleton implements EventHandler {
  function handleEvent( $event ) {
    $changes = Content::get( 'changes' );
    $changes->setData( "* " . date( "H:i:s", $event->time ) .
                       " : {$event->type} to {$event->target->cid}\n" . 
                       $changes->getData() );
    $changes->persist();
  }
}

// register the ChangeLog with the eventbus (implicitly for ANY event)
// TODO: this must be changed to only to update events (future implementation)
EventBus::getInstance()->subscribe( ChangeLog::getInstance() );
