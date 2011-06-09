<?php

class CommentContent extends Content {
  public function render() {
    return $this->data;
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->cid}Raw" class="raw">
$this->data
</textarea><br>
EOT;
  }

}
