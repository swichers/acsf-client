<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
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
 * @Action(name = "Theme")
 */
class Theme extends ActionBase {

  /**
   * Processes the stored theme change notifications.
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
   * @example_command
   *   curl '{base_url}/api/v1/theme/process' \
   *     -H 'Content-Type: application/json' \
   *     -X POST -d '{"sitegroup_id": "tangle001"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "message": "The request to process theme notification has been
   *   accepted.",
   *     "sitegroups": [
   *       "tangle001"
   *     ],
   *     "time": "2014-05-02T16:21:25+00:00"
   *   }
   */
  public function process() : array {

  }

  /**
   * Receives a theme event notification.
   *
   * @version v1
   * @title Send a theme notification
   * @group Themes
   * @http_method POST
   * @resource /api/v1/theme/notification
   *
   * @params
   *   scope     | string | yes | The scope. Either "theme", "site", "group",
   *   or "global". event     | string | yes | The type of theme event. Either
   *   "create", "modify", or "delete". nid       | int    | no  | The node ID
   *   of the related entity (site or group). Not relevant for the "global"
   *   scope. theme     | string | no  | The system name of the theme. Only
   *   relevant for "theme" scope notifications. timestamp | int    | no  | A
   *   Unix timestamp of when the event occurred. uid       | int    | no  |
   *   The user id owning the notification and who should get notified if an
   *   error occurs during processing.
   *
   * @example_command
   *   curl '{base_url}/api/v1/theme/notification' \
   *     -v -u {user_name}:{api_key} -X POST \
   *     -H 'Content-Type: application/json' \
   *     -d '{"scope": "site", "event": "modify", "nid": 123}'
   * @example_response
   *   {
   *     "message": "The site.modify notification has been received.",
   *     "time": "2014-02-16T20:04:12-06:00",
   *     "notification": {
   *       "scope": "site",
   *       "event": "modify",
   *       "nid": 123
   *     }
   *   }
   */
  public function sendNotification(string $scope, string $event) : array {

  }

  /**
   * Distributes themes to a webnode.
   *
   * @version v1
   * @title Distribute themes
   * @group Themes
   * @http_method POST
   * @resource /site-api/v1/theme/deploy
   *
   * @params
   *   sitegroup | string | no | The sitegroup to which themes should be
   *   deployed. webnode   | string | no | The webnode to which themes should
   *   be deployed.
   *
   * @example_command
   *   curl '{base_url}/site-api/v1/theme/deploy' \
   *     -H 'Content-Type: application/json' \
   *     -X POST -d '{"sitegroup": "tangle001", "webnode":
   *   "managed-47.gardens.hosting.acquia.com"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "message": "The request to deploy themes has been accepted.",
   *     "sitegroup": "tangle001",
   *     "webnode": "managed-47.gardens.hosting.acquia.com",
   *     "task_id": "47",
   *     "time": "2014-05-02T16:21:25+00:00"
   *   }
   */
  public function deploy() : array {

  }

}
