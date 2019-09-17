<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Entity;

use swichers\Acsf\Client\Client;

interface EntityInterface {

  public function __construct(Client $client, int $id, EntityInterface $parent = NULL);

  public function id() : int;

  public function getParent();
}
