<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\PagingTrait;

/**
 * @Action(name = "Collections")
 */
class Collections extends ActionBase {

  use PagingTrait;

  /**
   * Gets a list of site collections.
   *
   * @version v1
   * @title List site collections
   * @group Site collections
   * @http_method GET
   * @resource /api/v1/collections
   *
   * @params
   *   limit  | int  | no | A positive integer (max 100). | 10
   *   page   | int  | no | A positive integer.           | 1
   *
   * @example_command
   *   curl '{base_url}/api/v1/collections?limit=20&page=2' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *      "count": 111,
   *      "time" : "2016-11-25T13:18:44+00:00",
   *      "collections": [
   *        {
   *          "id": 196,
   *          "name": "collection2",
   *          "internal_domain": "domain1.site-factory.com",
   *          "primary_site": 220,
   *          "site_count": 2,
   *          "groups": [
   *             91
   *          ],
   *        }
   *      ]
   *   }
   */
  public function list(array $options = []) : array {
    $options = [
      $options['limit'] ?? 10,
      $options['page'] ?? 1,
    ];

    $options = $this->validatePaging($options);

    return $this->client->apiGet('collections', $options)->toArray();
  }

  /**
   * Create a new site collection.
   *
   * @version v1
   * @title Create a site collection
   * @group Site collections
   * @http_method POST
   * @resource /api/v1/collections
   *
   * @params
   *   name                   | string    | yes | The name of the new site collection.
   *   site_ids               | int|array | yes | Either a single site ID, or an array of site IDs.
   *   group_ids              | int|array | yes | Either a single group ID, or an array of group IDs.
   *   internal_domain_prefix | string    | no  | The site collection's internal domain prefix. Uses the "name" parameter's value if not set.
   *
   * @example_command
   *   curl '{base_url}/api/v1/collections' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"name": "mycollection", "site_ids": [100, 200], "group_ids": [2]}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id": 191,
   *     "name": "mycollection",
   *     "time": "2016-11-25T13:18:44+00:00",
   *     "internal_domain": "mycollecton.site-factory.com"
   *   }
   */
  public function create(string $name, array $siteIds, $groupIds, array $options = []) : array {
    $data = [
      'name' => $name,
      'site_ids' => $siteIds,
      'group_ids' => $groupIds,
      'internal_domain_prefix' => $options['internal_domain_prefix'] ?? NULL,
    ];

    return $this->client->apiPost('collections', $data)->toArray();
  }

  /**
   * Get detailed information about a site collection.
   *
   * @version v1
   * @title Site collection details
   * @group Site collections
   * @http_method GET
   * @resource /api/v1/collections/{collection_id}
   * @example_command
   *   curl '{base_url}/api/v1/collections/123' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *  {
   *    "id": 261,
   *    "time": "2016-11-25T13:18:44+00:00",
   *    "created": 1489075420,
   *    "owner": "admin",
   *    "name": "collection1",
   *    "internal_domain": "collection1.site-factory.com",
   *    "external_domains": [
   *      "domain1.site-factory.com"
   *    ],
   *    "groups": [
   *      91
   *    ],
   *    "sites": [
   *      236,
   *      231
   *    ],
   *    "primary_site": 236
   *  }
   */
  public function get(int $collectionId) : EntityInterface {

  }

}

