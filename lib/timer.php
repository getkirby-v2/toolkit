<?php

class timer {

  public static $time = null;

  static function start() {
    $time = explode(' ', microtime());
    static::$time = (double)$time[1] + (double)$time[0];
  }

  static function stop() {
    $time  = explode(' ', microtime());
    $time  = (double)$time[1] + (double)$time[0];
    $timer = static::$time;
    return round(($time-$timer), 5);
  }

}