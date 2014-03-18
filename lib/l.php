<?php

/**
 * 
 * Language
 * 
 * Some handy methods to handle multi-language support
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class L {
  
  /**
   * The global language array
   * 
   * @var array
   */
  static public $lang = array();

  /**
   * Gets a language value by key
   * 
   * <code>
   * 
   * // for german users
   * echo l::get('yes');
   * // output: Ja
   * 
   * // for english users
   * echo l::get('yes');
   * // output: yes
   * 
   * a::show( l::get() );
   * // returns the whole language array
   * 
   * </code>
   *
   * @param  mixed    $key The key to look for. Pass false or null to return the entire language array. 
   * @param  mixed    $default Optional default value, which should be returned if no element has been found
   * @return mixed
   */
  static public function get($key = null, $default = null) {
    if(empty($key)) return static::$lang;
    return isset(static::$lang[$key]) ? static::$lang[$key] : $default;
  }

  /** 
   * Sets a language value by key
   *
   * <code>
   * 
   * // in the german translation file
   * l::set('yes', 'Ja');
   * 
   * // in the english translation file    
   * l::set('yes', 'yes');
   * 
   * // set multiple values at once
   * l::set(array(
   *     'yes' => 'Ja',
   *     'no'  => 'Nein'
   * ));   
   * 
   * </code>
   * 
   * @param  mixed   $key The key to define
   * @param  mixed   $value The value for the passed key
   */  
  static public function set($key, $value=null) {
    if(is_array($key)) {
      static::$lang = array_merge(static::$lang, $key);
    } else {
      static::$lang[$key] = $value;
    }
  }

  /**
   * Removes a variable from the language array
   * 
   * @param string $key
   */
  static public function remove($key) {
    unset(static::$lang[$key]);
  }
  
}