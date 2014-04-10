<?php

/**
 * Cache
 *
 * The ultimate cache wrapper for
 * all available drivers
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Cache {

  static public $driver = null;

  static public function setup($driver, $args = null) {
    $ref  = new ReflectionClass('Cache\\Driver\\' . $driver);
    return static::$driver = $ref->newInstanceArgs(array($args));
  }

  static public function __callStatic($method, $args) {

    if(is_null(static::$driver)) {
      throw new Exception('Please define a cache driver');
    }

    if(!is_a(static::$driver, 'Cache\\Driver')) {
      throw new Exception('The cache driver must be an instance of the Cache\\Driver class');
    }

    if(method_exists(static::$driver, $method)) {
      return call(array(static::$driver, $method), $args);
    } else {
      throw new Exception('Invalid cache method: ' . $method);
    }
  }

}