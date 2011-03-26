<?php

class PageContent extends Content {
  public function setData( $data ) {
    $this->data = $data;
  }
  
  public function render() {
    return $this->data;
  }
  
  public function editor() {
    return <<<EOT
<textarea id="bodyRaw">
$this->data
</textarea><br>
EOT;
  }
}
