<?php

/**
 * Top-level SkooSCool include file
 * This file includes all functionality in one go and centralizes some
 * boilerplate.
 */

date_default_timezone_set('Europe/Brussels');
setlocale(LC_ALL, 'nl_NL');

include_once dirname(__FILE__) . '/Config.php';

include_once dirname(__FILE__) . '/Console.php';

include_once dirname(__FILE__) . '/Singleton.php';

include_once dirname(__FILE__) . '/Objects.php';

include_once dirname(__FILE__) . '/SessionStore.php';
include_once dirname(__FILE__) . '/MySQLStore.php';

include_once dirname(__FILE__) . '/Events.php';
include_once dirname(__FILE__) . '/Logging.php';
include_once dirname(__FILE__) . '/ChangeLog.php';

include_once dirname(__FILE__) . '/User.php'; 

include_once dirname(__FILE__) . '/Content.php';
include_once dirname(__FILE__) . '/PageContent.php';
include_once dirname(__FILE__) . '/HtmlContent.php';

include_once dirname(__FILE__) . '/CommentContent.php';

include_once dirname(__FILE__) . '/AlbumContent.php';
include_once dirname(__FILE__) . '/PictureContent.php';

include_once dirname(__FILE__) . '/Skin.php';

include_once dirname(__FILE__) . '/AuthorizationManager.php';

include_once dirname(__FILE__) . '/SessionManager.php';

// create initial structure for the transient object cache store
if( ! is_array( SessionManager::getInstance()->ObjectCache ) ) {
  SessionManager::getInstance()->ObjectCache = array();
}

include_once dirname(__FILE__) . '/Navigator.php';

// process login post
if( isset($_POST['login']) && isset($_POST['pass']) ) {
  SessionManager::getInstance()->login( $_POST['login'], $_POST['pass'] );
}

// process logout get
if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
  SessionManager::getInstance()->logout();
}

include_once dirname(__FILE__) . '/Context.php';
