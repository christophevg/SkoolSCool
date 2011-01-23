<?php

abstract class Singleton {
  /**
   * Singleton instances holder
   */
  private static $instances = array();

  /**
   * Private constructor locks down Singleton
   */
  final private function __construct() {}

  /**
   * Prevents cloning the instance.
   */
  final private function __clone() {}

  /**
   * Singleton Retrieval Method
   * @return unique instance of the singleton
   */
  final static public function getInstance() {
    if( !isset(self::$instances[get_called_class()]) ) {
      self::$instances[get_called_class()] = new static;
      self::$instances[get_called_class()]->init();
    }
    return self::$instances[get_called_class()];
  }

  /**
   * Initializes a new singleton instance.
   */
  protected function init() {}

  /**
   * Destroys the singleton instances
   */
  final public static function destroyAll() {
    self::$instances = array();
  }
}
