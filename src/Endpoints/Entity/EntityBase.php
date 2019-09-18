<?php


namespace swichers\Acsf\Client\Endpoints\Entity;


use swichers\Acsf\Client\Client;

abstract class EntityBase implements EntityInterface {

  /**
   * @var \swichers\Acsf\Client\Client
   */
  protected $client;

  /**
   * @var \swichers\Acsf\Client\Endpoints\Entity\EntityInterface
   */
  protected $parent;

  /**
   * @var int
   */
  protected $entityId;

  public function __construct(Client $client, int $id, EntityInterface $parent = NULL) {
    $this->client = $client;
    $this->parent = $parent;
    $this->entityId = $id;
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    return $this->parent ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function id() : int {
    return $this->entityId;
  }

}
