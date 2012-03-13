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
    $base = 'http://' . str_replace( '//', '/', 
          $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/' );
    return <<<EOT
<!DOCTYPE html>
<html>
<head>
  <base href="${base}">
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" >

  <title>Vrije Basisschool Schriek &amp; Grootlo</title>

  <link rel="stylesheet" type="text/css" href="./skins/vbsg/screen.css?{$this->includeVersion}">
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/navigation.css?{$this->includeVersion}">
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/fileuploader.css?{$this->includeVersion}">

  <!-- site automation -->
  <script src="./skins/vbsg/notify.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/ajax.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/popup.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/editing.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/json2.js?{$this->includeVersion}"></script>

  <!-- breakdown support -->
  <script src="./skins/vbsg/breakdown/js/breakdown.js?{$this->includeVersion}"></script>

  <!-- calendar support -->
  <script src="http://www.google.com/jsapi"></script>
  <script src="./skins/vbsg/cal.js/src/cal.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/cal.js/src/providers/google.js?{$this->includeVersion}"></script>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/cal.css?{$this->includeVersion}">

  <!-- photo support -->
  <script src="./skins/vbsg/photo.js/src/photo.js?{$this->includeVersion}"></script>
  <script src="./skins/vbsg/photo.js/src/providers/google.js?{$this->includeVersion}"></script>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/photo.css?{$this->includeVersion}">

  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/screen.ie6.css?{$this->includeVersion}">
  <![endif]-->
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="./skins/vbsg/screen.ie7.css?{$this->includeVersion}">
  <![endif]-->
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-25725717-1']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); 
      ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 
                'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(ga, s);
    })();
  </script>
</head>
<body>
  <div class="wrapper">

    <div class="page-{$this->content->url}">
    
      <div class="toolbar-wrapper">
        <div id="user-toolbar" class="toolbar">
          <img class="logo" src="./skins/vbsg/images/vbsg-logo.png" 
               alt="Vrij Basisschool Schriek & Grootlo">
          <p>Vrije Basisschool Schriek &amp; Grootlo</p>
          <div class="userbar">
{$this->insertUserBar}
          </div>
        </div>
      </div>

      <div id="navigation-toolbar" class="toolbar">
        <div class="_navigation">
{$this->includeNavigation}
        </div>
      </div>

      <div class="banner"></div>
  
      <div class="site {$this->toggleSubNavigation}">
        {$this->insertSectionNavigation}
        <div class="content">
{$content}
          <div id="subcontent" class="subcontent">
{$this->subContent}
<br clear="both"><br>
{$this->addNewComment}
          </div>
        </div>
      </div>

      <div class="push"></div>

    </div>

  </div>

  <div class="_footer">
{$this->includeFooter}
  </div>

{$this->insertPopups}

{$this->insertMessages}
</body>
<!-- this page was generated in {$this->duration} seconds  -->
</html>
EOT;
  }
  
  protected function includeVersion() {
    return "1.1-3";
  }
  
  protected function includeNavigation() {
    $navigation = Content::get('navigation');
    $html = Breakdown::getConverter()->makeHtml("* [home]\n".(string)$navigation);
    $html = ereg_replace( '</?p>','', $html );
    // add class to show current (TODO: do this in a clean way ;-) )
    $root = Context::$request->url[0];
    $html = str_replace( "<li><a href=\"$root\">", 
                         "<li class=\"selected\"><a href=\"$root\">", 
                         $html );
    // add link to directly edit the navigation page
    if( AuthorizationManager::getInstance()
        ->can( $this->user )->update( $navigation ) )
    {
      $html .= '<div class="icon command edit" onclick="window.location=\'navigation?mode=edit\'"></div>';
    }
    return $html;
  }
  
  protected function includeFooter() {
    $footer = Content::get('footer');
    $html = Breakdown::getConverter()->makeHtml((string)$footer);
    $html = ereg_replace( '</?p>','', $html );
    // add link to directly edit the footer page
    if( AuthorizationManager::getInstance()
        ->can( $this->user )->update( $footer ) )
    {
      $html .= '<div class="icon edit command" onclick="javascript:window.location=\'footer?mode=edit\'"></div></a>';
    }
    return '<div class="info">' . $html . '</div>';
  }

  protected function bodyContent() {
    if( ! $this->contentIsReadable() ) { return ""; }
    $contentClass = get_class( $this->content );
    return <<<EOT
<script>
var bodyContent = "{$this->content->url}";
var contentClass = "$contentClass";
</script>
<div id="{$this->content->url}Container" class="container">
  {$this->editControls}
  <div id="{$this->content->url}View" class="body">
    {$this->contentAsHtml}
  </div>
  {$this->bodyEditor}
</div>
EOT;
  }

  protected function toggleSubNavigation() {
    return $this->hasSubNavigation() ? "withSubNavigation" : "";
  }
  
  protected function hasSubNavigation() {
    return ( ( $this->content->id != "home" ) and 
             ( get_class($this->content) == "PageContent" or
               get_class($this->content) == "HtmlContent" ) and
             ( Navigator::getInstance()->currentSectionHasNavigation() ) );
  }
  
  protected function insertSectionNavigation() {
    if( ! $this->hasSubNavigation() ) { return; }
    $navigation = Navigator::getInstance()->getCurrentSectionNavigation();
    $html = Breakdown::getConverter()->makeHtml($navigation);
    // TODO: do this in a "nicer" way ;-)
    $self = join( '/', Context::$request->url );
    $html = str_replace( "<li><a href=\"$self\">", 
                         "<li class=\"selected\"><a href=\"$self\">", 
                         $html );
    return <<<EOT
    <div id="_subnavigation" class="_subnavigation">
    $html
    </div>
EOT;
  }

  private function generateCommands( $commands ) {
    $html = "";
    foreach( $commands as $command => $isActive ) {
      $lc_command = strtolower($command);
      $state = $isActive ? " active" : " inactive";
      $html .= <<<EOT
      <div id="{$this->content->url}{$command}Command" 
           class="icon {$lc_command} command{$state}"
           onclick="{$lc_command}Content('{$this->content->url}');"></div>
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
      <div id="{$this->content->url}{$state}State" class="icon wait state{$activation}"></div>
EOT;
    }
    return <<<EOT
<div id="{$this->content->url}Controls" class="controls">
{$commands}{$states}</div>
EOT;
  }
  
  protected function bodyEditor() {
    if( ! $this->contentIsEditable() ) { return ""; }
    return <<<EOT
<div id="{$this->content->url}Editor" class="editor">
  {$this->content->editor}
  {$this->editorControls}
</div>
EOT;
  }
  
  protected function editorControls() {
    $commands = $this->generateCommands( array( "preview" => true,
                                                "cancel"  => true ) );
    return <<<EOT
<div id="{$this->content->url}EditorControls" class="editorcontrols">
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
    {$this->content->author}
  </div>
  <div class="time">
    {$this->content->timeLabel}
  </div>
  <div class="balloon">
    {$this->bodyContent}
  </div>
</div>
EOT;
  }

  // TODO : move this to AuthorizationManager
  private function contentAllowsComments() {
    // Temporary: no comments
    return false;

    return  ( ! $this->user->isAnonymous() )
         && $this->content->id     != "home" 
         && $this->content->id     != "fotoboek"
         && $this->content->author != "system";
  }
  
  protected function addNewComment() {
    if( ! $this->contentAllowsComments() ) { return; }
    return <<<EOT
<div class="comment">
  <div class="commenter">Jij</div>
  <div class="balloon">
    <div class="container">
      <div class="add">
        <form method="post">
          <textarea class="newComment" name="comment" type="text"></textarea><br>
          <div class="icon command save" onclick="this.parentNode.submit();"></div>
        </form>
      </div>
    </div>
  </div>
</div>
EOT;
  }

  protected function actualRequest() {
    return Context::$request->id == $this->content->id ? 
            '' : ' ' . Context::$request->object;
  }

  protected function PageContentAsEmbedded() {
    $excerpt   = $this->contentAsHtml();
    $original  = Context::$request->string;
    $more      = '';
    if( strlen($excerpt) > 500 ) { 
      $excerpt = substr( $excerpt, 0, 500 ) . '...';
      $more = <<<EOT
<p class="more"><a href="{$original}">lees verder...</a></p>
EOT;
    }
    return <<<EOT
<div class="embedded content page {$this->content->url}{$this->actualRequest}">
  {$excerpt}
  {$this->socialBar}
  {$more}
</div>
EOT;
  }

  protected function NewsContentAsBody() {
    return $this->mainTemplate( 
        $this->bodyContent . 
        "<br>\n<br>\n<a href=\"nieuws\">&lt;&lt; terug naar het nieuws</a>"
    );
  }

  protected function NewsContentAsEmbedded() {
    return $this->PageContentAsEmbedded();
  }
  
  protected function socialBar() {
    // only show socialBar when the user is logged on
    if( $this->user->isAnonymous() ) { return ""; }
    // and only if we're showing the actually requested content
    if( Context::$request->object != $this->content->id ) { return; }

    $commentCount = count($this->content->children);
    // don't show the social-bar if there is no "activity"
    if( $commentCount < 1 ) { return; }
    return <<<EOT
  <div class="embedded socialbar" onclick="javascript:window.location='{$this->content->url}';">
    {$commentCount}
  </div>
EOT;
  }
  
  protected function HtmlContentAsEmbedded() {
    return <<<EOT
<div class="embedded content html {$this->content->url}{$this->actualRequest}">
  {$this->content}
</div>
EOT;
  }

  protected function NewsListAsEmbedded() {
    return <<<EOT
<div class="embedded content news {$this->content->url}{$this->actualRequest}">
  {$this->contentAsHtml}
</div>
EOT;
  }
  
  /**
   * Returns the content that is currently in scope as HTML. Content is stored
   * as a BreakDown encoded string unless the content object tells us, it 
   * already produces HTML.
   */ 
  protected function contentAsHtml() {
    if( is_null( $this->content ) ) { return 'missing content ...'; }
    return $this->content->isHtml() ? (string)$this->content :
      Breakdown::getConverter()->makeHtml((string)$this->content);    
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
| <a href="javascript:" onclick="showPopup('contact');">contacteer ons</a>
EOT;
  }

  /**
   * If a user is logged in, we display his name and a logout action.
   */
  private function showUser() {
    return <<<EOT
{$this->user} ({$this->user->role}) 
| <a href="?action=logout">afmelden</a>
| <a href="javascript:" onclick="showPopup('addcontent');">toevoegen</a>
| <a href="javascript:" onclick="showPopup('contact');">contacteer ons</a>
EOT;
  }

  protected function insertPopups() {
    return $this->insertLogonPopup()
         . $this->insertRegisterPopup()
         . $this->insertAddContentPopup()
         . $this->insertContactPopup();
  }
  
  protected function insertLogonPopup() {
    return <<<EOT
<div class="overlay" id="logon-overlay">
  <div id="logon-popup" class="popup withRoundedCorners">
	  <div class="actions">
		  <a id="closer" href="javascript:" class="icon close"
			   onclick="hidePopup('logon');"><span>close</span></a>
	  </div>

    <h1>Aanmelden</h1>

	  <div class="openid">
      <script>
      var openid = {
        config : { 
          google   : 'https://www.google.com/accounts/o8/id',
          myopenid : 'http://myopenid.com',
          yahoo    : 'http://me.yahoo.com'
        },
        signin : function(provider) {
          document.getElementById('openid').value = this.config[provider];
          document.getElementById('openidForm').submit();
          return false;
        }
      };
      </script>

      <form id="openidForm" action="./" method="post">
        <input id="openid" type="hidden" name="openid_identifier" /> 
      </form>

	    <p>Meld je aan met een van je bestaande profielen:</p>
	    <div class="providers">
        <a href="javascript:openid.signin('google');" class="openid google"></a>
        <!--<a href="javascript:" class="openid facebook"></a>-->
        <a href="javascript:openid.signin('yahoo');" class="openid yahoo"></a>
        <a href="javascript:openid.signin('myopenid');" class="openid myopenid"></a>
      </div>

	  </div>
	  
	  <div class="credentials">
	    <p>Beschik je over een specifieke gebruikersnaam en paswoord voor deze
	      site, geef die dan hier in ...</p>
      <form action="./" method="post">
        <span class="label">Naam</span> <input name="login">
        <span class="label">Paswoord</span> <input type="password" name="pass"><br>
        <input class="button" type="submit" value="meld aan...">
      </form>
    </div>
  </div>
</div>
EOT;
  }

  protected function insertRegisterPopup() {
    return <<<EOT
<div class="overlay" id="register-overlay">
	<div id="register-popup" class="popup withRoundedCorners">
		<h1>Ready to register ?</h1>
		<div class="actions">
			<a id="closer" href="javascript:" class="icon close"
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

    <form action="./" method="post">
      username : <input name="login"><br>
      password : <input type="password" name="pass"><br>
      repeat password : <input type="password" name="pass"><br>
      <input type="submit" value="register">
    </form>

  </div>
</div>
EOT;
  }

  protected function insertAddContentPopup() {
    $options = array( 'PageContent'  => "Een algemene tekstpagina",
                      'NewsContent'  => "Een nieuwsbericht",
                      'HtmlContent'  => "Een HTML pagina",
                      'AlbumContent' => "Een foto album" );
    $html = "";
    foreach( $options as $type => $label ) {
      if( AuthorizationManager::getInstance()
            ->can( Context::$currentUser )
            ->update( $type ) )
      {
        $html .= "<option value=\"$type\">$label</option>\n";
      }
    }

    return <<<EOT
<div class="overlay" id="addcontent-overlay">
	<div id="addcontent-popup" class="popup withRoundedCorners">
		<div class="actions">
			<a id="closer" href="javascript:" class="icon close"
				 onclick="hidePopup('addcontent');"><span>close</span></a>
		</div>
		<h1>Voeg nieuwe inhoud toe...</h1>
    <script>
    
function addContent() {
  var form = document.getElementById('addcontent-form');
  var name = document.getElementById('addcontent-name').value;

  if( name.match( /[a-z]+/ ) ) {
    if(document.getElementById("selectContentType").value != "AlbumContent") {
      form.action = name.replace( / /g, "-" );
    } else {
      var button = document.getElementById( 'addcontent-submit' );
      button.value = "Bezig...";
      button.disabled = true;
      upload_feedback( "Uw bestand wordt overgebracht naar de server...", 5 );
    }
    form.submit();
  } else {
    alert( "Gelieve een naam te kiezen." );
    document.getElementById("addcontent-name").focus();
  }
  return false;
}

function clear_upload_feedback() {
  document.getElementById( 'progress-msg' ).innerHTML = "";
  document.getElementById( 'progress-bar' ).style.width = "0%";
}

function upload_feedback( msg, pct ) {
  document.getElementById( 'progress-msg' ).innerHTML = msg;
  document.getElementById( 'progress-bar' ).style.width = pct + "%";
}

function upload_done( url ) {
  document.getElementById( 'addcontent-form' ).reset();
  var button = document.getElementById( 'addcontent-submit' );
  button.value = "voeg toe...";
  button.disabled = false;
  upload_feedback( "Klaar ...", 100 );
  alert( "Uw album is overgebracht naar de server. De foto's zijn weldra " + 
         "beschikbaar in het fotoboek. Dit kan tot 15 minuten duren." );
  clear_upload_feedback();
}

function changeContent() {
  var type = document.getElementById("selectContentType").value;
  document.getElementById( "addcontent-form" ).className = type;
  document.getElementById( "addcontent-name" ).value = "";
  document.getElementById( "addcontent-name-error" ).innerHTML = "";
  switch( type ) {
    case "PageContent":
    case "NewsContent":
    case "HtmlContent":
      document.getElementById( "addcontent-form" ).method="GET";
      document.getElementById( "addcontent-form" ).enctype="";
      document.getElementById( "addcontent-form" ).target="";
      break;
    case "AlbumContent":
      document.getElementById( "addcontent-form" ).method="POST";
      document.getElementById( "addcontent-form" ).enctype="multipart/form-data";
      document.getElementById( "addcontent-form" ).encoding="multipart/form-data";
      document.getElementById( "addcontent-form" ).target="iframe";
      break;
  }
}

function checkName() {
  var input = document.getElementById("addcontent-name");
  var value = input.value.replace(/^\s+/, "").replace(/\s+$/, ""); // trim
  var spinner = document.getElementById("addcontent-name-spinner");
  var error   = document.getElementById("addcontent-name-error");
  error.innerHTML = "";
  if( value != '' ) {
    spinner.className = "icon wait state active";
    __remote__.fetch( value, function(response) {
      spinner.className = "icon wait state inactive";
      if( response == value ) {
        error.innerHTML = "Deze naam is reeds in gebruik";
        input.focus();
        input.select();
      }
    } );
  }
}

var waiting_before_checking_name_change = null;

function watchChangeName() {
  if( waiting_before_checking_name_change != null ) { 
    clearTimeout( waiting_before_checking_name_change );
  }
  waiting_before_checking_name_change = setTimeout( checkName, 1000 );
}

function drop(file) {
  if( file.value.match(/\.(zip|ZIP)$/) ) {
    var result = "ok";
  } else {
    var result = "nok";
    file.value = "";
  }
  document.getElementById("addcontent-form").className = result;
  document.getElementById("addcontent-name").value = 
    file.value.replace( /\..*$/, "").replace( /^([^\\\/]*[\\\/])*/g, "" );
  checkName();
}

</script>

    </script>
    <form id="addcontent-form" action="" method="GET">
      <input type="hidden" name="create" value="true">
      <input type="hidden" name="mode"   value="edit">
      <span class="label">Soort</span>
      <select id="selectContentType" name="type" onchange="changeContent();">
        <option value="choose">Kies een soort ...</option>
{$html}
      </select><br><br>
      <div id="addcontent-file">
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000">

        <div id="dropzone">
          <div id="dropicon"></div>
          <input id="file" type="file" name="file" onchange="drop(this)" multiple>
          <div id="dropname"  class="dropmsg"></div>
          <div id="droperror" class="dropmsg">Not a ZIP file, try again.</div>
        </div>
        <div id="instructions"></div>
      </div>

      <span id="addcontent-name-label" class="label">Naam</span>
      <input id="addcontent-name" type="text" name="name" onkeyup="watchChangeName();">
      <div class="dummy controls">
        <div id="addcontent-name-spinner" class="icon wait state inactive"></div>
        <span id="addcontent-name-error" class="error-msg"></span>
      </div>

      <p align="center">
        <input id="addcontent-submit" type="submit" class="button" 
               value="voeg toe..." onclick="return addContent();">
      </p>

    </form>
    <iframe id="iframe" name="iframe" style="display:none;" src=""></iframe>
    <div id="progress">
      <div id="progress-msg"></div>
      <div id="progress-bar"></div>
    </div>
  </div>
</div>
EOT;
  }

  protected function insertContactPopup() {
    return <<<EOT
<div class="overlay" id="contact-overlay">
	<div id="contact-popup" class="popup withRoundedCorners">
		<div class="actions">
			<a id="closer" href="javascript:" class="icon close"
				 onclick="hidePopup('contact');"><span>close</span></a>
		</div>
		<h1>Contacteer ons...</h1>
    <form id="contact-form" method="POST">
      <span class="label">Naam</span>
      <input name="name" type="text" id="contact-name"><br>
      <br>
      <span class="label">Email</span>
      <input name="email" type="text" id="contact-email"><br>
      <br>
      <span class="label">Boodschap</span>
      <textarea name="message" id="contact-boodschap" rows="4" cols="30"></textarea><br>
      <br>
      <center>
        Controleer zeker je email adres goed!<br>
        Anders kunnen we je misschien niet bereiken.<br>
        <input type="submit" class="button" value="verzend..." onclick="">
      </center>
    </form>
  </div>
</div>
EOT;
  }
  
  /**
   * Inserts the HTML representing messages that were collected during the
   * processing of the request.
   */
  protected function insertMessages() {
    return Messages::getInstance()->asHtml();
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
