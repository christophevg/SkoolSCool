<?php

class XSFR {
  
  public static $key = "CSFR-Request";
  
  public static function ensureSession() {
    // set a fresh xfsr value for this session IF the user has logged on
    // a xfsr session is only "useful" for logged-on users because only
    // logged-on users can "change" objects
    if( ! SessionManager::getInstance()->currentUser->isAnonymous() && 
        ! isset($_COOKIE[self::$key]) )
    {
      setcookie( self::$key, Session::makeId(), 0, '/' );
    }
  }

  public static function clearSession() {
    setcookie( self::$key, '', time() - 4800, '/' );
  }
  
  public static function validateRequest($request) {
    return isset($_COOKIE[self::$key]) && 
           $_COOKIE[self::$key] == $request;
  }

}
