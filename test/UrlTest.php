<?php

require_once('lib/bootstrap.php');

class UrlTest extends PHPUnit_Framework_TestCase {
  
  public function testHasQuery() {

    $this->assertTrue(url::hasQuery('http://getkirby.com/?search=some'));
    $this->assertFalse(url::hasQuery('http://getkirby.com/docs/support'));

  }

  public function testMakeAbsoluteWithDomain() {

    $this->assertEquals(
      url::makeAbsolute('/', 'http://getkirby.com'),
      'http://getkirby.com'
    );

    $this->assertEquals(
      url::makeAbsolute('http://someothersite.com', 'http://getkirby.com'),
      'http://someothersite.com'
    );

    $this->assertEquals(
      url::makeAbsolute('/a/root/path', 'http://getkirby.com'),
      'http://getkirby.com/a/root/path'
    );

    $this->assertEquals(
      url::makeAbsolute('page-b', 'http://getkirby.com/page-a'),
      'http://getkirby.com/page-b'
    );

    $this->assertEquals(
      url::makeAbsolute('child', 'http://getkirby.com/parent/'),
      'http://getkirby.com/parent/child'
    );

    $this->assertEquals(
      url::makeAbsolute('page', 'http://getkirby.com/?query=weird'),
      'http://getkirby.com/page'
    );

    $this->assertEquals(
      url::makeAbsolute('page', 'http://getkirby.com/#hash'),
      'http://getkirby.com/page'
    );

    $this->assertEquals(
      url::makeAbsolute('page', 'http://getkirby.com/param:kirby'),
      'http://getkirby.com/page'
    );

  }

  public function testMakeAbsoluteWithoutDomain() {

    $this->assertEquals(
      url::makeAbsolute('/'),
      '/'
    );

    $this->assertEquals(
      url::makeAbsolute('http://someothersite.com'),
      'http://someothersite.com'
    );

    $this->assertEquals(
      url::makeAbsolute('/a/root/path'),
      '/a/root/path'
    );

    $this->assertEquals(
      url::makeAbsolute('page-b'),
      '/page-b'
    );

    $this->assertEquals(
      url::makeAbsolute('child', '/parent/'),
      '/parent/child'
    );

    $this->assertEquals(
      url::makeAbsolute('page', '/?query=weird'),
      '/page'
    );

    $this->assertEquals(
      url::makeAbsolute('page', '/#hash'),
      '/page'
    );

    $this->assertEquals(
      url::makeAbsolute('page', '/param:kirby'),
      '/page'
    );

  }

}