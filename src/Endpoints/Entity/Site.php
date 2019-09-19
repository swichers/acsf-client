<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;
use swichers\Acsf\Client\Endpoints\PagingTrait;

/**
 * @Entity(name = "Site")
 */
class Site extends EntityBase {

  use PagingTrait;

  /**
   * Get detailed information about a site.
   *
   * @version v1
   * @title Site details
   * @http_method GET
   * @resource /api/v1/sites/{site_id}
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function details() : array {
    return $this->client->apiGet(['sites', $this->id()])->toArray();
  }

  /**
   * Delete a site.
   *
   * @version v1
   * @title Site delete
   * @http_method DELETE
   * @resource /api/v1/sites/{site_id}
   *
   * @params
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123' \
   *     -X DELETE -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id": 123,
   *     "owner": "johnsmith",
   *     "site": "unicorns",
   *     "time": "1970-01-01T01:02:03+00:00",
   *     "task_id": 16
   *   }
   */
  public function delete() : array {
    return $this->client->apiDelete(['sites', $this->id()], [])->toArray();
  }

  /**
   * Duplicate a site.
   *
   * @version v1
   * @title Duplicate a site
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/duplicate
   *
   * @params
   *   site_name  | string    | yes | The new site name.
   *   group_ids  | int|array | no  | Either a single group ID, or an array of
   *   group IDs. exact_copy | bool      | no  | A boolean indicating whether
   *   or not to create an exact copy. | false
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/duplicate' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"site_name": "mysite2", "exact_copy": true}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id": 183,
   *     "site": "mysite2"
   *   }
   */
  public function duplicate(string $siteName, array $options = []) : array {
    unset($options['site_name']);

    $options = [
      'site_name' => $options['site_name'] ?? $siteName,
      'group_ids' => $options['group_ids'] ?? [],
      'exact_copy' => $options['exact_copy'] ?? FALSE,
    ];

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
   * @version v1
   * @title Create a site backup
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/backup
   * @group Sites
   *
   * @params
   *   label           | string | no | The human-readable description of this
   *   backup. callback_url    | string | no | The callback URL, which is
   *   invoked upon completion. callback_method | string | no | The callback
   *   method, "GET", or "POST". Uses "POST" if empty. caller_data     | string
   *   | no | Data that should be included in the callback, json encoded.
   *   components      | array  | no | Array of components to be included in
   *   the backup. The following component names are accepted: codebase,
   *   database, public files, private files, themes. When omitting this
   *   parameter it will default to a backup with every component.
   *
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/backup' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"label": "Weekly", "callback_url": "http://mysite.com",
   *   "callback_method": "GET"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "task_id": 183
   *   }
   */
  public function backup(array $options = []) : array {
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
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/backups?limit=20&page=2' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function listBackups(array $options = []) : array {
    $options = $this->validatePaging($options);
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
   * @version v1
   * @title Clear a site's caches
   * @http_method POST
   * @resource /api/v1/sites/{site_id}/cache-clear
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites/123/cache-clear' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id" : 123,
   *     "time" : "2017-05-04T09:25:26+00:00",
   *     "task_ids": {
   *       'drupal_cache_clear' : 1234,
   *       'varnish_cache_clear' : 1234
   *     }
   *   }
   */
  public function clearVarnishCache() : array {
    return $this->client->apiGet(['sites', $this->id(), 'cache-clear'])
      ->toArray();
  }

  /**
   * Get domains by node ID.
   *
   * @version v1
   * @title Get domains
   * @group Domains
   * @http_method GET
   * @resource /api/v1/domains/{node_id}
   * @example_command
   *   curl '{base_url}/api/v1/domains/{node_id}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function getDomains() : array {
    return $this->client->apiGet(['domains', $this->id()])->toArray();
  }

  /**
   * Adds a domain.
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
   * @example_command
   *   curl '{base_url}/api/v1/domains/{node_id}/add' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"domain_name": "www.domaintoadd.com" }' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function addDomain(string $domainName) : array {
    return $this->client->apiPost(
      ['domains', $this->id(), 'add'],
      ['domain_name' => $domainName]
    )->toArray();
  }

  /**
   * Removes a domain.
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
   * @example_command
   *   curl '{base_url}/api/v1/domains/{node_id}/remove' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"domain_name": "www.domaintoremove.com" }' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "node_id": 121,
   *     "node_type": "site collection",
   *     "domain": "www.domaintoremove.com",
   *     "time": "2016-10-28T09:25:26+00:00",
   *     "removed": true,
   *     "message": "Your domain name has been successfully removed from
   *   &lt;site collection name&gt;."
   *   }
   */
  public function removeDomain(string $domainName) : array {
    return $this->client->apiPost(
      ['domains', $this->id(), 'remove'],
      ['domain_name' => $domainName]
    )->toArray();
  }

}
