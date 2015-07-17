<?php

/**
 * T(ranslation)
 *
 * A small class to keep track of translations.
 *
 * @package   Kirby Toolkit
 * @author    Tim Kächele <mail@timkaechele.me>
 * @link      http://getkirby.com
 * @copyright Tim Kächele
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class T {
  public static $data = array();
  public static $language;

  /**
   * Sets the output language.
   * Example language codes: en, fr, de, ...
   *
   * @param string $lang the language code
   *
   */
  public static function setLanguage($lang) {
    static::$language = $lang;
  }

  /**
   * Returns the current language as a langauge code.
   *
   * @return string language code
   */
  public static function getLanguage() {
    return static::$language;
  }

  /**
   * Sets a translation for the given key.
   *
   * The key should look like this:
   * {Language Code}.{Your Actual Key}
   *
   * Example:
   * <code>
   *   T::set("en.user.username", "Username");
   *   T::set("de.user.username", "Benutzername");
   * </code>
   *
   * @param string $key the key with a langauge code.
   * @param string $value the translation for the given key.
   */
  public static function set($key, $value) {
    $langKey = str::split($key, '.')[0];
    if(!(array_key_exists($langKey, static::$data))) {
      static::$data[$langKey] = array();
    }
    // removes the language code from the key
    $translationKey = str::substr($key, str::length($langKey) + 1);
    static::$data[$langKey][$translationKey] = $value;
  }

  /**
   * Returns the right translation for the currently set language
   * and the requested key.
   *
   * If you didn't set the langauge or there's no translation for the key
   * in the set langauge null will be returned.
   *
   * <code>
   *   T::set("de.snake", "Schlange");
   *   T::get("snake") // => null, because no langauge is set
   *
   *   T::setLanguage("de");
   *   T::get("snake"); // => "Schlange"
   *
   *   T::setLanguage("en");
   *   T::get("snake"); // null, because there's no translation for the key
   *
   *   T::get("user.userame"); // null, because the key doesn't exist.
   * </code>
   *
   * @param string $key the requested key.
   * @return string or null
   *
   */
  public static function get($key) {
    if(!isset(static::$language)) {
      return null;
    }
    // if the language doesn't exist
    if(!(array_key_exists(static::$language, static::$data))) {
      return null;
    }
    // if the key doesn't exist.
    if(!(array_key_exists($key, static::$data[static::$language]))) {
      return null;
    }
    return static::$data[static::$language][$key];
  }
}