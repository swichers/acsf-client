<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

interface EntityInterface {

  /**
   * @return int
   */
  public function id() : int;

  /**
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface|null
   */
  public function getParent();

}
