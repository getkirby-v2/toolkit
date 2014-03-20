<?php 

if(!defined('SORT_NATURAL')) define('SORT_NATURAL', 'natural');

/**
 * Collection
 *  
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Collection extends I {

  protected $pagination;

  /**
   * Returns a slice of the collection
   * 
   * @param int $offset The optional index to start the slice from
   * @param int $limit The optional number of elements to return
   * @return Collection
   */      
  public function slice($offset = null, $limit = null) {
    if($offset === null and $limit === null) return $this;
    $collection = clone $this;
    $collection->data = array_slice($collection->data, $offset, $limit);
    return $collection;
  }

  /**
   * Returns a new collection with a limited number of elements
   * 
   * @param int $limit The number of elements to return
   * @return Collection
   */      
  public function limit($limit) {
    return $this->slice(0, $limit);
  }

  /**
   * Returns a new collection starting from the given offset
   * 
   * @param int $offset The index to start from
   * @return Collection
   */      
  public function offset($offset) {
    return $this->slice($offset);
  }

  /**
   * Returns the array in reverse order
   * 
   * @return Collection
   */      
  public function flip() {
    $collection = clone $this;
    $collection->data = array_reverse($collection->data, true);
    return $collection;
  }

  /**
   * Counts all elements in the array
   * 
   * @return int 
   */      
  public function count() {
    return count($this->data);
  }  

  /**
   * Returns the first element from the array
   * 
   * @return mixed
   */      
  public function first() {
    $array = $this->data;
    return array_shift($array); 
  }

  /**
   * Returns the last element from the array
   * 
   * @return mixed
   */      
  public function last() {
    $array = $this->data;
    return array_pop($array); 
  }

  /** 
   * Returns the nth element from the array
   * 
   * @return mixed
   */
  public function nth($n) {
    $array = array_values($this->data);
    return (isset($array[$n])) ? $array[$n] : false;
  }

  /**
   * Converts the current object into an array
   * 
   * @return array
   */      
  public function toArray($callback = null) {
    if(is_null($callback)) return $this->data;
    return array_map($callback, $this->data);
  }

  /**
   * Converts the current object into a json string
   * 
   * @return string
   */      
  public function toJson() {
    return json_encode($this->data);
  }

  /**
   * Appends an element to the data array
   * 
   * @param string $key
   * @param mixed $object
   * @return Collection
   */
  public function append($key, $object) {
    $this->data = $this->data + array($key => $object);
    return $this;
  }

  /**
   * Prepends an element to the data array
   * 
   * @param string $key
   * @param mixed $object
   * @return Collection
   */
  public function prepend($key, $object) {
    $this->data = array($key => $object) + $this->data;
    return $this;
  }

  /**
   * Returns a new collection without the given element(s)
   * 
   * @param args any number of keys, passed as individual arguments
   * @return Collection
   */      
  public function not() {
    $collection = clone $this;
    foreach(func_get_args() as $kill) {
      unset($collection->data[$kill]);
    }
    return $collection;
  }

  /**
   * Returns a new collection without the given element(s)
   * 
   * @param args any number of keys, passed as individual arguments
   * @return Collection
   */      
  public function without() {
    return call_user_func_array(array($this, 'not'), func_get_args());
  }

  /**
   * Shuffle all elements in the array
   * 
   * @return object a new shuffled collection 
   */      
  public function shuffle() {
    $collection = clone $this;
    $keys = array_keys($collection->data); 
    shuffle($keys); 
    $collection->data = array_merge(array_flip($keys), $collection->data); 
    return $collection;
  }

  /**
   * Returns an array of all keys in the collection
   * 
   * @return array
   */
  public function keys() {
    return array_keys($this->data);
  }

  /**
   * Tries to find the key for the given element
   * 
   * @param  mixed $needle the element to search for
   * @return mixed the name of the key or false
   */      
  public function keyOf($needle) {
    return array_search($needle, $this->data);
  }

  /**
   * Tries to find the index number for the given element
   * 
   * @param  mixed $needle the element to search for
   * @return mixed the name of the key or false
   */      
  public function indexOf($needle) {
    return array_search($needle, array_values($this->data));
  }

  /**
   * Filter the elements in the array by a callback function
   * 
   * @param  func $callback the callback function
   * @return Collection
   */      
  public function filter($callback) {
    $collection = clone $this;
    $collection->data = array_filter($collection->data, $callback);
    return $collection;
  }

  /**
   * Find a single item by a key and value pair
   * 
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function findBy($key, $value) {
    foreach($this->data as $item) {
      if($this->extractValue($item, $key) == $value) return $item;
    }
  }

  /**
   * Filters the current collection by a field, operator and search value
   * 
   * @return Collection
   */      
  public function filterBy() {

    $args       = func_get_args();
    $operator   = '=='; 
    $field      = @$args[0];
    $value      = @$args[1];
    $split      = @$args[2];
    $operators  = array('!=', '==', '*=', '>', '<', '>=', '<=');
    $collection = clone $this;

    if(is_string($value) and in_array($value, $operators)) {
      $operator = $value;
      $value    = @$args[2];
      $split    = @$args[3];
    }          
    
    switch($operator) {

      // ignore matching elements
      case '!=':

        foreach($collection->data as $key => $item) {
          if($split) {
            $values = explode($split, (string)$this->extractValue($item, $field));
            if(in_array($value, $values)) unset($collection->$key);
          } else if($this->extractValue($item, $field) == $value) {
            unset($collection->$key);
          }
        }
        break;    
      
      // search
      case '*=':
        
        foreach($collection->data as $key => $item) {
          if($split) {
            $values = explode($split, (string)$this->extractValue($item, $field));
            foreach($values as $val) {
              if(strpos($val, $value) === false) {
                unset($collection->$key);
                break;
              }
            }
          } else if(strpos($this->extractValue($item, $field), $value) === false) {
            unset($collection->$key);
          }
        }

        break;

      // greater than
      case '>':

        foreach($collection->data as $key => $item) {
          if($this->extractValue($item, $field) > $value) continue;
          unset($collection->$key);
        }

        break;

      // less than
      case '<':

        foreach($collection->data as $key => $item) {
          if($this->extractValue($item, $field) < $value) continue;
          unset($collection->$key);
        }

        break;

      // greater than and equal to
      case '>=':

        foreach($collection->data as $key => $item) {
          if($this->extractValue($item, $field) >= $value) continue;
          unset($collection->$key);
        }

        break;

      // less than and equal to
      case '<=':

        foreach($collection->data as $key => $item) {
          if($this->extractValue($item, $field) <= $value) continue;
          unset($collection->$key);
        }

        break;
                            
      // take all matching elements          
      default:

        foreach($collection->data as $key => $item) {

          if($split) {
            $values = explode($split, (string)$this->extractValue($item, $field));
            if(!in_array($value, $values)) unset($collection->$key);
          } else if($this->extractValue($item, $field) != $value) {            
            unset($collection->$key);
          }
        
        }

        break;

    }

    return $collection;

  }   

  /**
   * Makes sure to provide a valid value for each filter method
   * no matter if an object or an array is given
   * 
   * @param mixed $item
   * @param string $field
   * @return mixed
   */
  protected function extractValue($item, $field) {
    if(is_array($item) and isset($item[$field])) {
      return $item[$field];
    } else if(is_object($item)) {
      return $item->$field();
    } else {
      return false;
    }
  } 

  /**
   * Sorts the collection by a certain field
   * 
   * @param   string  $field The name of the column
   * @param   string  $direction desc (descending) or asc (ascending)
   * @param   const   $method A PHP sort method flag
   * @return  Collection
   */
  public function sortBy($field, $direction = 'desc', $method = SORT_REGULAR) {

    $collection = clone $this;
    $direction  = (strtolower($direction) == 'desc') ? SORT_DESC : SORT_ASC;
    $data       = $collection->data;
    $helper     = array();

    foreach($collection->data as $key => $row) {      
      $helper[$key] = (string)$row->$field();
    }

    // natural sorting    
    if($method === SORT_NATURAL) {
      natsort($helper);
      if($direction === SORT_DESC) $helper = array_reverse($helper);
    } else if($direction === SORT_DESC) {
      arsort($helper, $method);
    } else {
      asort($helper, $method);
    }

    // empty the collection data
    $collection->data = array();
    
    foreach($helper as $key => $val) {
      $collection->data[$key] = $data[$key];
    }
    
    return $collection;
    
  }

  /**
   * Add pagination
   *
   * @param int $limit the number of items per page
   * @param array $options and optional array with options for the pagination class
   * @return object a sliced set of data
   */
  public function paginate($limit, $options = array()) {

    if(is_a($limit, 'Pagination')) {
      $this->pagination = $limit;
      return $this;
    }

    $pagination = new Pagination($this->count(), $limit, $options);
    $pages = $this->slice($pagination->offset(), $pagination->limit());
    $pages->pagination = $pagination;

    return $pages;

  }
    
  /**
   * Get the previously added pagination object
   *
   * @return object
   */
  public function pagination() {
    return $this->pagination;
  }

  /**
   * Map a function to each item in the collection
   * 
   * @param function $callback
   * @return Collection
   */
  public function map($callback) {
    $this->data = array_map($callback, $this->data);
    return $this;
  }

  /**
   * Extracts all values for a single field into 
   * a new array
   * 
   * @param string $field
   * @return array
   */
  public function pluck($field) {
    return array_values(array_map(function($item) use($field) {
      return $this->extractValue($item, $field);
    }, $this->data));
  }

  /**
   * Makes it possible to echo the entire object
   * 
   * @return string
   */
  public function __toString() {
    return implode('<br />', array_map(function($item) {
      return (string)$item;
    }, $this->data));
  }

}