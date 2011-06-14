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

class User {
  /**
   * Factory method to create a user object.
   * @param (optional) $name of the user. when omitted, the default/anonymous
   *        user object will be returned.
   * @return User object
   */
  static function get( $name = '' ) {
    if( $data = Objects::getStore('persistent')
          ->from('users')->fetchData( $name ) )
    {
      $object = new User( $data );
    } else {
      $object = new User( array( 'name' => 'anonymous' ) );
    }
    return $object;
  }
  
  /**
   * Private constructor.
   * @param $data hash containing user information
   */
  function __construct( $data = array() ) {
    $this->login  = isset($data['login']) ? $data['login'] : $data['name'];
    $this->name   = $data['name'];
    $this->pass   = isset( $data['pass'] ) ? $data['pass'] : null;
    $this->email  = isset( $data['email'] ) ? $data['email'] : null;
    $this->rights = isset( $data['rights'] ) ?
                      split( ',', $data['rights'] ) : array();
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
  
  function isContributor() {
    return $this->hasRight('contributor');
  }

  function isAdmin() {
    return $this->hasRight('admin');
  }
  
  function hasRight($right) {
    return in_array( $right, $this->rights );
  }
  
  function __get($property) {
    switch($property) {
      case 'role':
        return $this->isAdmin() ? "admin" :
          ( $this->isContributor() ? "constributor" : "" );
        break;
      case 'login':
        return $this->login;
    }
    return "";
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
