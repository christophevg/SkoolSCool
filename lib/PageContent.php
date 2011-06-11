<?php

class PageContent extends Content {
  public function render() {
    return $this->data;
  }

  public function append($content) {
    return $this->data .= $content;
  }

  public function prepend($content) {
    return $this->data = $content . $this->data;
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->cid}Raw" class="raw">
$this->data
</textarea><br>
EOT;
  }
}
