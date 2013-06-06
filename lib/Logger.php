<?php

/**
 * Logger Class
 * Wrapper around KLogger that takes care of user information,...
 *
 * @author Christophe VG
 */

class Logger {
  // setup the logger
  static $logger;
  
  static function init() {
    self::$logger = new KLogger(Config::$logdir, Config::$loglevel);
  }
  
  // create wrapper function
  function log( $msg ) {
    $user = SessionManager::getInstance()->currentUser;
    if($user->isAnonymous()) {
      self::$logger->logDebug( "[anonymous] $msg" );
    } else {
      self::$logger->logDebug( "[{$user->name}] $msg" );
    }
  }
}

Logger::init();
