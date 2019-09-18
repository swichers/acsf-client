<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\PagingTrait;

/**
 * @Action(name = "Sites")
 */
class Sites extends ActionBase {

  use PagingTrait;

  /**
   * Gets a list of sites.
   *
   * @throws Exception
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites?limit=20&page=2' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "count": 111,
   *     "sites": {
   *       {
   *         "id": 191,
   *         "site": "site1",
   *         "stack_id": 1,
   *         "domain": "domain1.site-factory.com"
   *         "groups": [
   *           91
   *         ],
   *         "site_collection": 176,
   *         "is_primary": true
   *       },
   *       {
   *         "id": 196,
   *         "site": "site2",
   *         "stack_id": 2,
   *         "domain": "domain2.site-factory.com",
   *         "groups": [
   *           91,
   *           105
   *         ],
   *         "site_collection": false,
   *         "is_primary": true
   *       }
   *     }
   *   }
   * @version v1
   * @title List sites
   * @http_method GET
   * @resource /api/v1/sites
   *
   * @params
   *   limit           | int  | no | A positive integer (max 100). | 10
   *   page            | int  | no | A positive integer.           | 1
   *   canary          | bool | no | No value necessary.           | false
   *   show_incomplete | bool | no | No value necessary.           | false
   *
   */
  public function list(array $options = []) : array {
    $options = [
      'limit' => $options['limit'] ?? 10,
      'page' => $options['page'] ?? 1,
      'canary' => $options['canary'] ?? FALSE,
      'show_incomplete' => $options['show_incomplete'] ?? FALSE,
    ];

    $options = $this->validatePaging($options);

    return $this->client->apiGet('sites', $options)->toArray();
  }


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
  public function get(int $siteId) : EntityInterface {
    return $this->client->getEntity('Site', $siteId);
  }

  /**
   * Create a new site.
   *
   * @version v1
   * @title Create a site
   * @http_method POST
   * @resource /api/v1/sites
   *
   * @params
   *   site_name       | string    | yes | The new site name.
   *   group_ids       | int|array | no  | Either a single group ID, or an array of group IDs.
   *   install_profile | string    | no  | The install profile to be used to install the site.
   *   stack_id        | int       | if multiple stacks exist | The stack id where the site should go.
   *
   * @group Sites
   * @example_command
   *   curl '{base_url}/api/v1/sites' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"site_name": "mysite"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id": 191,
   *     "site": "site1",
   *     "domains": [
   *       "mysite.site-factory.com"
   *     ]
   *   }
   */
  public function create(string $siteName, array $options = []) : array {
    unset($options['site_name']);

    $options = [
      'site_name' => $options['site_name'] ?? $siteName,
      'group_ids' => $options['group_ids'] ?? [],
      'install_profile' => $options['install_profile'] ?? NULL,
      'stack_id' => $options['stack_id'] ?? 1,
    ];

    return $this->client->apiPost('sites', $options)->toArray();
  }

}
