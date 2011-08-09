<?php

/**
 * SkoolSCool
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

// include all functionality
include_once 'lib/SkoolSCool.php';

// a few short-hands from the request
$user    = Context::$currentUser;
$request = Context::$request->object;
$style   = Context::$request->style;

// get the (default) skin (=look & feel)
$skin = Skin::get( 'vbsg' );

/**
 * retrieve the relevant content
 */
$content = Content::get( $request );

if( $content == null ) {
  // unknown user finding unknown content == missing content
  if( $user->isAnonymous() ) {
    $content = Content::get('404');
  } else {
    // if the current user has write access, he might be requesting to create
    // new content
    // check if we're requested to create this page (get param create exists)
    // and try to retrieve the wanted content type
    $newContent = isset( $_GET['create'] );
    $type = isset( $_GET['type'] ) 
            && in_array( $_GET['type'], 
              array( 'PageContent', 'NewsContent' ) ) ?
              $_GET['type'] : null;
    // if we're requested to create new content and a known type (get param 
    // type=[page|news]) is provided, create a new content object
    if( $newContent && $type ) {
      $content = Content::create($type, $request);
    } elseif( $newContent ) {
      // else if don't know the type, show the newContent "wizard" page
      $content = Content::get('newContent');
      $content->replace( '{{id}}', $request );
    } else {
      // if we're not even requested to create new content ... it's unknown
      $content = Content::get('unknownContent');
      $content->replace( '{{id}}', $request );
    }
  }
}

/**
 * process incoming new content
 */
if( isset($_POST['message']) ) {
  mail( Config::$feedbackMail, "Nieuw Bericht op de website",
        "Van : {$_POST['name']}:\n\n{$_POST['message']}\n" );
}

if( isset($_POST['comment']) ) {
  // create new CommentContent object
  $data = $_POST['comment'];
  $id = time();
  $comment = new CommentContent( array( id     => $id, 
                                        author => $user->login, 
                                        body   => $data ) );
  Objects::getStore( 'persistent' )->put( $comment );
  // add object to children of current content
  $content->addChild( $comment );
}


/**
 * show the content to the user using the skin
 * the method reflects the render style
 */
print $skin->$style( $content )->to( $user );
