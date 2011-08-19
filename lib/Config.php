<?php

/**
 * static configuration class
 */
 
class Config {
  // site configuration
  static $server = 'localhost';

  // database connection
  static $dbname = 'vbsg';
  static $user   = 'vbsg';
  static $pass   = 'vbsg';
  
  // email
  static $feedbackMail = 'xtof';
  
  // default content
  static $defaultPageBody = "# %%name%%\n\nYour content goes here ...";
  static $defaultHtmlBody = "<h1>%%name%%</h1>\n\n<p>Your content goes here ...</p>";
}

// include the local configuration (if available in directory of index.php)
@include dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/config.php';
