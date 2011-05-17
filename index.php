<?php

/**
 * SkoolSCool
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

/**
 * get the (default) skin (=look & feel)
 */
$skin = Skin::get();

/**
 * content requests are passed through the 'cid' (content id) get parameter
 * example: http://skoolscool.org/index.php?cid=somePage
 * at server level this can be rewritten
 * example: http://skoolscool.org/somePage
 */
$request = isset($_GET['cid']) ? $_GET['cid'] : "";

/**
 * get the current user
 */
$user = SessionManager::getInstance()->currentUser;

/**
 * retrieve the relevant content
 * if it returns nothing, we get the default content
 */
$content = Content::get( $request );
if( ! $content ) { $content = Content::get(); }
EventBus::getInstance()
  ->publish( new Event( EventType::NAVIGATION, null, "request", $content ) );

/**
 * show the content to the user using the skin
 */
print $skin->show( $content )->to( $user );
