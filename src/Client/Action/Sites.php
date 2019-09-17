<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Client\Action\PagingTrait;
use swichers\Acsf\Client\Entity\Site;

/**
 * @Action(name = "Sites")
 */
class Sites extends BaseAction {

  use PagingTrait;

  public function listSites(array $options = []) : array {
    $options = [
        'limit' => 10,
        'page' => 1,
        'canary' => FALSE,
        'show_incomplete' => FALSE,
      ] + $options;

    $options = $this->validatePaging($options);

    return $this->client->apiGet('sites', $options)->toArray();
  }

  public function getSite(int $siteId) {
    return $this->client->getEntity('Site', $siteId);
    return new Site($this->client, $siteId);
  }

  public function createSite(string $siteName, array $options = []) : array {
    unset($options['site_name']);

    $options = [
        'site_name' => $siteName,
        'group_ids' => [],
        'install_profile' => NULL,
        'stack_id' => 1,
      ] + $options;

    return $this->client->apiPost('sites', $options)->toArray();
  }

}
