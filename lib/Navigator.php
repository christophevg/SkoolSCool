<?php

/**
 * Navigator
 *
 * The navigator parses the "navigation" content, which is a 2-level bullet
 * tree and provides information about it.
 */

class Navigator extends Singleton {
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
    $currentSectionID = Context::getInstance()->path->getRootID();    
    $section = $this->sections[$currentSectionID];
    return $section ? join( "\n", $section ) : "";
  }

  function getSectionOf( $page ) {
    return $this->tree[$this->reverse[$page]];
  }

}
