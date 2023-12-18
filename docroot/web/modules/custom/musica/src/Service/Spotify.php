<?php

namespace Drupal\musica\Service;

use Drupal\Core\Cache\DatabaseBackend as Cache;
use Drupal\Core\Cache\Context\UserCacheContext;
use Drupal\Core\Config\ConfigFactory;
use Kerox\OAuth2\Client\Provider\Spotify as SpotifyClient;
use Kerox\OAuth2\Client\Provider\SpotifyScope;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Utility\Error;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

/**
 * Service for Spotify API.
 *
 * Provides OAuth authentication.
 */
final class Spotify {

  private string $endpoint;
  private array $cacheLabels;
  private Cache $cache;
  private LoggerChannel $logger;
  private SpotifyClient $provider;

  /**
   * Spotify Service constructor.
   */
  public function __construct(
    Cache $cache,
    UserCacheContext $context,
    LoggerChannel $logger,
    ConfigFactory $configFactory
    ) {
    $this->cache = $cache;
    $this->logger = $logger;

    $settings = $configFactory->get('musica.settings');
    $this->endpoint = $settings->get('spotify_api_endpoint');
    $this->provider = new SpotifyClient([
      'redirectUri'  => $settings->get('spotify_redirect_url'),
      'clientId'     => $settings->get('spotify_client_id'),
      'clientSecret' => $settings->get('spotify_client_secret'),
    ]);

    $this->cacheLabels = [$context->getLabel() . ':' . $context->getContext()];

  }

  /**
   * Header cache control parser.
   */
  private function parseCacheTimestamp(string $header): int {
    $matches = [];
    if (preg_match('/.*max-age=([0-9]*)$/', $header, $matches) === 1 && array_key_exists(1, $matches)) {
      return (int) $matches[1];
    }
    else {
      return 0;
    }
  }

  /**
   * Determines response expiration by inspecting the responses' cache headers.
   *
   * @return int
   *   Defaults to zero (no cache). Otherwise returns expiration in seconds as
   *   found in the cache header.
   */
  private function expires(Response $response): int {
    $expires = 0;
    if ($response->hasHeader('cache-control')) {
      $headers = $response->getHeader('cache-control');
      if (array_key_exists(0, $headers)) {
        $expires = $this->parseCacheTimestamp($headers[0]);
      }
    }
    return $expires;
  }

  /**
   * Returns aa fully typed and populated resource.
   */
  public function getResourceObject() {}

  /**
   * Requests, caches and returns the specified API resource.
   *
   * @TODO: looking for more efficient OPENAPI/hydration methods.
   */
  public function getResource(string $resource = 'artists/4Z8W4fKeB5YxbusRsdQVPb'): string {
    if ($this->cache->get($resource) !== FALSE) {
      return $this->cache->get($resource)->data;
    }

    $content = '';
    $client = new GuzzleHttpClient(['base_uri' => $this->endpoint]);

    /** @var \GuzzleHttp\Psr7\Request */
    $request = $this->provider->getAuthenticatedRequest(
      'GET',
      $resource,
      $this->getToken()
    );

    try {
      $response = $client->send($request);
      $content = $response->getBody()->getContents();

      $expires = $this->expires($response);
      if ($expires > 0) {
        $this->cache->set(
          $resource,
          $content,
          $expires + microtime(TRUE),
          ['musica', 'musica:artist']
        );
      }
    }
    catch (GuzzleException $e) {
      Error::logException($this->logger, $e);
    }

    return $content;
  }

  public function getToken() {
    $token = $this->cache->get('musica:spotify:access_token');
    if ($token !== FALSE) {
      return $token->data;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Retrieves authorization token from Spotify API.
   *
   * If token has expired a new one is created, otherwise it is served from
   * cache.
   */
  public function authorize(): void {
    if ($this->cache->get('musica:spotify:access_token') !== FALSE) {
      return;
    }

    // Re-authorize application if a refresh token is available.
    if ($this->cache->get('musica:spotify:refresh_token') !== FALSE) {
      $this->getAccessToken('refresh_token', [
        'refresh_token' => $this->cache->get('musica:spotify:refresh_token')->data,
      ]);
    }

    if (!isset($_GET['code'])) {
      // If we don't have an authorization code then get one.
      $authUrl = $this->provider->getAuthorizationUrl([
        'scope' => [
          SpotifyScope::USER_READ_EMAIL->value,
          SpotifyScope::USER_TOP_READ->value,
          SpotifyScope::USER_READ_PLAYBACK_STATE->value,
          SpotifyScope::USER_MODIFY_PLAYBACK_STATE->value,
          SpotifyScope::USER_READ_CURRENTLY_PLAYING->value,
          SpotifyScope::USER_READ_PLAYBACK_POSITION->value,
          SpotifyScope::USER_READ_RECENTLY_PLAYED->value,
          SpotifyScope::STREAMING->value,
          SpotifyScope::PLAYLIST_READ_PRIVATE->value,
          SpotifyScope::USER_LIBRARY_MODIFY->value,
          SpotifyScope::USER_LIBRARY_READ->value,
        ],
      ]);
      // $this->cache->set('musicaSpotifyAuthState', $this->provider->getState(), Cache::CACHE_PERMANENT, $this->cacheLabels);

      header('Location: ' . $authUrl);
      exit;
    }

    // Request an access token using the authorization code grant.
    $code = $_GET['code'];
    $this->getAccessToken('authorization_code', ['code' => $code]);
  }

  /**
   * Request an access token using the authorization code grant.
   */
  private function getAccessToken(string $grant, array $options = []): void {
    try {
      $token = $this->provider->getAccessToken($grant, $options);

      $this->cache->set(
        'musica:spotify:access_token',
        $token->getToken(),
        $token->getExpires(),
        $this->cacheLabels
      );

      $this->cache->set(
        'musica:spotify:refresh_token',
        $token->getRefreshToken(),
        Cache::CACHE_PERMANENT,
        $this->cacheLabels
      );
    }
    catch (IdentityProviderException $e) {
      Error::logException($this->logger, $e, 'Spotify authentication failed: @error', [
        '@error' => $e->getMessage(),
      ]);
    }
  }

}
