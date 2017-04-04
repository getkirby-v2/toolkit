<?php

namespace Cache\Driver;

use Cache\Driver;

/**
 * WinCache
 *
 * @package   Kirby Toolkit
 * @author    Mert Salık <salik@itu.edu.tr>
 * @link      http://getkirby.com
 * @copyright Mert Salık
 *
 *    Usage:
 *    c::set('cache.driver', 'wincache');
 */
class WinCache extends Driver {

  /**
   * Adds a variable in user cache and overwrites a variable if it already exists in the cache 
   *
   * <code>
   *    // Put an item in the cache for 15 minutes
   *    Cache::set('value', 'my value', 15);
   * </code>
   *
   * @param  string  $key
   * @param  mixed   $value
   * @param  int     $minutes
   * @return void
   */
  public function set($key, $value, $minutes = null) {
    return wincache_ucache_set($key, $this->value($value, $minutes), $this->expiration($minutes));
  }

  /**
   * Gets a variable stored in the user cache.
   *
   * <code>
   *    // Get an item from the cache driver
   *    $value = Cache::get('value');
   *
   *    // Return a default value if the requested item isn't cached
   *    $value = Cache::get('value', 'default value');
   * </code>
   *
   * @param  string  $key
   * @param  mixed   $default
   * @return mixed
   */
  public function retrieve($key) {
    return wincache_ucache_get($key);
  }

  /**
   * Checks if a variable exists in the user cache
   *
   * @param string $key
   * @return boolean
   */
  public function exists($key) {
    return wincache_ucache_exists($key);
  }

  /**
   * Deletes variables from the user cache 
   *
   * @param string $key
   * @return boolean
   */
  public function remove($key) {
    return wincache_ucache_delete($key);
  }

  /**
   * Deletes entire content of the user cache
   *
   * @return boolean
   */
  public function flush() {
    return wincache_ucache_clear();
  }

}