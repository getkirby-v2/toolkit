<?php

/**
 * 
 * XML
 * 
 * The Kirby XML parser and creator Class
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Xml {

  /**
   * Converts a string to a xml-safe string
   * Converts it to html-safe first and then it
   * will replace html entities to xml entities
   *
   * <code>
   *
   * echo xml::encode('some über crazy stuff');
   * // output: some &#252;ber crazy stuff 
   *  
   * </code>
   *    
   * @param  string  $text
   * @param  boolean $html True: convert to html first
   * @return string
   */  
  static public function encode($string, $html = true) {

    // convert raw text to html safe text
    if($html) $text = html::encode($string, false);

    // convert html entities to xml entities
    return strtr($text, html::entities());

  }

  /**
   * Removes all xml entities from a string
   * and convert them to html entities first
   * and remove all html entities afterwards.
   *
   * <code>
   * 
   * echo xml::decode('some <em>&#252;ber</em> crazy stuff');
   * // output: some &uuml;ber crazy stuff
   * 
   * </code>
   * 
   * @param  string  $string
   * @return string
   */  
  static public function decode($string) {
    // convert xml entities to html entities
    $string = strtr($string, static::entities());
    return html::decode($string);
  }  

  /** 
   * Parses a XML string and returns an array
   * 
   * @param  string  $xml
   * @return mixed
   */
  static public function parse($xml) {

    $xml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $xml);
    $xml = @simplexml_load_string($xml, null, LIBXML_NOENT);
    $xml = @json_encode($xml);
    $xml = @json_decode($xml, true);
    return (is_array($xml)) ? $xml : false;

  }

  /**
   * Returns a translation table of xml entities to html entities
   * 
   * @return array
   */
  static public function entities() {
    return array_flip(html::entities());    
  }

}
