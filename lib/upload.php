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
  const ERROR_UNACCEPTED          = 5;

  public $options = array();
  public $error   = null;
  public $file    = null;

  public function __construct($to, $params = array()) {

    $defaults = array(
      'input'     => 'file',
      'to'        => $to,
      'overwrite' => true,
      'maxSize'   => detect::maxUploadSize(),
      'accept'    => null,
    );

    $this->options = array_merge($defaults, $params);

    try {
      $this->move();
      $this->file = new Media($this->to());
    } catch(Exception $e) {
      $this->error = $e;
    }

  }

  public function error() {
    return $this->error;
  }

  public function source() {
    return isset($_FILES[$this->options['input']]) ? $_FILES[$this->options['input']] : null;
  }

  public function to() {

    $source        = $this->source();
    $name          = f::name($source['name']);
    $extension     = f::extension($source['name']);
    $safeName      = f::safeName($name);
    $safeExtension = str_replace('jpeg', 'jpg', str::lower($extension));

    return str::template($this->options['to'], array(
      'name'          => $name,
      'filename'      => $source['name'],
      'safeName'      => $safeName,
      'safeFilename'  => $safeName . '.' . $safeExtension,
      'extension'     => $extension,
      'safeExtension' => $safeExtension
    ));

  }

  public function file() {
    return $this->file;
  }

  protected function move() {

    $source = $this->source();

    if(is_null($source['name']) or is_null($source['tmp_name'])) {
      throw new Error('The file has not been found', static::ERROR_MISSING_FILE);
    }

    if($source['error'] !== 0) {
      throw new Error('The upload failed', static::ERROR_FAILED_UPLOAD);
    }

    if(file_exists($this->to()) and $this->options['overwrite'] === false) {
      throw new Error('The file exists and cannot be overwritten', static::ERROR_UNALLOWED_OVERWRITE);
    }

    if($source['size'] > $this->options['maxSize']) {
      throw new Error('The file is too big', static::ERROR_FILE_TOO_BIG);
    }

    if(is_callable($this->options['accept'])) {
      $accepted = call($this->options['accept'], new Media($source['tmp_name']));
      if($accepted === false) {
        throw new Error('The file is not accepted by the server', static::ERROR_UNACCEPTED);
      }
    }

    if(!@move_uploaded_file($source['tmp_name'], $this->to())) {
      throw new Error('The file could not be moved', static::ERROR_MOVE_FAILED);
    }

  }

}