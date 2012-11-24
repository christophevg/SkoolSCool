<?php

/**
 * System.Changes
 * implementation of a System page displaying a changelog and offering the
 * possibility to revert changes.
 *
 * TODO: remove HTML from class :-(
 * TODO: remove this all together ;-) -> HtmlContent with JS app to REST API
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
    return false;
  }
  
  public function render() {
    $dbname = Config::$dbname;
    $dbh = new PDO( "mysql:host=127.0.0.1;dbname=$dbname", 
                    Config::$user, Config::$pass,
                    array( PDO::ATTR_PERSISTENT => true ) );
    $sql = <<<EOT
SELECT id, ts, author, body new_body, tags new_tags, 
       (SELECT body FROM allObjects 
         WHERE id=current.id and ts<current.ts
         ORDER BY ts DESC
         LIMIT 1) old_body,
       (SELECT tags FROM allObjects 
         WHERE id=current.id and ts<current.ts
         ORDER BY ts DESC
         LIMIT 1) old_tags
  FROM (SELECT * FROM allObjects
         WHERE type 
          LIKE "%Content"
         ORDER BY ts DESC
         LIMIT 30) current;
EOT;
    $stmt = $dbh->prepare( $sql );

    $html = '<h1>Changes...</h1>';

    if( $stmt->execute() === false ) {
      Messages::getInstance()->addCritical( "Failed to retrieve changes..." );
    } else {
      $rows = $stmt->fetchAll();

      $html .= <<<EOT
<p>
 Dit zijn de laatste 30 wijzigingen op de site ...
</p>

<script>
  function undo(id, ts) {
    if( confirm( "Bent u zeker dat u de wijziging aan " + id + " van tijdstip " + ts + " ongedaan wil maken? " +
                 "Dit kan niet hersteld worden." ) )
    {
      __remote__.remove(id, ts, function(response) {
        if( response != "ok" ) {
          notify( "undo failed :\\n" + response );
        } else {
          location.reload(true);
        }
      } );
    }
  }
</script>

<center>
<table class="changelog">
EOT;
  
      function createTagSpan(&$item, $index, $change) {
        $item = "<span class=\"tag_$change\">$item</span>";
      }
  
      foreach( $rows as $row ) {
        $url = str_replace( ' ', '-', $row['id'] );
        if( $diff = $this->createDiff($row['old_body'], $row['new_body']) ) {
          $diffViewer = "<div class=\"diffviewer\">{$diff}</div>";
        } else {
          $diffViewer = "";
        }

        $old_tags = split( ' ', $row['old_tags'] );
        $new_tags = split( ' ', $row['new_tags'] );

        $added_tags   = array_diff( $new_tags, $old_tags );
        $removed_tags = array_diff( $old_tags, $new_tags );
        
        array_walk( $added_tags,   'createTagSpan', 'add'    );
        array_walk( $removed_tags, 'createTagSpan', 'remove' );

        $added_tags   = join( ' ', $added_tags);
        $removed_tags = join( ' ', $removed_tags);

        if( $added_tags != "" or $removed_tags != "" ) {
          $tagViewer = "<b>tags:</b> <span class=\"changed_tags\">{$added_tags} {$removed_tags}</span>";
        } else {
          $tagViewer = "";
        }

        $html .= <<<EOT
<tr>
  <td valign="top" width="150">
    {$row["ts"]}<br>
    <a href="{$url}">{$row["id"]}</a><br>
    $row[author]
    <p align="right"><a href="javascript:" onclick="undo('{$row["id"]}','{$row["ts"]}');">undo</a></p>
  </td>
  <td valign="top">
    {$diffViewer}
    {$tagViewer}
  </td>
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
    if( ! $diff->differs() ) { return false; }
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
