<?php

/**
 * Navigator
 *
 * The navigator parses the "navigation" content, which is a 2-level bullet
 * tree and provides information about it.
 */

class Navigator {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new Navigator();
      self::$instance->init();
    }
    return self::$instance;
  }

  private $sections = array();
  
  function init() {
    $lines = split("\n", (string)Content::get('navigation') );
    $section = "";
    foreach( $lines as $line ) {
      if( preg_match( "/^\*[^\*\[]*\[([^\]]*)\]/", $line, $matches ) ) {
        $section = $matches[1];
        $this->sections[$section] = array();
      }
      if( preg_match( "/^\*\*[^\[]*\[([^\]]*)\]/", $line, $matches ) ) {
        $subSection = $matches[0];
        $this->sections[$section][] = $subSection;
      }
    }
  }
  
  function currentSectionHasNavigation() {
    return $this->getCurrentSectionNavigation() != "";
  }
  
  function getCurrentSectionNavigation() {
    $currentSectionID = str_replace( "-", " ", Context::$request->url[0] );
    return array_key_exists($currentSectionID, $this->sections) ? 
      join( "\n", $this->sections[$currentSectionID] ) : "";
  }

  function getSectionOf( $page ) {
    return $this->tree[$this->reverse[$page]];
  }

}
