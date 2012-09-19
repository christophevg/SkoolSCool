<?php

/**
 * Top-level SkooSCool include file
 * This file includes all functionality in one go and centralizes some
 * boilerplate.
 */

date_default_timezone_set('Europe/Brussels');
setlocale(LC_ALL, 'nl_NL');

include_once dirname(__FILE__) . '/JSConsole.php';

include_once dirname(__FILE__) . '/LightOpenIDClient/LightOpenIDClient.php';

include_once dirname(__FILE__) . '/gPhoto.php';

include_once dirname(__FILE__) . '/recaptchalib.php';

include_once dirname(__FILE__) . '/I18N.php';
include_once dirname(__FILE__) . '/I18N.nl.php';

include_once dirname(__FILE__) . '/Messages.php';

include_once dirname(__FILE__) . '/Config.php';

include_once dirname(__FILE__) . '/Objects.php';

include_once dirname(__FILE__) . '/Session.php';

include_once dirname(__FILE__) . '/SessionStore.php';
include_once dirname(__FILE__) . '/SessionCache.php';
include_once dirname(__FILE__) . '/MySQLStore.php';

include_once dirname(__FILE__) . '/User.php'; 
include_once dirname(__FILE__) . '/Identity.php'; 

include_once dirname(__FILE__) . '/Content.php';
include_once dirname(__FILE__) . '/PageContent.php';
include_once dirname(__FILE__) . '/HtmlContent.php';

include_once dirname(__FILE__) . '/NewsList.php';
include_once dirname(__FILE__) . '/NewsContent.php';

include_once dirname(__FILE__) . '/CommentContent.php';

include_once dirname(__FILE__) . '/SystemContent.php';
include_once dirname(__FILE__) . '/diff/diff.php';
include_once dirname(__FILE__) . '/System.Changes.php';

include_once dirname(__FILE__) . '/Skin.php';

include_once dirname(__FILE__) . '/AuthorizationManager.php';

include_once dirname(__FILE__) . '/SessionManager.php';

include_once dirname(__FILE__) . '/Navigator.php';

include_once dirname(__FILE__) . '/Request.php';

include_once dirname(__FILE__) . '/Context.php';
