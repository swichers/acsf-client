<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "SslCertificates)
 */
class SslCertificates extends ActionBase {

  /**
   * Gets the SSL certificates that are installed on a stack.
   *
   * @version v1
   * @title INTERNAL, SUBJECT TO CHANGE - Get the SSL certificates installed on
   *   a stack
   * @group SslCertificates
   * @http_method GET
   * @resource /api/v1/ssl/certificates
   *
   * @params
   *   limit       | int    | no                       | A positive integer
   *   (max 100).    | 10 page        | int    | no                       | A
   *   positive integer.              | 1 stack_id    | int    | if multiple
   *   stacks exist | The stack id.
   *
   * @example_command
   *   curl '{base_url}/api/v1/ssl/certificates?stack_id=1' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "count": 2,
   *     "stack_id": 1,
   *     "certificates": [
   *       {
   *         "id": "4",
   *         "label": "Test Certificate 1",
   *         "certificate": "-----BEGIN CERTIFICATE-----...-----END
   *   CERTIFICATE-----",
   *         "private_key": "-----BEGIN RSA PRIVATE KEY-----...-----END RSA
   *   PRIVATE KEY-----",
   *         "ca": "-----BEGIN CERTIFICATE-----...-----END CERTIFICATE-----",
   *         "flags": {
   *           "active": true
   *         }
   *       },
   *       {
   *         "id": "5",
   *         "label": "Test Certificate 2",
   *         "certificate": "-----BEGIN CERTIFICATE-----...-----END
   *   CERTIFICATE-----",
   *         "private_key": "-----BEGIN RSA PRIVATE KEY-----...-----END RSA
   *   PRIVATE KEY-----",
   *         "ca": "-----BEGIN CERTIFICATE-----...-----END CERTIFICATE-----",
   *         "flags": {
   *           "active": false
   *         }
   *       }
   *     ]
   *   }
   */
  public function list(array $options = []) : array {

  }

  /**
   * Installs a new SSL certificate on a stack.
   *
   * @version v1
   * @title INTERNAL, SUBJECT TO CHANGE - Install a new SSL certificate on a
   *   stack
   * @group SslCertificates
   * @http_method POST
   * @resource /api/v1/ssl/certificates
   *
   * @params
   *   stack_id        | int    | if multiple stacks exist | The stack id.
   *   label           | string | yes                      | The label to
   *   identify the SSL certificate. certificate     | string | yes
   *            | The SSL certificate in PEM format. private_key     | string |
   *   yes                      | The SSL private key in PEM format.
   *   ca_certificates | string | no                       | CA intermediate
   *   certificates in PEM format.
   *
   * @example_command
   *   curl '{base_url}/api/v1/ssl/certificates' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{
   *       "stack_id": 1,
   *       "label": "My New Cert",
   *       "certificate": "-----BEGIN CERTIFICATE-----abc123....-----END
   *   CERTIFICATE-----",
   *       "private_key": "-----BEGIN RSA PRIVATE KEY-----secret....-----END
   *   RSA PRIVATE KEY-----",
   *       "ca_certificates": "-----BEGIN CERTIFICATE-----123abc....-----END
   *   CERTIFICATE-----"
   *     }' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "message": "The SSL certificate has been accepted."
   *   }
   */
  public function create(string $label, string $certificate, string $privateKey, array $options = []) : array {

  }

}
