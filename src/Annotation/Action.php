<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Annotation;

/**
 * Annotation definition for API endpoint Actions.
 *
 * @Annotation(\Doctrine\Common\Annotations\Annotation)
 * @Target("CLASS")
 */
class Action {

  /**
   * The name of the Action type.
   *
   * @var string
   *
   * @Required
   */
  public $name;

  /**
   * The entity type of the Action.
   *
   * @var string
   */
  public $entityType;

  /**
   * Get the name of the Action type.
   *
   * @return string
   *   The name of the Action type.
   */
  public function getName() {

    return $this->name;
  }

  /**
   * Get the name of the Entity this Action can create (if available).
   *
   * @return string|null
   *   The name of the entity type or NULL if this Action does not create
   *   entities.
   */
  public function getEntityType() {

    return $this->entityType ?? NULL;
  }

}
