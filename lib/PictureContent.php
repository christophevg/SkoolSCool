<?php

class PictureContent extends Content {
  public function setData( $data ) {
    $this->data = unserialize($data);
  }
  
  public function getData() {
    return $this->data;
  }
  
  public function render() {
    return $this->label;
  }
  
  public function __get( $prop ) {
    switch($prop) {
      case "file":
      case "label":
        return $this->data[$prop];
    }
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->cid}Raw" class="raw">
$this->data
</textarea><br>
EOT;
  }
}
