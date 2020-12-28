<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\ClientInterface;

/**
 * Base Entity implementation.
 */
abstract class AbstractEntity implements EntityInterface {

  /**
   * An ACSF client.
   *
   * @var \swichers\Acsf\Client\ClientInterface
   */
  protected $client;

  /**
   * The parent of this entity (if any).
   *
   * @var \swichers\Acsf\Client\Endpoints\Entity\EntityInterface|null
   */
  protected $parent;

  /**
   * The ID of this entity.
   *
   * @var int
   */
  protected $entityId;

  /**
   * EntityBase constructor.
   *
   * @param \swichers\Acsf\Client\ClientInterface $client
   *   An ACSF client.
   * @param int $id
   *   The ID of this entity.
   * @param \swichers\Acsf\Client\Endpoints\Entity\EntityInterface|null $parent
   *   The parent of this entity (if any).
   */
  public function __construct(ClientInterface $client, int $id, EntityInterface $parent = NULL) {

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
  public function id(): int {

    return $this->entityId;
  }

}
