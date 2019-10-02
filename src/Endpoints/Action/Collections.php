<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Collections.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Collections")
 */
class Collections extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Gets a list of site collections.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of Collections.
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
   * @example_response
   * ```json
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
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['limit', 'page']);
    $options = $this->constrictPaging($options);

    return $this->client->apiGet('collections', $options)->toArray();
  }

  /**
   * Create a new site collection.
   *
   * @param string $name
   *   The name of the new collection.
   * @param array $siteIds
   *   An array of site Ids to add to the new collection.
   * @param array $groupIds
   *   An array of group Ids.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Information about the new collection.
   *
   * @version v1
   * @title Create a site collection
   * @group Site collections
   * @http_method POST
   * @resource /api/v1/collections
   *
   * @params
   * name      | string    | yes | The name of the new site collection.
   * site_ids  | int|array | yes | Either a single site ID, or an array of site
   *                               IDs.
   * group_ids | int|array | yes | Either a single group ID, or an array of
   *                               group IDs.
   * internal_domain_prefix | string  | no  | The site collection's internal
   *                                          domain prefix. Uses the "name"
   *                                          parameter's value if not set.
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "name": "mycollection",
   *     "time": "2016-11-25T13:18:44+00:00",
   *     "internal_domain": "mycollecton.site-factory.com"
   *   }
   * ```
   */
  public function create(string $name, array $siteIds, array $groupIds, array $options = []): array {

    $options = $this->limitOptions($options, ['internal_domain_prefix']);

    $options['name'] = $name;
    $options['site_ids'] = $this->cleanIntArray($siteIds);
    $options['group_ids'] = $this->cleanIntArray($groupIds);

    return $this->client->apiPost('collections', $options)->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Collection';
  }

}
