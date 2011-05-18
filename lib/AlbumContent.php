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
      case "key"  :
        return Content::get($this->data[$prop])->file;
        break;
      case "label":
      case "body" :
        return $this->data[$prop];
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
