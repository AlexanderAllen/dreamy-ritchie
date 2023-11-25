<?php

// phpcs:disable

class LFMContainer {
  private $_value;

  private function __construct($value) {
     $this->_value = $value;
  }

  // Unit function
  public static function entity($val) {
     return new static($val);
  }

  // Map function
  public function map(callable $f) {
    $result = call_user_func($f, $this->_value);
    return static::entity($result);
  }

  // Print out the container
  public function __toString(): string {
     return "Container[ {$this->_value} ]";
  }

  // Deference container
  public function __invoke() {
     return $this->_value;
  }

  public function __call($callable, $arguments = []) {
    $this->map($callable);
  }
}

$c = LFMContainer::entity('Cher')->map('htmlspecialchars')->map('strtolower');

// Soemthing like this would be amazing.
// 1 $c = Container::of('</ Hello FP >')->map('htmlspecialchars')->map('strtolower');

// $mbidString = Musica::Spotify->currentTrack()->artistMBID();
// $infoObject = Musica::LastFM->artist($mbidString)->getinfo();
// $infoObject2 = Musica::MusicBrainz->artist($mbidString)->getinfo();

// first try
// $infoObject = LastFM::artist($mbidString)->getinfo()
// LFMContainer::entity('Cher')->map('getInfo');

// In addition, i'd be nice if track, artist, albums were not scalar values but
// rather generic interfaced objects that can be passed around and consumed
// by multiple services.



// works
// $c = LFMContainer::entity('Cher')->map('htmlspecialchars')->map('strtolower');
