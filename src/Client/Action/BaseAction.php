<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Action;

use swichers\Acsf\Client\Client;

class BaseAction implements ActionInterface {

  protected $client;

  public function __construct(Client $acsf_client) {
    $this->client = $acsf_client;
  }

}
