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
   * Display the markup.
   *
   * @return array
   *   Render array.
   */
  public function content() {
    $client = new GuzzleHttpClient(['base_uri' => 'https://ws.audioscrobbler.com/2.0']);

    $params = [
      'api_key' => getenv('LASTFM_API_KEY') ?? '',
      'method' => 'auth.gettoken',
    ];
    $params['api_sig'] = $this->sign($params);
    $params['format'] = 'json';

    // Fetch a request token.
    // See https://www.last.fm/api/desktopauth.
    $options = ['query' => $params];
    $response = $client->request('GET', '', $options);
    $response->getStatusCode();

    if ($response->getStatusCode() == 200) {
      $json = $response->getBody()->getContents();
      $obj = json_decode($json);
      $request_token = $obj->token;
    }

    // $response->getHeaders();
    // $response->getStatusCode();
    // $body = $response->getBody();
    // $c = $body->getContents();

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
      // '$markup' => var_export($c),
    ];
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
    asort($parameters, SORT_NATURAL);

    foreach ($parameters as $key => $value) {
      $sig .= "{$key}{$value}";
    }

    // Append secret to signature string.
    $sig .= getenv('LASTFM_API_SECRET') ?? '';
    return md5($sig);
  }

}
