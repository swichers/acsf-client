<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * Backup endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Backup")
 */
class Backup extends AbstractEntity {

  use ValidationTrait;

  /**
   * Get temporary site backup URL.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A temporary site backup URL.
   *
   * @version v1
   * @title Get a temporary site backup URL
   * @http_method GET
   * @resource /api/v1/sites/{site_id}/backups/{backup_id}/url
   *
   * @params
   *   lifetime | int | no | The number of seconds the temporary URL is good
   *   for. | 60
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "url": "https:\/\/example.com",
   *     "lifetime": 300
   *   }
   * ```
   */
  public function getUrl(array $options = []): array {

    $options = $this->limitOptions($options, ['lifetime']);
    if (isset($options['lifetime'])) {
      $options['lifetime'] = max(1, $options['lifetime']);
    }

    return $this->client->apiGet(
      [
        'sites',
        $this->getParent()->id(),
        'backups',
        $this->id(),
        'url',
      ],
      $options
    )->toArray();
  }

  /**
   * Delete a site backup.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The delete request response.
   *
   * @throws \swichers\Acsf\Client\Exceptions\InvalidOptionException
   *
   * @version v1
   * @title Delete a site backup
   * @http_method DELETE
   * @resource /api/v1/sites/{site_id}/backups/{backup_id}
   *
   * @params
   * callback_url    | string | no | The callback URL, which is invoked upon
   *                                 completion.
   * callback_method | string | no | The callback method, "GET", or "POST".
   *                                 Uses "POST" if empty.
   * caller_data     | string | no | Data that should be included in the
   *                                 callback, json encoded.
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "task_id": 16
   *   }
   * ```
   */
  public function delete(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'callback_url',
        'callback_method',
        'caller_data',
      ]
    );

    $options = $this->validateBackupOptions($options);

    return $this->client->apiDelete(
      [
        'sites',
        $this->getParent()->id(),
        'backups',
        $this->id(),
      ],
      $options
    )->toArray();
  }

  /**
   * Restore a site backup.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Restore task information.
   *
   * @version v1
   * @title Restore a site backup
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/restore
   *
   * @params
   * target_site_id  | int    | no | ID of the site to restore the backup onto.
   * backup_id       | int    | no | ID of the backup to restore defaults to the
   *                                 most recent.
   * callback_url    | string | no | The callback URL, which is invoked upon
   *                                 completion.
   * callback_method | string | no | The callback method, "GET", or "POST".
   *                                 Uses "POST" if empty.
   * caller_data     | string | no | Data that should be included in the
   *                                 callback, json encoded.
   * components      | array  | no | Array of components to be restored from the
   *                                 backup. The following component names are
   *                                 accepted: database, public files,
   *                                 private files, themes. When omitting this
   *                                 parameter it will default to the backup's
   *                                 every component.
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "task_id": 1024
   *   }
   * ```
   */
  public function restore(array $options = []): array {

    $options = $this->limitOptions($options, $this->backupFields);

    $options['target_site_id'] = $this->getParent()->id();
    $options['backup_id'] = $this->id();

    $options = $this->validateBackupOptions($options);

    return $this->client->apiPost(
      [
        'sites',
        $this->getParent()->id(),
        'restore',
      ],
      $options
    )->toArray();
  }

}
