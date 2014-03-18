<?php

/**
 * Config 
 * 
 * This is the core class to handle 
 * configuration values/constants. 
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class C {

  /** 
   * The static config array
   * It contains all config values
   * 
   * @var array
   */
  static public $data = array();

  /** 
   * Sets a config value by key
   *
   * <code>
   * 
   * // sets the hostname for the database for example
   * c::set('db.host', 'localhost');
   *
   * // you can store all the values here you will need later somewhere
   * c::set('title', 'The Title of your App');
   * 
   * // setting multiple variables at once
   * c::set(array(
   *   'key1' => 'val1',
   *   'key2' => 'val2',
   *   'key3' => 'val3'
   * ));
   * 
   * </code>
   *
   * @param  string  $key The key to define
   * @param  mixed   $value The value for the passed key
   */  
  static public function set($key, $value = null) {
    if(is_array($key)) {
      // set all new values
      static::$data = array_merge(static::$data, $key);
    } else {
      static::$data[$key] = $value;
    }
  }
  
  /** 
   * Gets a config value by key
   * 
   * <code>
   * 
   * // get the hostname for the database and take the localhost if it's not defined.
   * c::get('db.host', 'localhost');
   * 
   * // sample output: 'The Title of your App'
   * $title = echo c::get('title');
   * 
   * // get an array of values
   * $array = c::get(array('key1', 'key2'));
   * 
   * </code>
   *
   * @param  string  $key The key to look for. Pass false to get the entire config array
   * @param  mixed   $default The default value, which will be returned if the key has not been found
   * @return mixed   The found config value
   */  
  static public function get($key = null, $default = null) {
    if(empty($key)) return static::$data;
    return isset(static::$data[$key]) ? static::$data[$key] : $default;
  }

  /**
   * Removes a variable from the config array
   * 
   * <code>
   *
   * // remove the title from the config array
   * c::remove('title');
   *  
   * // the title will no longer return anything
   * c::get('title');
   * 
   * </code>
   * 
   * @param string $key
   * @return array
   */
  static public function remove($key = null) {
    // reset the entire array
    if(is_null($key)) return static::$data = array();
    // unset a single key
    unset(static::$data[$key]);
    // return the array without the removed key
    return static::$data;
  }

}