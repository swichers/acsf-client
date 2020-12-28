<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Collection endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Collection")
 */
class Collection extends AbstractEntity {

  use ValidationTrait;

  /**
   * Get detailed information about a site collection.
   *
   * @version v1
   * @title Site collection details
   * @group Site collections
   * @http_method GET
   * @resource /api/v1/collections/{collection_id}
   * @example_response
   * ```json
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
   * ```
   */
  public function details(): array {

    return $this->client->apiGet(['collections', $this->id()])->toArray();
  }

  /**
   * Delete a site collection.
   *
   * @return array
   *   Deletion confirmation.
   *
   * @version v1
   * @title Delete a site collection
   * @group Site collections
   * @http_method DELETE
   * @resource /api/v1/collections/{collection_id}
   *
   * @example_response
   * ```json
   *   {
   *     "id" : 101,
   *     "time" : "2016-10-28T09:25:26+00:00",
   *     "deleted" : true,
   *     "message" : "Your site collection was successfully deleted."
   *   }
   * ```
   */
  public function delete(): array {

    return $this->client->apiDelete(['collections', $this->id()], [])->toArray(
    );
  }

  /**
   * Add site(s) to a site collection.
   *
   * @param array $siteIds
   *   An array of site IDs to add to the collection.
   *
   * @return array
   *   Task information.
   *
   * @version v1
   * @title Add site(s) to a site collection.
   * @group Site collections
   * @http_method POST
   * @resource /api/v1/collections/{collection_id}/add
   *
   * @params
   *   site_ids | int|array | yes | Either a single site ID, or an array of
   *                                site IDs.
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "name: "foobarcollection",
   *     "time": "2017-04-20T10:58:18+00:00",
   *     "site_ids_added": [
   *       121
   *     ],
   *     "added": true,
   *     "message": "One site was successfully added to the site collection.",
   *     "warning": [
   *       "The site aabbcc (site ID: 101) is already part of the current site
   *   collection.; The site ddeeff (site ID: 126) is already part of the
   *   current site collection.",
   *       "Site xxyyzz (site ID: 121) was removed from examplegroup (group ID:
   *   91)."
   *     ],
   *     "site_ids_skipped": [
   *       101,
   *       126
   *     ]
   *   }
   * ```
   */
  public function addSite(array $siteIds): array {

    $data = [
      'site_ids' => $this->cleanIntArray($siteIds),
    ];

    if (empty($data['site_ids'])) {
      throw new InvalidOptionException('No site_ids provided.');
    }

    return $this->client->apiPost(['collections', $this->id(), 'add'], $data)
      ->toArray();
  }

  /**
   * Remove site(s) from a site collection.
   *
   * @param array $siteIds
   *   An array of site IDs to remove from the collection.
   *
   * @return array
   *   Task information.
   *
   * @version v1
   * @title Remove site(s) from a site collection.
   * @group Site collections
   * @http_method POST
   * @resource /api/v1/collections/{collection_id}/remove
   *
   * @params
   *   site_ids | int|array | yes | Either a single site ID, or an array of
   *                                site IDs.
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "name: "foobarcollection",
   *     "time": "2017-04-20T10:58:18+00:00",
   *     "site_ids_removed": [
   *       121
   *     ],
   *     "removed": true,
   *     "message": "One site was successfully removed from the site
   *   collection."
   *   }
   * ```
   */
  public function removeSite(array $siteIds): array {

    $data = [
      'site_ids' => $this->cleanIntArray($siteIds),
    ];

    if (empty($data['site_ids'])) {
      throw new InvalidOptionException('No site_ids provided.');
    }

    return $this->client->apiPost(['collections', $this->id(), 'remove'], $data)
      ->toArray();
  }

  /**
   * Set the primary site of a site collection.
   *
   * @param int $siteId
   *   The ID of the site to set as the primary.
   *
   * @return array
   *   Task information.
   *
   * @version v1
   * @title Set the primary site of a site collection.
   * @group Site collections
   * @http_method POST
   * @resource /api/v1/collections/{collection_id}/set-primary
   *
   * @params
   *   site_id | int | yes | A single site ID.
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "name": "foobarcollection",
   *     "time": "2017-04-20T10:58:18+00:00",
   *     "primary_site_id: 101,
   *     "switched": true,
   *     "message": "It can take a few minutes to switch over to the new
   *   primary site."
   *   }
   * ```
   */
  public function setPrimarySite(int $siteId): array {

    $data = [
      'site_id' => $siteId,
    ];

    return $this->client->apiPost(
      [
        'collections',
        $this->id(),
        'set-primary',
      ],
      $data
    )->toArray();
  }

}
