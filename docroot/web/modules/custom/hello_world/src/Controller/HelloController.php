<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client as GuzzleHttpClient;
/**
 * Hello world.
 *
 * @package Drupal\hello_world\Controller
 */
class HelloController extends ControllerBase {
  /**
   * Guzzle HTTP Client.
   *
   * @var GuzzleHttp\Client
   */
  protected $client;

  /**
   * Display the markup.
   *
   * @return array
   *   Render array.
   */
  public function content() {
    $this->client = new GuzzleHttpClient(['base_uri' => 'https://ws.audioscrobbler.com/2.0']);
    $api_key = getenv('LASTFM_API_KEY') ?? '';

    // Step 2 - Fetch request token.
    $params = [
      'api_key' => $api_key,
      'method' => 'auth.gettoken',
    ];
    $token = $this->request($params);
    $token = $token->token;

    // TODO: Steps 3/4 need to be broken into separate pages/form/steps.
    // If the user does not authorize the token the session req is not valid.
    // Step 3 - request user authorization (only once)
    $redirect = "http://www.last.fm/api/auth/?api_key={$api_key}&token={$token}";
    $content = "Visit {$redirect} to authorize the application.";

    // Step 4 - Fetch web service session.
    // TODO: need a persistent storage for the request token, so it can be grabbed
    // on a second visit and used for the session request.
    // $session_request = [
    //   'api_key' => $api_key,
    //   'method' => 'auth.getSession',
    //   'token' => $token,
    // ];
    // $session_response = $this->request($session_request);


    return [
      '#type' => 'markup',
      '#markup' => $content,
      // '$markup' => var_export($c),
    ];
  }

  protected function request(array $parameters = []) {
    $parameters['api_sig'] = $this->sign($parameters);
    $parameters['format'] = 'json';

    // Fetch a request token.
    // See https://www.last.fm/api/desktopauth.
    $options = ['query' => $parameters];
    try {
      $response = $this->client->request('GET', '', $options);
    }
    catch (\Throwable $th) {
      \Drupal::messenger()->addError($th->getMessage());
    }

    if ($response->getStatusCode() == 200) {
      $json = $response->getBody()->getContents();
      return json_decode($json);
    }
    else {
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
    $sig .= getenv('LASTFM_API_SECRET') ?? '';
    return md5($sig);
  }

}
