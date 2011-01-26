<?php

class CommentContent extends Content {
  public function setData( $data ) {
    $this->data = $data;
  }
  
  public function render() {
    return $this->data;
  }
  
  public function editor() {
    return <<<EOT
<textarea style="">
$this->data;
</textarea>
EOT;
  }

}
