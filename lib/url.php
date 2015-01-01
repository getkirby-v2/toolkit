<?php

/**
 * Url
 *
 * A bunch of handy methods to work with URLs
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Url {

  static public $home    = '/';
  static public $to      = null;
  static public $current = null;

  static public function scheme($url = null) {
    if(is_null($url)) return 'http' . ((empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? '' : 's' );
    return parse_url($url, PHP_URL_SCHEME);
  }

  /**
   * Returns the current url with all bells and whistles
   *
   * @return string
   */
  static public function current() {
    if(!is_null(static::$current)) return static::$current;
    return static::$current = static::scheme() . '://' . server::get('HTTP_HOST') . server::get('REQUEST_URI');
  }

  /**
   * Returns the url for the current directory
   *
   * @return string
   */
  static public function currentDir() {
    return dirname(static::current());
  }

  /**
   */
  static public function host($url = null) {
    if(is_null($url)) $url = static::current();
    return parse_url($url, PHP_URL_HOST);
  }

  /**
   * Returns the port for the given url
   *
   * @return mixed
   */
  static public function port($url = null) {
    if(is_null($url)) $url = static::current();
    return parse_url($url, PHP_URL_PORT);
  }

  /**
   * Returns only the cleaned path of the url
   */
  static public function path($url = null) {

    if(is_null($url)) $url = static::current();

    // if a path is passed, let's pretend this is an absolute url
    // to trick the url parser. It's a bit hacky but it works
    if(!static::isAbsolute($url)) $url = 'http://0.0.0.0/' . $url;

    return trim(parse_url($url, PHP_URL_PATH), '/');

  }

  /**
   * Returns the params in the url
   */
  static public function params($url = null) {
    if(is_null($url)) $url = static::current();
    $path = static::path($url);
    if(empty($path)) return array();
    $params = array();
    foreach(explode('/', $path) as $part) {
      $pos = strpos($part, ':');
      if($pos === false) continue;
      $params[substr($part, 0, $pos)] = substr($part, $pos+1);
    }
    return $params;
  }

  /**
   * Returns the path without params
   */
  static public function fragments($url = null) {
    if(is_null($url)) $url = static::current();
    $path = static::path($url);
    if(empty($path)) return null;
    $frag = array();
    foreach(explode('/', $path) as $part) {
      if(strpos($part, ':') === false) $frag[] = $part;
    }
    return $frag;
  }

  /**
   * Returns the query as array
   */
  static public function query($url = null) {
    if(is_null($url)) $url = static::current();
    parse_str(parse_url($url, PHP_URL_QUERY), $array);
    return $array;
  }

  /**
   */
  static public function hash($url = null) {
    if(is_null($url)) $url = static::current();
    return parse_url($url, PHP_URL_FRAGMENT);
  }

  static public function build($parts = array(), $url = null) {

    if(is_null($url)) $url = static::current();

    $defaults = array(
      'scheme'    => static::scheme($url),
      'host'      => static::host($url),
      'port'      => static::port($url),
      'fragments' => static::fragments($url),
      'params'    => static::params($url),
      'query'     => static::query($url),
      'hash'      => static::hash($url),
    );

    $parts  = array_merge($defaults, $parts);
    $result = array($parts['scheme'] . '://' . $parts['host'] . r(!empty($parts['port']), ':' . $parts['port']));

    if(!empty($parts['fragments'])) $result[] = implode('/', $parts['fragments']);
    if(!empty($parts['params']))    $result[] = static::paramsToString($parts['params']);
    if(!empty($parts['query']))     $result[] = '?' . static::queryToString($parts['query']);
    if(!empty($parts['hash']))      $result[] = '#' . $parts['hash'];

    return implode('/', $result);

  }

  static public function queryToString($query = null) {
    if(is_null($query)) $query = url::query();
    return http_build_query($query);
  }

  static public function paramsToString($params = null) {
    if(is_null($params)) $params = url::params();
    $result = array();
    foreach($params as $key => $val) $result[] = $key . ':' . $val;
    return implode('/', $result);
  }

  static public function stripPath($url = null) {
    if(is_null($url)) $url = static::current();
    return static::build(array('fragments' => array(), 'params' => array()), $url);
  }

  static public function stripFragments($url = null) {
    if(is_null($url)) $url = static::current();
    return static::build(array('fragments' => array()), $url);
  }

  static public function stripParams($url = null) {
    if(is_null($url)) $url = static::current();
    return static::build(array('params' => array()), $url);
  }

  /**
   * Strips the query from the URL
   *
   * <code>
   *
   * echo url::stripQuery('http://www.youtube.com/watch?v=9q_aXttJduk');
   * // output: http://www.youtube.com/watch
   *
   * </code>
   *
   * @param  string  $url
   * @return string
   */
  static public function stripQuery($url = null) {
    if(is_null($url)) $url = static::current();
    return static::build(array('query' => array()), $url);
  }

  /**
   * Strips a hash value from the URL
   *
   * <code>
   *
   * echo url::stripHash('http://testurl.com/#somehash');
   * // output: http://testurl.com/
   *
   * </code>
   *
   * @param  string  $url
   * @return string
   */
  static public function stripHash($url) {
    if(is_null($url)) $url = static::current();
    return static::build(array('hash' => ''), $url);
  }

  /**
   * Checks if an URL is absolute
   *
   * @return boolean
   */
  static public function isAbsolute($url) {
    // don't convert absolute urls
    return (str::startsWith($url, 'http://') or str::startsWith($url, 'https://'));
  }

  /**
   * Convert a relative path into an absolute URL
   *
   * @param string $path
   * @param string $home
   * @return string
   */
  static public function makeAbsolute($path, $home = null) {

    if(static::isAbsolute($path)) return $path;

    // build the full url
    $path = ltrim($path, '/');
    $home = is_null($home) ? static::$home : $home;

    if(empty($path)) return $home;

    return $home == '/' ? '/' . $path : $home . '/' . $path;

  }

  /**
   * Tries to fix a broken url without protocol
   *
   * @param string $url
   * @return string
   */
  static public function fix($url) {
    // make sure to not touch absolute urls
    return (!preg_match('!^(https|http|ftp)\:\/\/!i', $url)) ? 'http://' . $url : $url;
  }

  /**
   * Returns the home url if defined
   *
   * @return string
   */
  static public function home() {
    return static::$home;
  }

  /**
   * The url smart handler. Must be defined before
   *
   * @return string
   */
  static public function to() {
    return call_user_func_array(static::$to, func_get_args());
  }

  /**
   * Return the last url the user has been on if detectable
   *
   * @return string
   */
  static public function last() {
    return r::referer();
  }

  /**
   * Returns the base url
   *
   * @param string $url
   * @return string
   */
  static public function base($url = null) {
    if(is_null($url)) $url = static::current();
    return static::scheme($url) . '://' . static::host($url);
  }

  /**
   * Shortens a URL
   * It removes http:// or https:// and uses str::short afterwards
   *
   * <code>
   *
   * echo url::short('http://veryveryverylongurl.com', 30);
   * // output: veryveryverylongurl.com
   *
   * </code>
   *
   * @param  string  $url The URL to be shortened
   * @param  int     $chars The final number of characters the URL should have
   * @param  boolean $base True: only take the base of the URL.
   * @param  string  $rep The element, which should be added if the string is too long. Ellipsis is the default.
   * @return string  The shortened URL
   */
  static public function short($url, $length = false, $base = false, $rep = '…') {

    if($base) $url = static::base($url);

    // replace all the nasty stuff from the url
    $url = str_replace(array('http://', 'https://', 'ftp://', 'www.'), '', $url);

    // try to remove the last / after the url
    $url = rtrim($url, '/');

    return ($length) ? str::short($url, $length, $rep) : $url;

  }

}

// basic home url setup
url::$home = url::base();

// basic url generator setup
url::$to = function($path = '/') {

  if(url::isAbsolute($path)) return $path;

  $path = ltrim($path, '/');

  if(empty($path)) return url::home();

  return url::home() . '/' . $path;

};