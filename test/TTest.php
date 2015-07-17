<?php

require_once('lib/bootstrap.php');

class TTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    T::set('de.user.username', 'Benutzername');
    T::set('de.user.password', 'Passwort');
    T::set('en.user.username', 'Username');
    T::set('en.user.password', 'Password');
  }

  public function testGetEnglishTranslation() {
    t::setLanguage('en');
    $this->assertEquals('Username', T::get('user.username'));
    $this->assertEquals('Password', T::get('user.password'));
  }

  public function testGetGermanTranslation() {
    T::setLanguage('de');
    $this->assertEquals('Benutzername', T::get('user.username'));
    $this->assertEquals('Passwort', T::get('user.password'));
  }

  public function testGetNotExistentKeyMissingLanguage() {
    T::setLanguage('fr');
    $this->assertEquals(null, T::get('user.username'));
  }

  public function testGetNotExistentKeyMissingKey() {
    T::setLanguage('en');
    $this->assertEquals(null, T::get('user.passwordConfirmation'));
  }

  public function testGetCurrentLanguage() {
    T::setLanguage('de');
    $this->assertEquals('de', T::getLanguage());
  }

  public function testWithMultipleSettings() {
    T::setLanguage('en');
    T::setLanguage('de');
    $this->assertEquals('de', T::getLanguage());
  }


}