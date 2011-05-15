<?php

class AlbumContent extends Content {
  public function setData( $data ) {
    $this->data = unserialize($data);
  }
  
  public function render() {
    return $this->body;
  }
  
  public function __get( $prop ) {
    switch($prop) {
      case "key" : return Content::get($this->data['key'])->data; break;
      case "body": return $this->data['body'];
      default    : return "";
    }
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->cid}Raw" class="raw">
{$this->data->body}
</textarea><br>
EOT;
  }
}
