<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Sites.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "SslCertificates")
 */
class SslCertificates extends AbstractAction {

  use ValidationTrait;

  /**
   * Gets the SSL certificates that are installed on a stack.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of installed SLL certificates.
   *
   * @version v1
   * @title INTERNAL, SUBJECT TO CHANGE - Get the SSL certificates installed on
   *   a stack
   * @group SslCertificates
   * @http_method GET
   * @resource /api/v1/ssl/certificates
   *
   * @params
   * limit       | int    | no | A positive integer (max 100).    | 10
   * page        | int    | no | A positive integer.              | 1
   * stack_id    | int    | ?  | The stack id.
   *
   * @example_response
   * ```json
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
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['page', 'limit', 'stack_id']);
    $options = $this->constrictPaging($options);
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    return $this->client->apiGet('ssl/certificates', $options)->toArray();
  }

  /**
   * Installs a new SSL certificate on a stack.
   *
   * @param string $label
   *   The label to identify the SSL certificate.
   * @param string $certificate
   *   The SSL certificate in PEM format.
   * @param string $privateKey
   *   The SSL private key in PEM format.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of installed SLL certificates.
   *
   * @version v1
   * @title INTERNAL, SUBJECT TO CHANGE - Install a new SSL certificate on a
   *   stack
   * @group SslCertificates
   * @http_method POST
   * @resource /api/v1/ssl/certificates
   *
   * @params
   * stack_id        | int    | ?   | The stack id.
   * label           | string | yes | The label to identify the SSL certificate.
   * certificate     | string | yes | The SSL certificate in PEM format.
   * private_key     | string | yes | The SSL private key in PEM format.
   * ca_certificates | string | no  | CA intermediate certificates in PEM
   *                                  format.
   *
   * @example_command
   * ```sh
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
   * ```
   * @example_response
   * ```json
   *   {
   *     "message": "The SSL certificate has been accepted."
   *   }
   * ```
   */
  public function create(string $label, string $certificate, string $privateKey, array $options = []): array {

    $options = $this->limitOptions($options, ['stack_id', 'ca_certificates']);
    $options['label'] = $label;
    $options['certificate'] = $certificate;
    $options['private_key'] = $privateKey;
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    return $this->client->apiPost('ssl/certificates', $options)->toArray();
  }

}
