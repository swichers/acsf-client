<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Discovery;

interface DiscovererInterface {

  /**
   * Scan for classes with annotations.
   *
   * @return array
   *   An array of discovered classes.
   */
  public function getItems() : array;

}
