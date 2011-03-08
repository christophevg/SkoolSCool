<?php

include_once dirname(__FILE__) . '/../lib/breakdown/php/breakdown.php';

/**
 * Default Skin
 * This is a very bare-bone Skin implementation. It's focus is on the
 * development of the features that can be used in a Skin, not on the look
 * and feel or layout.
 */

class DefaultSkin extends Skin {

  /**
   * The body method is one of the methods that need to be implemented to 
   * provide the parent Skin an implementation to operate minimally.
   * It renders the body, given a content object. This content object contains
   * a reference to the author. The object renders itself in a string context.
   * The $this object, contains references to the current user and a 
   * subcontent object. All other (unknown) properties are mapped to methods
   * on the Skin's implementation.
   */
  function body() {
    return <<<EOT
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./skins/default/screen.css">
  <script src="./lib/breakdown/js/breakdown.js"></script>
  <script src="./skins/default/users.js"></script>
  <script src="./skins/default/editing.js"></script>
</head>
<body>
  $this->userBar
  <h1>SkoolSCool</h1>
  <div class="body">
    $this->bodyContent  
    <div class="subcontent">
      $this->subContent
    </div>
  </div>
  $this->footer
</body>
<!-- this page was generated in $this->duration seconds  -->
</html>
EOT;
  }
  
  function bodyContent() {
    return $this->user->isContributor() ?
      $this->editableBodyContent() : $this->readOnlyBodyContent();
  }

  function readOnlyBodyContent() {
    $content = $this->content;
    $body = Breakdown::getConverter()->makeHtml((string)$content);
    return <<<EOT
  <div id="bodyView">
    <div id="bodyContent">
      $body
    </div>
    <div class="info">
      Author: $content->author @ $content->time
    </div>
  </div>
EOT;
  }
  
  function editableBodyContent() {
    $content = $this->content;
    return <<<EOT
  <div id="bodyView">
    $this->bodyControls
    <div id="bodyContent">
      $content
    </div>
    <div class="info">
      Author: $content->author @ $content->time
    </div>
  </div>
  $this->editor
EOT;
  }

  function editor() {
    $content = $this->content;
    return <<<EOT
  <div id="bodyEdit" style="display:none;">
    $content->editor
  </div>
EOT;
  }

  /**
   * Returns the duration of the processing of the request based on the
   * global __START variable and the current microtime.
   */
  function duration() {
    global $__START;
    return microtime(true) - $__START;
  }

  /**
   * The item method is the second method that must be provided to have a 
   * minimal Skin implementation. It is used to render content that is linked
   * to body-level content.
   */
  function item() {
    return $this->user->isContributor() ?
      $this->editableItem() : $this->readOnlyItem();
  }
  
  function readOnlyItem() {
    $content = $this->content;
    $body = Breakdown::getConverter()->makeHtml((string)$content);
    return <<<EOT
<div class="item">
  <h2>SubContent</h2>
  <b><i>$content->author</i></b> added child <b><i>$body</i></b>
</div>
EOT;
  }
  
  function editableItem() {
    $content = $this->content;
    return <<<EOT
<div class="item">
  $this->itemControls
  <h2>SubContent</h2>
  <b><i>$content->author</i></b> added child <b><i>$content</i></b>
</div>
EOT;
  }
  
  /**
   * In stead of the item and body methods, it is also possible to supply
   * specific item and body methods for each content type. If these are
   * available, they will be used, otherwise the general body and item methods
   * will be called.
   */
  function CommentAsItem() {
    $content = $this->content;
    return <<<EOT
<div class="comment item">
$this->itemControls
<h2>Comment</h2>
<b><i>$content->author</i></b> added child <b><i>$content</i></b>
</div>
EOT;
  }

  /**
   * Below this point are Skin specific methods, exposed as properties to the
   * methods above.
   */  
  
  function userBar() {
    if( $this->user->isAnonymous() ) {
      return $this->showLogin();
    } else {
      return $this->showUser();
    }
  }
  
  function showLogin() {
    return <<<EOT
<div class="userbar">
<div id="userActions">
  <a href="#" onclick="showLogon();">log on</a> | 
  <a href="#">register</a>
</div>
<div id="logon" style="display:none;">
<form action="./" method="post">
  username : <input name="login"> password : <input type="password" name="pass"> <input type="submit">
</form>
<a href="#" onclick="showUserActions();">cancel</a>
</div>
</div>
EOT;
  }

  function showUser() {
    return <<<EOT
<div class="userbar">
$this->user : <a href="?action=logout">logout</a>    
</div>
EOT;
  }
  
  function footer() {
    return <<<EOT

    <hr>
    a footer

EOT;
  }
  
  function itemControls() {
    return <<<EOT
<div class="controls">
  <a href="#">add</a>
  | <a href="#">remove</a>
  | <a href="#">edit</a>
</div>
EOT;
  }

  function bodyControls() {
    return <<<EOT
<div class="controls">
  <a href="#" onclick="editBody()">edit</a>
  <span id="bodySave" style="display:none;"> | <a href="#">save</a>
</div>
EOT;
  }

}
