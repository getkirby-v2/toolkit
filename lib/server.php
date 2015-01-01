<?php

/**
 * 
 * Server
 * 
 * Makes it more convenient to get variables
 * from the global server array
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Server {

  /**
   * Gets a value from the _SERVER array
   * 
   * <code>
   * 
   * server::get('document_root');
   * // sample output: /var/www/kirby
   * 
   * server::get();
   * // returns the whole server array
   *
   * </code>   
   *
   * @param  mixed    $key The key to look for. Pass false or null to return the entire server array. 
   * @param  mixed    $default Optional default value, which should be returned if no element has been found
   * @return mixed
   */  
  static public function get($key = false, $default = null) {
    if(empty($key)) return $_SERVER;
    return a::get($_SERVER, str::upper($key), $default);
  }

}
