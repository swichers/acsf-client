<?php declare(strict_types = 1);


namespace swichers\Acsf\Discovery;


interface ManagerInterface {

  public function __construct(DiscovererInterface $discovery);

  public function getAvailable();

  public function get($name);

  public function create($name, ...$constructor_args);

}
