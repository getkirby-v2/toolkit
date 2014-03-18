<?php

namespace Kirby\Toolkit\Email\Service;

use Kirby\Toolkit\Email\Service;
use Kirby\Toolkit\L;
use Kirby\Toolkit\Remote;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Mailgun Email Service
 * 
 * Sends emails with Mailgun
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Mailgun extends Service {

  /**
   * Sends an email with Mailgun
   */
  public function send() {

    if(!$this->email->options['key'])    raise('Invalid API key', 'invalid-api-key'); 
    if(!$this->email->options['domain']) raise('Invalid API domain', 'invalid-api-domain');

    $url  = 'https://api.mailgun.net/v2/' . $this->email->options['domain'] . '/messages';
    $auth = base64_encode('api:' . $this->email->options['key']);

    $headers = array(
      'Accept: application/json',
      'Authorization: Basic ' . $auth
    );

    $data = array(
      'from'     => $this->email->from,
      'to'       => $this->email->to,
      'subject'  => $this->email->subject,
      'text'     => $this->email->body
    );

    $this->response = remote::post($url, array(
      'data'    => $data, 
      'headers' => $headers
    ));
    
    if($this->response->code() != 200) raise('The mail could not be sent!', 'send-error');
    
  }

}