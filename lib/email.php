<?php

/**
 * Email
 * 
 * A simple email handling class which supports
 * multiple email services. Check out the email subfolder
 * for all available services
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Email {

  static public $services = array();

  // configuration
  static public $defaults = array(
    'disabled' => false,
    'service'  => 'mail',
    'to'       => null,
    'from'     => null,
    'replyTo'  => null,
    'subject'  => null,
    'body'     => null,
  );

  public $options = array();

  // email details
  public $service = null;
  public $to      = null;
  public $from    = null;
  public $replyTo = null;
  public $subject = null;
  public $body    = null;

  protected $errors = array();
  
  public $response = array();

  /**
   * Constructor
   * 
   * @param string $service The name of the service driver you want to use
   */
  public function __construct($params = null) {
    $this->options = array_merge(static::$defaults, (array)$params);
    $this->service = $this->options['service'];
    $this->to      = $this->options['to'];
    $this->from    = $this->options['from'];
    $this->replyTo = $this->options['replyTo'];
    $this->subject = $this->options['subject'];
    $this->body    = $this->options['body'];
  }

  /**
   * Sends the constructed email
   * 
   * @param array $params Optional way to set values for the email
   * @return boolean
   */
  public function send($params = null) {

    if(!is_null($params)) {

      $this->options['service'] = $this->service;
      $this->options['to']      = $this->to;
      $this->options['from']    = $this->from;
      $this->options['replyTo'] = $this->replyTo;
      $this->options['subject'] = $this->subject;
      $this->options['body']    = $this->body;
      $this->options = array_merge($this->options, (array)$params);

      // overwrite the values
      $this->service = $this->options['service'];
      $this->to      = $this->options['to'];
      $this->from    = $this->options['from'];
      $this->replyTo = $this->options['replyTo'];
      $this->subject = $this->options['subject'];
      $this->body    = $this->options['body'];

    }

    // if there's no dedicated reply to address, use the from address
    if(is_null($this->replyTo)) $this->replyTo = $this->from;

    // validate the email 
    $this->validate();

    // don't send if the validation failed
    if($this->failed()) return false;

    // check if the email service is available
    if(!isset(static::$services[$this->service])) throw new Exception('The email service is not available: ' . $this->service);

    // run the service
    call(static::$services[$this->service], $this);

    return ($this->failed()) ? false : true;

  }

  /**
   * Validates the constructed email 
   * to make sure it can be sent at all
   */
  public function validate() {

    if(static::$defaults['disabled']) $this->raise('disabled', 'Email has been disabled');
  
    $data = array(
      'to'      => $this->extractAddress($this->to),
      'from'    => $this->extractAddress($this->from),
      'replyTo' => $this->extractAddress($this->replyTo),
      'subject' => $this->subject,
      'body'    => $this->body
    );

    if(!v::email($data['to']))      $this->raise('to', 'Invalid recipient');
    if(!v::email($data['from']))    $this->raise('from', 'Invalid sender');
    if(!v::email($data['replyTo'])) $this->raise('replyTo', 'Invalid reply address');

    if(empty($data['subject']))     $this->raise('subject', 'Missing subject');
    if(empty($data['body']))        $this->raise('body', 'Missing body');

  }

  /**
   * Returns all errors
   * 
   * @return array
   */
  public function errors() {
    return $this->errors;
  }

  /**
   * Returns a specific error by code
   * 
   * @param string $code
   */
  public function error($code = null) {
    return is_null($code) ? a::first($this->errors) : a::get($this->errors, $code);
  }

  /**
   * Raises an internal error
   */
  public function raise($code = null, $message) {
    return $this->errors[$code] = $message;
  }
  
  /**
   * Returns the optional response from the service
   * 
   * @return mixed
   */
  public function response() {
    return $this->response;
  }

  /**
   * Checks if sending the email failed
   * 
   * @return boolean
   */
  public function failed() {
    return count($this->errors) > 0;
  }

  /**
   * Checks if sending the email succeeded
   * 
   * @return boolean
   */
  public function passed() {
    return !$this->failed();
  }

  /**
   * Extracts the email address from an address string
   * 
   * @return string
   */
  protected function extractAddress($string) {
    if(v::email($string)) return $string;
    preg_match('/<(.*?)>/i', $string, $array);
    return (empty($array[1])) ? $string : $array[1];
  }

}


email::$services['postmark'] = function($email) {

  if(empty($email->options['key'])) return $email->raise('invalid-api-key', 'Invalid API key');

  // reset the api key if we are in test mode
  if($email->options['test']) $email->options['key'] = 'POSTMARK_API_TEST';

  // the url for postmarks api
  $url = 'https://api.postmarkapp.com/email';

  $headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'X-Postmark-Server-Token: ' . $email->options['key']
  );

  $data = array(
    'From'     => $email->from,
    'To'       => $email->to,
    'ReplyTo'  => $email->replyTo,
    'Subject'  => $email->subject,
    'TextBody' => $email->body
  );

  // fetch the response
  $email->response = Remote::post($url, array(
    'data'    => json_encode($data), 
    'headers' => $headers
  ));
  
  if($email->response->code() != 200) return $email->raise('send-error', 'The mail could not be sent!');

};