<?php

/**
 * HtmlContent
 * Content-class that simply stores or generates HTML, based on PageContent.
 */

class HtmlContent extends PageContent {
  public function __construct( $data = array() ) {
    parent::__construct( $data );
  }
  
  public function isHtml() { 
    return true;
  }

  public function createDefaultBody($name) {
    return str_replace( '%%name%%', $name, Config::$defaultHtmlBody );
  }

  public function render() {
    return $this->body;
  }
}
