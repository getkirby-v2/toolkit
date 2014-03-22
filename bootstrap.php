<?php

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('MB')) define('MB', (int)function_exists('mb_get_info'));

// a super simple autoloader 
function load($classmap) {
  spl_autoload_register(function($class) use ($classmap) {
    $class = strtolower($class);
    if(!isset($classmap[$class])) return false;
    include($classmap[$class]);
  });
}

// auto-load all toolkit classes
load(array(

  // classes
  'a'                 => __DIR__ . DS . 'lib' . DS . 'a.php',
  'c'                 => __DIR__ . DS . 'lib' . DS . 'c.php',
  'cookie'            => __DIR__ . DS . 'lib' . DS . 'cookie.php',
  'cache'             => __DIR__ . DS . 'lib' . DS . 'cache.php',
  'collection'        => __DIR__ . DS . 'lib' . DS . 'collection.php',
  'crypt'             => __DIR__ . DS . 'lib' . DS . 'crypt.php',
  'data'              => __DIR__ . DS . 'lib' . DS . 'data.php',
  'database'          => __DIR__ . DS . 'lib' . DS . 'database.php',
  'databaseconnector' => __DIR__ . DS . 'lib' . DS . 'database' . DS . 'connector.php',
  'databasequery'     => __DIR__ . DS . 'lib' . DS . 'database' . DS . 'query.php',
  'detect'            => __DIR__ . DS . 'lib' . DS . 'detect.php',
  'dimensions'        => __DIR__ . DS . 'lib' . DS . 'dimensions.php',
  'dir'               => __DIR__ . DS . 'lib' . DS . 'dir.php',
  'embed'             => __DIR__ . DS . 'lib' . DS . 'embed.php',
  'f'                 => __DIR__ . DS . 'lib' . DS . 'f.php',
  'folder'            => __DIR__ . DS . 'lib' . DS . 'folder.php',
  'form'              => __DIR__ . DS . 'lib' . DS . 'form.php',
  'header'            => __DIR__ . DS . 'lib' . DS . 'header.php',
  'html'              => __DIR__ . DS . 'lib' . DS . 'html.php',
  'i'                 => __DIR__ . DS . 'lib' . DS . 'i.php',
  'l'                 => __DIR__ . DS . 'lib' . DS . 'l.php',
  'media'             => __DIR__ . DS . 'lib' . DS . 'media.php',
  'obj'               => __DIR__ . DS . 'lib' . DS . 'obj.php',
  'pagination'        => __DIR__ . DS . 'lib' . DS . 'pagination.php',
  'password'          => __DIR__ . DS . 'lib' . DS . 'password.php',
  'r'                 => __DIR__ . DS . 'lib' . DS . 'r.php',
  'redirect'          => __DIR__ . DS . 'lib' . DS . 'redirect.php',
  'remote'            => __DIR__ . DS . 'lib' . DS . 'remote.php',
  'response'          => __DIR__ . DS . 'lib' . DS . 'response.php',
  'router'            => __DIR__ . DS . 'lib' . DS . 'router.php',
  's'                 => __DIR__ . DS . 'lib' . DS . 's.php',
  'server'            => __DIR__ . DS . 'lib' . DS . 'server.php',
  'sql'               => __DIR__ . DS . 'lib' . DS . 'sql.php',
  'str'               => __DIR__ . DS . 'lib' . DS . 'str.php',
  'thumb'             => __DIR__ . DS . 'lib' . DS . 'thumb.php',
  'tpl'               => __DIR__ . DS . 'lib' . DS . 'tpl.php',
  'upload'            => __DIR__ . DS . 'lib' . DS . 'upload.php',
  'url'               => __DIR__ . DS . 'lib' . DS . 'url.php',
  'v'                 => __DIR__ . DS . 'lib' . DS . 'v.php',
  'visitor'           => __DIR__ . DS . 'lib' . DS . 'visitor.php',
  'xml'               => __DIR__ . DS . 'lib' . DS . 'xml.php',
  'yaml'              => __DIR__ . DS . 'lib' . DS . 'yaml.php',
  
  // vendors
  'spyc'              => __DIR__ . DS . 'vendors' . DS . 'yaml' . DS . 'yaml.php',

));

// load all helpers
include(__DIR__ . DS . 'helpers.php');