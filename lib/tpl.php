<?php 

/**
 * Tpl
 * 
 * Super simple template engine
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Tpl {

  static public $data = array();

  static public function set($key, $value = null) {
    if(is_array($key)) {
      foreach($key as $k => $v) {
        static::$data[$k] = $v;
      }
      return static::$data;
    }
    return static::$data[$key] = $value;
  }

  static public function load($file, $data = array(), $return = true) {
    if(!file_exists($file)) return false;
    ob_start();
    extract(array_merge(static::$data, (array)$data));
    require($file);
    $content = ob_get_contents();
    ob_end_clean();
    if($return) return $content;
    echo $content;
  }

}