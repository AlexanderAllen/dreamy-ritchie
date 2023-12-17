<?php

namespace Drupal\musica\Service;

use Drupal\Core\Cache\DatabaseBackend as Cache;
use Drupal\Core\Cache\Context\UserCacheContext;
use Kerox\OAuth2\Client\Provider\Spotify as SpotifyClient;
use Kerox\OAuth2\Client\Provider\SpotifyScope;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Utility\Error;

/**
 * Service for Spotify API.
 *
 * Provides OAuth authentication.
 */
final class Spotify {


  private string $apiKey;
  private string $apiSecret;
  private Cache $cache;
  private UserCacheContext $cacheContext;
  private LoggerChannel $logger;

  /**
   * Class constructor.
   */
  public function __construct(Cache $cache, UserCacheContext $context, LoggerChannel $logger) {
    $this->cache = $cache;
    $this->cacheContext = $context;
    $this->logger = $logger;

    // @todo move secrets to config form / drupal db?
    $this->apiKey = getenv('LASTFM_API_KEY') ?? '';
    $this->apiSecret = getenv('LASTFM_API_SECRET') ?? '';
  }

  public function authorize() {
    if ($this->cache->get('musicaSpotifyAuthToken') !== FALSE) {
      return;
    }

    $provider = new SpotifyClient([

      'redirectUri'  => 'https://d10ee.lndo.site/hello',
    ]);

    if (!isset($_GET['code'])) {
      // If we don't have an authorization code then get one.
      $authUrl = $provider->getAuthorizationUrl([
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
      $provider_state = $provider->getState();
      $this->cache->set('musicaSpotifyAuthState', $provider_state);

      header('Location: ' . $authUrl);
      exit;
    }

    // Request an access token using the authorization code grant.
    try {
      $code = $_GET['code'];
      $token = $provider->getAccessToken('authorization_code', ['code' => $code]);

      $this->cache->set(
        'musicaSpotifyAuthToken',
        [
          'access' => $token->getToken(),
          'refresh' => $token->getRefreshToken(),
          'scope' => $token->getValues()['scope'],
        ],
        $token->getExpires(),
        [$this->cacheContext->getLabel() . ':' . $this->cacheContext->getContext()]
      );
    }
    catch (IdentityProviderException $e) {
      Error::logException($this->logger, $e, 'Spotify authentication failed: @error', [
        '@error' => $e->getMessage(),
      ]);
    }

  }

}
