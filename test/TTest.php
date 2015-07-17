<?php

require_once('lib/bootstrap.php');

class TTest extends PHPUnit_Framework_TestCase {

  public function __construct() {
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

  public function testSetWithArray() {
    T::set("en", array(
      "bookmark.type" => "Bookmark Type",
      "bookmark.url" => "URL"
      )
    );
    T::set("de", array(
      "bookmark.type" => "Lesezeichen Typ",
      "bookmark.url" => "Link"
      )
    );
    T::setLanguage('en');
    $this->assertEquals('Bookmark Type', T::get('bookmark.type'));
    $this->assertEquals('URL', T::get('bookmark.url'));

    T::setLanguage('de');
    $this->assertEquals('Lesezeichen Typ', T::get('bookmark.type'));
    $this->assertEquals('Link', T::get('bookmark.url'));
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