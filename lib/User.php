<?php

/**
 * User
 * A user object represents any human person related to the website. It can be
 * a visitor, an author, ...
 * It is linked to the global DataBase, from where it retrieves its 
 * information. If a requested user is not known, a default "anonymous" user
 * object will be returned.
 * Creation of objects is only available through the factory method "get".
 */

include_once dirname(__FILE__) . '/DBI.php';

class User {
  /**
   * Factory method to create a user object.
   * @param (optional) $name of the user. when omitted, the default/anonymous
   *        user object will be returned.
   * @return User object
   */
  static function get( $name = null ) {
    if( $name ) { $data = DBI::getInstance()->from( 'users' )->get( $name ); }
    if( ! $data ) { $data = array( 'name' => 'anonymous' ); }
    return new User( $data );
  }
  
  /**
   * Private constructor.
   * @param $data hash containing user information
   */
  final private function __construct( $data ) {
    $this->name = $data['name'];
    $this->pass = $data['pass'];
  }
  
  /**
   * Renders the User as a string
   * @return String representing the user.
   */
  final public function __toString() {
    return $this->name;
  }
  
  /**
   * Indicates whether the user object represents an anonymous user
   * @return Boolean indicating whether the user is an anonymous user.
   */
  function isAnonymous() {
    return $this->name == 'anonymous';
  }
  
  /**
   * Checks if a supplied pass matches the user's pass.
   * @param $pass to validate
   * @return Boolean indicating whether the pass is valid.
   */
  function authenticate( $pass ) {
    return $this->pass == md5($pass);
  }
}
