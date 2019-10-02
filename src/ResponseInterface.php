<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

interface ResponseInterface {

  public function getOriginalResponse();

  public function toArray(bool $throw = TRUE);

}
