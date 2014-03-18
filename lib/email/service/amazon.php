<?php

namespace Kirby\Toolkit\Email\Service;

use Kirby\Toolkit\Email\Service;
use Kirby\Toolkit\L;
use Kirby\Toolkit\Remote;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Amazon Email Service
 * 
 * Sends emails with Amazon SES
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Amazon extends Service {

  /**
   * Sends an email with Amazon SES
   */
  public function send() {

    if(!$this->email->options['key'])    raise('Invalid API key', 'invalid-api-key');
    if(!$this->email->options['secret']) raise('Invalid API secret', 'invalid-api-secret');

    $setup = array(
      'Action'                           => 'SendEmail',
      'Destination.ToAddresses.member.1' => $this->email->to,
      'ReplyToAddresses.member.1'        => $this->email->replyTo,
      'ReturnPath'                       => $this->email->replyTo,
      'Source'                           => $this->email->from,
      'Message.Subject.Data'             => $this->email->subject,
      'Message.Body.Text.Data'           => $this->email->body
    );

    $params = array();

    foreach($setup as $key => $value) {
      $params[] = $key . '=' . str_replace('%7E', '~', rawurlencode($value));
    }

    sort($params, SORT_STRING);
    
    $host      = 'email.us-east-1.amazonaws.com';
    $url       = 'https://' . $host . '/';
    $date      = gmdate('D, d M Y H:i:s e');
    $signature = base64_encode(hash_hmac('sha256', $date, $this->email->options['secret'], true));
    $query     = implode('&', $params);
    $headers   = array();
    $auth      = 'AWS3-HTTPS AWSAccessKeyId=' . $this->email->options['key'];
    $auth     .= ',Algorithm=HmacSHA256,Signature=' . $signature;

    $headers[] = 'Date: ' . $date;
    $headers[] = 'Host: ' . $host;
    $headers[] = 'X-Amzn-Authorization: '. $auth;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    
    $this->response = remote::post($url, array(
      'data'    => $query, 
      'headers' => $headers
    ));

    if(!in_array($this->response->code(), array(200, 201, 202, 204))) raise('The mail could not be sent!', 'send-error');
    
  }

}