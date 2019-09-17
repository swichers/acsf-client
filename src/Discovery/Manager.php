<?php declare(strict_types = 1);

namespace swichers\Acsf\Discovery;

use Exception;

class Manager implements ManagerInterface {

  /**
   * @var \swichers\Acsf\Discovery\Discoverer
   */
  protected $discovery;


  public function __construct(DiscovererInterface $discovery) {
    $this->discovery = $discovery;
  }

  public function create($name, ...$constructor_args) {
    $workers = $this->discovery->getItems();
    if (array_key_exists($name, $workers)) {
      $class = $workers[$name]['class'];
      if (!class_exists($class)) {
        throw new Exception('Implementation class does not exist.');
      }
      return new $class(...$constructor_args);
    }

    throw new Exception('Implementation does not exist.');
  }

  public function get($name) {
    $workers = $this->discovery->getItems();
    if (isset($workers[$name])) {
      return $workers[$name];
    }

    throw new Exception('Implementation not found.');
  }

  public function getAvailable() {
    return $this->discovery->getItems();
  }

}
