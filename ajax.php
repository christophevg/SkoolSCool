<?php

/**
* SkoolSCool - Ajax interface
* A small and very specific CMS for an elementary school's website
* @author Christophe VG <contact+skoolscool@christophe.vg>
*/

include_once 'lib/SkoolSCool.php';

// GET REQUESTS = check for existance of content
function handle_get() {
  $request = trim(str_replace( '-', ' ', $_GET['id'] ));
  $content_exists = true;
  if(substr($request, 0, 10) == 'bestanden/' ) {
    $content_exists = file_exists($request);
  } else {
    $content_exists = ! is_null(Content::get( $request ));
  }
  if( $content_exists ) {
    print $request;
  }
}

// POST REQUESTS
function handle_post() {
  // ajax requests pass the object through the 'id' (content id) post parameter
  // and the data to update using the 'data' post parameter
  $request = trim(str_replace( '-', ' ', $_POST['id'] ));
  if(isset($_POST['data'])) {
    $data = $_POST['data'];
  } elseif(isset($_POST['ts'])) {
    return handle_delete();
  }
  if( get_magic_quotes_gpc() ) {
    $data = stripslashes($_POST['data']);
  }
  $data = json_decode( $data );

  // get the current user
  $user = SessionManager::getInstance()->currentUser;

  // retrieve the relevant content
  $content = Content::get( $request );
  if( $content == null ) {
    print "unknown content: $request";
    exit();
  }

  // check if the user can update the requested content, if so, do it, else fail
  if( AuthorizationManager::getInstance()->can( $user )->update( $content ) ) {
    foreach( $data as $key => $value ) {
      // TODO: move this logic to setters on the content objects
      if( $key == 'date' ) { $value = strtotime( $value );  }
      if( $key == 'tags' ) { $value = split( ' ', $value ); }
      $content->$key = $value;
    }
    // mark the change as performed by this user
    $content->author = SessionManager::getInstance()->currentUser;
    Objects::getStore('persistent')->put($content);
    print "ok";
  } else {
    print "not allowed";
  }
}

function handle_delete() {
  $id = trim(str_replace( '-', ' ', $_POST['id'] ));
  $ts = $_POST['ts'];
    // get the current user
  $user = SessionManager::getInstance()->currentUser;

  // retrieve the relevant content
  $content = Content::get( $id );
  if( $content == null ) {
    print "unknown content: $id";
    exit();
  }

  // check if the user can update the requested content, if so, do it, else fail
  if( AuthorizationManager::getInstance()->can( $user )->update( $content ) ) {
    Objects::getStore('persistent')->filter( 'id', $id )
                                   ->filter( 'ts', $ts )
                                   ->remove();
    print "ok";
  } else {
    print "not allowed";
  }
}

switch( $_SERVER['REQUEST_METHOD'] ) {
  case 'GET' :  handle_get();  break;
  case 'POST':  handle_post(); break;  
  default: print "unsupported method";
}
