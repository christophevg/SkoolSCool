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
      $date  = date("j M Y", $item->date );
      $lines = split( "\n", $item->body );
      $title = str_replace( "# ", "", $lines[0] );
      $html .= "<p>$date - <a href=\"nieuws/{$item->url}\">$title</a></p>";
    }
    return $html;
  }
  
  public function isHtml() { return true; }

  public function editor() {
    return "";
  }
}
