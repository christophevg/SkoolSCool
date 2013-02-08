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

// handle incoming requests: messages -> mail, content-updates
include_once 'handle_updates.php';

// a few short-hands from the context
$user    = Context::$currentUser;
$request = Context::$request;

// another short-hand for AuthorizationManager
$am = AuthorizationManager::getInstance();

// get the (default) skin (=look & feel)
$skin = Skin::get( Config::$skin );

// retrieve the relevant content
$content = Content::get( $request->id );

// if we didn't get any content ... there might be a variety of reasons
if( $content == null ) {

  // if the current user has no read access, it is normal we didn't get the
  // content
  if( ! $am->can( $user )->read() ) {
    // unauthorized access
    $content = Content::get( '401', $request->string );

  // if the current user has write access, he might be requesting to create
  // new content.
  } elseif( $am->can( $user )->update() ) {

    // if we're requested to create new content and a contenttype is provided,
    // create a new content object
    if( $request->requiresCreation && $request->contentType ) {
      $content = Content::create( $request->contentType, $request->id );

    // else if don't know the type, show the newContent "wizard" page
    } elseif( $request->requiresCreation ) {
      $content = Content::get( 'newContent', $request->string );

    // if we're not even requested to create new content ... it's unknown
    } else {
      $content = Content::get( 'unknownContent', $request->string );
    }

  // user without update rights, finding unknown content == missing content
  } else {
    if( $request->style == "embed" ) {
      $content = null;
    } else {
      $content = Content::get( '404', $request->string );
    }
  }
}

/**
 * show the content to the user using the skin
 * the method reflects the render style
 */
print $skin->{Context::$request->style}( $content )->to( $user );
