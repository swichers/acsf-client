<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Action;

use swichers\Acsf\Client\Client;

interface ActionInterface {

  public function __construct(Client $acsf_client);

}
