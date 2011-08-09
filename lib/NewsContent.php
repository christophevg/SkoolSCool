<?php

class NewsContent extends PageContent {
  var $date;

  public function __construct( $data = array() ) {
    parent::__construct( $data );

    $this->date = isset($data['date']) ? $data['date'] : time();
  }
    
  public function toHash() {
    $hash = parent::toHash();
    $hash['date'] = $this->date;
    return $hash;
  }
  
  public function editor() {
    $data = date( "d/M/Y", $this->date );
    return "<input type=\"date\" value=\"{$date}\"><br><br>\n" 
         . parent::editor();
  }
}
