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
$request = isset($_GET['cid']) ? $_GET['cid'] : 'home';

/**
 * get the current user
 */
$user = SessionManager::getInstance()->currentUser;

/**
 * retrieve the relevant content
 * if it returns nothing, we get the default content
 */
$content = Content::get( $request );
if( ! $content ) {
  // is the current user has write access, he might be requesting to create
  // new content
  if( !$user->isAnonymous() ) {
    // check if we're requested to create this page (get param create=true)
    // if a known type (get param type=[page|album|picture]) is provided, 
    // create a new content object, else show the newContent "wizard" page
    $newContent = isset( $_GET['create'] ) && $_GET['create'] == 'true';
    $type = isset( $_GET['type'] ) 
            && in_array( $_GET['type'], 
              array( 'PageContent', 'AlbumContent', 'PictureContent' ) ) ?
              $_GET['type'] : null;
    if( $newContent && $type ) {
      $content = Content::create($type, $request);
      $event = new Event( EventType::ACTION, "new content ($type): $request", $request );
    } elseif( $newContent ) {
      $content = Content::get('newContent');
      $event = new Event( EventType::ACTION, "new content: $request", $request );
    } else {
      $content = Content::get('unknownContent');
      $event = new Event( EventType::ACTION, "unknown content: $request", $request );
    }
  } else {
    $content = Content::get('404');
    $event = new Event( EventType::ERROR, "missing content: $request", $request );
  }
} else {
  $event = new Event( EventType::NAVIGATION, "to $request", $content );
}
EventBus::getInstance()->publish( $event );

/**
 * show the content to the user using the skin
 */
print $skin->show( $content )->to( $user );
