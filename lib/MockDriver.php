<?php

include_once dirname(__FILE__) . '/MockData.php';

class MockDriver implements Driver {
  public function __construct( $set ) {
    $this->set = $set;
  }
  
  public function from( $table ) {
    $this->table = $table;
  }
  
  public function get( $id ) {
    return SessionManager::getInstance()
      ->MockData[$this->set][$this->table][$id];
  }

  public function in( $table ) {
    $this->table = $table;
  }
  
  public function set( $id, $data ) {
    $all = SessionManager::getInstance()->MockData;
    $all[$this->set][$this->table][$id]['data'] = $data;
    SessionManager::getInstance()->MockData = $all;
  }
}
