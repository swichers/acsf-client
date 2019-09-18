<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Client;

abstract class ActionBase implements ActionInterface {

  protected $client;

  /**
   * {@inheritdoc}
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

}
