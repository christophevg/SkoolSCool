<?php

/**
 * VBSG Skin
 * Implementation: Christophe VG <contact+skoolscool@christophe.vg>
 * Design: Ilse Heremans
 *
 * This is the Skin of the new website of my daughter's school - the
 * reason for the development of SkoolSCool
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
include_once dirname(__FILE__) . '/vbsg/breakdown/php/breakdown.php';

class VbsgSkin extends Skin {
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
  protected function body() {
    return $this->mainTemplate($this->bodyContent());
  }
  
  private function mainTemplate($content) {
    return <<<EOT
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/screen.css">
  <!--<link rel="stylesheet" type="text/css" href="./skins/vbsg/debug.css">-->

  <script src="./skins/vbsg/breakdown/js/breakdown.js"></script>
  <script src="./skins/vbsg/notify.js"></script>
  <script src="./skins/vbsg/ajax.js"></script>
  <script src="./skins/vbsg/popup.js"></script>
  <script src="./skins/vbsg/editing.js"></script>
  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/screen.ie6.css">
  <![endif]-->
</head>
<body>
  <div style="background-color: white;">
    <div id="toolbar">
      <div id="userbar">
{$this->insertUserBar}
        <img id="logo" src="skins/vbsg/images/vbsg-logo.png">
      </div>
    </div>
    <div id="header">
      <div id="navigation">
{$this->includeNavigation}
      </div>
    </div>
  </div>
  <div id="banner"></div>
  <div id="site">
    <div class="body">
{$content}
      <div class="subcontent">
{$this->subContent}
      </div>
    </div>
  <br clear="both">
  {$this->footer}
  </div>
{$this->insertPopups}
</body>
<!-- this page was generated in {$this->duration} seconds  -->
</html>
EOT;
  }
  
  protected function includeNavigation() {
    $navigation = Content::get('navigation');
    $html = Breakdown::getConverter()->makeHtml((string)$navigation);
    $html = ereg_replace( '</?p>','', $html );
    // add class to show current (TODO: do this in a clean way ;-) )
    $root = Context::getInstance()->path->getRoot()->cid;
    $html = str_replace( "<li><a href=\"$root\">", 
                         "<li class=\"selected\"><a href=\"$root\">", 
                         $html );
    // add link to directly edit the navigation page
    if( AuthorizationManager::getInstance()
        ->can( $this->user )->update( $navigation ) )
    {
      $html .= '<a href="navigation?mode=edit">(edit)</a>';
    }
    return $html;
  }

  protected function bodyContent() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<script>
var bodyContent = "{$this->content->cid}";
</script>
<div id="{$this->content->cid}Container" class="container">
  {$this->editControls}
  <div id="{$this->content->cid}View" class="body">
    {$this->contentAsHtml}
  </div>
  {$this->itemEditor}
</div>
EOT;
  }

  private function generateCommands( $commands ) {
    $html = "";
    foreach( $commands as $command => $isActive ) 
    {
      $lc_command = strtolower($command);
      $state = $isActive ? " active" : " inactive";
      $html .= <<<EOT
      <span id="{$this->content->cid}{$command}Command" class="command{$state}">
        <a href="javascript:" onclick="{$lc_command}Content('{$this->content->cid}');">{$lc_command}</a>
      </span>

EOT;
    }
    return $html;
  }

  protected function editControls() {
    if( ! $this->contentIsEditable() ) { return ""; }
    $commands = $this->generateCommands( array( "Edit" => true, 
                                                "Save" => false, 
                                                "Cancel" => false ) );
    $states = "";
    foreach( array( "Saving" => false ) as $state => $isActive ) {
      $activation = $isActive ? " active" : "";
      $states .= <<<EOT
      <span id="{$this->content->cid}{$state}State" class="state{$activation}">{$state}</span>
    
EOT;
    }
    return <<<EOT
<div id="{$this->content->cid}Controls" class="controls">
{$commands}{$states}</div>
EOT;
  }
  
  protected function itemEditor() {
    if( ! $this->contentIsEditable() ) { return ""; }
    return <<<EOT
<div id="{$this->content->cid}Editor" class="editor">
  {$this->content->editor}
  {$this->editorControls}
</div>
EOT;
  }
  
  protected function editorControls() {
    $commands = $this->generateCommands( array( "preview" => true,
                                                "cancel"  => true ) );
    return <<<EOT
<div id="{$this->content->cid}EditorControls" class="editorcontrols">
  {$commands}
</div>
EOT;
  }
  
  /**
   * This is the second fall-back function. Just like body it is called if
   * no specific skin method is provided for the currently active content type
   */
  protected function item() {
    return $this->bodyContent();
  }
  
  /**
   * This another fall-back function. Just like body and item it is called if
   * no specific skin method is provided for the currently active content type
   * and rendering style.
   */
  protected function embedded() {
    return $this->contentAsHtml();
  }

  /**
   * In stead of the item and body methods, it is also possible to supply
   * specific item and body methods for each content type. If these are
   * available, they will be used, otherwise the general body and item methods
   * will be called.
   * It this case, Comments will be rendered in specific way AND only in case
   * the user is logged on. Anonymous users don't see comments.
   */
  protected function CommentContentAsItem() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="comment">
  <div class="commenter">
    {$this->largeGravatar}<br>
    {$this->content->author}
  </div>
  {$this->bodyContent}
</div>
EOT;
  }

  protected function PageContentAsEmbedded() {
    $commentCount = count($this->content->children);
    return <<<EOT
<div class="embedded page {$this->content->cid}" onclick="javascript:window.location='{$this->content->cid}';">
  {$this->contentAsHtml}
  <div class="embedded socialbar">
    {$commentCount}
  </div>
</div>
EOT;
  }
  
  protected function AlbumContentAsItem() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="albumkey">
  <a href="{$this->content->cid}"
    ><img src="images/75x75/{$this->content->key}"><br>
    {$this->content->label}</a>
</div>
EOT;
  }

  protected function AlbumContentAsEmbedded() {
    return <<<EOT
<div class="embedded album {$this->content->cid}" onclick="javascript:window.location='{$this->content->cid}';">
  <h1>Fotoboek</h1>
  <img class="key" src="images/215x139/{$this->content->key}">
</div>
EOT;
  }


  protected function PictureContentAsItem() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="preview">
  <a href="{$this->content->cid}"
    ><img src="images/75x75/{$this->content->file}"><br>
    {$this->content->label}</a>
</div>
EOT;
  }

  protected function PictureContentAsBody() {
    if( ! $this->contentIsReadable() ) { return ""; }
    $picture = <<<EOT
<div class="picture">
  {$this->content->label}<br>
  {$this->previousPicture}
  <img src="images/{$this->content->file}">
  {$this->nextPicture}
</div>
EOT;
    return $this->mainTemplate($picture);
  }

  protected function PictureContentAsEmbedded() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="embedded picture">
<a href="{$this->content->cid}">
  <img src="images/{$this->content->file}"><br>
  {$this->content->label}
</a>
</div>
EOT;
  }
  
  protected function previousPicture() {
    if( $album = $this->getCurrentAlbum() ) {
      $current = array_search( $this->content->cid, $album->children );
      if( $current > 0 ) {
        $prev = Content::get($album->children[$current - 1]);
        return <<<EOT
  <a href="{$prev->cid}"><img src="images/75x75/{$prev->file}"></a>
EOT;
      }
    }
    // in case we don't provide a previous picture, provide a placeholder
    return '<span class="placeholder">&nbsp</span>';
  }
  
  protected function nextPicture() {
    if( $album = $this->getCurrentAlbum() ) {
      $current = array_search( $this->content->cid, $album->children );
      if( $current < count($album->children) - 1 ) {
        $next = Content::get($album->children[$current + 1]);
        return <<<EOT
  <a href="{$next->cid}"><img src="images/75x75/{$next->file}"></a>
EOT;
      }
    }
    // in case we don't provide a previous picture, provide a placeholder
    return '<span class="placeholder">&nbsp</span>';
  }

  private function getCurrentAlbum() {
    $path = Context::getInstance()->path->asArray();
    if( count($path) > 1 ) {
      $album = $path[count($path)-2];
      if( get_class($album) == "AlbumContent" ) {
        return $album;
      }
    }
  }

  /**
   * Returns the content that is currently in scope as HTML. Content is stored
   * as a BreakDown encoded string.
   */ 
  protected function contentAsHtml() {
    return Breakdown::getConverter()->makeHtml((string)$this->content);    
  }
  
  /**
   * Gravatar support. Two template functions return a large and small image.
   */
  protected function largeGravatar() { return $this->gravatar(50); }
  protected function smallGravatar() { return $this->gravatar(25); }

  private function gravatar($size = 50) {
    $url = $this->gravatarURL($size);
    return <<<EOT
<img class="gravatar" src="{$url}" width="$size" height="$size">
EOT;
  }

  /**
   * Returns a Gravatar URL, based on the content's author's email address
   */
  private function gravatarURL($size = 50) {
    $defaultImage = "http://" . $_SERVER['HTTP_HOST'] . 
                    dirname($_SERVER['SCRIPT_NAME'])  . 
                    "/skins/vbsg/images/unknown_user.png";
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
  protected function insertUserBar() {
    return $this->user->isAnonymous() ? 
      $this->showLogin() : $this->showUser();
  }
  
  /**
   * If a user is not logged in, we offer him a logon form.
   */
  private function showLogin() {
    return <<<EOT
<a href="javascript:" onclick="showPopup('logon');">aanmelden</a>
| <a href="javascript:" onclick="showPopup('register');">registreren</a>
| (<a href="?initMockData=true">reset</a>)
EOT;
  }

  /**
   * If a user is logged in, we display his name and a logout action.
   */
  private function showUser() {
    return <<<EOT
{$this->smallGravatar} {$this->user} ({$this->user->role}) 
| <a href="?action=logout">afmelden</a>
| <a href="javascript:" onclick="showPopup('addcontent');">toevoegen</a>
| (<a href="?initMockData=true">reset</a>)
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
  
  protected function insertPopups() {
    return <<<EOT
<div id="logon-overlay">
  <div id="logon-popup" class="withRoundedCorners">
    <h1>Logon ...</h1>
	  <div class="actions">
		  <a id="closer" href="#" class="icon close"
			   onclick="hidePopup('logon');"><span>close</span></a>
	  </div>
    <form action="./" method="post">
      username : <input name="login"><br>
      password : <input type="password" name="pass"><br>
      <input type="submit">
    </form>
  </div>
</div>

<div id="register-overlay">
	<div id="register-popup" class="withRoundedCorners">
		<h1>Ready to register ?</h1>
		<div class="actions">
			<a id="closer" href="#" class="icon close"
				 onclick="hidePopup('register');"><span>close</span></a>
		</div>

    <p>
Not yet registered ? Fill out the form below, make some choices and press the
register button. Print the registration form, sign it and have your child
return it to school. We'll send you a confirmation once you account has been
activated.
    </p>

    <hr>

    <p>
If you already have an account with any of the following providers, you can
use that to identify yourself. We will not receive nor store your password,
but simply rely on your provider to prove that you are you. To identify
yourself, just press the icon of your provider. After successfully having
identified yourself, you will return to this site to complete your
registration.
    </p>

    <p>
  <a href="javascript:" onclick="">Google/Gmail</a>
| <a href="javascript:" onclick="">MyOpenID</a>
| <a href="javascript:" onclick="">Yahoo</a>
    </p>

    <hr>

    <p>
If you don't have such an account, no problem, we'll manage it for you. Just
provide us with the information below and we'll start your registration right
now.
    </p>

    <p>
      <form action="./" method="post">
        username : <input name="login"><br>
        password : <input type="password" name="pass"><br>
        repeat password : <input type="password" name="pass"><br>
        <input type="submit" value="register">
      </form>
    </p>
  </div>
</div>

<div id="addcontent-overlay">
	<div id="addcontent-popup" class="withRoundedCorners">
		<h1>Add new Content...</h1>
		<div class="actions">
			<a id="closer" href="#" class="icon close"
				 onclick="hidePopup('addcontent');"><span>close</span></a>
		</div>
    <script>
function addContent() {
  var form = document.getElementById('addcontent-form');
  var name = document.getElementById('addcontent-name');

  form.action = name.value;
  form.submit();
}
    </script>
    <form id="addcontent-form" action="?create&mode=edit&type=" method="GET">
      <input type="hidden" name="create" value="true">
      <input type="hidden" name="mode"   value="edit">
      name : <input type="text" id="addcontent-name"><br>
      type : <select name="type">
              <option value="PageContent">Page</option>
              <option value="AlbumContent">Album</option>
              <option value="PictureContent">Picture</option>
            </select><br>
      <input type="submit" value="add..." onclick="addContent();">
    </form>
  </div>
</div>
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

  /**
   * Wrapper function for the AuthorizationManager to check if the current
   * user can update the current content.
   */
  private function contentIsEditable() {
    return AuthorizationManager::getInstance()
            ->can( $this->user )->update( $this->content );
  }

  /**
   * Wrapper function for the AuthorizationManager to check if the current
   * user can read the current content.
   */
  private function contentIsReadable() {
    return AuthorizationManager::getInstance()
            ->can( $this->user )->read( $this->content );
  }

}