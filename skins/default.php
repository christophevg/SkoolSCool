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
include_once dirname(__FILE__) . '/default/breakdown/php/breakdown.php';

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
  protected function body() {
    return $this->mainTemplate($this->bodyContent());
  }
  
  private function mainTemplate($content) {
    return <<<EOT
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./skins/default/screen.css">

  <script src="./skins/default/breakdown/js/breakdown.js"></script>
  <script src="./skins/default/notify.js"></script>
  <script src="./skins/default/ajax.js"></script>
  <script src="./skins/default/users.js"></script>
  <script src="./skins/default/editing.js"></script>
  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="./skins/default/screen.ie6.css">
  <![endif]-->
</head>
<body>
  {$this->userBar}
  <h1 style="margin:0px">SkoolSCool - Default Skin</h1>
  location : {$this->breadCrumbs}<br>
  navigation:
  [ <a href="?cid=home">home</a> ]
  [ <a href="?cid=info">info</a> ]
  [ <a href="?cid=pictures">pictures</a> ]
  <hr>
  <div class="body">
    {$content}
    <div class="subcontent">
      {$this->subContent}
    </div>
  </div>
  <br clear="both">
  {$this->footer}
</body>
<!-- this page was generated in {$this->duration} seconds  -->
</html>
EOT;
EOT;
  }
  
  protected function breadCrumbs() {
    $path = array();
    foreach( Context::getInstance()->path->getPath() as $part ) {
      $cid = $part->cid;
      $path[] = "<a href=\"?cid=$cid\">$cid</a>";
    }
    return join( " &gt;&gt; ", $path );
  }

  protected function bodyContent() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
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
   * In stead of the item and body methods, it is also possible to supply
   * specific item and body methods for each content type. If these are
   * available, they will be used, otherwise the general body and item methods
   * will be called.
   * It this case, Comments will be rendered in specific way AND only in case
   * the user is logged on. Anonymous users don't see comments.
   */
  protected function CommentAsItem() {
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
  
  protected function AlbumAsItem() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="album">
  <a href="?cid={$this->content->cid}"
    ><img src="images/75x75/{$this->content->key}"><br>
    {$this->content->label}</a>
</div>
EOT;
  }

  protected function PictureAsItem() {
    if( ! $this->contentIsReadable() ) { return ""; }
    return <<<EOT
<div class="preview">
  <a href="?cid={$this->content->cid}"
    ><img src="images/75x75/{$this->content->file}"><br>
    {$this->content->label}</a>
</div>
EOT;
  }

  protected function PictureAsBody() {
    if( ! $this->contentIsReadable() ) { return ""; }
    $picture = <<<EOT
<div class="picture">
  {$this->content->label}<br>
  <img src="images/{$this->content->file}">
</div>
EOT;
    return $this->mainTemplate($picture);
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
<img src="{$url}" width="$size" height="$size">
EOT;
  }

  /**
   * Returns a Gravatar URL, based on the content's author's email address
   */
  private function gravatarURL($size = 50) {
    $defaultImage = "http://" . $_SERVER['HTTP_HOST'] . 
                    dirname($_SERVER['SCRIPT_NAME'])  . 
                    "/skins/default/images/unknown_user.png";
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
    <a href="javascript:" onclick="showLogon();">log on</a>
  | <a href="javascript:" onclick="showRegister();">register</a>
  | <a href="?initMockData=true">reset</a>
  </div>
  <div id="logon" style="display:none;">
    <form action="./" method="post">
      username : <input name="login">
      password : <input type="password" name="pass">
      <input type="submit">
    </form>
    <a href="javascript:" onclick="showUserActions();">cancel</a>
  | <a href="javascript:" onclick="showRegister();">register</a>
  | <a href="?initMockData=true">reset</a>
  </div>
</div>

<div id="register-overlay" onclick="hidePopUp();">
	<div id="popup" class="withRoundedCorners" onclick="return false;">
		<h1>Ready to register ?</h1>
		<div class="actions">
			<a id="closer" href="#" class="icon close"
				 onclick="hideRegister();"><span>close</span></a>
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
EOT;
  }

  /**
   * If a user is logged in, we display his name and a logout action.
   */
  private function showUser() {
    return <<<EOT
<div class="userbar">
{$this->smallGravatar}
{$this->user} ({$this->user->role})<br>
<a href="?action=logout">logout</a>
| <a href="?initMockData=true">reset</a>
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
