<?php 

namespace Kirby\Toolkit;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Params
 * 
 * Parsing and creation of custom URL parameters
 * e.g. http://getkirby.com/param1:value1/param2:value2
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Params extends Collection {

  /**
   * Constructor
   * 
   * @param mixed $array
   */
  public function __construct($array = null) {
    if(!is_array($array)) {
      $parts = str::split($array, '/');
      $array = array();
      foreach($parts as $part) {
        $param = str::split($part, ':');
        $key   = array_shift($param);
        $value = implode(':', $param);
        $array[$key] = $value;
      }      
    }
    parent::__construct($array);
  }

  /**
   * Converts the array of params to a string
   * 
   * @return string
   */
  public function toString() {
    $result = array();
    foreach($this->toArray() as $key => $value) {
      $result[] = $key . ':' . $value;
    }
    return implode('/', $result);
  }

  /**
   * Makes it possible to echo the entire object
   * to get the stringified version
   * 
   * @return string
   */
  public function __toString() {
    return $this->toString();
  }    

  /**
   * Static factory method for the Params object
   * 
   * @param mixed $array
   * @return objects
   */
  static public function create($array) {
    return new static($array);
  }

}