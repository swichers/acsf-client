<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Entity;

use swichers\Acsf\Client\Action\BaseAction;
use swichers\Acsf\Client\Annotation\Entity;
use swichers\Acsf\Client\Client;

/**
 * @Entity(name = "Backup")
 */
class Backup extends BaseAction implements EntityInterface {

  protected $site;

  protected $backup_id;

  public function __construct(Client $client, int $id, EntityInterface $parent = NULL) {
    parent::__construct($client);

    $this->site = $parent;
    $this->backup_id = $id;
  }

  public function getParent() {
    return $this->site;
  }

  public function getTemporaryUrl(array $options = []) : array {
    $options = [
        'lifetime' => 60,
      ] + $options;

    return $this->client->apiGet([
      'sites',
      $this->site->id(),
      'backups',
      $this->backup_id,
      'url',
    ], $options)->toArray();
  }

  public function delete(array $options = []) : array {
    $options = [
        'callback_url' => NULL,
        'callback_method' => NULL,
        'caller_data' => NULL,
      ] + $options;

    return $this->client->apiDelete([
      'sites',
      $this->site->id(),
      'backups',
      $this->backup_id,
    ], $options)->toArray();
  }

  public function restore(array $options) : array {
    $options = [
        'target_site_id' => $this->site->id(),
        'backup_id' => $this->id(),
        'callback_url' => NULL,
        'callback_method' => 'GET',
        'caller_data' => NULL,
      ] + $options;

    return $this->client->apiPost([
      'sites',
      $this->site->id(),
      'restore',
    ], $options)->toArray();
  }

  public function id() : int {
    return $this->backup_id;
  }

}
