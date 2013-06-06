<?php

/**
 * bootstrap
 * sets up all required supporting services like:
 * - a transient objectCache to store objects in until they are removed or
 *   stored in the persistent store
 * - process login events
 * - process logout events
 * - process federated (openid and facebook) login events
 * - restart a cookie-based long-lived session
 * - start a URL-provided session (used for one-time logins)
 * @author Christophe VG
 */

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

// setup facebook connection
$facebook = new Facebook(array(
  'appId'  => Config::$facebookAppId,
  'secret' => Config::$facebookSecret
));

// process login post
if( isset($_POST['login']) && isset($_POST['pass']) ) {
  if( $sm->login( $_POST['login'], $_POST['pass'] ) != null ) {
    Logger::log("login using password");
  } else {
    Logger::log("login using password failed " .
                "for '{$_POST[login]}' with pass '{$_POST[pass]}'");
  }
}

// process logout get
if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
  Logger::log("requested log out");
  $sm->logout();
  $openid->logoff();
  // remove session cookie
  unset( $_COOKIE['session'] );
  setcookie( 'session', '', time() - 42000, '/');
  // remove XSFR session
  XSFR::clearSession();
  // redirect to home
  header("Location: home" );
}

function getFederatedUser() {
  global $openid, $facebook;
  if(isset($openid) && $user = $openid->getUser()) {
    return $user->identity;
  }
  if( $user = $facebook->getUser()) {
    return $user;
  }
  return false;
}

function getFederatedEmail() {
  global $openid;
  if(isset($openid) && $user = $openid->getUser()) {
    return $user->email;
  }
  return false;
}

function clearFederatedUser() {
  global $openid, $facebook;
  $openid->logoff();
  $openid = null; // FIXME: somehow Yahoo's log-in session is restarted
  $facebook->destroySession();
}

// if we have no current user but have a federated user try to log it on
if( $sm->currentUser->isAnonymous() && $federatedUser = getFederatedUser() ) {
  $hashedFederatedUser = md5($federatedUser);
  $federatedEmail = getFederatedEmail();
  $sm->login_federated( $hashedFederatedUser );
  // if we have no current user now, the federated user is unknown
  if( $sm->currentUser->isAnonymous() ) {
    Logger::log( "federated login failed using '$federatedUser' ($hashedFederatedUser) with email '$federatedEmail'" );
    Messages::getInstance()->addWarning( I18N::$UNKNOWN_FEDERATED_LOGIN );
  } else {
    Logger::log( "federated login using '$federatedUser' ($hashedFederatedUser) with email '$federatedEmail'");
    // redirect to clean home page (removes federated GET parameters)
    header('Location: home');
  }
  // clear it: we used it to log in, no longer of any use now.
  clearFederatedUser();
}

// if we still don't have a user, try to restart an existing session
if( $sm->currentUser->isAnonymous()
    && isset( $_COOKIE['session'] ) && $_COOKIE['session'] != '' )
{
  if( $sm->login_federated( $_COOKIE['session'] ) ) {
    Logger::log( "restart session" );
  } else {
    Logger::log( "restart session failed" );
  }
}

// if we still don't have a user, try to use a one time password
if( $sm->currentUser->isAnonymous() && isset($_GET['start']) ) {
  if( $sm->login_otp( $_GET['start'] ) ) {
    Logger::log( "one-time-pass login");
  } else {
    Logger::log( "one-time-pass login failed using '{$_GET['start']}'");
  }
}

// provide the browser/user with a cookie-based anti-XSFR session/uid
if( ! $sm->currentUser->isAnonymous() ) {
  XSFR::ensureSession();
}

// if a user is logged in and we have a federated user and they aren't connected
// through an identity AND we have an explicit request to connect them
// => create an identity
if( ! $sm->currentUser->isAnonymous() && $federatedUser = getFederatedUser() ) {
  // if we already have an identity check it ...
  $hashedFederatedUser = md5($federatedUser);
  $federatedEmail = getFederatedEmail();
  if( $identity = Identity::get( $hashedFederatedUser ) ) {
    if( $sm->currentUser == User::get( $identity->user ) ) {
      Messages::getInstance()->addInfo("Online profiel reeds gekend.");
      Logger::log("federated login already known for '$federatedUser' ($hasedFederatedUser) with email '$federatedEmail'");
    } else {
      Messages::getInstance()->addCritical("Online profiel kan niet 2x gelinkt worden." );
      Logger::log("federated login alternate entry '$federatedUser' ($hasedFederatedUser) with email '$federatedEmail'");
    }
    // clear it: we're already logged in, we can't do anything with this info
    clearFederatedUser();
  } else {
    if( isset($_GET['action']) && $_GET['action'] = 'link-profile'  ) {
      Objects::getStore( 'persistent' )
        ->put( new Identity(array('id' => $hashedFederatedUser,
                                  'user' => $sm->currentUser->id)));
      Messages::getInstance()->addInfo("Online profiel succesvol gelinkt." );
      Logger::log("federated login linked '$federatedUser' ($hashedFederatedUser) with email '$federatedEmail'");
      clearFederatedUser();
    } elseif( isset($_GET['action']) && $_GET['action'] = 'cancel-link-profile'  ) {
      Messages::getInstance()->addInfo("Online profiel werd NIET gelinkt." );
      Logger::log("federated login linking canceled '$federatedUser' ($hashedFederatedUser) with email '$federatedEmail'");
      clearFederatedUser();
    } else {
      if( Request::getInstance()->id != 'link profiel' ) {
        header("Location: link-profiel");
        exit();
      }
    }
  }
}

// init Context
Context::init();
