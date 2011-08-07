<?php

/**
 * SkoolSCool - Ajax interface
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

sleep(1);

/**
 * ajax requests pass the object through the 'id' (content id) post parameter
 * and the data to update using the 'data' post parameter
 */
$request = $_POST['id'];
$data    = stripslashes($_POST['data']);

/**
 * get the current user
 */
$user = SessionManager::getInstance()->currentUser;

/**
 * retrieve the relevant content
 */
$content = Content::get( $request );
if( $content == null ) {
  print "unknown content: $request";
  exit();
}

/**
 * check if the user can update the requested content, if so, do it, else fail
 */
if( AuthorizationManager::getInstance()->can( $user )->update( $content ) ) {
  $content->body = $data;
  Objects::getStore('persistent')->put($content);
  print "ok";
} else {
  print "not allowed";
}
