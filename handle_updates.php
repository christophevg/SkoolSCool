<?php

/**
 * SkoolSCool
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 *
 * process incoming new content
 */

// feedback/content form
if( isset($_POST['message']) ) {
  $resp = recaptcha_check_answer( Config::$recaptchaPrivateKey,
                                  $_SERVER["REMOTE_ADDR"],
                                  $_POST["recaptcha_challenge_field"],
                                  $_POST["recaptcha_response_field"]);

  if( ! $resp->is_valid ) {
    Messages::getInstance()->addCritical( I18N::$RECAPTCHA_FAILURE );
  } else {
    mail( Config::$feedbackMail, "Nieuw bericht via de website",
          "Van   : {$_POST['name']}\n" .
          "Email : {$_POST['email']}\n\n" .
          "{$_POST['message']}\n" );
    Messages::getInstance()->addInfo( I18N::$CONTACT_SUCCESS );
  }
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
