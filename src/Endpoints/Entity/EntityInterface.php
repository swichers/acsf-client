<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\EndpointInterface;

/**
 * Entity contract.
 *
 * Primarily used for type hinting and restriction.
 */
interface EntityInterface extends EndpointInterface {

  /**
   * Get the entity ID.
   *
   * @return int
   *   This entity's ID.
   */
  public function id(): int;

  /**
   * Get the parent of this entity.
   *
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface|null
   *   The parent of this entity (if available).
   */
  public function getParent();

}
