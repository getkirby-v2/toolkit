<?php

namespace Kirby\Toolkit\Email;

use Kirby\Toolkit\Email;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Email Service Abstract
 * 
 * This class is a template for all email 
 * service drivers. Extend this to create a service class
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class Service {

  // the parent email object
  protected $email = null;

  // an optional response from the service
  protected $response = null;

  /**
   * Constructor
   * 
   * @param object The parent Email object
   */
  public function __construct(Email $email) {
    $this->email = $email;
  }

  /**
   * Returns the response if available
   * 
   * @return mixed
   */
  public function response() {
    return $this->response;
  }

}