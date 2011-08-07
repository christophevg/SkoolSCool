<?php

class AlbumContent extends Content {
  public function render() {
    return $this->body;
  }
  
  public function __get( $prop ) {
    switch($prop) {
      case "key"  :
        return Content::get($this->$prop)->file;
        break;
      case "label":
      case "body" :
        return $this->$prop;
    }
  }

  public function editor() {
    return <<<EOT
<textarea id="{$this->id}Raw" class="raw">
{$this->data->body}
</textarea><br>
EOT;
  }
}
