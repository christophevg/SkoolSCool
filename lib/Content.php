<?php

include_once dirname(__FILE__) . '/DBI.php';

abstract class Content {
  static function get( $name = 'default' ) {
    if( $data = DBI::getInstance()->from( 'content' )->get( $name ) ) {
      $contentClass = ucfirst($data['type']) . 'Content';
      include_once dirname(__FILE__) . '/' . $contentClass . '.php';
      return new $contentClass( $data );
    }
    return null;
  }
  
  public function __construct( $data ) {
    // common data
    $this->cid      = $data['cid'];
    $this->author   = User::get( $data['author'] );
    $this->time     = date( "d M Y (H:i:s)", $data['time'] );
    $this->children = $data['children'];
    // custom data
    $this->setData( $data['data'] );
  }
  
  public function __toString() {
    return $this->render();
  }
  
  function __get( $name ) {
    if( method_exists( $this, $name ) ) {
      return $this->$name();
    }
  }
  
  abstract public function editor();
  abstract public function setData( $data );
  abstract public function render();
} 
