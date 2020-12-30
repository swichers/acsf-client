<?php

/**
 * @file
 * Clear Varnish and Drush cache on the target environment.
 *
 * Usage: php cc.php test
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to redeploy code on.
define('TARGET_ENV', $argv[1] ?? '');

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);

printf("Clearing site caches.\n");
$client->getAction('Sites')->clearCaches();
printf("Cache clear complete.\n");

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
