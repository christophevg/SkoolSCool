<?php

/**
 * System.Changes
 * implementation of a System page displaying a changelog and offering the
 * possibility to revert changes.
 */

class SystemChanges extends Content {

  public function __construct() {
    $this->id       = 'system:changes';
    $this->url      = str_replace( ' ', '-', $this->id );
    $this->created  = null;
    $this->updated  = null;
    $this->author   = User::get( 'system' );
    $this->children = array();
    $this->tags     = array( 'admins-only' );
  }

  public function isHtml() {
    return true;
  }
  
  public function editor() {
    return "";
  }
  
  public function render() {
    $dbname = Config::$dbname;
    $dbh = new PDO( "mysql:host=127.0.0.1;dbname=$dbname", 
                    Config::$user, Config::$pass,
                    array( PDO::ATTR_PERSISTENT => true ) );
    $stmt = $dbh->prepare( 'SELECT ts, id, author, body FROM allObjects WHERE type LIKE "%Content" ORDER BY ts DESC LIMIT 30' );

    $html = '<h1>Changes...</h1>';

    if( $stmt->execute() === false ) {
      Messages::getInstance()->addError( "Failed to retrieve changes..." );
    } else {
      $rows = $stmt->fetchAll();

      $html .= <<<EOT
<style>
TABLE.changelog {
  border-collapse: collapse;
}
TABLE.changelog,
TABLE.changelog TH, 
TABLE.changelog TD {
  border: 1px solid white;
}

TABLE.changelog TD {
  padding: 5px;
}

DIV.viewer {
  width: 100%;
  height: 200px;
  overflow: auto;
  border: 1px solid #aaa;
}
</style>

<p>
 Dit zijn de laatste 30 wijzigingen op de site ...
</p>

<center>
<table class="changelog" width="90%">
EOT;

      foreach( $rows as $row ) {
        $url = str_replace( ' ', '-', $row['id'] );
        $html .= <<<EOT
<tr>
  <td valign="top" width="100">$row[ts]</td>
  <td valign="top"><a href="$url">$row[id]</a></td>
  <td valign="top" width="100">$row[author]</td>
  <td><div class="viewer">$row[body]</div></td>
</tr>
EOT;
      }
    $html .="</table></center>";
    }
    return $html;
  }

}