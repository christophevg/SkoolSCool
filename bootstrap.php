<?php

// short hand to SessionManager
$sm = SessionManager::getInstance();

// create initial structure for the transient object cache store
if( ! is_array( $sm->ObjectCache ) ) {
  $sm->ObjectCache = array();
}

// get an instance of the client and configure it
$openid = LightOpenIDClient::getInstance( Config::$server )
            ->withRequired( 'contact/email' )
            ->cacheInSession();

// process login post
if( isset($_POST['login']) && isset($_POST['pass']) ) {
  $sm->login( $_POST['login'], $_POST['pass'] );
}

// process logout get
if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
  $sm->logout();
  $openid->logoff();
  // remove session cookie
  unset( $_COOKIE['session'] );
  setcookie( 'session', '', time() - 42000, '/');
}

// if we have no current user but have an openID-based user try to log it on
if( $sm->currentUser->isAnonymous() 
    && $openid_user = $openid->getUser() ) 
{
  $sm->login_federated( $openid_user->identity );
  // if we have no current user now, the OpenID-based user is a new one,
  // point him to the registration popup
  if( $sm->currentUser->isAnonymous() ) {
    Messages::getInstance()->addWarning( I18N::$UNKNOWN_FEDERATED_LOGIN );
  }
}

// if we still don't have a user, try to restart an existing session
if( $sm->currentUser->isAnonymous()
    && isset( $_COOKIE['session'] ) && $_COOKIE['session'] != '' )
{
  $sm->login_federated( $_COOKIE['session'] );
}

// init Context
Context::init();
