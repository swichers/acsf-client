<?php
/**
 * @file
 * Clear Varnish and Drush cache on the target environment.
 *
 * Usage: php cc.php test
 * Usage: php cc.php dev 2
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ServiceLoader;

require 'config.php';
require '../vendor/autoload.php';

// The environment to redeploy code on.
define('TARGET_ENV', $argv[1] ?? '');
// The ACSF stack to target.
define('STACK_ID', $argv[2] ?? 1);

if (empty(TARGET_ENV)) {
  echo "Must supply a target environment.\n\n";
  printf(
    "Example: php %s test\n",
    basename(__FILE__)
  );
  die(1);
}

$start_time = new DateTime();

$base_config = [
  'username' => API_USERNAME,
  'api_key' => API_KEY,
  'site_group' => ACSF_SITE_GROUP,
];

$client = ServiceLoader::buildFromConfig(
  ['acsf.client.connection' => ['environment' => TARGET_ENV] + $base_config]
)->get('acsf.client');

printf("Clearing site caches.\n");
$client->getAction('Sites')->clearCaches();
printf("Cache clear complete.\n");

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
