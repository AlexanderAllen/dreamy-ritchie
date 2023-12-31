<?php

declare(strict_types=1);

namespace Drupal\musica_views\EventSubscriber;

use Drupal\musica\Service\Spotify;
use Drupal\views\ResultRow;
use Drupal\views_remote_data\Events\RemoteDataQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MyRemoteDataSubscriber implements EventSubscriberInterface {

  private Spotify $spotify;

  /**
   * Event Subscriber constructor.
   */
  public function __construct(
    Spotify $spotify,
    ) {
    $this->spotify = $spotify;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      RemoteDataQueryEvent::class => 'onQuery',
    ];
  }

  /**
   * Subscribes to populate the view results.
   *
   * @param \Drupal\views_remote_data\Events\RemoteDataQueryEvent $event
   *   The event.
   */
  public function onQuery(RemoteDataQueryEvent $event): void {

    $supported_bases = ['musica_views_remote_data'];
    $base_tables = array_keys($event->getView()->getBaseTables());
    if (count(array_intersect($supported_bases, $base_tables)) > 0) {

      $data = $this->spotify->getResourceObject();
      $event->addResult(new ResultRow(['data' => $data]));
    }
  }

}
