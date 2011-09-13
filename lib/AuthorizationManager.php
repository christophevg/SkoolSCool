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
  final public function read( $resource ) {
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
   * Validator for Users accessing Pages
   * @param $user accessing a page
   * @param $page accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the page
   *         according to the given access style.
   */
  private function UserPageContent( $user, $page, $access = 'read' ) {
    // policy: read access for all / write access for known users only
    // system-owned pages can only be edited by admins
    return $access == "read" ? true :
      ( is_object($page) && $page->hasAuthor(User::get('system')) ?
        $user->isAdmin() : ! $user->isAnonymous() );
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
   * Validator for Users accessing/uploading Albums
   * @param $user accessing Album
   * @param $album accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the album
   *         according to the given access style.
   */
  private function UserAlbumContent( $user, $album, $access = 'read' ) {
    // policy: update access for admins only ... for now
    return $user->isAdmin() || $user->isContributor();
  }
  
} 
