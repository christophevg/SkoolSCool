<?php

class Console {
  public static function log( $msg ) {
    $msg = str_replace( "\n", " ", $msg );
    print "<script>console.log('$msg');</script>";
  }
}