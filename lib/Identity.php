<?php

/**
 * Identity
 * An object that represents a federated identity, linking it to an interal
 * user account.
 * Creation of objects is only available through the factory method "get".
 */

class Identity extends Object {
  var $user;
  
  /**
   * Factory method to create an identity object.
   * @param unique identifier provided by the federated identity provider
   * @return Identity object
   */
  static function get( $id ) {
    return Objects::getStore('persistent')->fetch( $id );
  }
  
  /**
   * Constructor.
   * @param $data hash containing user information
   */
  function __construct( $data = array() ) {
    parent::__construct( $data );
    
    $this->user   = isset( $data['user']   ) ? $data['user']  : null;
  }
  
  function toHash() {
    $hash = parent::toHash();
    $hash['user']   = $this->user;
    return $hash;
  }
  
  function __get($property) {
    switch($property) {
      case 'user':
        return $this->user;
        break;
      case 'id':
        return $this->id;
    }
    return "";
  }
}
