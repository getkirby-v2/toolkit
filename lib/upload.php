<?php

/**
 * Upload
 * 
 * File Upload class
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Upload {

  const ERROR_MISSING_FILE        = 0;
  const ERROR_FAILED_UPLOAD       = 1;
  const ERROR_UNALLOWED_OVERWRITE = 2;
  const ERROR_FILE_TOO_BIG        = 3;
  const ERROR_MOVE_FAILED         = 4;
  
  public $options = array();
  public $result  = null;

  public function __construct($params = array()) {

    $defaults = array(
      'input'     => 'file',
      'to'        => null,
      'overwrite' => true,
      'maxSize'   => detect::maxUploadSize(),
      'accept'    => null,
    );

    $this->options = array_merge($defaults, $params);

  }

  public function source() {
    return isset($_FILES[$this->options['input']]) ? $_FILES[$this->options['input']] : null;
  }

  public function input($input) {
    $this->options['input'] = $input;
    return $this;
  }

  public function to($to = null) {
  
    if(!is_null($to)) {
      $this->options['to'] = $to;
      return $this;      
    }

    $source    = $this->source();
    $name      = f::name($source['name']);
    $extension = f::extension($source['name']);
    $safeName  = f::safeName($name);

    return str::template($this->options['to'], array(
      'name'         => $name,
      'filename'     => $source['name'],
      'safeName'     => $safeName,
      'safeFilename' => $safeName . '.' . $extension,
      'extension'    => $extension,
    ));

  }

  public function overwrite($overwrite) {
    $this->options['overwrite'] = $overwrite;
    return $this;
  }

  public function accept($accept) {
    $this->options['accept'] = $accept;
    return $this;
  }

  public function maxSize($size) {
    $this->options['maxSize'] = $size;
    return $this;
  }

  /**
   * Validates and moves the uploaded file
   * 
   * @return boolean
   */
  protected function move() {

    $source = $this->source();

    if(is_null($source['name']) || is_null($source['tmp_name'])) {
      throw new Exception('The file has not been found', static::ERROR_MISSING_FILE);
    }

    if($source['error'] != 0) {
      throw new Exception('The upload failed', static::ERROR_FAILED_UPLOAD);
    }

    if(file_exists($this->options['to']) and $this->options['overwrite'] === false) {
      throw new Exception('The file exists and cannot be overwritten', static::ERROR_UNALLOWED_OVERWRITE);
    }

    if($source['size'] > $this->options['maxSize']) {
      throw new Exception('The file is too big', static::ERROR_FILE_TOO_BIG);
    }

    if(!@move_uploaded_file($source['tmp_name'], $this->to())) {
      throw new Exception('The file could not be moved', static::ERROR_MOVE_FAILED);
    }

  }

  protected function execute() {

    if(!is_null($this->result)) return $this->result;

    try {
      $this->move();
      $this->result = new Media($this->to());
    } catch(Exception $e) {
      $this->result = $e;      
    }

  }

  public function then($callback) {

    $this->execute();

    call_user_func($callback, $this->result);

    return $this;

  }

  public function success($callback) {
    
    $this->execute();
    
    if(!is_a($this->result, 'Exception')) {
      call_user_func($callback, $this->result);
    } 

    return $this;

  }

  public function error($callback) {

    $this->execute();
    
    if(is_a($this->result, 'Exception')) {
      call_user_func($callback, $this->result);
    } 

    return $this;

  }

}