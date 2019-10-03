# Acquia Cloud Site Factory Client Library

> A Symfony-based PHP library for working with the ACSF platform.

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.com/swichers/acsf-client.svg?token=Lm6gQQWsBsnzoGah2JXY&branch=master)](https://travis-ci.com/swichers/acsf-client)
[![Codacy Quality](https://api.codacy.com/project/badge/Grade/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)
[![Codacy Coverage](https://api.codacy.com/project/badge/Coverage/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)

Acquia Cloud Site Factory is a hosting platform provided by Acquia. As part of that platform an API is provided for developers to use. The goal of this library is to wrap that API in such a way that it becomes trivial to leverage it in PHP applications.

**Caution:** Test coverage in this library is around validation of calls to the API, and is not testing interaction with the actual ACSF API. Not all wrapped endpoints have been validated as properly implemented, and live calls to the API may have unexpected results. Review all scripts and calls for proper behavior before executing against a live environment.

## Installation

**Requirements**

* PHP >= 7.2
* [API access to ACSF](https://docs.acquia.com/site-factory/extend/api/#obtaining-your-api-key)
* Composer for dependency installs

Installation should be straightforward when using composer.

```sh
composer require swichers/acsf-client
```

## Usage

```php
<?php declare(strict_types=1);

  require 'vendor/autoload.php';

  use swichers\Acsf\Client\ServiceLoader;
  
  $base_config = [
    'username' => 'example.user',
    'api_key' => 'example.key',
    'site_group' => 'example.group',
  ];

  $container = ServiceLoader::build();
  $container->setParameter('acsf.client.connection', ['environment' => 'live'] +
  $base_config);

  $client = $container->get('acsf.client');

  // Check the service status.
  print_r($client->getAction('Status')->ping());
```

## Development

**Running tests**
```sh
$ vendor/bin/phpunit
```

**Checking code formatting**
```sh
$ vendor/bin/phpcs
```

## Example: Backport and code deploy

This script shows an example of backporting specific sites from live to a UAT environment and then deploying a new tag to the UAT environment.

```php
<?php declare(strict_types=1);

  require 'vendor/autoload.php';

  use swichers\Acsf\Client\ServiceLoader;
  use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

  $base_config = [
    'username' => 'example.user',
    'api_key' => 'example.key',
    'site_group' => 'example.group',
    'environment' => 'live',
  ];

  // Utilize the Symfony service container for ease of client creation.
  $container = ServiceLoader::build();
  $container->setParameter('acsf.client.connection', $base_config);
  $client = $container->get('acsf.client');

  // Grab all available sites.
  $site_ids = array_column($client->getAction('Sites')->list()['sites'], 'id');

  // Start a backport from production to UAT.
  $task_info = $client->getAction('Stage')->backport('uat', $site_ids, [
    'synchronize_all_users' => 'yes',
    'wipe_target_environment' => 'yes',
    'detailed_status' => 'no',
  ]);

  // Wait for that backport to finish while printing the current status.
  $client->getEntity('Task', $task_info['task_id'])->wait(15, function (EntityInterface $task, array $task_status) {
    printf("Backport (%d): %s\n", $task->id(), $task_status['status_string']);
  });

  // Change the connection to the UAT environment.
  $client->setConfig(['environment' => 'uat'] + $base_config);

  // Deploy a new tag to UAT.
  $task_info = $client->getAction('Update')->updateCode('tags/1.5.0-build');
  // Wait for that task to finish.
  $client->getEntity('Task', $task_info['task_id'])->wait();

  // Clear Drupal and Varnish cache for the backported sites.
  foreach ($site_ids as $site_id) {
    $client->getEntity('Site', $site_id)->clearCache();
  }
```
