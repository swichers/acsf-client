<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Dynamic Requests.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "PageView")
 */
class PageView extends AbstractAction {

  use ValidationTrait;

  /**
   * Gets the monthly dynamic request statistics by domain.
   *
   * @param string $date
   *   The month to request data for.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Dynamic request statistics.
   *
   * @version v1
   * @title List monthly dynamic request statistics by domain.
   * @http_method GET
   * @resource /api/v1/dynamic-requests/monthly/domains
   * @group Dynamic requests
   *
   * @params
   * stack_id   | int    | yes | The stack id for which to fetch the dynamic
   *                             request statistics. If there is only one
   *                             stack, this parameter can be omitted.
   * date        | string | yes | The month in the format of YYYY-MM.
   * domain_name | string | no  | Full domain name or a prefix for filtering
   *                              the results.
   * sort_order  | string | no  | The sort order direction.
   *                              Either asc or desc. | asc
   * limit       | int    | no  | The number of domains to be listed.
   *                              (max 100) | 10
   * page        | int    | no  | The page number to show in the list.| 1
   *
   * @example_response
   * ```json
   *  {
   *    "count": 1,
   *    "time": "2016-11-25T13:18:44+00:00",
   *    "most_recent_data": "2016-11-23",
   *    "dynamic_requests": {
   *      "domain1.example.com": {
   *        "date": "2016-11",
   *        "stack_id": 1,
   *        "total_dynamic_requests": 106,
   *        "2xx_dynamic_requests": 100,
   *        "3xx_dynamic_requests": 3,
   *        "4xx_dynamic_requests": 2,
   *        "5xx_dynamic_requests": 1,
   *        "total_runtime": 101.4,
   *        "2xx_runtime": 100,
   *        "3xx_runtime": 0.9,
   *        "4xx_runtime": 0.4,
   *        "5xx_runtime": 0.1
   *      }
   *    }
   *  }
   * ```
   */
  public function getMonthlyDataByDomain(string $date, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'stack_id',
        'domain_name',
        'sort_order',
        'limit',
        'page',
      ]
    );
    $options['date'] = $date;
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    return $this->genericDataRequest('date', TRUE, $options);

  }

  /**
   * Gets the monthly aggregated dynamic request statistics.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Dynamic request statistics.
   *
   * @version v1
   * @title List monthly aggregated dynamic request statistics.
   * @http_method GET
   * @resource /api/v1/dynamic-requests/monthly
   * @group Dynamic requests
   *
   * @params
   * stack_id   | int    | yes | The stack id for which to fetch the dynamic
   *                             request statistics. If there is only one
   *                             stack, this parameter can be omitted.
   * start_from | string | no  | The first date from which to start showing the
   *                             statistics in the format of YYYY-MM.
   * sort_order | string | no  | The sort order direction. Either asc or desc.
   *             | desc limit      | int    | no  | The number of months to be
   *   listed. Maximum value is 120. | 12 page       | int    | no  | The page
   *   number to show in the list.                     | 1
   *
   * @example_response
   * ```json
   *  {
   *    "count": 1,
   *    "time": "2016-11-25T13:18:44+00:00",
   *    "most_recent_data": "2016-11-23",
   *    "dynamic_requests": {
   *      "2016-10": {
   *        "date": "2016-10",
   *        "stack_id": 1,
   *        "total_dynamic_requests": 106,
   *        "2xx_dynamic_requests": 100,
   *        "3xx_dynamic_requests": 3,
   *        "4xx_dynamic_requests": 2,
   *        "5xx_dynamic_requests": 1,
   *        "total_runtime": 101.4,
   *        "2xx_runtime": 100,
   *        "3xx_runtime": 0.9,
   *        "4xx_runtime": 0.4,
   *        "5xx_runtime": 0.1
   *      }
   *    }
   *  }
   * ```
   */
  public function getMonthlyData(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'stack_id',
        'start_from',
        'sort_order',
        'limit',
        'page',
      ]
    );
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    return $this->genericDataRequest('start_from', FALSE, $options);
  }

  /**
   * Single function to request both reports.
   *
   * @param string $dateKey
   *   The key of the date value.
   * @param bool $byDomain
   *   TRUE if the report should be by domain.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The request result.
   */
  protected function genericDataRequest(string $dateKey, bool $byDomain = FALSE, array $options = []): array {

    if (isset($options[$dateKey])) {
      $options[$dateKey] = $this->requirePatternMatch(
        $options[$dateKey],
        '/^[0-9]{4}-[0-9]{2}$/'
      );
    }

    // This is the only endpoint that uses sort_order instead of order.
    if (isset($options['sort_order'])) {
      $options['order'] = $options['sort_order'];
      $options = $this->constrictPaging($options);
      $options['sort_order'] = $options['order'];
      unset($options['order']);
    }
    else {
      $options = $this->constrictPaging($options);
    }

    $method = ['dynamic-requests', 'monthly'];
    if ($byDomain) {
      $method[] = 'domains';
    }

    return $this->client->apiGet($method, $options)->toArray();
  }

}
