<?php

include_once dirname(__FILE__) . '/Singleton.php';

class AuthorizationManager extends Singleton {
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
  final public function update( $resource ) {
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
    $validator = get_class($accessor) . get_class($resource);
    if( method_exists( $this, $validator ) ) {
      return $this->$validator($accessor, $resource, $access );
    }
    return false;
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
    return $access == "read" ? true : ! $user->isAnonymous();
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
   * Validator for Users accessing Albums
   * @param $user accessing an album
   * @param $album accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the comment
   *         according to the given access style.
   */
  private function UserAlbumContent( $user, $album, $access = 'read' ) {
    // policy: read access for all / write access for known users only
    return $access == "read" ? true : ! $user->isAnonymous();
  }

  /**
   * Validator for Users accessing Pictures
   * @param $user accessing a picture
   * @param $picture accessed by the user
   * @param $access string representation of the access type. 
   *        possible values: read, update
   * @return Boolean indicating if the user can access the comment
   *         according to the given access style.
   */
  private function UserPictureContent( $user, $picture, $access = 'read' ) {
    // policy: read access for all / write access for known users only
    return $access == "read" ? true : ! $user->isAnonymous();
  }
} 
