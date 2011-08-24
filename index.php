<?php

/**
 * SkoolSCool
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

// include all functionality
include_once 'lib/SkoolSCool.php';

// bootstrap: setup caches, process login/out requests
include_once 'bootstrap.php';

// maybe we're receiving a file ... process that and do nothing else ;-)
include_once 'handle_file_upload.php';

// a few short-hands from the context
$user    = Context::$currentUser;
$request = Context::$request;

// get the (default) skin (=look & feel)
$skin = Skin::get( 'vbsg' );

// retrieve the relevant content
$content = Content::get( $request->id );

if( $content == null ) {
  // if the current user has write access, he might be requesting to create
  // new content.
  if( AuthorizationManager::getInstance()->can( $user )->update() ) {
    // if we're requested to create new content and a contenttype is provided,
    // create a new content object
    if( $request->requiresCreation && $request->contentType ) {
      $content = Content::create( $request->contentType, $request->id );
    } elseif( $request->requiresCreation ) {
      // else if don't know the type, show the newContent "wizard" page
      $content = Content::get( 'newContent', $request->string );
    } else {
      // if we're not even requested to create new content ... it's unknown
      $content = Content::get( 'unknownContent', $request->string );
    }
  } else {
    // user without update rights, finding unknown content == missing content
    $content = Content::get( '404', $request->string );
  }
}

/**
 * process incoming new content
 */
 
// feedback/content form
if( isset($_POST['message']) ) {
  mail( Config::$feedbackMail, "Nieuw Bericht op de website",
        "Van : {$_POST['name']}:\n\n{$_POST['message']}\n" );
}

// comments
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
print $skin->{Context::$request->style}( $content )->to( $user );
