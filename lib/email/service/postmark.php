<?php

/**
 * Postmark Email Service
 * 
 * Sends emails with Postmark
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Postmark extends Service {

  /**
   * Sends an email with Postmark
   */
  public function send() {

    if(!$this->email->options['key']) raise('Invalid API key', 'invalid-api-key');

    // reset the api key if we are in test mode
    if($this->email->options['test']) $this->email->options['key'] = 'POSTMARK_API_TEST';

    // the url for postmarks api
    $url = 'https://api.postmarkapp.com/email';

    $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
      'X-Postmark-Server-Token: ' . $this->email->options['key']
    );

    $data = array(
      'From'     => $this->email->from,
      'To'       => $this->email->to,
      'ReplyTo'  => $this->email->replyTo,
      'Subject'  => $this->email->subject,
      'TextBody' => $this->email->body
    );

    // fetch the response
    $this->response = Remote::post($url, array(
      'data'    => a::json($data), 
      'headers' => $headers
    ));
    
    if($this->response->code() != 200) raise('The mail could not be sent!', 'send-error');

  }
  
}