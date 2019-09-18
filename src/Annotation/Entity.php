<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Entity {

  /**
   * @Required
   *
   * @var string
   */
  public $name;

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

}
