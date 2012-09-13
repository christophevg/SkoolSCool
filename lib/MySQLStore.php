<?php

/**
 * MySQLStore
 * ObjectStore implementation on top of MySQL
 */

class MySQLStore implements ObjectStore {
  private $dbh;

  private $user;
  private $pass;
  private $dbname;
  
  private $fetchStmt; // prepared fetch statement to retrieve objects by id
  
  private $filters;
  private $orderBy;
  private $order;

  /**
   * The constructor accepts the name of the database instance along with a
   * username and password and sets up the connection
   */
  public function __construct( $dbname, $user, $pass ) {
    $this->dbname = $dbname;
    $this->user   = $user;
    $this->pass   = $pass;
    $this->clear();
    $this->connect();
  }
  
  private function connect() {
    $this->dbh = new PDO( "mysql:host=127.0.0.1;dbname={$this->dbname}", 
                          $this->user, $this->pass,
                          array( PDO::ATTR_PERSISTENT => true ) );
    $this->fetchStmt =
      $this->dbh->prepare( 'SELECT * FROM objects WHERE id = ?' );
  }
  
  public function __sleep() {
    return array( 'dbname', 'user', 'pass' );
  }
  
  public function __wakeup() {
    $this->connect();
  }
  
  /**
   * Method to fetch a single object or multiple objects based on it's ID.
   */
  public function fetch( $id ) {
    if( is_array( $id ) ) {
      $objects = array();
      foreach( $id as $i ) {
        array_push( $objects, $this->fetch($i) );
      }
      return $objects;
    } else {
      $this->fetchStmt->execute( array( $id ) );      
      $row = $this->fetchStmt->fetch();

      // syslog(LOG_WARNING, "retrieved object : " . $id );

      return $this->constructObject( $row );
    }
  }
  
  private function constructObject( $data ) {
    if( isset( $data['type'] ) ) {
      $object = new $data['type']( $data );
      return $object->setStore($this);
    }
    return null;
  }

  public function put( $object ) {
    $object->setStore($this);
    
    // TODO: preparing stmt can be put in a caching factory
    $props        = array( ':type' => get_class( $object ) );
    $columns      = array( 'type' );
    $placeholders = array( ':type' );
    foreach( $object->toHash() as $prop => $value ) {
      $props[":$prop"] = $value;
      array_push( $columns,      $prop    );
      array_push( $placeholders, ":$prop" );
    }

    $stmt = $this->dbh->prepare( 'INSERT INTO allObjects ( ' . 
                                 join( ', ', $columns ) . 
                                 ' ) VALUES ( ' .
                                 join( ', ', $placeholders ) . ' );' );
    if( $stmt->execute( $props ) === false ) {
      print_r( $stmt->errorInfo() );
    }

    return $object;
  }
  
  public function filter( $property, $value ) {
    $this->filters[$property] = $value;
    return $this;
  }
  
  public function orderBy( $by, $desc ) {
    $this->orderBy = $by;
    $this->order   = $desc;
    return $this;
  }
  
  public function retrieve($limit) {
    $clauses = array();
    // TODO: preparing stmt can be put in a caching factory
    foreach( $this->filters as $column => $value ) {
      $clauses[] = "$column = \"$value\"";
    }
    $orderBy = isset($this->orderBy) ? 
      " ORDER BY {$this->orderBy}" . ( $this->order ? " DESC" : "") : "";
    $limit = isset($limit) ? " LIMIT $limit" : "";

    $stmt = $this->dbh->prepare( 'SELECT * FROM objects WHERE ' . 
                                 join( ' AND ', $clauses ) .
                                 $orderBy .
                                 $limit );

    if( $stmt->execute() === false ) {
      print_r( $stmt->errorInfo() );
    }

    $this->clear();

    // fetch all rows, and construct objects
    $rows = $stmt->fetchAll();
    $objects = array();
    foreach( $rows as $row ) {
      array_push( $objects, $this->constructObject( $row ) );
    }

    // syslog(LOG_WARNING, "fetched objects : " . count($objects) );
    return $objects;
  }
  
  public function clear() {
    $this->filters = array();
    $this->orderBy = null;
    $this->order   = null;
  }
}

// create a transparant SessionCache, wrapping the actual MySQLStore
// and register that as the persistent store for Objects
Objects::addStore( 'persistent',
                   new SessionCache( new MySQLStore( Config::$dbname,
                                                     Config::$user,
                                                     Config::$pass ) ) );
