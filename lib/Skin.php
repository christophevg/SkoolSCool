<?php

abstract class Skin {
  static function get( $name = 'default' ) {
    $skinFile = dirname(__FILE__) . "/../skins/$name.php";
    if( file_exists( $skinFile ) ) {
      include_once( $skinFile );
      $skinClass = ucfirst($name) . 'Skin';
      return new $skinClass();
    } else {
      print "unknown skinFile : $skinFile";
    }
    return null;
  }
  
  function to( $user = null ) {
    $this->user = $user;
    return $this;
  }
  
  function show( $content = "" ) {
    if( ! is_array( $this->contents ) ) { $this->contents = array(); }
    array_push( $this->contents, $content );
    return $this;
  }

  function __toString() {
    $out = $this->applySkin();
    array_pop( $this->contents );
    return $out;
  }
  
  function content() {
    return $this->contents[count($this->contents)-1];
  }
  
  function __get( $name ) {
    if( method_exists( $this, $name ) ) {
      return $this->$name();
    }
  }
  
  function subContent() {
    $subContent = "";
    foreach( $this->content->children as $child ) {
      $subContent .= $this->show( Content::get($child) );
    }
    return $subContent;
  }

  private function applySkin() {
    $type = count($this->contents) > 1 ? 'item' : 'body';
    $contentType = str_replace( 'Content', '' , get_class($content) );
    $skinMethod = $contentType . 'As' . ucfirst( $type );
    if( method_exists( $this, $skinMethod ) ) {
      return $this->$skinMethod();
    } elseif( method_exists( $this, $type ) ) {
      return $this->$type();
    } else {
      return (string)$this->content;
    }
  }

}
