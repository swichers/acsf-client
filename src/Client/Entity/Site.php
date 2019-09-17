<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Entity;

use swichers\Acsf\Client\Action\BaseAction;
use swichers\Acsf\Client\Annotation\Entity;
use swichers\Acsf\Client\Client;

/**
 * @Entity(name = "Site")
 */
class Site extends BaseAction implements EntityInterface {

  /** @var int */
  protected $site_id;

  public function __construct(Client $acsf_client, int $site_id, EntityInterface $parent = NULL) {
    parent::__construct($acsf_client);

    $this->site_id = $site_id;
  }

  public function getParent() {
    return NULL;
  }

  public function getDetails() {
    return $this->client->apiGet(['sites', $this->id()])->toArray();
  }

  public function id() : int {
    return $this->site_id;
  }

  public function delete() {
    return $this->client->apiDelete(['sites', $this->id()], [])->toArray();
  }

  public function duplicate(string $siteName, array $options = []) {
    unset($options['site_name']);

    $options = [
        'site_name' => $siteName,
        'group_ids' => [],
        'exact_copy' => FALSE,
      ] + $options;

    return $this->client->apiPost([
      'sites',
      $this->id(),
      'duplicate',
    ], $options)->toArray();
  }

  public function backup(array $options = []) {
    $options = [
        'label' => NULL,
        'callback_url' => NULL,
        'callback_method' => NULL,
        'caller_data' => NULL,
        'components' => [
          'codebase',
          'database',
          'public files',
          'private files',
          'themes',
        ],
      ] + $options;

    return $this->client->apiPost([
      'sites',
      $this->id(),
      'backup',
    ], $options)->toArray();
  }

  public function listBackups(array $options = []) {
    $options = $this->validatePaging($options);
    return $this->client->apiGet([
      'sites',
      $this->id(),
      'backups',
    ], $options)->toArray();
  }

  public function clearVarnishCache() {
    return $this->client->apiGet(['sites', $this->id(), 'cache-clear'])
      ->toArray();
  }

}
