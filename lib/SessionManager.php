<?php

session_start();

class SessionManager extends Singleton implements EventPublisher {
  private $store;

  function init() {
    // perform dummy logout to initialise to anonymous when session has
    // no currentUser stored
    if( ! $this->currentUser ) { 
      $this->logout();
      EventBus::getInstance()
        ->publish( new Event( EventType::SECURITY, "new session", $this ));
    }
  }
  
  function login( $login = null, $pass = null ) {
    if( ! $this->currentUser->isAnonymous() ) { $this->logout(); }
    $user = User::get( $login );
    if( $user && $pass && $user->authenticate( $pass ) ) {
      $this->currentUser = $user;
      EventBus::getInstance()
        ->publish( new Event( EventType::SECURITY,
                              "{$this->currentUser->login} logged in",
                              $this ) );
    }
  }

  function logout() {
    if( $this->currentUser ) {
      EventBus::getInstance()
        ->publish( new Event( EventType::SECURITY,
                              "{$this->currentUser->login} logged out",
                              $this ) );
    }
    // destroy session
    $_SESSION = array();
    session_destroy();
    session_start();
    // retrieve an anonymous user
    $this->currentUser = User::get();
  }
  
  function __set( $key, $value ) {
    $_SESSION[$key] = $value;
  }

  function __get( $key ) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
  }
  
  function __toString() {
    return '[' . session_id() . ']';
  }
} 
