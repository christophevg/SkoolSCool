<?php

class NewsList extends Content {
  public function __construct( $data = array() ) {
    parent::__construct( $data );
  }
  
  public function render() {
    $items = Objects::getStore('persistent')
      ->filter( "type", "NewsContent" )
      ->orderBy( "date", true )
      ->retrieve( isset($_GET['embed']) ? 10 : null );

    $html = "<h1>Nieuws</h1>";
    foreach( $items as $item ) {
      $date  = date("d/M/Y", $item->date );
      $title = str_replace( "-", " ", $item->id );
      $html .= "$date - <a href=\"nieuws/{$item->id}\">$title</a><br>";
    }
    return $html;
  }
  
  public function isHtml() { return true; }

  public function editor() {
    return "";
  }
}
