<?php

include_once dirname(__FILE__) . '/MockData.php';

class MockDriver implements Driver {
  public function __construct( $args ) {
    global $MockData;
    $this->data = $MockData[$args]; 
  }

  public function from( $table ) {
    $this->table = $this->data[$table];
  }
  
  public function get( $id ) {
    return $this->table[$id];
  }
}
