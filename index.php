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
 * content can be SHOWn in full or be EMBEDded
 */
$style = isset( $_GET['embed'] ) ? "embed" : "show";

/**
 * retrieve the relevant content
 */
if( $content = Content::get( $request ) ) {
  $event = new Event( EventType::NAVIGATION, "$style $request", $content );    
} else {
  // unknown user finding unknown content == missing content
  if( $user->isAnonymous() ) {
    $content = Content::get('404');
    $event = new Event( EventType::ERROR, "missing content: $request", $request );
  } else {
    // if the current user has write access, he might be requesting to create
    // new content
    // check if we're requested to create this page (get param create exists)
    // and try to retrieve the wanted content type
    $newContent = isset( $_GET['create'] );
    $type = isset( $_GET['type'] ) 
            && in_array( $_GET['type'], 
              array( 'PageContent', 'AlbumContent', 'PictureContent' ) ) ?
              $_GET['type'] : null;
    // if we're requested to create new content and a known type (get param 
    // type=[page|album|picture]) is provided, create a new content object
    if( $newContent && $type ) {
      $content = Content::create($type, $request);
      $event = new Event( EventType::ACTION, "new content ($type): $request", $request );
    } elseif( $newContent ) {
      // else if don't know the type, show the newContent "wizard" page
      $content = Content::get('newContent');
      $event = new Event( EventType::ACTION, "new content: $request", $request );
    } else {
      // if we're not even requested to create new content ... it's unknown
      $content = Content::get('unknownContent');
      $content->replace( '{{cid}}', $request );
      $event = new Event( EventType::ACTION, "unknown content: $request", $request );
    }
  }
}
EventBus::getInstance()->publish( $event );

/**
 * show the content to the user using the skin
 * the method reflects the render style
 */
print $skin->$style( $content )->to( $user );
