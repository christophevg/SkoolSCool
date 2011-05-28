<?php

/**
 * Top-level SkooSCool include file
 * This file includes all functionality in one go and centralizes some
 * boilerplate.
 */

date_default_timezone_set('Europe/Brussels');

include_once dirname(__FILE__) . '/Events.php';
include_once dirname(__FILE__) . '/Logging.php';
include_once dirname(__FILE__) . '/ChangeLog.php';

include_once dirname(__FILE__) . '/User.php'; 

include_once dirname(__FILE__) . '/Content.php';
include_once dirname(__FILE__) . '/PageContent.php';
include_once dirname(__FILE__) . '/AlbumContent.php';
include_once dirname(__FILE__) . '/PictureContent.php';

include_once dirname(__FILE__) . '/Skin.php';

include_once dirname(__FILE__) . '/AuthorizationManager.php';

include_once dirname(__FILE__) . '/Context.php';

include_once dirname(__FILE__) . '/SessionManager.php';

// if the session contains a Context, put this in the static singleton
if( !is_null( SessionManager::getInstance()->Context ) ) {
  Context::$singleton = SessionManager::getInstance()->Context;
  Context::$singleton->refresh();
}
// register a shutdown function to save the singleton back in the session
function StoreContext() {
  SessionManager::getInstance()->Context = Context::$singleton;
}
register_shutdown_function('StoreContext');
