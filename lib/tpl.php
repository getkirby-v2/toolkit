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
class Tpl extends Silo {

  public static $data = array();

  public static function load($_file, $_data = array(), $_return = true) {
    if(!file_exists($_file)) return false;
    ob_start();
    extract(array_merge(static::$data, (array)$_data));
    require($_file);
    $_content = ob_get_contents();
    ob_end_clean();
    if($_return) return $_content;
    echo $_content;
  }

  /**
   * Block contents.
   *
   * @var array
   */
  static public $blocks = [];

  /**
   * Extend contents.
   *
   * @var array
   */
  static public $extends = [];
  
  /**
   * Start block.
   * 
   * @param  String $name
   * @return Null
   */
  public static function block_start($name) {

    if (!isset(static::$blocks[$name])){
      static::$blocks[$name] = [];
    }

    ob_start();
  }

  /**
   * End block.
   * 
   * @param  String $name
   * @return Null
   */
  public static function block_end($name)
  {
    $out = ob_get_clean();

    if (isset(static::$blocks[$name])) {
      static::$blocks[$name][] = $out;
    }
  }

  /**
   * Get block content.
   * 
   * @param  String $name
   * @param  array  $options
   * @return String
   */
  public static function block($name, $options = [])
  {
    if (!isset(static::$blocks[$name])) {
      return null;
    }

    $options = array_merge([
        'print' => true
    ], $options);

    $block = implode("\n", static::$blocks[$name]);

    if ($options['print']) {
      echo $block;
    }

    return $block;
  }

  /**
   * Open extend block for include the main template.
   * 
   * @param $template
   */
  public static function extend_start($template)
  {
    $template_file = Kirby::instance()->roots()->templates() . DS . $template . '.php';

    if (file_exists($template_file) and !isset(static::$extends[$template])) {
      static::$extends[$template] = $template_file;
    }
  }

  /**
   * Close the extend block and print out.
   *
   * @param $template
   * @return null
   */
  public static function extend_end($template)
  {
    if (isset(static::$extends[$template])) {
      ob_start();
      include static::$extends[$template];
      echo ob_get_clean();
    }
  }
}
