<?php

class NewsList extends Content {
  public function __construct( $data = array() ) {
    parent::__construct( $data );
  }
  
  public function render() {
    $isEmbedded = isset($_GET['embed']);
    $items = Objects::getStore('persistent')
      ->filter( "type", "NewsContent" )
      ->orderBy( "date", true )
      ->retrieve( $isEmbedded ? 10 : null );

    $html = "<h1>Nieuws</h1>";
    foreach( $items as $item ) {
      $date  = date("j M Y", $item->date );
      $lines = split( "\n", $item->body );
      $title = str_replace( "# ", "", $lines[0] );
      $html .= <<<EOT
<div class="news">
  <span class="date">$date -</span>
  <span class="item"><a href="nieuws/{$item->url}">$title</a></span>
</div>
EOT;
    }
    if( $isEmbedded ) {
      $html .= <<<EOT
<br><br>
<p class="more">
  <a href="nieuws">toon al het nieuws...</a>
</p>
EOT;
    }
    return $html;
  }
  
  public function isHtml() { return true; }

  public function editor() {
    return "";
  }
}
