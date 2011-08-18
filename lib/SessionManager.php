<?php

session_start();

class SessionManager {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new SessionManager();
      self::$instance->init();
    }
    return self::$instance;
  }

  private $store;

  function init() {
    // perform dummy logout to initialise to anonymous when session has
    // no currentUser stored
    if( ! $this->currentUser ) { 
      $this->logout();
    }
  }
  
  function login( $login = null, $pass = null ) {
    if( ! $this->currentUser->isAnonymous() ) { $this->logout(); }
    $user = User::get( $login );
    if( $user && $pass && $user->authenticate( $pass ) ) {
      return $this->setUser($user);
    } else {
      Messages::getInstance()->addWarning( I18N::$FAILED_LOGON );
    }
  }
  
  function login_federated( $id ) {
    if( $identity = Identity::get( $id ) ) {
      if( $user = User::get( $identity->user ) ) {
        return $this->setUser($user);
      }
    }
    // if for some reason the login failed, reset the session to clear
    // everything
    $this->logout();
  }
  
  // When we successfully set a new user, post-login, we also create a new
  // session for one month/30 days
  private function setUser($user) {
    $this->currentUser = $user;
    if( $session = Session::create( $user ) ) {
      setCookie( "session", $session->id, time() + 3600 * 24 * 30 ,'/' );
    }
  }
  
  function logout() {
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
