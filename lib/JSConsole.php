<?php

/**
 * JSConsole
 *
 * Small class that provides a simple interface to access the Javascript
 * console "directly" from PHP.
 */

class JSConsole {
  function log( $msg ) {
    $msg = str_replace( '"', '\"', $msg );
    echo <<<EOT
    <script>
    if( console && typeof console.log == "function" ) {
      console.log( "$msg" );
    }
    </script>
EOT;
  }
}
