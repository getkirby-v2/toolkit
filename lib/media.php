<?php

/**
 * Media
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Media {

  // optional url where the file is reachable
  public $url = null;

  // the full path for the file
  protected $root = null;

  // the filename including the extension
  protected $filename = null;

  // the name excluding the extension
  protected $name = null;

  // the extension of the file
  protected $extension = null;

  // the content of the file
  protected $content = null;

  // cache for the exif object
  protected $exif = null;

  // cache for the dimensions object
  protected $dimensions = null;

  /**
   * Constructor
   *
   * @param string $root
   */
  public function __construct($root, $url = null) {
    $this->url       = $url;
    $this->root      = realpath($root);
    $this->filename  = basename($root);
    $this->name      = pathinfo($root, PATHINFO_FILENAME);
    $this->extension = strtolower(pathinfo($root, PATHINFO_EXTENSION));
  }

  /**
   * Returns the full root of the asset
   *
   * @return string
   */
  public function root() {
    return $this->root;
  }

  /**
   * Returns the url
   *
   * @return string
   */
  public function url() {
    return $this->url;
  }

  /**
   * Returns a md5 hash of the root
   */
  public function hash() {
    return md5($this->root);
  }

  /**
   * Returns the parent directory path
   *
   * @return string
   */
  public function dir() {
    return dirname($this->root);
  }

  /**
   * Returns the filename of the file
   * i.e. somefile.jpg
   *
   * @return string
   */
  public function filename() {
    return $this->filename;
  }

  /**
   * Returns the name of the file without extension
   *
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns the filename as safe name
   *
   * @return string
   */
  public function safeName() {
    return f::safeName($this->filename());
  }

  /**
   * Returns the extension of the file
   * i.e. jpg
   *
   * @return string
   */
  public function extension() {
    // return the current extension
    return $this->extension;
  }

  /**
   * Reads the file content and parses it
   *
   * @param string $format
   * @return mixed
   */
  public function read($format = null) {
    return str::parse($this->content(), $format);
  }

  /**
   * Setter and getter for the file content
   *
   * @param string $content
   * @return string
   */
  public function content($content = null, $format = null) {

    if(!is_null($content)) {
      if(is_array($content)) {
        switch($format) {
          case 'json':
            $content = json_encode($content);
            break;
          case 'yaml':
            $content = yaml::encode($content);
            break;
          default:
            $content = serialize($content);
            break;
        }
      } else if(is_object($content)) {
        $content = serialize($content);
      }
      return $this->content = $content;
    }

    if(is_null($this->content)) {
      $this->content = file_get_contents($this->root);
    }

    return $this->content;

  }

  /**
   * Saves the file
   *
   * @param string $content
   * @return boolean
   */
  public function save($content = null, $format = null) {
    $content = $this->content($content, $format);
    return f::write($this->root, $content);
  }

  /**
   * Alternative for save
   *
   * @param string $content
   * @return boolean
   */
  public function write($content = null, $format = null) {
    return $this->save($content, $format);
  }

  /**
   * Change the file's modification date to now
   * and create it with an empty content if it is not there yet
   *
   * @return boolean
   */
  public function touch() {
    return touch($this->root);
  }

  /**
   * Appends the content and saves the file
   *
   * @param string $content
   * @return boolean
   */
  public function append($content) {
    $this->content = $this->content() . $content;
    return $this->save();
  }

  /**
   * Deletes the file
   *
   * @return boolean
   */
  public function delete() {
    return f::remove($this->root);
  }

  /**
   * Alternative for delete
   *
   * @return boolean
   */
  public function remove() {
    return f::remove($this->root);
  }

  /**
   * Moves the file to a new location
   *
   * @param string $to
   * @return boolean
   */
  public function move($to) {
    if(!f::move($this->root, $to)) {
      return false;
    } else {
      $this->root = $to;
      return true;
    }
  }

  /**
   * Copies the file to a new location
   *
   * @param string $to
   * @return boolean
   */
  public function copy($to) {
    return f::copy($this->root, $to);
  }

  /**
   * Returns the file size as integer
   *
   * @return int
   */
  public function size() {
    return f::size($this->root);
  }

  /**
   * Returns the human readable version of the file size
   *
   * @return string
   */
  public function niceSize() {
    return f::niceSize($this->size());
  }

  /**
   * Get the file's last modification time.
   *
   * @return int
   */
  public function modified($format = null) {
    return f::modified($this->root, $format);
  }

  /**
   * Returns the mime type of a file
   *
   * @param string $file
   * @return string
   */
  public function mime() {
    return f::mime($this->root);
  }

  /**
   * Categorize the file
   *
   * @return string
   */
  public function type() {
    return f::type($this->root);
  }

  /**
   * Checks if a file is of a certain type
   *
   * @param string $value An extension or mime type
   * @return boolean
   */
  public function is($value) {
    return f::is($this->root, $value);
  }

  /**
   * Returns the file content as base64 encoded string
   *
   * @return string
   */
  public function base64() {
    return base64_encode($this->content());
  }

  /**
   * Returns the file as data uri
   *
   * @return string
   */
  public function dataUri() {
    return 'data:' . $this->mime() . ';base64,' . $this->base64();
  }

  /**
   * Checks if the file exists
   *
   * @return boolean
   */
  public function exists() {
    return file_exists($this->root);
  }

  /**
   * Checks if the file is writable
   *
   * @return boolean
   */
  public function isWritable() {
    return is_writable($this->root);
  }

  /**
   * Checks if the file is readable
   *
   * @return boolean
   */
  public function isReadable() {
    return is_readable($this->root);
  }

  /**
   * Checks if the file is executable
   *
   * @return boolean
   */
  public function isExecutable() {
    return is_executable($this->root);
  }

  /**
   * Sends an appropriate header for the asset
   *
   * @param boolean $send
   * @return mixed
   */
  public function header($send = true) {
    return header::type($this->mime(), false, $send);
  }

  /**
   * Safely requires a file if it exists
   *
   * @param array $data Optional variables, which will be made available to the file
   */
  static public function load($data = array()) {
    return f::load($this->root, $data);
  }

  /**
   * Read and send the file with the correct headers
   *
   * @param string $file
   */
  public function show() {
    f::show($this->root);
  }

  /*
   * Automatically sends all needed headers for the file to be downloaded
   * and echos the file's content
   *
   * @param string $filename Optional filename for the download
   */
  public function download($filename = null) {
    f::download($this->root, $filename);
  }

  /**
   * Returns the exif object for this file (if image)
   *
   * @return Exif
   */
  public function exif() {
    if(!is_null($this->exif)) return $this->exif;
    return $this->exif = new Exif($this);
  }

  /**
   * Returns the PHP imagesize array
   *
   * @return array
   */
  public function imagesize() {
    return (array)getimagesize($this->root);
  }

  /**
   * Returns the dimensions of the file if possible
   *
   * @return Dimensions
   */
  public function dimensions() {

    if(isset($this->cache['dimensions'])) return $this->cache['dimensions'];

    if($this->type() == 'image') {
      $size   = $this->imagesize();
      $width  = a::get($size, 0, 0);
      $height = a::get($size, 1, 0);
    } else {
      $width  = 0;
      $height = 0;
    }

    return $this->cache['dimensions'] = new Dimensions($width, $height);

  }

  /**
   * Returns the width of the asset
   *
   * @return int
   */
  public function width() {
    return $this->dimensions()->width();
  }

  /**
   * Returns the height of the asset
   *
   * @return int
   */
  public function height() {
    return $this->dimensions()->height();
  }

  /**
   * Returns the ratio of the asset
   *
   * @return int
   */
  public function ratio() {
    return $this->dimensions()->ratio();
  }

  /**
   * Checks if the dimensions of the asset are portrait
   *
   * @return boolean
   */
  public function isPortrait() {
    return $this->dimensions()->portrait();
  }

  /**
   * Checks if the dimensions of the asset are landscape
   *
   * @return boolean
   */
  public function isLandscape() {
    return $this->dimensions()->landscape();
  }

  /**
   * Checks if the dimensions of the asset are square
   *
   * @return boolean
   */
  public function isSquare() {
    return $this->dimensions()->square();
  }

  /**
   * Returns the orientation as string
   * landscape | portrait | square
   *
   * @return string
   */
  public function orientation() {
    return $this->dimensions()->orientation();
  }

  /**
   * Returns a full link to this file
   * Perfect for debugging in connection with echo
   *
   * @return string
   */
  public function __toString() {
    return $this->root;
  }

}