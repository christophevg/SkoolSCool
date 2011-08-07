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
    return "";
  }
  
  public function render() {
    return $this->body;
  }
}
