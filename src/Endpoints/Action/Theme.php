<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Theme.
 *
 * Site Factory sites' themes need to be shared across multiple servers. The
 * process is controlled by the Factory using git repositories, one-per-tangle.
 * When a theme event (create/modify/delete) occurs on a site or the Factory a
 * theme change notification is sent to the Factory and subsequently processed
 * by WIP. It is the responsibility of this resource to remotely start a WIP
 * task to process those notifications.
 *
 * When a customer is using external theme repositories they need a way to
 * signal to the Factory that a change has occurred in the external repository
 * so that those changes may be propagated to the relevant sites and webnodes.
 * Theme event notifications are the method by which such signals are received
 * and subsequently processed by the Factory.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Theme")
 */
class Theme extends AbstractAction {

  use ValidationTrait;

  /**
   * Processes the stored theme change notifications.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Service response.
   *
   * @version v1
   * @title Process theme modifications
   * @http_method POST
   * @resource /api/v1/theme/process
   * @group Themes
   *
   * @params
   *   sitegroup_id | string | no | The ID of a specific sitegroup to process
   *   e.g. "tangle001".
   *
   * @example_response
   * ```json
   *   {
   *     "message": "The request to process theme notification has been
   *   accepted.",
   *     "sitegroups": [
   *       "tangle001"
   *     ],
   *     "time": "2014-05-02T16:21:25+00:00"
   *   }
   * ```
   */
  public function process(array $options = []): array {

    $options = $this->limitOptions($options, ['sitegroup_id']);

    return $this->client->apiPost('theme/process', $options)->toArray();
  }

  /**
   * Receives a theme event notification.
   *
   * @param string $scope
   *   The scope. Either "theme", "site", "group", or "global".
   * @param string $event
   *   The type of theme event. Either "create", "modify", or "delete".
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Notification confirmation.
   *
   * @version v1
   * @title Send a theme notification
   * @group Themes
   * @http_method POST
   * @resource /api/v1/theme/notification
   *
   * @params
   * scope     | string | yes | The scope. Either "theme", "site", "group",
   *                            or "global".
   * event     | string | yes | The type of theme event. Either
   *                            "create", "modify", or "delete".
   * nid       | int    | no  | The node ID of the related entity (site or
   *                            group). Not relevant for the "global" scope.
   * theme     | string | no  | The system name of the theme. Only relevant for
   *                            "theme" scope notifications.
   * timestamp | int    | no  | A Unix timestamp of when the event occurred.
   * uid       | int    | no  | The user id owning the notification and who
   *                            should get notified if an error occurs during
   *                            processing.
   *
   * @example_response
   * ```json
   *   {
   *     "message": "The site.modify notification has been received.",
   *     "time": "2014-02-16T20:04:12-06:00",
   *     "notification": {
   *       "scope": "site",
   *       "event": "modify",
   *       "nid": 123
   *     }
   *   }
   * ```
   */
  public function sendNotification(string $scope, string $event, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'nid',
        'theme',
        'timestamp',
        'uid',
      ]
    );
    $options['scope'] = $scope;
    $options['event'] = $event;
    $this->requireOneOf(
      $options['scope'],
      ['theme', 'site', 'group', 'global']
    );
    $this->requireOneOf($options['event'], ['create', 'modify', 'delete']);

    if ($options['scope'] === 'global') {
      unset($options['nid']);
    }

    if ($options['scope'] !== 'theme') {
      unset($options['theme']);
    }

    return $this->client->apiPost('theme/notification', $options)->toArray();
  }

  /**
   * Distributes themes to a webnode.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Service response.
   *
   * @version v1
   * @title Distribute themes
   * @group Themes
   * @http_method POST
   * @resource /site-api/v1/theme/deploy
   *
   * @params
   * sitegroup | string | no | The sitegroup to which themes should be deployed.
   * webnode   | string | no | The webnode to which themes should be deployed.
   *
   * @example_response
   * ```json
   *   {
   *     "message": "The request to deploy themes has been accepted.",
   *     "sitegroup": "tangle001",
   *     "webnode": "managed-47.gardens.hosting.acquia.com",
   *     "task_id": "47",
   *     "time": "2014-05-02T16:21:25+00:00"
   *   }
   * ```
   */
  public function deploy(array $options = []): array {

    $options = $this->limitOptions($options, ['sitegroup', 'webnode']);

    return $this->client->apiPost('theme/deploy', $options)->toArray();
  }

}
