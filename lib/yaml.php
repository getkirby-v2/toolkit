<?php

/**
 * YAML
 *
 * The Kirby YAML parser and creator Class
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Yaml {

  /**
   * Creates a new yaml string from an array
   *
   * @param array $array
   * @return string
   */
  static public function encode($array) {
    return preg_replace('!^---\n!', '', spyc::yamldump($array));
  }

  /**
   * Creates a new yaml file from an array
   *
   * @param string $file
   * @param array $array
   * @return boolean
   */
  static public function write($file, $array) {
    return f::write($file, static::encode($array));
  }

  /**
   * Parses a yaml string and returns the array
   *
   * @param string $yaml
   * @return array
   */
  static public function decode($yaml) {
    return spyc::yamlload($yaml);
  }

  /**
   * Reads and parses a yaml file and returns the array
   *
   * @param string $file
   * @return array
   */
  static public function read($file) {
    return spyc::yamlload($file);
  }

}