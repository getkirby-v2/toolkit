<?php

/**
 * Router
 *
 * The router makes it possible to react
 * on any incoming URL scheme
 *
 * Partly inspired by Laravel's router
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Router {

  // request instance
  protected $request = null;

  // the matched route if found
  protected $route = null;

  // all registered routes
  protected $routes = array(
    'GET'    => array(),
    'POST'   => array(),
    'HEAD'   => array(),
    'PUT'    => array(),
    'PATCH'  => array(),
    'DELETE' => array()
  );

  // The wildcard patterns supported by the router.
  protected $patterns = array(
    '(:num)'     => '([0-9]+)',
    '(:alpha)'   => '([a-zA-Z]+)',
    '(:any)'     => '([a-zA-Z0-9\.\-_%=]+)',
    '(:all)'     => '(.*)',
  );

  // The optional wildcard patterns supported by the router.
  protected $optional = array(
    '/(:num?)'   => '(?:/([0-9]+)',
    '/(:alpha?)' => '(?:/([a-zA-Z]+)',
    '/(:any?)'   => '(?:/([a-zA-Z0-9\.\-_%=]+)',
    '/(:all?)'   => '(?:/(.*)',
  );

  // additional events, which can be triggered by routes
  protected $filters = array();

  /**
   * Constructor
   *
   * @param array $routes
   */
  public function __construct($routes = array()) {
    $this->register($routes);
  }

  /**
   * Returns the found route
   *
   * @return mixed
   */
  public function route() {
    return $this->route;
  }

  /**
   * Returns the arguments array from the current route
   *
   * @return array
   */
  public function arguments() {
    if($route = $this->route()) return $route->arguments();
  }

  /**
   * Adds a new route
   *
   * @param object $route
   * @return object
   */
  public function register($pattern, $params = array(), $optional = array()) {

    if(is_array($pattern)) {
      foreach($pattern as $v) {
        $this->register($v['pattern'], $v);
      }
      return $this;
    }

    $defaults = array(
      'pattern'   => $pattern,
      'https'     => false,
      'ajax'      => false,
      'filter'    => null,
      'method'    => 'GET',
      'arguments' => array(),
    );

    $route = new Obj(array_merge($defaults, $params, $optional));

    // convert single methods or methods separated by | to arrays
    if(is_string($route->method)) {

      if(strpos($route->method, '|') !== false) {
        $route->method = str::split($route->method, '|');
      } else if($route->method == 'ALL') {
        $route->method = array_keys($this->routes);
      } else {
        $route->method = array($route->method);
      }

    }

    foreach($route->method as $method) {
      $this->routes[strtoupper($method)][$route->pattern] = $route;
    }

    return $route;

  }

  /**
   * Add a new router filter
   *
   * @param string $name A simple name for the filter, which can be used by routes later
   * @param closure $function A filter function, which should be called before routes
   */
  public function filter($name, $function) {
    $this->filters[$name] = $function;
  }

  /**
   * Return all registered filters
   *
   * @return array
   */
  public function filters() {
    return $this->filters;
  }

  /**
   * Call all matching filters
   *
   * @param mixed $filters
   */
  protected function filterer($filters) {
    foreach((array)$filters as $filter) {
      if(array_key_exists($filter, $this->filters) and is_callable($this->filters[$filter])) {
        return call_user_func($this->filters[$filter]);
      }
    }
  }

  /**
   * Returns all added routes
   *
   * @param string $method
   * @return array
   */
  public function routes($method = null) {
    return is_null($method) ? $this->routes : $this->routes[strtoupper($method)];
  }

  /**
   * Iterate through every route to find a matching route.
   *
   * @param  string $path Optional path to match against
   * @return Route
   */
  public function run($path = null) {

    $method = r::method();
    $ajax   = r::ajax();
    $https  = r::ssl();
    $routes = a::get($this->routes, $method, array());

    // detect path if not set manually
    if($path === null) $path = implode('/', (array)url::fragments(detect::path()));

    // empty urls should never happen
    if(empty($path)) $path = '/';

    foreach($routes as $route) {

      if($route->https and !$https) continue;
      if($route->ajax  and !$ajax)  continue;

      // handle exact matches
      if($route->pattern == $path) {
        $this->route = $route;
        break;
      }

      // We only need to check routes with regular expression since all others
      // would have been able to be matched by the search for literal matches
      // we just did before we started searching.
      if(strpos($route->pattern, '(') === false) continue;

      $preg = '#^'. $this->wildcards($route->pattern) . '$#u';

      // If we get a match we'll return the route and slice off the first
      // parameter match, as preg_match sets the first array item to the
      // full-text match of the pattern.
      if(preg_match($preg, $path, $parameters)) {
        $this->route = $route;
        $this->route->arguments = array_slice($parameters, 1);
        break;
      }

    }

    if($this->route and $this->filterer($this->route->filter) !== false) {
      return $this->route;
    } else {
      return null;
    }

  }

  /**
   * Translate route URI wildcards into regular expressions.
   *
   * @param  string  $uri
   * @return string
   */
  protected function wildcards($pattern) {

    $search  = array_keys($this->optional);
    $replace = array_values($this->optional);

    // For optional parameters, first translate the wildcards to their
    // regex equivalent, sans the ")?" ending. We'll add the endings
    // back on when we know the replacement count.
    $pattern = str_replace($search, $replace, $pattern, $count);

    if($count > 0) $pattern .= str_repeat(')?', $count);

    return strtr($pattern, $this->patterns);

  }

  /**
   * Find a registered route by a field and value
   *
   * @param string $field
   * @param string $value
   * @return object
   */
  public function findRouteBy($field, $value) {
    foreach($this->routes as $method => $routes) {
      foreach($routes as $route) {
        if($route->$field() == $value) return $route;
      }
    }
  }

}
