<?php

namespace Drupal\Tests\cas\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the user's ability to reset their password.
 *
 * @group cas
 */
class CasPasswordResetTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['cas'];

  /**
   * The CAS settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $settings;

  /**
   * A user linked with a CAS account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $casUser;

  /**
   * A user not linked with a CAS account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $nonCasUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->settings = $this->config('cas.settings');
    // Create two users, one associated with CAS and one that's not.
    $this->casUser = $this->drupalCreateUser([], 'user_with_cas');
    $this->container->get('cas.user_manager')->setCasUsernameForAccount($this->casUser, 'user_with_cas');
    $this->nonCasUser = $this->drupalCreateUser([], 'user_without_cas');
  }

  /**
   * Tests password reset form as anonymous.
   */
  public function testPasswordResetAsAnonymous() {
    // Test with the 'user_accounts.restrict_password_management' setting off.
    $this->settings->set('user_accounts.restrict_password_management', FALSE)->save();

    // Check that a CAS user is able to reset their password.
    $this->drupalPostForm('/user/password', ['name' => 'user_with_cas'], 'Submit');
    $this->assertSession()->addressEquals('user/login');
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');

    // Check that a non-CAS user is able to reset their password.
    $this->drupalPostForm('/user/password', ['name' => 'user_without_cas'], 'Submit');
    $this->assertSession()->addressEquals('user/login');
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');

    // Test with the 'user_accounts.restrict_password_management' setting on.
    $this->settings->set('user_accounts.restrict_password_management', TRUE)->save();

    // Check that a CAS user is not able to reset their password.
    $this->drupalPostForm('/user/password', ['name' => 'user_with_cas'], 'Submit');
    $this->assertSession()->addressEquals('user/password');
    $this->assertSession()->pageTextContains('The requested account is associated with CAS and its password cannot be managed from this website.');

    // Test a customized error message for the same user.
    $this->settings->set('error_handling.message_restrict_password_management', 'You cannot manage your password. Back to <a href="[site:url]">homepage</a>.')->save();

    $this->getSession()->reload();
    $this->assertSession()->pageTextContains('You cannot manage your password. Back to homepage.');
    $this->assertSession()->linkExists('homepage');

    // Check that a non-CAS user is able to reset their password.
    $this->drupalPostForm('/user/password', ['name' => 'user_without_cas'], 'Submit');
    $this->assertSession()->addressEquals('user/login');
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');
  }

  /**
   * Tests password reset form as authenticated user.
   */
  public function testPasswordResetAsAuthenticated() {
    // Test with the 'user_accounts.restrict_password_management' setting off.
    $this->settings
      ->set('user_accounts.restrict_password_management', FALSE)
      // Allow CAS users normal login.
      ->set('user_accounts.prevent_normal_login', FALSE)
      ->save();

    // Check that a non-CAS user is able to reset their password.
    $this->drupalLogin($this->nonCasUser);
    $this->drupalPostForm('/user/password', [], 'Submit');
    $this->assertSession()->addressEquals($this->nonCasUser->toUrl());
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');

    // Check that a CAS user is able to reset their password.
    $this->drupalLogin($this->casUser);
    $this->drupalPostForm('/user/password', [], 'Submit');
    $this->assertSession()->addressEquals($this->casUser->toUrl());
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');

    // Test with the 'user_accounts.restrict_password_management' setting on.
    $this->settings->set('user_accounts.restrict_password_management', TRUE)->save();

    // Check that a CAS user's access to the /user/password route is denied.
    $this->drupalGet('/user/password');
    $this->assertSession()->statusCodeEquals(403);

    // Check that a non-CAS user is able to reset their password.
    $this->drupalLogin($this->nonCasUser);
    $this->drupalPostForm('/user/password', [], 'Submit');
    $this->assertSession()->addressEquals($this->nonCasUser->toUrl());
    $this->assertSession()->pageTextContains('Further instructions have been sent to your email address.');
  }

}
