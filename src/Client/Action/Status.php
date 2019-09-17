<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "Status")
 */
class Status extends BaseAction {

  public function ping() {
    return $this->apiGet('ping')->toArray();
  }

  public function getStatus() {
    return $this->client->apiGet('status');
  }

  public function setStatus(array $data) {
    return $this->client->apiPut('status', $data);
  }

  public function siteFactoryInfo() {
    return $this->client->apiGet('sf-info')->toArray();
  }

}
