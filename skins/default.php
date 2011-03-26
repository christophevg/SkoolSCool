<?php

/**
 * Default Skin
 * Author: Christophe VG <contact+skoolscool@christophe.vg>
 *
 * This is a very bare-bone Skin implementation. It's focus is on the
 * the features that can be used in a Skin and not on the look and feel or
 * layout.
 * This skin applies as little visual layouting as possible and simply
 * shows the possibilities of SkoolSCool functionality.
 * It can serve as a base for designing actual skins and aims to explain as
 * many patterns, possibilities and examples as possible.
 */

// keep track of time to show of how fast we render these pages ;-)
$GLOBALS['__START'] = microtime(true);

/**
 * We use breakdown to allow end users to mark-up their content without
 * the need to know HTML or other complex ways to add a little mark-up
 * Breakdown is a light-weight-like implementation of MarkDown.
 * It has both a PHP and Javascript implementation, which allows for rendering
 * the result from both languages with exactly the same result.
 */
include_once dirname(__FILE__) . '/../lib/breakdown/php/breakdown.php';

class DefaultSkin extends Skin {
  /**
   * The body method is one of the methods that need to be implemented to 
   * provide the parent Skin an implementation to operate minimally.
   * It renders the body based on the content object.
   * This content object contains a reference to the author. It also renders
   * itself in a string context.
   * The $this object, contains references to the current user and a 
   * subContent object. All other (unknown) properties are mapped to methods
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
  <h1>SkoolSCool - Default Skin</h1>
  <hr>
  <div class="body">
    $this->bodyContent  
    <div class="subcontent">
      $this->subContent
    </div>
  </div>
  <br clear="both">
  $this->footer
</body>
<!-- this page was generated in $this->duration seconds  -->
</html>
EOT;
  }
  
  /**
   * Method implementing part of the body. It displays the main content,
   * edit-controls and editor. To display the content, it first calls out to
   * BreakDown to transform the user supplied content into HTML.
   */
  protected function bodyContent() {
    $content = $this->content;
    $body = Breakdown::getConverter()->makeHtml((string)$content);
    return <<<EOT
  <div id="bodyView">
    $this->bodyEditControls
    <div id="bodyContent">
      $body
    </div>
    <div class="info">
      Author: $content->author @ $content->time
    </div>
  </div>
  $this->bodyEditor
EOT;
  }

  /**
   * If the user is logged, so at least a contributor, we offer controls to
   * start editing the body content.
   */
  protected function bodyEditControls() {
    if( ! $this->user->isContributor() ) { return ""; }
    return <<<EOT
  <div class="controls">
    <a href="#" onclick="editBody()">edit</a>
    <span id="bodySave" style="display:none;"> | <a href="#">save</a>
  </div>
EOT;
  }

  /**
   * If the user is logged, so at least a contributor, we offer insert the
   * editor to interact with the underlying content-data.
   */
  protected function bodyEditor() {
    if( ! $this->user->isContributor() ) { return ""; }
    $content = $this->content;
    return <<<EOT
  <div id="bodyEdit" style="display:none;">
    $content->editor
  </div>
EOT;
  }

  /**
   * The item method is the second method that must be provided to have a 
   * minimal Skin implementation. It is used to render content that is linked
   * to body-level content.
   */
  function item() {
    $content = $this->content;
    $body = Breakdown::getConverter()->makeHtml((string)$content);
    return <<<EOT
<div class="item">
  $this->itemEditControls
  <h2>SubContent</h2>
  <b><i>$content->author</i></b> added child <b><i>$body</i></b>
  $this->itemEditor
</div>
EOT;
  }
  
  function itemEditControls() {
    if( ! $this->user->isContributor() ) { return ""; }
    return <<<EOT
  <div class="controls">
    <a href="#">add</a>
    | <a href="#">remove</a>
    | <a href="#">edit</a>
  </div>
EOT;
  }
  
  function itemEditor() {
    if( ! $this->user->isContributor() ) { return ""; }
    $content = $this->content;
    return <<<EOT
  <div id="itemEdit" style="display:none;">
    $content->editor
  </div>
EOT;
  }
  
  /**
   * In stead of the item and body methods, it is also possible to supply
   * specific item and body methods for each content type. If these are
   * available, they will be used, otherwise the general body and item methods
   * will be called.
   * It this case, Comments will be rendered in specific way AND only in case
   * the user is logged on. Anonymous users don't see comments.
   */
  function CommentAsItem() {
    if( ! $this->user->isContributor() ) { return ""; }
    $content = $this->content;
    $body = Breakdown::getConverter()->makeHtml((string)$content);
    return <<<EOT
<div class="comment">
  <div class="commenter">
    <img src="$this->gravatarURL" width="50" height="50"><br>
    $content->author
  </div>
  <div class="body">
    $this->itemEditControls
    $body
  </div>
</div>
EOT;
  }
  
  /**
   * Returns a Gravatar URL, based on the content's author's email address
   */
  protected function gravatarURL() {
    $defaultImage = "http://" . $_SERVER['HTTP_HOST'] . 
                    dirname($_SERVER['SCRIPT_NAME'])  . 
                    "/skins/default/images/unknown_user.png";
    $size   = "50";
    $rating = "G";
    $border = "000000";
    $url    = "http://www.gravatar.com/avatar.php?gravatar_id=%s".
              "&default=%s&size=%s&border=%s&rating=%s";

    return sprintf(	$url, md5($this->content->author->email), 
                    $defaultImage, $size, $border, $rating );
  }

  /**
   * Switch between a user logon form or user info and logout actions.
   */  
  protected function userBar() {
    return $this->user->isAnonymous() ? 
      $this->showLogin() : $this->showUser();
  }
  
  /**
   * If a user is not logged in, we offer him a logon form.
   */
  private function showLogin() {
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

  /**
   * If a user is logged in, we display his name and a logout action.
   */
  private function showUser() {
    return <<<EOT
<div class="userbar">
$this->user : <a href="?action=logout">logout</a>    
</div>
EOT;
  }

  /**
   * Returns the footer of the page.
   */
  protected function footer() {
    return <<<EOT
    <hr>
    a footer
EOT;
  }
  
  /**
   * Returns the duration of the processing of the request based on the
   * global __START variable and the current microtime.
   */
  protected function duration() {
    global $__START;
    return round(microtime(true) - $__START, 4);
  }

}
