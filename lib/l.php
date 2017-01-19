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
class L extends Silo {
  public static $data = array();

  public static function get($key = null, $default = null) {
    $value = parent::get($key, $default);
    if (is_array($default)) {
      return str::template($value, $default);
    }

    return $value;
  }
}
