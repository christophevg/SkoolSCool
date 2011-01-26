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
<textarea id="bodyRaw" style="width:400px;height:100px;">
$this->data
</textarea><br>
<a href="#" onclick="previewBody();">preview</a> |
<a href="#" onclick="cancelBody();">cancel</a>
EOT;
  }
}
