<?php

/**
 * Skin
 *
 * This abstract class is the base class for all skins. It implements the
 * external interface that's being called from the index.php driver.
 * Although the skin provides content rendering by default, in normal
 * circumstances, an implementing Skin will at least provide an implementation
 * for the body() and item() methods.
 */
abstract class Skin {
  /**
   * Factory Method to get a skin object based on its name.
   */
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
  
  /**
   * Chaining method to set the user to which the content will be shown,
   * using this skin.
   */
  function to( $user = null ) {
    $this->user = $user;
    return $this;
  }

  /**
   * Chaining method to set the content that will be shown, using this skin.
   */
  function show( $content = "" ) {
    if( ! is_array( $this->contents ) ) { $this->contents = array(); }
    array_push( $this->contents, $content );
    return $this;
  }

  /**
   * In a string context, the skin is applied to the currently top content.
   */
  function __toString() {
    $out = $this->applySkin();
    array_pop( $this->contents );
    return $out;
  }
  
  // below this point are internal functions

  /**
   * Returns the currently top-most content on the skinning stack.
   */
  protected function content() {
    return $this->contents[count($this->contents)-1];
  }

  /**
   * Returns the assembled rendering of all sub-content items.
   */
  protected function subContent() {
    $subContent = "";
    foreach( $this->content->children as $child ) {
      $subContent .= $this->show( Content::get($child) );
    }
    return $subContent;
  }

  /**
   * A skin can reference methods through a getter interface.
   */
  function __get( $name ) {
    if( method_exists( $this, $name ) ) {
      return $this->$name();
    }
  }

  /**
   * Applies this skin to the currently topmost content. Based on the type
   * a different method is chosen if it is available.
   */
  private function applySkin() {
    // if there is only 1 content object on the stack, it's a body, else
    // we're dealing with sub-items.
    $type = count($this->contents) > 1 ? 'item' : 'body';

    $contentType = str_replace( 'Content', '' , get_class($this->content) );
    $skinMethod = $contentType . 'As' . ucfirst( $type );

    if( method_exists( $this, $skinMethod ) ) {  // e.g. CommandAsItem
      return $this->$skinMethod();
    } elseif( method_exists( $this, $type ) ) {  // e.g. item
      return $this->$type();
    } else {
      return (string)$this->content;             // just render the content
    }
  }
}
