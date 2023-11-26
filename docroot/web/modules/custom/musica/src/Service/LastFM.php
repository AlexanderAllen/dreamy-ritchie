<?php

namespace Drupal\musica\Service;

use Drupal\Core\Messenger\Messenger;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\Spec\YamlParametersTrait;
use GuzzleHttp\Client as GuzzleHttpClient;
use stdClass;

/**
 * Hello world.
 */
class LastFM implements ServiceInterface {
  use YamlParametersTrait;

  /**
   * Guzzle HTTP Client.
   */
  protected GuzzleHttpClient $client;

  /**
   * LastFM API key.
   */
  public string $apiKey;

  /**
   * Name of service in the API parameters specifications.
   */
  public readonly string $specName;

  /**
   * LastFM private key.
   */
  protected string $apiSecret;

  protected Messenger $messenger;

  /**
   * Class constructor.
   */
  public function __construct(Messenger $messenger) {
    $this->messenger = $messenger;
    $this->client = new GuzzleHttpClient(['base_uri' => 'https://ws.audioscrobbler.com/2.0']);
    $this->specName = 'LastFM';
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
    $token = $this->sendRequest($params);
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
    $session_response = $this->sendRequest($session_request);

    // See https://wiki.php.net/rfc/nullsafe_operator.
    return $session_response?->session?->key;
  }

  /**
   * Build and send a service request for a namespaced call.
   */
  public function request(string $namespace, string $call, array $request): stdClass {
    // Append API key to request.
    // @todo throw exception if API key is not present.
    $request = [...$request, 'api_key' => $this->apiKey];

    // Fetch the request specifications for the call.
    $spec = $this->serviceNsRequestParameters($this->specName, $namespace, $call);

    // // Merge the spec with the user request and drop any empty parameters.
    $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

    // // @todo can the response be mapped to a typed native object instead of stdClass?
    $response = $this->sendRequest($merged_request);
    return $response;
  }

  /**
   * Generic reqeuest method.
   *
   * @param array $parameters
   *   Parameters argument.
   */
  public function sendRequest(array $parameters = []) {
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
