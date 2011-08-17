<?php

// create initial structure for the transient object cache store
if( ! is_array( SessionManager::getInstance()->ObjectCache ) ) {
  SessionManager::getInstance()->ObjectCache = array();
}

// get an instance of the client and configure it
$openid = LightOpenIDClient::getInstance( Config::$server )
            ->withRequired( 'contact/email' )
            ->cacheInSession();

// process login post
if( isset($_POST['login']) && isset($_POST['pass']) ) {
  SessionManager::getInstance()->login( $_POST['login'], $_POST['pass'] );
}

// process logout get
if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
  SessionManager::getInstance()->logout();
  $openid->logoff;
}

// if we have no current user but have an openID-based user try to log it on
if( SessionManager::getInstance()->currentUser->isAnonymous() 
    && $openid_user = $openid->getUser() ) 
{
  SessionManager::getInstance()->login_federated( $openid_user->identity );
  // if we have no current user now, the OpenID-based user is a new one,
  // point him to the registration popup
  // TODO: later
}

// init Context
Context::init();
