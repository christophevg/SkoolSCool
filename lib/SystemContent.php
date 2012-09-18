<?php

/**
 * SystemContent
 * interface for content rendered by the system. Currently made explictly
 * compatible with Content to allow polymorphic-like usage.
 * TODO: should be a sub-class of Content interface ;-(
 */

interface SystemContent {
  public function toHash();
  public function __toString();
  public function hasAuthor( $author );
  public function hasSubContent( $subContent );
  public function addChild( $childContent );
  public function hasTag( $tag );
  public function replace($find, $replace);
  public function isHtml();
  public function editor();
  public function render();
} 
