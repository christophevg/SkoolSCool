<?php

/**
 * static configuration class
 */
 
class Config {
  // site configuration
  static $server = '';

  // database connection
  static $dbname = '';
  static $user   = '';
  static $pass   = '';
  
  // email
  static $feedbackMail = '';
  
  // default content
  static $defaultPageBody = "# %%name%%\n\nYour content goes here ...";
  static $defaultHtmlBody = "<h1>%%name%%</h1>\n\n<p>Your content goes here ...</p>";
  
  // google account
  static $googleAccount  = '';
  static $googlePass     = '';
}

// include the local configuration (if available in directory of index.php)
@include dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/config.php';

// check if minimal configurations have been set
foreach( array( 'server', 'dbname', 'user', 'pass', 'feedbackMail', 
                'googleAccount', 'googlePass' ) as $var )
{
  if( Config::$$var == '' ) { 
    die( "Please configure the $var-variable" );
  }
}
