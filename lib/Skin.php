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
  
  function show( $content = null ) {
    $this->content = $content;
    return $this;
  }

  function __toString() {
    return $this->applySkin( $this->content );
  }
  
  function __get( $name ) {
    if( method_exists( $this, $name ) ) {
      return $this->$name();
    }
  }
  
  function subcontent() {
    $subContent = "";
    foreach( $this->content->children as $child ) {
      $subContent .= $this->applySkin( Content::get($child), 'item' );
    }
    return $subContent;
  }

  private function applySkin( $content, $type = 'body' ) {
    $contentType = str_replace( 'Content', '' , get_class($content) );
    $skinMethod = $contentType . 'As' . ucfirst( $type );
    if( method_exists( $this, $skinMethod ) ) {
      return $this->$skinMethod( $content );
    } elseif( method_exists( $this, $type ) ) {
      return $this->$type( $content );
    } else {
      return (string)$content;
    }
  }

}
