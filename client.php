<?php
/**
 * @file
 *   Demo client for the VISTA Campus OAuth login funcitonality.
 */

require_once 'vendor/autoload.php';

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Oauth\OauthPlugin;

// Client configuration.
const VISTA_APP_ENDPOINT     = 'http://community3dev.devcloud.acquia-sites.com/api';
const VISTA_APP_OAUTH_KEY    = 'YzjVHuk8xoXCTcNwYg57yiCW5w59tucC';
const VISTA_APP_OAUTH_SECRET = 'ZDrWuDsunNkRroU5psb6QyMmT86XkYST';

// Assertion options. 
assert_options(ASSERT_BAIL, TRUE);

// Initialize client.
$client = new Client(VISTA_APP_ENDPOINT);

// Setup OAuth.
$oauth = new OauthPlugin(
  array(
    'consumer_key' => VISTA_APP_OAUTH_KEY,
    'consumer_secret' => VISTA_APP_OAUTH_SECRET,
  )
);
$client->addSubscriber($oauth);

// Good login.
$login = new validateUser('testuser', 'drib.samoa.cite.clay', $client);
assert($login->login() === TRUE);
print "Login succeeded.\n\n";

// Bad login - no user.
$login = new validateUser('no_user@example.com', 'password!', $client);
// This will throw an exception.
assert($login->login() === FALSE);
print "Login failed, invalid account.\n\n";
 
// Bad login - wrong password.
$login = new validateUser('a_user@example.com', 'bad password!', $client);
// This will throw an exception.
assert($login->login() === FALSE);
print "Login failed, invalid account.\n\n";

/**
 * Demo user validator.
 */
class validateUser {

  /**
   * @param string $username
   *   VISTA Campus username or email address.
   * @param string $password
   *   VISTA Campus password
   * @param \Guzzle\Http\Client $client
   *   Guzzle HTTP client object.
   */

  public function __construct($username, $password, Client $client) {
    $this->login = array(
      'username' => $username,
      'password' => $password,
    );

    $this->client = $client;
  }

  /**
   * Send the login request to VISTA Campus.
   *
   * @return boolean
   *   Returns TRUE for a valid login, FALSE otherwise.
   */
  public function login() {
    try {
      $response = $this->client->post('user/login')
        ->addPostFields($this->login)
        ->send();
      print $response;
      return $response->getStatusCode() == 200;
    }
    catch (ClientErrorResponseException $e) {
      print sprintf("Caught exception: \n\n%s\n\n", $e->getMessage());
      return FALSE;
    }
  }
}
 ?>