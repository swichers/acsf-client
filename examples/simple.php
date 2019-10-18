<?php
/**
 * @file
 * A simple usage of the library.
 *
 * This script shows an example of backporting specific sites from live to a UAT
 * environment and then deploying a new tag to the UAT environment.
 */

declare(strict_types = 1);

require 'vendor/autoload.php';

use swichers\Acsf\Client\ServiceLoader;

$base_config = [
  'username' => 'example.user',
  'api_key' => 'example.key',
  'site_group' => 'example.group',
  'environment' => 'live',
];

// Utilize the Symfony service container for ease of client creation.
$client =
  ServiceLoader::buildFromConfig(['acsf.client.connection' => $base_config])
    ->get('acsf.client');

// Grab all available sites.
$site_ids = array_column($client->getAction('Sites')->list()['sites'], 'id');

// Start a backport from production to the target environment.
$task_info = $client->getAction('Stage')->backport('uat', $site_ids);

// Wait for that backport to finish.
$client->getEntity('Task', intval($task_info['task_id']))->wait();

// Change the connection to the target environment.
$client->setConfig(['environment' => 'uat'] + $base_config);

// Deploy a new tag to the target environment.
$task_info = $client->getAction('Update')->updateCode('tags/1.5.0-build');

// Wait for that task to finish.
$client->getEntity('Task', intval($task_info['task_id']))->wait();

// Clear Drupal and Varnish cache for the backported sites.
$client->getAction('Sites')->clearCaches();

exit(1);
