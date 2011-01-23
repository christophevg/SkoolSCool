<?php

class CommentContent extends Content {
  public function setData( $data ) {
    $this->data = $data;
  }
  
  public function render() {
    return $this->data;
  }
}
