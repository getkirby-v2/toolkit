<?php

require_once('lib/bootstrap.php');

class UrlTest extends PHPUnit_Framework_TestCase {

  public function testHasQuery() {

    $this->assertTrue(url::hasQuery('http://getkirby.com/?search=some'));
    $this->assertFalse(url::hasQuery('http://getkirby.com/docs/support'));

  }

  public function testSolveRelative() {

    $this->assertEquals(
      url::solveRelative('http://getkirby.com', '/'),
      'http://getkirby.com'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com', 'http://someothersite.com'),
      'http://someothersite.com'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com', '/a/root/path'),
      'http://getkirby.com/a/root/path'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com/page-a', 'page-b'),
      'http://getkirby.com/page-b'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com/parent/', 'child'),
      'http://getkirby.com/parent/child'
    );

    $this->assertEquals(
      url::solveRelative('https://getkirby.com/page/parent-a', 'parent-b/subpage'),
      'https://getkirby.com/page/parent-b/subpage'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com/?query=weird', 'page'),
      'http://getkirby.com/page'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com/#hash', 'page'),
      'http://getkirby.com/page'
    );

    $this->assertEquals(
      url::solveRelative('http://getkirby.com/param:kirby', 'page'),
      'http://getkirby.com/page'
    );

  }

  public function testShort() {

    $this->assertEquals(
      url::short('http://no-www.org'),
      'no-www.org'
    );

  }

}