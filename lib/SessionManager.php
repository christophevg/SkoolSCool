<?php

session_start();

include_once dirname(__FILE__) . '/Singleton.php';

class SessionManager extends Singleton implements EventPublisher {
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
    EventBus::getInstance()
      ->publish( new Event( EventType::SECURITY,
                            "{$this->currentUser->login} logged out",
                            $this ) );
    // login as an anonymous user to logout
    $this->currentUser = User::get();
    // clean up session a bit
    session_regenerate_id();
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

// process login post
if( isset($_POST['login']) && isset($_POST['pass']) ) {
  SessionManager::getInstance()->login( $_POST['login'], $_POST['pass'] );
}

// process logout get
if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
  SessionManager::getInstance()->logout();
}
