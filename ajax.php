<?php

/**
 * SkoolSCool - Ajax interface
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

// GET REQUESTS
function handle_get() {
  $request = trim(str_replace( '-', ' ', $_GET['id'] ));
  $content = Content::get( $request );
  if( $content == null ) {
    print "unknown content";
  } else {
    print $request;
  }
}

// POST REQUESTS
function handle_post() {
  // ajax requests pass the object through the 'id' (content id) post parameter
  // and the data to update using the 'data' post parameter
  $request = trim(str_replace( '-', ' ', $_POST['id'] ));
  $data    = json_decode( stripslashes($_POST['data']) );

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
     if( $key == 'date' ) { $value = strtotime( $value ); }
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

switch( $_SERVER['REQUEST_METHOD'] ) {
  case 'GET' :  handle_get();  break;
  case 'POST':  handle_post(); break;  
  default: print "unsupported method";
}
