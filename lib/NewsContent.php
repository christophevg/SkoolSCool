<?php

class NewsContent extends PageContent {
  var $date;

  public function __construct( $data = array() ) {
    parent::__construct( $data );

    $this->date = isset($data['date']) & $data['date'] > 1 ? 
      $data['date'] : time();
  }
    
  public function toHash() {
    $hash = parent::toHash();
    $hash['date'] = $this->date;
    return $hash;
  }
  
  public function editor() {
    $date = date( "j-m-Y", $this->date );
    return <<<EOT
<input id="{$this->url}date" type="text" value="{$date}"> (d-m-jjjj)<br>
<script> Editor.get( "{$this->url}" ).add( "date" ); </script>
<br>
EOT
    . parent::editor();
  }
}
