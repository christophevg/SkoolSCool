<?php

session_start();

include_once dirname(__FILE__) . '/Singleton.php';

class SessionManager extends Singleton {
  function init() {
    // perform dummy logout to initialise to anonymous when session has
    // no currentUser stored
    if( ! $this->currentUser ) { $this->logout(); }
  }

  function login( $login = null, $pass = null ) {
    $this->logout();
    $user = User::get( $login );
    if( $user && $pass && $user->authenticate( $pass ) ) {
      $this->currentUser = $user;
    }
  }

  function logout() {
    // login as an anonymous user to logout
    $this->currentUser = User::get();
  }
  
  function __set( $key, $value ) {
    $_SESSION[$key] = $value;
  }

  function __get( $key ) {
    return $_SESSION[$key];
  }
} 

// process login post
if( $_POST['login'] && $_POST['pass'] ) {
  SessionManager::getInstance()->login( $_POST['login'], $_POST['pass'] );
}

// process logout get
if( $_GET['action'] == 'logout' ) {
  SessionManager::getInstance()->logout();
}