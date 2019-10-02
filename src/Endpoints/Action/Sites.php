<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Sites.
 *
 * Sites are the product of the Site Factory. This resource is responsible for
 * managing those sites.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Sites",
 *   entityType = "Site"
 * )
 */
class Sites extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Gets a list of sites.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of sites.
   *
   * @group Sites
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
   * @example_response
   * ```json
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
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'limit',
        'page',
        'canary',
        'show_incomplete',
      ]
    );
    $options = $this->constrictPaging($options);
    if (isset($options['canary'])) {
      $options['canary'] = $this->ensureBool($options['canary']);
    }
    if (isset($options['show_incomplete'])) {
      $options['show_incomplete'] = $this->ensureBool(
        $options['show_incomplete']
      );
    }

    return $this->client->apiGet('sites', $options)->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Site';
  }

  /**
   * Create a new site.
   *
   * @param string $siteName
   *   The name of the new site.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The new site information.
   *
   * @version v1
   * @title Create a site
   * @http_method POST
   * @resource /api/v1/sites
   *
   * @params
   *   site_name       | string    | yes | The new site name.
   *   group_ids       | int|array | no  | Either a single group ID, or an
   *                                       array of group IDs.
   * install_profile   | string    | no  | The install profile to be used to
   *                                       install the site.
   * stack_id          | int       | ? | The stack id where the site should go.
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "site": "site1",
   *     "domains": [
   *       "mysite.site-factory.com"
   *     ]
   *   }
   * ```
   */
  public function create(string $siteName, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'group_ids',
        'install_profile',
        'stack_id',
      ]
    );
    $options['site_name'] = $siteName;
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    if (isset($options['group_ids'])) {
      $options['group_ids'] = $this->cleanIntArray($options['group_ids']);
    }

    return $this->client->apiPost('sites', $options)->toArray();
  }

}
