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
    $this->tags     = array( 'admin-only' );
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
    $sql = <<<EOT
SELECT id, ts, author, body new, (SELECT body FROM allObjects 
                                   WHERE id=current.id and ts<current.ts
                                   ORDER BY ts DESC
                                   LIMIT 1) old
  FROM (SELECT * FROM allObjects
         WHERE type 
          LIKE "%Content"
         ORDER BY ts DESC
         LIMIT 10) current;
EOT;
    $stmt = $dbh->prepare( $sql );

    $html = '<h1>Changes...</h1>';

    if( $stmt->execute() === false ) {
      Messages::getInstance()->addError( "Failed to retrieve changes..." );
    } else {
      $rows = $stmt->fetchAll();

      $html .= <<<EOT
<p>
 Dit zijn de laatste 30 wijzigingen op de site ...
</p>

<center>
<table class="changelog" width="90%">
EOT;

      foreach( $rows as $row ) {
        $url = str_replace( ' ', '-', $row['id'] );
        $diff = $this->createDiff($row['old'], $row['new']);
        $html .= <<<EOT
<tr>
  <td valign="top" width="100">$row[ts]</td>
  <td valign="top"><a href="$url">$row[id]</a></td>
  <td valign="top" width="100">$row[author]</td>
  <td><div class="diffviewer">$diff</div></td>
</tr>
EOT;
      }
    $html .="</table></center>";
    }
    return $html;
  }

  function createDiff($old, $new) {
    $diff = new diff( $old, $new );
    $diff->showContext(1);
    $html = "<table class=\"diff\">\n";
    while($line = $diff->getNextLine()) {
      if( $line->isAddition() ) {
        $class = 'add';     $label = '+';
        $o = '';    $n = $line->getNewIndex();
      } elseif( $line->isDeletion() ) {
        $class = 'del';     $label = '-';
        $n = '';    $o = $line->getOldIndex();
      } elseif( $line->isContext() ) {
        $class = 'context'; $label = '';
        $o = $line->getOldIndex();    $n = $line->getNewIndex();
      } else { // ->isSkip()
        $class = 'skip';    $label = '';
        $o = '...';  $n = '...';
      }
      $html .= <<<EOT
<tr class="{$class}">
  <td class="index">{$o}</td>
  <td class="index">{$n}</td>
  <td class="label">{$label}</td>
  <td class="line">{$line}</td>
</tr>
EOT;
    }
    $html .= "</table>\n";

    return $html;
  }
}
