<?php

require_once('lib/bootstrap.php');

class HelpersTest extends PHPUnit_Framework_TestCase {

  public function testInvalid()
  {
    $data = [
      'username' => 123,
      'email' => 'homersimpson.com',
      'zip' => 'abc',
    ];

    $rules = [
      'username' => ['alpha'],
      'email' => ['required', 'email'],
      'zip' => ['integer'],
    ];

    $messages = [
      'username' => 'The username must not contain numbers',
      'email' => 'Invalid email',
      'zip' => 'The ZIP must contain only numbers',
    ];

    $result  = invalid($data, $rules, $messages);
    $this->assertEquals($messages, $result);

    $data = [
      'username' => 'homer',
      'email' => 'homer@simpson.com',
      'zip' => 123,
    ];

    $result  = invalid($data, $rules, $messages);
    $this->assertEquals([], $result);
  }

  public function testInvalidSimple()
  {
    $data = ['homer', null];
    $rules = [['alpha'], ['required']];
    $result = invalid($data, $rules);
    $this->assertEquals(1, $result[1]);
  }

  public function testInvalidRequired()
  {
    $rules = ['email' => ['required']];
    $messages = ['email' => ''];

    $result = invalid(['email' => null], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result = invalid(['name' => 'homer'], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result = invalid(['email' => ''], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result = invalid(['email' => []], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result = invalid(['email' => '0'], $rules, $messages);
    $this->assertEquals([], $result);

    $result = invalid(['email' => 0], $rules, $messages);
    $this->assertEquals([], $result);

    $result = invalid(['email' => false], $rules, $messages);
    $this->assertEquals([], $result);

    $result = invalid(['email' => 'homer@simpson.com'], $rules, $messages);
    $this->assertEquals([], $result);
  }

  public function testInvalidOptions()
  {
    $rules = [
      'username' => ['min' => 6]
    ];

    $messages = ['username' => ''];

    $result  = invalid(['username' => 'homer'], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result  = invalid(['username' => 'homersimpson'], $rules, $messages);
    $this->assertEquals([], $result);

    $rules = [
      'username' => ['between' => [3, 6]]
    ];

    $result  = invalid(['username' => 'ho'], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result  = invalid(['username' => 'homersimpson'], $rules, $messages);
    $this->assertEquals($messages, $result);

    $result  = invalid(['username' => 'homer'], $rules, $messages);
    $this->assertEquals([], $result);
  }
}
