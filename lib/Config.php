<?php

/**
 * static configuration class
 */
 
class Config {
  // database connection
  static $dbname = 'vbsg';
  static $user   = 'vbsg';
  static $pass   = 'vbsg';
  
  // email
  static $feedbackMail = 'xtof';
}

// include the local configuration (if available in directory of index.php)
@include dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/config.php';
