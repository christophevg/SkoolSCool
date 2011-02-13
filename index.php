<?php

/**
 * SkoolSCool
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

// keep track of the very first moment we started processing the request
$__START = microtime(true);

include_once 'lib/User.php'; 
include_once 'lib/Content.php';
include_once 'lib/Skin.php';

include_once 'lib/SessionManager.php';

/**
 * content requests are passed through the 'c' (content) get parameter
 * example: http://skoolscool.org/index.php?c=somePage
 * at server level this can be rewritten
 * example: http://skoolscool.org/somePage
 */
$request = $_GET['c'];

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

/**
 * get the (default) skin (=look & feel)
 */
$skin = Skin::get();

/**
 * show the content to the user using the skin
 */
print $skin->show( $content )->to( $user );
