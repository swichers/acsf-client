<?php


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "PageView")
 */
class PageView extends ActionBase {

  /**
   * Gets the monthly aggregated dynamic request statistics.
   *
   * @version v1
   * @title List monthly aggregated dynamic request statistics.
   * @http_method GET
   * @resource /api/v1/dynamic-requests/monthly
   * @group Dynamic requests
   *
   * @params
   *   stack_id   | int    | yes | The stack id for which to fetch the dynamic request statistics. If there is only one stack, this parameter can be omitted.
   *   start_from | string | no  | The first date from which to start showing the statistics in the format of YYYY-MM.
   *   sort_order | string | no  | The sort order direction. Either asc or desc.            | desc
   *   limit      | int    | no  | The number of months to be listed. Maximum value is 120. | 12
   *   page       | int    | no  | The page number to show in the list.                     | 1
   *
   * @example_command
   *   curl '{base_url}/api/v1/dynamic-requests/monthly?stack_id=1&sort_order=asc&limit=6&page=1' \
   *     -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function getMonthlyData() : array {

  }

  /**
   * Gets the monthly dynamic request statistics by domain.
   *
   * @version v1
   * @title List monthly dynamic request statistics by domain.
   * @http_method GET
   * @resource /api/v1/dynamic-requests/monthly/domains
   * @group Dynamic requests
   *
   * @params
   *   stack_id    | int    | yes | The stack id for which to fetch the dynamic request statistics. If there is only one stack, this parameter can be omitted.
   *   date        | string | yes | The month in the format of YYYY-MM.
   *   domain_name | string | no  | Full domain name or a prefix for filtering the results.
   *   sort_order  | string | no  | The sort order direction. Either asc or desc.            | asc
   *   limit       | int    | no  | The number of domains to be listed. (max 100)            | 10
   *   page        | int    | no  | The page number to show in the list.                     | 1
   *
   * @example_command
   *   curl '{base_url}/api/v1/dynamic-requests/monthly/domains?&stack_id=1&date=2016-11&limit=6&page=1' \
   *     -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   */
  public function getMonthlyDataByDomain() : array {

  }

}
