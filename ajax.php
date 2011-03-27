<?php

/**
 * SkoolSCool - Ajax interface
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

/**
 * ajax requests are passed through the 'cid' (content id) post parameter
 * example: http://skoolscool.org/index.php?cid=somePage
 * at server level this can be rewritten
 * example: http://skoolscool.org/somePage
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
