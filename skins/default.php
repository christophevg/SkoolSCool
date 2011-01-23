<?php

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
  function body( $content ) {
    return <<<EOT
$this->userBar
<h1>Default Skin</h1>
<div>
  <b>$this->user</b> requested <b>$content</b> by <u>$content->author</u>
    <div>
$this->subcontent
    </div>
</div>
$this->footer

EOT;
  }

  /**
   * The item method is the second method that must be provided to have a 
   * minimal Skin implementation. It is used to render content that is linked
   * to body-level content.
   */
  function item( $content ) {
    return <<<EOT

<h2>SubContent</h2>
<b><i>$content->author</i></b> added child <b><i>$content</i></b>

EOT;
  }

  /**
   * In stead of the item and body methods, it is also possible to supply
   * specific item and body methods for each content type. If these are
   * available, they will be used, otherwise the general body and item methods
   * will be called.
   */
  function CommentAsItem( $content ) {
    return <<<EOT

<h2>Comment</h2>
<b><i>$content->author</i></b> added child <b><i>$content</i></b>

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

<form action="./" method="post">
  username : <input name="login"> password : <input type="password" name="pass"> <input type="submit">
</form>
    
EOT;
  }

  function showUser() {
    return <<<EOT

$this->user : <a href="?action=logout">logout</a>    

EOT;
  }
  
  function footer() {
    return <<<EOT

    <hr>
    a footer

EOT;
  }
}
