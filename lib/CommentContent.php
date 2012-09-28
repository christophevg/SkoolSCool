<?php

class CommentContent extends Content {
  var $body;

  public function __construct( $data = array() ) {
    parent::__construct( $data );
    $this->body = $data['body'];
  }

  public function render() {
    return $this->body ? $this->body : "";
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->url}body" class="raw">
$this->body
</textarea><br>
<script> Editor.get( "{$this->url}" ).addBody( "body" ); </script>
EOT;
  }

}
