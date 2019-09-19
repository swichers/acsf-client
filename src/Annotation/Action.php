<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Annotation;

use Doctrine\Common\Annotations\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
class Action {

  /**
   * @Required
   *
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $entity_type;

  /**
   * Get the name of the Action.
   *
   * @return string
   */
  public function getName() {

    return $this->name;
  }

  /**
   * Get the name of the Entity this Action can create (if available).
   *
   * @return string|NULL
   */
  public function getEntityType() {

    return $this->entity_type ?? NULL;
  }

}
