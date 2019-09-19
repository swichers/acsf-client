<?php declare(strict_types=1);


namespace swichers\Acsf\Client\Endpoints\Action;


use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

/**
 * An Entity creator aware version of ActionBase.
 *
 * @package swichers\Acsf\Client\Endpoints\Action
 */
abstract class ActionGetEntityBase extends ActionBase {

  /**
   * Gets an object helper for the entity ID.
   *
   * @param int $entityId
   *   The ID of the entity to wrap.
   *
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface
   *   An instance of the object.
   *
   * @throws \swichers\Acsf\Client\Exceptions\MissingEntityException
   */
  public function get(int $entityId): EntityInterface {
    return $this->client->getEntity($this->getEntityType(), $entityId);
  }

  /**
   * Get the Entity type to use when loading entities from the client.
   *
   * @return string
   *   The name of the Entity type.
   */
  abstract function getEntityType(): string;
}
