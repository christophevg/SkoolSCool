<?php

class AuthorizationManager {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new AuthorizationManager();
    }
    return self::$instance;
  }

  private $accessor;

  /**
   * Sets the accessor part of the authorization request
   * @param $accessor an object representing the accessor of a resource
   * @return this to allow chaining in DSL style
   */
  final public function can( $accessor ) {
    $this->accessor = $accessor;
    return $this;
  }

  /**
   * Checks whether the previously set accessor can read the given resource
   * @param $resource representing the accessed resource
   * @return Boolean indicating if the accessor can read the resource
   */
  final public function read( $resource = null ) {
    return $this->check( $this->accessor, $resource, 'read' );
  }

  /**
   * Checks whether the previously set accessor can update the given resource
   * @param $resource representing the accessed resource
   * @return Boolean indicating if the accessor can update the resource
   */
  final public function update( $resource = null ) {
    return $this->check( $this->accessor, $resource, 'update' );
  }

  /**
   * Checks whether the previously set accessor can read the given resource
   * @param $accessor an object representing the accessor of a resource
   * @param $resource representing the accessed resource
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the accessor can access the resource
   *         according to the given access style.
   */
  final private function check( $accessor, $resource, $access ) {
    if( is_null( $accessor ) ) { return false; }
    $accessorClass = is_object($accessor) ? get_class($accessor) : $accessor;
    $resourceClass = is_object($resource) ? get_class($resource) : $resource;
    // check content-generic access
    $validator = $accessorClass . 'Content';
    if( method_exists( $this, $validator ) ) {
      if( ! $this->$validator( $accessor, $resource, $access ) ) {
        return false;
      }
    }
    // FIXME: this should be integrated better
    if( substr($resourceClass, 0, 6) == 'System' ) {
      // don't handle specific System Content Objects
      return true;
    }

    // check content-type specific access
    $validator = $accessorClass . $resourceClass;
    if( method_exists( $this, $validator ) ) {
      return $this->$validator($accessor, $resource, $access );
    }
    return false;
  }
  
  /**
   * Validator for Users' generic access
   * @param $user accessing a page
   * @param $anything is always null
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access anything
   *         according to the given access style.
   */
  final private function User( $user, $anything, $access = 'read' ) {
    // policy: read access for all / write access for known users only
    return $access == 'read' ? true : ! $user->isAnonymous();
  }
  
  /**
   * Validator for Users' global content access
   * @param $user accessing some content
   * @param $content any content object 
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access this content
   *         according to the given access style.
   */
  final private function UserContent( $user, $content, $access = 'read' ) {
    // is the supplied content isn't Content we don't care and pass on the
    // responsibility
    if( ! is_a( $content, 'Content' ) ) { return true; }

    // 0. admin user can see everything, always
    if( $user->isAdmin() ) { return true; }

    // 1. user-only content: no anonymous users
    if( $content->hasTag('user-only') && $user->isAnonymous() ) {
      return false;
    }

    // 2. generic <group>-only content
    // policy: tags can contain "<group>-only" with group being a usergroup
    $groups = $content->getTagsMatching( '/^([a-z]+)-only$/', 1 );
    foreach( $groups as $group ) {
      if( $group != "user" && ! $user->hasRight( $group ) ) { return false; }
    }

    // 3. generic not-<group> content
    // policy: tags can contain "not-<group>" with group being a usergroup
    $groups = $content->getTagsMatching( '/^not-([a-z]+)$/', 1 );
    foreach( $groups as $group ) {
      if( $group != "user" && $user->hasRight( $group ) ) { return false; }
    }
    
    return true;
  }

  /**
   * Validator for Users accessing Pages
   * @param $user accessing a page
   * @param $page accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the page
   *         according to the given access style.
   */
  private function UserPageContent( $user, $page, $access = 'read' ) {
    // policy: read access for all / write access for contributors only
    // system-owned pages can only be edited by admins
    return $access == "read" ? true :
      ( is_object($page) && $page->hasAuthor(User::get('system')) ?
        $user->isAdmin() : $user->isContributor() );
  }

  /**
   * Validator for Users accessing NewsPages
   * @param $user accessing a newspage
   * @param $page accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the page
   *         according to the given access style.
   */
  private function UserNewsContent( $user, $page, $access = 'read' ) {
    // policy: read access for all / write access for known users only
    return $access == "read" ? true : ! $user->isAnonymous();
  }

  /**
   * Validator for Users accessing NewsLists
   * @param $user accessing a newslist
   * @param $page accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the page
   *         according to the given access style.
   */
  private function UserNewsList( $user, $page, $access = 'read' ) {
    // policy: read access for all / no write access
    return $access == "read";
  }

  /**
   * Validator for Users accessing HtmlPages
   * @param $user accessing a page
   * @param $page accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the page
   *         according to the given access style.
   */
  private function UserHtmlContent( $user, $page, $access = 'read' ) {
    // policy: read access for all / write access for admin only
    return $access == "read" ? true : $user->isAdmin();
  }

  /**
   * Validator for Users accessing Comments
   * @param $user accessing a comment
   * @param $comment accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the comment
   *         according to the given access style.
   */
  private function UserCommentContent( $user, $comment, $access = 'read' ) {
    // policy: read access only for known users / write access for authr/admin
    return $access == "read" ? ! $user->isAnonymous()
      : $comment->hasAuthor($user) || $user->isAdmin();
  }

  /**
   * Validator for Users accessing/uploading files
   * @param $user accessing file
   * @param $file accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the file
   *         according to the given access style.
   */
  private function UserFileContent( $user, $file, $access = 'read' ) {
    // policy: update access for admins and contributors only
    return $user->isAdmin() || $user->isContributor();
  }

  /**
   * Validator for Users accessing/uploading Albums
   * @param $user accessing Album
   * @param $album accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the album
   *         according to the given access style.
   */
  private function UserAlbumContent( $user, $album, $access = 'read' ) {
    // policy: update access for admins and contributors only
    return $user->isAdmin() || $user->isContributor();
  }
  
  /**
   * Validator for Users accessing User records
   * @param $user accessing
   * @param $userObj accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the record
   *         according to the given access style.
   */
  private function UserUser( $user, $userObj, $access = 'read' ) {
    // only admin users can access user records
    return $user->isAdmin();
  }

  /**
   * Validator for Users accessing Identity records
   * @param $user accessing
   * @param $IdentityObj accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the record
   *         according to the given access style.
   */
  private function UserIdentity( $user, $identityObj, $access = 'read' ) {
    // only admin users can access user records
    return $user->isAdmin();
  }

} 
