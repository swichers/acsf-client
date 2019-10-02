<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

/**
 * An Entity creator aware version of ActionBase.
 */
abstract class AbstractEntityAction extends AbstractAction {

  /**
   * Gets an object helper for the entity ID.
   *
   * @param int $entityId
   *   The ID of the entity to wrap.
   *
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface
   *   An instance of the object.
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
  abstract public function getEntityType(): string;

}
