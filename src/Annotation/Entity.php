<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Annotation definition for API endpoint Entities.
 *
 * @Annotation
 * @Target("CLASS")
 */
class Entity {

  /**
   * The name of the Entity type.
   *
   * @var string
   *
   * @Required
   */
  public $name;

  /**
   * Get the name of the Entity type.
   *
   * @return string
   *   The name of the Entity type.
   */
  public function getName() {

    return $this->name;
  }

}
