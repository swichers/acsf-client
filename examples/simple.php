<?php

/**
 * @file
 * A simple usage of the library.
 *
 * This script shows an example of backporting specific sites from live to a UAT
 * environment and then deploying a new tag to the UAT environment.
 */

declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use swichers\Acsf\Client\ClientFactory;

$client = ClientFactory::create('example.user', 'example.key', 'example.group', 'live');

// Grab all available sites.
$site_ids = array_column($client->getAction('Sites')->listAll()['sites'], 'id');

// Start a backport from production to the target environment.
$task_info = $client->getAction('Stage')->backport('uat', $site_ids);

// Wait for that backport to finish.
$client->getEntity('Task', (int) $task_info['task_id'])->wait();

// Change the connection to the target environment.
$client->setEnvironment('uat');

// Deploy a new tag to the target environment.
$task_info = $client->getAction('Update')->updateCode('tags/1.5.0-build');

// Wait for that task to finish.
$client->getEntity('Task', (int) $task_info['task_id'])->wait();

// Clear Drupal and Varnish cache for the backported sites.
$client->getAction('Sites')->clearCaches();

exit(0);
