<?php

namespace Drupal\musica\Service;

use Drupal\Core\Messenger\Messenger;
use Drupal\musica\Service\ServiceInterface;
use Drupal\musica\Spec\YamlParametersTrait;
use GuzzleHttp\Client as GuzzleHttpClient;
use Psr\Http\Message\ResponseInterface;

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
    $o = $this->sendRequest($params);
    $token = json_decode($o->getBody()->getContents());
    return $token->token;
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
    $o = $this->sendRequest($session_request);
    $session_response = json_decode($o->getBody()->getContents());
    return $session_response?->session?->key;
  }

  /**
   * {@inheritdoc}
   */
  public function request(string $namespace, string $call, array $request) {
    // Append API key to request.
    // @todo throw exception if API key is not present.
    $request = [...$request, 'api_key' => $this->apiKey];

    // Fetch the request specifications for the call.
    $spec = $this->serviceNsRequestParameters($this->specName, $namespace, $call);

    // Merge the spec with the user request and drop any empty parameters.
    $merged_request = array_filter([...$spec, ...$request], fn ($value) => $value !== '');

    // // @todo can the response be mapped to a typed native object instead of stdClass?
    try {
      $res = $this->sendRequest($merged_request);
      if ($res->getStatusCode() === 200) {
        return $res->getBody()->getContents();
      }
    }
    catch (\Throwable $th) {
      $this->messenger->addError($th->getMessage());
    }
    return '';
  }

  /**
   * Uses Guzzle to send and receive signed reqeust.
   *
   * @param array $parameters
   *   Parameters argument.
   */
  public function sendRequest(array $parameters = []): ResponseInterface {
    $parameters['api_sig'] = $this->sign($parameters);
    $parameters['format'] = 'json';

    // Fetch a request token.
    // See https://www.last.fm/api/desktopauth.
    $options = ['query' => $parameters];
    return $this->client->request('GET', '', $options);
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
