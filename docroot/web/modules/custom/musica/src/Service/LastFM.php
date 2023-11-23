<?php

namespace Drupal\musica\Service;

use Drupal\Core\Messenger\Messenger;
use GuzzleHttp\Client as GuzzleHttpClient;

/**
 * Hello world.
 */
class LastFM {

  /**
   * Guzzle HTTP Client.
   *
   * @var GuzzleHttp\Client
   */
  protected $client;

  /**
   * LastFM API key.
   *
   * @var string
   */
  public $apiKey;

  /**
   * LastFM private key.
   *
   * @var string
   */
  protected $apiSecret;

  protected $messenger;

  /**
   * Class constructor.
   */
  public function __construct(Messenger $messenger) {
    $this->messenger = $messenger;
    $this->client = new GuzzleHttpClient(['base_uri' => 'https://ws.audioscrobbler.com/2.0']);
    $this->apiKey = getenv('LASTFM_API_KEY') ?? '';
    $this->apiSecret = getenv('LASTFM_API_SECRET') ?? '';
  }

  /**
   * Step 2 - Fetch request token.
   *
   * @return string
   *   Request token.
   */
  public function fetchRequestToken() {

    $params = [
      'api_key' => $this->apiKey,
      'method' => 'auth.gettoken',
    ];
    $token = $this->request($params);
    $token = $token->token;
    return $token;
  }

  /**
   * Step 4- Fetch  web service session key.
   */
  public function fetchSessionKey(string $request_token) {
    $session_request = [
      'api_key' => $this->apiKey,
      'method' => 'auth.getSession',
      'token' => $request_token,
    ];
    $session_response = $this->request($session_request);

    // See https://wiki.php.net/rfc/nullsafe_operator.
    return $session_response?->session?->key;
  }

  /**
   * Generic reqeuest method.
   *
   * @param array $parameters
   *   Parameters argument.
   */
  public function request(array $parameters = []) {
    $parameters['api_sig'] = $this->sign($parameters);
    $parameters['format'] = 'json';

    // Fetch a request token.
    // See https://www.last.fm/api/desktopauth.
    $options = ['query' => $parameters];
    try {
      $response = $this->client->request('GET', '', $options);
    }
    catch (\Throwable $th) {
      $this->messenger->addError($th->getMessage());
    }

    if ($response->getStatusCode() == 200) {
      $json = $response->getBody()->getContents();
      return json_decode($json);
    } else {
      return NULL;
    }
  }

  /**
   * Sign and return request parameters usign secret key.
   *
   * See https://www.last.fm/api/authspec#_8-signing-calls.
   *
   * @param array $parameters
   *   Associative array of request parameters.
   *
   * @return string
   *   Signature string.
   */
  protected function sign(array $parameters = []) {
    $sig = '';
    ksort($parameters, SORT_STRING);

    foreach ($parameters as $key => $value) {
      $sig .= "{$key}{$value}";
    }

    // Append secret to signature string.
    $sig .= $this->apiSecret;
    return md5($sig);
  }

}
