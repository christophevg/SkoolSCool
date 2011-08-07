<?php

/**
 * static configuration class
 */
 
class Config {
  static $dbname = "vbsg";
  static $user   = "vbsg";
  static $pass   = "vbsg";
}

// include the local configuration (if available in directory of index.php)
@include dirname(realpath($_SERVER["SCRIPT_FILENAME"])) . '/config.php';
