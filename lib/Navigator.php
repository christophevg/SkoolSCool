<?php

/**
 * Navigator
 *
 * The navigator parses the "navigation" content, which is a 2-level bullet
 * tree and provides information about it.
 */

class Navigator {
  private static $instance = null;

  final private function __construct() {}
  final private function __clone() {}
  final static public function getInstance() {
    if( !isset(self::$instance) ) {
      self::$instance = new Navigator();
      self::$instance->init();
    }
    return self::$instance;
  }

  private $content = null;     // raw navigation content object
  private $targets = array();  // all navigation targets (parsed)
  
  /**
   * Singleton constructor: loads content from datastore, parse targets and
   * (pre)loads all targets (to apply authorisation).
   */
  function init() {
    $this->content = Content::get('navigation');
    $this->parseTargets();
    $this->loadTargets();
  }

  /**
   * Parses all targets from the navigation
   */
  function parseTargets() {
    $lines = split("\n", (string)$this->content );
    foreach( $lines as $line ) {
      list($level, $line) = $this->parseIndentation($line);
      if( $level > 0 ) {
        list($oid, $context, $label) = $this->parseTarget($line);
        array_push( $this->targets, array( 'level'   => $level,
                                           'oid'     => $oid,
                                           'context' => $context,
                                           'label'   => $label ) );
      }
    }
  }
  
  /**
   * Parses the indentation of a single line from the navigation source.
   * @returns indentation level and the remainder of the line
   */
  function parseIndentation($line) {
    $line = trim($line);

    $level = 0;
    // count indentation
    while( substr($line, 0, 1) == '*' ) {
      $line = substr($line, 1);
      $level++;
    }
    return array( $level, $line );
  }

  /**
   * Parses a single line from the navigation source.
   * @returns content object id, optional context and optional label
   */
  function parseTarget($line) {
    $line = trim($line);
    if( substr($line, 0, 1) == '[' && substr($line, -1) == ']' ) {
      list( $oid, $context, $label ) = $this->parseLink($line);
    } else {
      $oid     = null;
      $context = null;
      $label   = $line;
    }
    return array( $oid, $context, $label );
  }

  /**
   * Parses a  single line from the navigation source, identified as a link
   * @returns content object id, optional context and optional label
   */
  function parseLink($line) {
    // strip link markers
    $line = substr($line, 1, -1);
    // split target and label
    if( preg_match( '/\|/', $line ) ) {
      list($target, $label) = preg_split( '/\|/', $line );
    } else {
      $target = $line;
      $label  = null;
    }
    // split context and oid
    $parts = preg_split( '/\//', $target );
    $oid = array_pop( $parts );
    $context = count($parts)> 0 ? join( '/', $parts ) : null;

    // FIXME: need to remove dashes to get id's
    $oid = str_replace('-',' ', $oid);
    return array( $oid, $context, $label );
  }

  /**
   * Loads all content objects, referenced in the Navigation.
   * TODO: this causes almost all content pages to be fetched from the
   *       data(store)BASE, ONE BY ONE, EVERY HIT. -> apply caching
   */
  function loadTargets() {
    for( $i=0; $i<count($this->targets); $i++ ) {
      $this->targets[$i]['content'] = Content::get($this->targets[$i]['oid']);
    }
  }

  /**
   * Returns the actual content object of the Navigation
   * @return navigation content object
   */
  function asContent() {
    return $this->content;
  }

  /**
   * Returns the HTML for the navigation, optionally only for a context
   * @param $context the id of the context
   * @return HTML of the navigation
   */
  function asHtml($context = null) {
    $html = Breakdown::getConverter()->makeHtml($this->asSource($context));
    return $html;
  }

  /**
   * Returns the source for the navigation, optionally only for a context
   * @param $context the id of the context
   * @return source of the navigation
   */
  function asSource($context = null) {
    $src = "";
    foreach( $this->targets as $target ) {
      if( $target['content'] != null ) {
        if( $context == null || $target['context'] == $context ) {
          $src .= str_pad( '', $target['level'], '*') . ' ';
          if( $target['oid'] != null ) {
            $src .= '[' . ($target['context'] != null ? $target['context'] . '/' : '')
                        . $target['oid']
                        . ($target['label'] != null ? '|' . $target['label'] : '')
                 .  ']';
          } else {
            $src .= $target['label'];
          }
          $src .= "\n";
        }
      }
    }
    return $src;
  }

  /**
   * Indicates if a given context as Navigation information
   * @param $context the id of the context
   * @return true or false, indicating if the context has a navigation
   */
  function contextHasNavigation($context = null) {
    return $this->getContextNavigationSource($context) != "";
  }
  
  /**
   * Returns the source for a context
   * @param $context the id of the context
   * @return source of navigation context
   */
  function getContextNavigationSource($context = null) {
    return $this->asSource($context);
  }

}
