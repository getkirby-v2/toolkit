<?php

namespace Kirby\Toolkit\Email\Service;

use Kirby\Toolkit\Email\Service;
use Kirby\Toolkit\L;
use Kirby\Toolkit\Str;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Mail Email Service
 * 
 * Sends emails with PHP's mail() function
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Mail extends Service {

  /**
   * Sends an email with PHP's mail function 
   */
  public function send() {

    $headers = array();
    
    $headers[] = 'From: ' . $this->email->from;
    $headers[] = 'Reply-To: ' . $this->email->replyTo;
    $headers[] = 'Return-Path: ' . $this->email->replyTo;
    $headers[] = 'Message-ID: <' . time() . '-' . $this->email->from . '>';
    $headers[] = 'X-Mailer: PHP v' . phpversion();
    $headers[] = 'Content-Type: text/plain; charset=utf-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';
   
    ini_set('sendmail_from', $this->email->from); 
    $send = mail($this->email->to, str::utf8($this->email->subject), str::utf8($this->email->body), implode("\r\n", $headers));
    ini_restore('sendmail_from');

    if(!$send) raise('The mail could not be sent!', 'send-error');

  }
  
}
