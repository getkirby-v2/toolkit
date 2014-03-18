<?php

/**
 * 
 * Database Connector
 * 
 * Used by the Database class to connect with different database types
 * Returns a PDO connection
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class DatabaseConnector {

  // the PDO connection
  protected $connection;

  // A unique id for the connection
  protected $id;
  
  // the DSN connection string 
  protected $dsn;
  
  // the optional prefix for all table names
  protected $prefix;
  
  // the database type. so far mysql and sqlite are supported
  protected $type;

  /**
   * Constructor
   * 
   * @param mixed $params Connection parameters
   */
  public function __construct($params = array()) {

    // get the connection method
    $type = a::get($params, 'type');

    // check for a valid connection method
    if(empty($type) or !method_exists($this, $type)) throw new Exception('The db type is not supported: ' . $type);

    // get the dsn
    $this->dsn = $this->$type($params);

    // store the id if available
    $this->id = a::get($params, 'id', $this->dsn);

    // store the database type
    $this->type = $type; 

    // store the prefix for table names
    $this->prefix = a::get($params, 'prefix');

    // try to connect
    $this->connection = new PDO($this->dsn, a::get($params, 'user'), a::get($params, 'password'));
    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }

  /**
   * Returns the established PDO connection 
   * 
   * @return object
   */
  public function connection() {
    return $this->connection;
  }

  /**
   * Returns the used database type
   * 
   * @return string
   */
  public function type() {
    return $this->type;
  }

  /**
   * Returns the optional prefix for table names
   * 
   * @return string
   */
  public function prefix() {
    return $this->prefix;
  }

  /**
   * Returns the dsn string
   * 
   * @return string
   */
  public function dsn() {
    return $this->dsn;
  }

  /**
   * Returns the connection id
   * 
   * @return string
   */
  public function id() {
    return $this->id;
  }

  // connection methods

  /**
   * Returns an sqlite dsn string
   * 
   * @param array $params
   * @return string
   */
  protected function sqlite($params) {    
    if(!isset($params['database'])) throw new Exception('The sqlite connection requires a "database" parameter');
    return 'sqlite:' . $params['database'];                    
  }

  /**
   * Returns a mysql dsn string
   * 
   * @param array $params
   * @return string
   */
  protected function mysql($params) {
    if(!isset($params['host']))     throw new Exception('The mysqlite connection requires a "host" parameter');
    if(!isset($params['database'])) throw new Exception('The mysqlite connection requires a "database" parameter');
    return 'mysql:host=' . $params['host'] . ';dbname=' . $params['database'] . ';charset=' . a::get($params, 'charset', 'utf8');
  }

}