<?php

// phpcs:disable Drupal.Commenting.DocComment.ContentAfterOpen
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.After
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.AfterLast
// phpcs:disable Squiz.WhiteSpace.FunctionSpacing.BeforeFirst, Drupal.Classes.ClassDeclaration.CloseBraceAfterBody
// phpcs:disable Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing

namespace Drupal\Tests\musica\Unit\LFM\Artist;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use CuyZ\Valinor\Mapper\Tree\Message\NodeMessage;
use PHPUnit\Framework\TestCase;
use Kint\Kint;


/**
 * Basic Valinor DTO hydration test.
 *
 * @group musica
 *
 * @see https://www.last.fm/api/show/artist.getTopAlbums
 * @see https://github.com/CuyZ/Valinor#example
 * @see https://valinor.cuyz.io/1.7/how-to/use-custom-object-constructors
 * @see https://valinor.cuyz.io/1.7/usage/type-strictness-and-flexibility/
 */
class HydrateTopAlbumsTest extends TestCase {

  public function testSimilarArtistsWithoutAttr() {
    $file = 'topalbums-full.json';
    $path = '/app/docroot/web/modules/custom/musica/tests/src/Unit/LFM/Artist/';
    $sauce = Source::file(new \SplFileObject($path . $file));

    try {
      // baseline6 reference
      // 'array{similarartists: array{artist: ' . EntityList::class . ', "@attr": ' . Attribute::class . '} }'

      // $signature = 'array{topalbums: array{album: ' . EntityListAlbum::class . ', "@attr": ' . Attribute::class . '} }'
      $signature = 'array{topalbums: array{album: ' . EntityListAlbum::class . '} }';
      $dto = (new MapperBuilder())
        ->allowSuperfluousKeys()
        ->allowPermissiveTypes()
        ->enableFlexibleCasting()
        ->mapper()
        ->map($signature, $sauce);

      $this->assertSame(TRUE, TRUE);
    }
    catch (\CuyZ\Valinor\Mapper\MappingError $error) {

      Kint::$enabled_mode = Kint::MODE_CLI;
      Kint::$expanded = FALSE;
      Kint::$depth_limit = 1;
      // $kint = Kint::createFromStatics(Kint::getStatics());

      $messages = Messages::flattenFromNode(
        $error->node()
      );

      // Formatters can be added and will be applied on all messages
      // $messages = $messages->formatWith(
      //   new \CuyZ\Valinor\Mapper\Tree\Message\Formatter\MessageMapFormatter([
      //     'some_code' => 'New content / code: {message_code}',
      //     '1655449641' => function (NodeMessage $message) {
      //       $o = $message->originalMessage();
      //       return $o->body();
      //     }
      //   ]),
      // );

      // If only errors are wanted, they can be filtered
      $errorMessages = $messages->errors();
      foreach ($errorMessages as $message) {
        // d($message);
        // Kint::dump($message);
        $b = $message->body();

        /** @var \CuyZ\Valinor\Mapper\Tree\Message\Message */
        $om = $message->originalMessage();
        // @phpstan-ignore-next-line
        $oms = $om->getMessage();

        $node = $message->node();
        $path = $node->path();
        $type = $node->type();
        $valid = $node->isValid();
        // $mapped = $node->mappedValue();
        // $node->

        printf("Original Message: %s, PATH: %s, TYPE: %s, VALID: %b \n", $oms, $path, $type, $valid);

        // Kint::dump($om);
        flush();
        ob_flush();
      }

    }

  }

}

final class EntityListAlbum {

  public function __construct(
    /** @var list<Album> */
    public readonly array $album,
  ) {}

}

/**
 * @phpstan-type ImageProps array{"#text": string, size: string}
 * @phpstan-type ArtistArray array{"name": string, mbid: string, url: string}
 */
final class Album {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-negative-int */
    public readonly int $playcount,
    /** @var string */
    public readonly string $url,
    /** @var Artist */
    public readonly Artist $artist,
    /** @var list<ImageProps> */
    public readonly array $image = [],
    /** @var string */
    public readonly string $mbid = '',
  ) {}

}


class Attribute {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $artist,
    public readonly string $page = '',
    public readonly string $perPage = '',
    public readonly string $totalPages = '',
    public readonly string $total = '',
  ) {}

}

/**
 * @phpstan-type ImageProps array{"#text": string, size: string}
 *
 * @see https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases
 */
final class Artist {

  public function __construct(
    /** @var non-empty-string */
    public readonly string $name,
    /** @var non-empty-string */
    public readonly string $mbid,
    /** @var non-empty-string */
    public readonly string $url,
  ) {}

}
