<?php

/**
 * DataBase Interface
 * Abstraction layer to the database
 */

include_once dirname(__FILE__) . '/Singleton.php';

// configuration should go in config mgmt solution
$DATASTORE = 'mock:devel';

class DBI extends Singleton {
  function init() {
    global $DATASTORE;
    list( $name, $args ) = split( ':', $DATASTORE );
    $dbiClass = ucfirst($name) . 'Driver';
    include_once dirname(__FILE__) . '/' . $dbiClass . '.php';
    $this->driver = new $dbiClass( $args );
  }
  
  function from( $table ) { 
    $this->driver->from( $table );
    return $this;
  }
  
  function get( $id ) { 
    return $this->driver->get( $id ); 
  }
}

interface Driver {
  public function __construct( $args );

  public function from( $table );
  public function get( $id );
}
