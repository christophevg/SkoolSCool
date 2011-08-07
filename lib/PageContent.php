<?php

class PageContent extends Content {
  var $body;

  private $replacements = array();

  public function __construct( $data = array() ) {
    parent::__construct( $data );

    $name = $data['id'];
    $this->body = isset( $data['body'] ) ? $data['body'] : 
                  $this->createDefaultBody($name);
  }
  
  public function createDefaultBody($name) {
    return str_replace( '%%name%%', $name, Config::$defaultPageBody );
  }
  
  public function toHash() {
    $hash = parent::toHash();
    $hash['body'] = $this->body;
    return $hash;
  }
  
  public function render() {
    return str_replace( array_keys  ($this->replacements),
                        array_values($this->replacements),
                        $this->body );
  }

  public function append($content) {
    return $this->body .= $content;
  }

  public function prepend($content) {
    return $this->body = $content . $this->body;
  }
  
  public function replace($find, $replace) {
    $this->replacements[$find] = $replace;
  }
  
  public function editor() {
    return <<<EOT
<textarea id="{$this->id}Raw" class="raw">
$this->body
</textarea><br>
EOT;
  }
}
