<?php

/**
 * SkoolSCool - Ajax interface
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

/**
 * ajax requests pass the object through the 'cid' (content id) post parameter
 * and the data to update using the 'data' post parameter
 * example: http://skoolscool.org/ajax.php?cid=somePage
 */
$request = $_POST['cid'];
$data    = $_POST['data'];

/**
 * get the current user
 */
$user = SessionManager::getInstance()->currentUser;

/**
 * retrieve the relevant content
 */
$content = Content::get( $request );

/**
 * check if the user can update the requested content, if so, do it, else fail
 */
if( AuthorizationManager::getInstance()
      ->can( $user )->update( $content ) )
{
  $data = DBI::getInstance()->in( 'content' )->set( $request, $data );
  print "ok";
} else {
  print "fail";
}
