<?php

/**
 * Class to manage long-lived sessions.
 */

class Session extends Object {
  var $user;

  /**
   * Factory method to retrieve an existing session object.
   * @param unique identifier for the session
   * @return Session object
   */
  static function get( $id ) {
    return Objects::getStore('persistent')->fetch( $id );
  }
  
  /**
   * Factory method to create a new session for a user
   */
  static function create( $user ) {
    return Objects::getStore('persistent')
      ->put(new Session( array( id => self::makeId(),
                                user => $user->id ) ) );
  }

  // generates a random string
  static function makeId() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
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
