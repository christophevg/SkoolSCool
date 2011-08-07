<?php

class PictureContent extends Content {
  public function render() {
    return $this->label;
  }
  
  public function __get( $prop ) {
    switch($prop) {
      case "file":
      case "label":
        return $this->$prop;
    }
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->id}Raw" class="raw">
$this->data
</textarea><br>
EOT;
  }
}
