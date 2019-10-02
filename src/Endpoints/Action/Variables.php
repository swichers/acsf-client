<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Update.
 *
 * This resource is a CRUD interface to Drupal configuration variables
 * (variable_get/set/del). It can be used to remotely modify the configuration
 * of the Factory.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Variables")
 */
class Variables extends AbstractAction {

  use ValidationTrait;

  /**
   * Get a variable by name.
   *
   * @param string $name
   *   The variable to retrieve.
   *
   * @return array
   *   The variable information.
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
   * @example_response
   * ```json
   *   {
   *     "name": "value"
   *   }
   * ```
   */
  public function get(string $name): array {

    return $this->client->apiGet('variables', ['name' => $name])->toArray();
  }

  /**
   * Get lists of variables.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of variables.
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
   * @example_response
   * ```json
   *   {
   *     "name": "value",
   *     "name": "value",
   *     "name": "value",
   *     ...
   *   }
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['search']);

    return $this->client->apiGet('variables', $options)->toArray();
  }

  /**
   * Set a configuration variable by name.
   *
   * @param string $name
   *   The variable to set.
   * @param mixed $value
   *   The value to set.
   *
   * @return array
   *   The data that was set.
   *
   * @version v1
   * @title Set a variable
   * @group Variables
   * @http_method PUT
   * @resource /api/v1/variables
   * @body
   *   name  | string | yes
   *   value | mixed  | no
   *
   * @example_response
   * ```json
   *   {
   *     "name": "value"
   *   }
   * ```
   */
  public function set(string $name, $value): array {

    $options = ['name' => $name, 'value' => $value];

    return $this->client->apiPut('variables', $options)->toArray();
  }

}
