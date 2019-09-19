<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * This resource is a CRUD interface to Drupal configuration variables
 * (variable_get/set/del). It can be used to remotely modify the configuration
 * of the Factory.
 *
 * @Action(name = "Variables")
 */
class Variables extends ActionBase {

  /**
   * Get a variable by name.
   *
   * @version v1
   * @title Get a variable
   * @group Variables
   * @http_method GET
   * @resource /api/v1/variables
   *
   * @params
   *   name | string | no
   *
   * @example_command
   *   curl '{base_url}/api/v1/variables?name=site_name' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "name": "value"
   *   }
   */
  public function get(string $name) : array {
    return $this->client->apiGet('variables', ['name' => $name])->toArray();
  }

  /**
   * Get lists of variables.
   *
   * @version v1
   * @title List variables
   * @group Variables
   * @http_method GET
   * @resource /api/v1/variables
   *
   * @params
   *   search | string | no
   *
   * @example_command
   *   curl '{base_url}/api/v1/variables?search=node' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "name": "value",
   *     "name": "value",
   *     "name": "value",
   *     ...
   *   }
   */
  public function list(array $options = []) : array {
    return $this->client->apiGet(
      'variables',
      ['search' => $options['search'] ?? NULL]
    )->toArray();
  }

  /**
   * Set a configuration variable by name.
   *
   * @version v1
   * @title Set a variable
   * @group Variables
   * @http_method PUT
   * @resource /api/v1/variables
   * @body
   *   name  | string | yes
   *   value | mixed  | no
   * @example_command
   *   curl '{base_url}/api/v1/variables' \
   *     -H 'Content-Type: application/json' \
   *     -X PUT -d '{"name": "site_name", "value": "My Site"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "name": "value"
   *   }
   */
  public function set(string $name, $value) : array {
    return $this->client->apiPut(
      'variables',
      ['name' => $name, 'value' => $value]
    )->toArray();
  }

}
