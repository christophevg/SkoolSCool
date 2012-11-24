<?php

/**
 * put.php
 * when a PUT method is used, this will parse the data send with it into
 * a $_PUT global variable. (cfr $_GET and $_POST)
 * 
 * @author Christophe VG
 */

$_PUT = array();

if( $_SERVER['REQUEST_METHOD'] == 'PUT') {
  parse_str(file_get_contents('php://input'), $_PUT );
}
