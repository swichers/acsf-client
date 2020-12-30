<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * Site endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Site")
 */
class Site extends AbstractEntity {

  use ValidationTrait;

  /**
   * Get detailed information about a site.
   *
   * @return array
   *   Detailed information about this site.
   *
   * @version v1
   * @title Site details
   * @http_method GET
   * @resource /api/v1/sites/{site_id}
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id": 123,
   *     "created": 1397483647,
   *     "owner": "John Drupal",
   *     "site": "site1",
   *     "stack_id": 1,
   *     "domains": [
   *       "domain1.site-factory.com",
   *       "domain2.site-factory.com"
   *     ],
   *     "groups": [
   *       91
   *     ],
   *     "part_of_collection": true,
   *     "is_primary": true,
   *     "collection_id": 241,
   *     "collection_domains": [
   *       "domain241.example.com",
   *       "anotherdomain.com"
   *     ]
   *   }
   * ```
   */
  public function details(): array {

    return $this->client->apiGet(['sites', $this->id()])->toArray();
  }

  /**
   * Delete a site.
   *
   * @return array
   *   Delete task information.
   *
   * @version v1
   * @title Site delete
   * @http_method DELETE
   * @resource /api/v1/sites/{site_id}
   *
   * @params
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id": 123,
   *     "owner": "johnsmith",
   *     "site": "unicorns",
   *     "time": "1970-01-01T01:02:03+00:00",
   *     "task_id": 16
   *   }
   * ```
   */
  public function delete(): array {

    return $this->client->apiDelete(['sites', $this->id()], [])->toArray();
  }

  /**
   * Duplicate a site.
   *
   * @param string $siteName
   *   The new site name.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The new site information.
   *
   * @version v1
   * @title Duplicate a site
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/duplicate
   *
   * @params
   * site_name  | string    | yes | The new site name.
   * group_ids  | int|array | no  | Either a single group ID, or an
   *                                array of group IDs.
   * exact_copy | bool      | no  | A boolean indicating whether or not to
   *                                create an exact copy. | false
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id": 183,
   *     "site": "mysite2"
   *   }
   * ```
   */
  public function duplicate(string $siteName, array $options = []): array {

    $options = $this->limitOptions($options, ['group_ids', 'exact_copy']);
    $options['site_name'] = $siteName;
    $options['exact_copy'] = $this->ensureBool($options['exact_copy'] ?? FALSE);
    if (isset($options['group_ids'])) {
      $options['group_ids'] = $this->cleanIntArray($options['group_ids']);
    }

    return $this->client->apiPost(
      [
        'sites',
        $this->id(),
        'duplicate',
      ],
      $options
    )->toArray();
  }

  /**
   * Create a site backup.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The backup task information.
   *
   * @version v1
   * @title Create a site backup
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/backup
   * @group Sites
   *
   * @params
   * label        | string | no | The human-readable description of this backup.
   * callback_url | string | no | The callback URL, which is invoked upon
   *                                 completion.
   * callback_method | string | no | The callback method, "GET", or "POST".
   *                                 Uses "POST" if empty.
   * caller_data | string | no | Data that should be included in the
   *                                 callback, json encoded.
   * components  | array  | no | Array of components to be restored from the
   *                                 backup. The following component names are
   *                                 accepted: database, public files,
   *                                 private files, themes. When omitting this
   *                                 parameter it will default to the backup's
   *                                 every component.
   *
   * @example_response
   * ```json
   *   {
   *     "task_id": 183
   *   }
   * ```
   */
  public function backup(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      array_merge(['label'], $this->backupFields)
    );

    $options = $this->validateBackupOptions($options);

    return $this->client->apiPost(
      [
        'sites',
        $this->id(),
        'backup',
      ],
      $options
    )->toArray();
  }

  /**
   * List site backups.
   *
   * Note that the results are sorted from newest backup to oldest.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of backups sorted from newest to oldest.
   *
   * @version v1
   * @title List site backups
   * @http_method GET
   * @resource /api/v1/sites/{site_id}/backups
   *
   * @params
   *   limit     | int    | no | A positive integer (max 100). | 10
   *   page      | int    | no | A positive integer.           | 1
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "backups": [
   *       {
   *         "id": 6,
   *         "nid": 123,
   *         "status": 1,
   *         "uid": 16,
   *         "timestamp": 1415044083,
   *         "bucket": "sitefactorybackups",
   *         "directory": "oldschool",
   *         "file": "oldschool_91_1415044083.tar.gz",
   *         "label": "Weekly",
   *         "componentList": [
   *           "codebase",
   *           "database",
   *           "public files",
   *           "private files",
   *           "themes"
   *         ]
   *       },
   *       {
   *         "id": 1,
   *         "nid": 123,
   *         "status": 1,
   *         "uid": 16,
   *         "timestamp": 1415042116,
   *         "bucket": "sitefactorybackups",
   *         "directory": "oldschool",
   *         "file": "oldschool_91_1415042116.tar.gz",
   *         "label": "Monthly",
   *         "componentList": [
   *           "codebase",
   *           "database",
   *           "public files",
   *           "private files",
   *           "themes"
   *         ]
   *       }
   *     ]
   *   }
   * ```
   */
  public function listBackups(array $options = []): array {

    $options = $this->limitOptions($options, ['limit', 'page']);
    $options = $this->constrictPaging($options);

    return $this->client->apiGet(
      [
        'sites',
        $this->id(),
        'backups',
      ],
      $options
    )->toArray();
  }

  /**
   * Clear Drupal and Varnish caches for a site.
   *
   * @return array
   *   Task information.
   *
   * @version v1
   * @title Clear a site's caches
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/cache-clear
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id" : 123,
   *     "time" : "2017-05-04T09:25:26+00:00",
   *     "task_ids": {
   *       'drupal_cache_clear' : 1234,
   *       'varnish_cache_clear' : 1234
   *     }
   *   }
   * ```
   */
  public function clearCache(): array {

    return $this->client->apiPost(['sites', $this->id(), 'cache-clear'], [])
      ->toArray();
  }

  /**
   * Get domains by node ID.
   *
   * @return array
   *   A list of domains.
   *
   * @version v1
   * @title Get domains
   * @group Domains
   * @http_method GET
   * @resource /api/v1/domains/{node_id}
   *
   * @example_response
   * ```json
   *   {
   *     "node_id": 121,
   *     "node_type": "site collection",
   *     "time": "2016-10-28T09:25:26+00:00",
   *     "domains": {
   *       "protected_domains": [
   *         "site.example.sfdev.acquia-test.co"
   *       ],
   *       "custom_domains": [
   *         "www.abc.com/def",
   *         "www.xyz.com"
   *       ]
   *     }
   *   }
   * ```
   */
  public function getDomains(): array {

    return $this->client->apiGet(['domains', $this->id()])->toArray();
  }

  /**
   * Adds a domain.
   *
   * @param string $domainName
   *   The domain name to add.
   *
   * @return array
   *   The task information.
   *
   * @version v1
   * @title Add domain
   * @group Domains
   * @http_method POST
   * @resource /api/v1/domains/{node_id}/add
   *
   * @params
   *   domain_name | string | yes | The domain name to add.
   *
   * @example_response
   * ```json
   *   {
   *     "node_id": 121,
   *     "node_type": "site collection",
   *     "domain": "www.domaintoadd.com",
   *     "time": "2016-10-28T09:25:26+00:00",
   *     "added": true,
   *     "messages": [
   *       "Your domain name was successfully added to the site collection."
   *     ]
   *   }
   * ```
   */
  public function addDomain(string $domainName): array {

    return $this->client->apiPost(
      [
        'domains',
        $this->id(),
        'add',
      ],
      ['domain_name' => $domainName]
    )->toArray();
  }

  /**
   * Removes a domain.
   *
   * @param string $domainName
   *   The domain name to remove.
   *
   * @return array
   *   The task information.
   *
   * @version v1
   * @title Remove domain
   * @group Domains
   * @http_method POST
   * @resource /api/v1/domains/{node_id}/remove
   *
   * @params
   *   domain_name | string | yes | The domain name to remove.
   *
   * @example_response
   * ```json
   *   {
   *     "node_id": 121,
   *     "node_type": "site collection",
   *     "domain": "www.domaintoremove.com",
   *     "time": "2016-10-28T09:25:26+00:00",
   *     "removed": true,
   *     "message": "Your domain name has been successfully removed from
   *   &lt;site collection name&gt;."
   *   }
   * ```
   */
  public function removeDomain(string $domainName): array {

    return $this->client->apiPost(
      [
        'domains',
        $this->id(),
        'remove',
      ],
      ['domain_name' => $domainName]
    )->toArray();
  }

}
