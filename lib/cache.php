<?php

class Cache {

  static public $root;

  static public function modified($id) {
    return @filemtime(static::$root . DS . $id . '.txt');
  }

  static public function set($id, $content) {
    return @file_put_contents(static::$root . DS . $id . '.txt', $content);
  }

  static public function get($id) {
    return @file_get_contents(static::$root . DS . $id . '.txt');
  }

}