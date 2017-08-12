<?php

require_once('lib/bootstrap.php');

class VTest extends PHPUnit_Framework_TestCase {

  public function testMatch() {

    $value = 'super-09';

    $this->assertTrue(v::match($value, '/[a-z0-9-]+/i'));

    $value = '#1asklajd.12jaxax';

    $this->assertFalse(v::match($value, '/^[a-z0-9-]+$/i'));

  }

  public function testSame() {

    $this->assertTrue(v::same('same same but different', 'same same but different'));
    $this->assertFalse(v::same('same same but different', 'same same but diffrent'));

  }

  public function testDifferent() {

    $this->assertFalse(v::different('same same but different', 'same same but different'));
    $this->assertTrue(v::different('same same but different', 'same same but diffrent'));

  }

  public function testDate() {

    $this->assertTrue(V::date('2017-12-24'));
    $this->assertTrue(V::date('29.01.1989'));
    $this->assertTrue(V::date('January 29, 1989'));

    $this->assertFalse(V::date('äöüß'));
    $this->assertFalse(V::date('2017-02-31'));
    $this->assertFalse(V::date('January 32, 1989'));

  }

  public function testEmail() {

    $this->assertTrue(v::email('bastian@getkirby.com'));
    $this->assertFalse(v::email('http://getkirby.com'));

  }

  public function testFilename() {

    $this->assertTrue(v::filename('my-awesome-image@2x.jpg'));
    $this->assertFalse(v::filename('my_fucked!up#image.jpg'));

  }

  public function testAccepted() {

    $this->assertTrue(v::accepted('on'));
    $this->assertTrue(v::accepted('yes'));
    $this->assertTrue(v::accepted('1'));
    $this->assertFalse(v::accepted('no'));

  }

  public function testMin() {

    $this->assertTrue(v::min('superstring', 5));
    $this->assertFalse(v::min('superstring', 20));

    $this->assertTrue(v::min(6, 5));
    $this->assertFalse(v::min(6, 20));

    $this->assertTrue(v::min(range(0,10), 5));
    $this->assertFalse(v::min(range(0,10), 20));

  }

  public function testMax() {

    $this->assertTrue(v::max('superstring', 11));
    $this->assertFalse(v::max('superstring', 5));

    $this->assertTrue(v::max(6, 11));
    $this->assertFalse(v::max(6, 5));

    $this->assertTrue(v::max(range(0,10), 11));
    $this->assertFalse(v::max(range(0,10), 5));

  }

  public function testBetween() {

    $this->assertTrue(v::between('superstring', 5, 11));
    $this->assertFalse(v::between('superstring', 3, 5));

    $this->assertTrue(v::between(6, 5, 11));
    $this->assertFalse(v::between(6, 3, 5));

    $this->assertTrue(v::between(range(0,10), 5, 11));
    $this->assertFalse(v::between(range(0,10), 3, 5));

  }

  public function testIn() {
    $this->assertTrue(v::in('a', array('a', 'b', 'c')));
    $this->assertFalse(v::in('a', array('b', 'c', 'd')));
  }

  public function testNotIn() {
    $this->assertTrue(v::notIn('a', array('b', 'c', 'd')));
    $this->assertFalse(v::notIn('a', array('a', 'b', 'c')));
  }

  public function testIp() {
    $this->assertTrue(v::ip('127.0.0.1'));
    $this->assertFalse(v::ip('not an ip'));
  }

  public function testAlpha() {
    $this->assertTrue(v::alpha('abc'));
    $this->assertFalse(v::alpha('1234'));
  }

  public function testNum() {
    $this->assertTrue(v::num('1234'));
    $this->assertFalse(v::num('abc'));
  }

  public function testAlphaNum() {
    $this->assertTrue(v::alphanum('abc1234'));
    $this->assertFalse(v::alphanum('#!asdas'));
  }

  public function testInteger() {
    $this->assertTrue(v::integer('1234'));
    $this->assertFalse(v::integer('0.1'));
  }

  public function testSize() {
    $this->assertTrue(v::size('super', 5));
    $this->assertTrue(v::size('1234', 1234));
    $this->assertTrue(v::size(range(0,9), 10));
  }

  public function testFilesize() {
    $this->assertTrue(v::filesize(['size' => 9000], 9));
    $this->assertFalse(v::filesize(['size' => 9000], 8));
    $this->assertFalse(v::filesize([], 8));
    $this->assertFalse(v::filesize('asdf', 8));
  }

  public function testMime() {
    $path = sys_get_temp_dir().'/kirby_test_mime';
    file_put_contents($path, 'sometext');
    $this->assertTrue(v::mime(['tmp_name' => $path], ['text/plain']));
    $this->assertTrue(v::mime($path, ['text/plain']));
    $this->assertFalse(v::mime($path, ['image/png']));
    unlink($path);
  }

  public function testImage()
  {
    $path = sys_get_temp_dir().'/kirby_test_image';
    file_put_contents($path, 'sometext');
    $this->assertFalse(v::image($path));
    // This is a GIF: http://probablyprogramming.com/2009/03/15/the-tiniest-gif-ever
    file_put_contents($path, base64_decode('R0lGODlhAQABAIABAP///wAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='));
    $this->assertTrue(v::image($path));
    unlink($path);
  }

}
