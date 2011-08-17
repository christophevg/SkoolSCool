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

class User extends Object {
  var $name;
  var $pass;
  var $email;
  var $rights;
  
  /**
   * Factory method to create a user object.
   * @param (optional) $name of the user. when omitted, the default/anonymous
   *        user object will be returned.
   * @return User object
   */
  static function get( $name = null ) {
    if( is_null($name) ) { return new User( array( 'name' => 'anonymous' ) ); }

    if( ! $object = Objects::getStore('persistent')->fetch( $name ) ) {
      $object = new User( array( 'name' => 'anonymous' ) );
    }
    return $object;
  }
  
  /**
   * Constructor.
   * @param $data hash containing user information
   */
  function __construct( $data = array() ) {
    parent::__construct( $data );
    
    $this->name   = isset( $data['name']   ) ? $data['name']  : $this->id;
    $this->pass   = isset( $data['pass']   ) ? $data['pass']  : null;
    $this->email  = isset( $data['email']  ) ? $data['email'] : null;
    $this->rights = isset( $data['rights'] ) ?
                    split( ',', $data['rights'] ) : array();
  }
  
  function toHash() {
    $hash = parent::toHash();
    $hash['name']   = $this->name;
    $hash['pass']   = $this->pass;
    $hash['email']  = $this->email;
    $hash['rights'] = join(',', $this->rights );
    return $hash;
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
      case 'id':
      case 'login':      
        return $this->id;
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
