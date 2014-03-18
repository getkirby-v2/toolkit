<?php

/**
 * Thumb
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Thumb extends Obj {

  static public $drivers = array();

  static public $defaults = array(
    'filename' => '{safeName}-{hash}.{extension}',
    'url'      => '/thumbs',
    'root'     => '/thumbs',
    'driver'   => 'im',
    'quality'  => 100,
    'blur'     => false,
    'width'    => null,
    'height'   => null,
    'upscale'  => false,
    'crop'     => false
  );

  public $source      = null;
  public $result      = null;
  public $destination = null;
  public $options     = array();

  /**
   * Constructor
   * 
   * @param mixed $source
   * @param array $params
   */
  public function __construct($source, $params = array()) {

    $this->source  = $this->result = is_a($source, 'Media') ? $source : new Media($source);
    $this->options = array_merge(static::$defaults, $this->params($params));

    $this->destination = new Obj();
    $this->destination->filename = str::template($this->options['filename'], array(
      'extension'    => $this->source->extension(),
      'name'         => $this->source->name(),
      'filename'     => $this->source->filename(),
      'safeName'     => f::safeName($this->source->name()),
      'safeFilename' => f::safeName($this->source->name()) . '.' . $this->extension(),
      'width'        => $this->options['width'],
      'height'       => $this->options['height'],
      'hash'         => md5($this->source->root() . $this->settingsIdentifier()),
    ));

    $this->destination->url  = $this->options['url'] . '/' . $this->destination->filename;
    $this->destination->root = $this->options['root'] . DS . $this->destination->filename;

    // don't create the thumbnail if it's not necessary
    if($this->isObsolete()) return;
    
    // don't create the thumbnail if it exists
    if(!$this->isThere()) {

      // check for a valid image
      if(!$this->source->exists() or $this->source->type() != 'image') throw new Exception('The given image is invalid');

      // check for a valid driver
      if(!array_key_exists($this->options['driver'], static::$drivers)) throw new Exception('Invalid thumbnail driver');    

      // create the thumbnail
      $this->create();

      // check if creating the thumbnail failed
      if(!file_exists($this->destination->root)) return;

    }

    // create the result object
    $this->result = new Media($this->destination->root, $this->destination->url);

  }

  /**
   * Makes it possible to pass a string of params
   * which is shorter and more convenient than 
   * passing a full array of keys and values: 
   * width:300|height:200|crop:true
   * 
   * @param array $params
   * @return array
   */
  public function params($params) {
    if(is_array($params)) return $params;
    $result = array();
    foreach(explode('|', $params) as $param) {
      $pos = strpos($param, ':');
      $result[trim(substr($param, 0, $pos))] = trim(substr($param, $pos+1));
    }
    return $result;
  }

  /**
   * Builds a hash for all relevant settings
   * 
   * @return string
   */
  public function settingsIdentifier() {

    // build the settings string
    return implode('-', array(
      ($this->options['width'])   ? $this->options['width']   : 0,
      ($this->options['height'])  ? $this->options['height']  : 0,
      ($this->options['upscale']) ? $this->options['upscale'] : 0,
      ($this->options['crop'])    ? $this->options['crop']    : 0,
       $this->options['blur'],
       $this->options['quality']
    ));

  }

  /**
   * Checks if the thumbnail already exists
   * and is newer than the original file
   * 
   * @return boolean
   */
  public function isThere() {

    // if the thumb already exists and the source hasn't been updated 
    // we don't need to generate a new thumbnail
    if(file_exists($this->destination->root) and f::modified($this->destination->root) >= $this->source->modified()) return true;

    return false;

  }

  /**
   * Checks if the thumbnail is not needed
   * because the original image is small enough
   * 
   * @return boolean
   */
  public function isObsolete() {

    // try to use the original if resizing is not necessary
    if($this->options['width']   <= $this->source->width()  and 
       $this->options['height']  <= $this->source->height() and 
       $this->options['crop']    == false                   and 
       $this->options['blur']    == false                   and
       $this->options['upscale'] == false) return true;

    return false;
    
  }

  /**
   * Calls the driver function and 
   * creates the thumbnail
   */
  protected function create() {
    return call_user_func_array(static::$drivers[$this->options['driver']], array($this));
  }
  
  /**
   * Makes all public methods of the result object 
   * available to the thumb class
   * 
   * @param string $method
   * @param mixed $arguments
   * @return mixed
   */
  public function __call($method, $arguments) {

    if(method_exists($this->result, $method)) {
      return call_user_func_array(array($this->result, $method), $arguments);
    }

  }

  /**
   * Generates and returns the full html tag for the thumbnail
   * 
   * @param array $attr An optional array of attributes, which should be added to the image tag
   * @return string
   */
  public function tag($attr = array()) {

    // don't return the tag if the url is not available
    if(!$this->result->url()) return false;
  
    return html::img($this->result->url(), array_merge(array(
      'alt'    => isset($this->options['alt'])   ? $this->options['alt']   : $this->result->name(),
      'class'  => isset($this->options['class']) ? $this->options['class'] : null,
    ), $attr));
    
  }

}

/**
 * GD Lib Driver
 */
thumb::$drivers['gd'] = function() {

  // to be implemented

};

/**
 * ImageMagick Driver
 */
thumb::$drivers['im'] = function($thumb) {

  $command = array();

  $command[] = isset($thumb->options['bin']) ? $thumb->options['bin'] : 'convert';
  $command[] = '"' . $thumb->source->root() . '"';
  $command[] = '-strip';
  $command[] = '-resize';
  $command[] = $thumb->options['width'] . 'x' . $thumb->options['height'] . '^';
  $command[] = '-quality ' . $thumb->options['quality'];

  if($thumb->options['crop']) {
    $command[] = '-gravity Center -crop ' . $thumb->options['width'] . 'x' . $thumb->options['height'] . '+0+0';
  } 

  if($thumb->options['blur']) {
    $command[] = '-blur 0x8';
  } 

  $command[] = '"' . $thumb->destination->root . '"';

  exec(implode(' ', $command));

};