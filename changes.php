<?php

/**
 * SkoolSCool - ChangeLog
 * A small and very specific CMS for an elementary school's website
 * @author Christophe VG <contact+skoolscool@christophe.vg>
 */

include_once 'lib/SkoolSCool.php';

if( ! SessionManager::getInstance()->currentUser->isAdmin() ) {
  header( "Location: ./" );
  exit;
}

$dbname = Config::$dbname;
$dbh = new PDO( "mysql:host=127.0.0.1;dbname=$dbname", 
                Config::$user, Config::$pass,
                array( PDO::ATTR_PERSISTENT => true ) );
$stmt = $dbh->prepare( 'SELECT ts, id, author, body FROM allObjects WHERE type LIKE "%Content" ORDER BY ts DESC LIMIT 30' );

if( $stmt->execute() === false ) {
  print_r( $stmt->errorInfo() );
}

$rows = $stmt->fetchAll();

print <<<EOT
<style>
DIV.viewer {
  width: 100%;
  height: 200px;
  overflow: auto;
}
</style>

<table width="100%" border="1">
EOT;

foreach( $rows as $row ) {
  $url = str_replace( ' ', '-', $row['id'] );
  print <<<EOT
<tr>
  <td valign="top" width="100">$row[ts]</td>
  <td valign="top"><a href="$url">$row[id]</a></td>
  <td valign="top" width="100">$row[author]</td>
  <td><div class="viewer">$row[body]</div></td>
</tr>
EOT;
}
print "</table>";
