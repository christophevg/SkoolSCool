<?php

/**
 * Navigator
 *
 * The navigator parses the "navigation" content, which is a 2-level bullet
 * tree and provides information about it.
 */

class Navigator extends Singleton {
  private $tree = array();
  private $reverse = array();
  
  function init() {
    $lines = split("\n", (string)Content::get('navigation') );
    $section = "";
    foreach( $lines as $line ) {
      if( preg_match( "/^\*[^\*\[]*\[([^\]]*)\]/", $line, $matches ) ) {
        $section = $matches[1];
        $this->tree[$section] = array();
      }
      if( preg_match( "/^\*\*[^\[]*\[([^\]]*)\]/", $line, $matches ) ) {
        $subSection = $matches[1];
        $this->tree[$section][] = $subSection;
        $this->reverse[$subSection] = $section;
      }
    }
  }

  function getSectionOf( $page ) {
    return $this->tree[$this->reverse[$page]];
  }

}
