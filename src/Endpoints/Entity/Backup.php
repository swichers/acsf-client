<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Backup")
 */
class Backup extends EntityBase {

  /**
   * Get temporary site backup URL.
   *
   * @version v1
   * @title Get a temporary site backup URL
   * @http_method GET
   * @resource /api/v1/sites/{site_id}/backups/{backup_id}/url
   *
   * @params
   *   lifetime | int | no | The number of seconds the temporary URL is good for. | 60
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/backups/101/url?lifetime=300' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "url": "https:\/\/s3.amazonaws.com\/sitefactorybackups\/site\/backup1_101_1415643727.tar.gz?AWSAccessKeyId=AKIAINAAC2EGOVCRW4WA\u0026Expires=1415713064\u0026Signature=pWucd8b6T%2FqzoNewXH6EuTyIr1g%3D",
   *     "lifetime": 300
   *   }
   */
  public function getUrl(array $options = []) : array {
    $options = ['lifetime' => $options['lifetime'] ?? 60];

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
   * @version v1
   * @title Delete a site backup
   * @http_method DELETE
   * @resource /api/v1/sites/{site_id}/backups/{backup_id}
   *
   * @params
   *   callback_url    | string | no | The callback URL, which is invoked upon completion.
   *   callback_method | string | no | The callback method, "GET", or "POST". Uses "POST" if empty.
   *   caller_data     | string | no | Data that should be included in the callback, json encoded.
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/backups/101' \
   *     -X DELETE -H 'Content-Type: application/json' \
   *     -d '{"callback_url": "http://mysite.com", "callback_method": "GET"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "task_id": 16
   *   }
   */
  public function delete(array $options = []) : array {
    $options = [
      'callback_url' => $options['callback_url'] ?? NULL,
      'callback_method' => $options['callback_method'] ?? NULL,
      'caller_data' => $options['caller_data'] ?? NULL,
    ];

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
   * @version v1
   * @title Restore a site backup
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/restore
   *
   * @params
   *   target_site_id  | int    | no | ID of the site to restore the backup onto.
   *   backup_id       | int    | no | ID of the backup to restore defaults to the most recent.
   *   callback_url    | string | no | The callback URL, which is invoked upon completion.
   *   callback_method | string | no | The callback method, "GET", or "POST". Uses "POST" if empty.
   *   caller_data     | string | no | Data that should be included in the callback, json encoded.
   *   components      | array  | no | Array of components to be restored from the backup. The following component names are accepted: database, public files, private files, themes. When omitting this parameter it will default to the backup's every component.
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/restore' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"target_site_id": 456, "backup_id": 789, "callback_url": "http://mysite.com", "callback_method": "GET"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "task_id": 1024
   *   }
   */
  public function restore(array $options = []) : array {
    $options = [
      'target_site_id' => $this->getParent()->id(),
      'backup_id' => $this->id(),
      'callback_url' => $options['callback_url'] ?? NULL,
      'callback_method' => $options['callback_method'] ?? 'GET',
      'caller_data' => $options['caller_data'] ?? NULL,
    ];

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
