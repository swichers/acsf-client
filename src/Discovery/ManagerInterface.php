<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Discovery;

interface ManagerInterface {

  public function getAvailable();

  public function get($name);

  public function create($name, ...$constructor_args);

}
