<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Collection")
 */
class Collection extends EntityBase {

  /**
   * Site collection details
   *
   * GET /api/v1/collections/{collection_id}
   */
  public function getInfo() : array {
    return $this->client->apiGet(['collections', $this->id()])->toArray();
  }

  /**
   * Delete a site collection.
   *
   * @version v1
   * @title Delete a site collection
   * @group Site collections
   * @http_method DELETE
   * @resource /api/v1/collections/{collection_id}
   * @example_command
   *   curl '{base_url}/api/v1/collections/101' \
   *     -X DELETE \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "id" : 101,
   *     "time" : "2016-10-28T09:25:26+00:00",
   *     "deleted" : true,
   *     "message" : "Your site collection was successfully deleted."
   *   }
   */
  public function delete() : array {
    return $this->client->apiDelete(['collections', $this->id()], [])->toArray(
    );
  }

  /**
   * Add site(s) to a site collection.
   *
   * POST /api/v1/collections/{collection_id}/add
   */
  public function addSite(array $siteIds) : array {
    $data = [
      'site_ids' => $siteIds,
    ];
    return $this->client->apiPost(['collections', $this->id(), 'add'], $data)
      ->toArray();
  }

  /**
   * Remove site(s) from a site collection.
   *
   * POST /api/v1/collections/{collection_id}/remove
   */
  public function removeSite(array $siteIds) : array {
    $data = [
      'site_ids' => $siteIds,
    ];
    return $this->client->apiPost(['collections', $this->id(), 'remove'], $data)
      ->toArray();
  }

  /**
   * Set the primary site of a site collection.
   *
   * POST /api/v1/collections/{collection_id}/set-primary
   */
  public function setPrimarySite(int $siteId) : array {
    $data = [
      'site_id' => $siteId,
    ];
    return $this->client->apiPost(
      ['collections', $this->id(), 'set-primary'],
      $data
    )->toArray();
  }

}
