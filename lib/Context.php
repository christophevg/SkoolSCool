<?php

/**
 * Context
 * A central static object representing the context in which a given request 
 * is processed.
 * It contains a request object that can be queried for information about
 * the current request.
 */

class Context {
  static $request;     
  static $currentUser;

  static function init() {
    // populate context
    self::$request     = Request::getInstance();
    self::$currentUser = SessionManager::getInstance()->currentUser;
  }
}
