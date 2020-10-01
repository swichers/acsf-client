# Acquia Cloud Site Factory Client Library

> A Symfony-based PHP library for working with the ACSF platform.

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.com/swichers/acsf-client.svg?token=Lm6gQQWsBsnzoGah2JXY&branch=master)](https://travis-ci.com/swichers/acsf-client)
[![Codacy Quality](https://api.codacy.com/project/badge/Grade/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)
[![Codacy Coverage](https://api.codacy.com/project/badge/Coverage/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)

Acquia Cloud Site Factory is a hosting platform provided by Acquia. The goal of this library is to wrap the ACSF API in such a way that it becomes trivial to leverage it in PHP applications. This library is a significant boon when it comes to streamlining interactions with ACSF. Usage of this library will, among other things, facilitate a much quicker and less error prone deployment process.

**Caution:** Test coverage in this library is around validation of calls to the API, and is not testing interaction with the actual ACSF API. Not all wrapped endpoints have been validated as properly implemented, and live calls to the API may have unexpected results. Review all scripts and calls for proper behavior before executing against a live environment.

## Why use this project?

You may already be familiar with projects such as [ACSF Tools](https://github.com/acquia/acsf-tools) or the [Acquia CLI](https://github.com/acquia/cli) and be wondering what this project has to offer over those.

| Feature                               | ACSF Client | ACSF Tools | Acquia CLI |
| ------------------------------------- | :---------: | :--------: | :--------: |
| PHP library                           |      ✅     |     ❌     |     ❌     |
| Supports Site Factory                 |      ✅     |     ✅     |     ❌     |
| Supports Acquia Cloud                 |      ❌     |     ❌     |     ✅     |
| Complete implementation of ACSF API   |      ✅     |     ❌     |     ❌     |
| Complete implementation of Cloud API  |      ❌     |     ❌     |     ✅     |
| Can bundle with codebase              |      ✅     |     ✅     |     ❌     |
| Can be standalone                     |      ✅     |     ✅     |     ✅     |
| Designed for scripting                |      ✅     |     ❌     |     ❌     |
| Designed for specific tasks           |      ❌     |     ✅     |     ❌     |

The primary use case for this project is creating custom PHP scripts for automating your development and management workflows within ACSF. Anything that you can do within the ACSF UI should be accomplishable through this library.

![Backporting sites to dev demonstration](https://user-images.githubusercontent.com/5890607/66103462-b6579d00-e56a-11e9-88c3-bc10936afb94.gif)

Common tasks that you can use this library to automate:

* Regular complete backups with custom names
* Regular backup pruning for backups meeting certain criteria
* Bbackports/staging to lower environments along with code deploys
* Production deployments, including backups, backports, and deployments
* Recreating domains on lower environments after backports
* Starting a deployment from your CI system

The [examples](examples/) folder contains several scripts that show some common tasks. They can serve as a starting point to build much more complex workflows custom tailored to your project.

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

Many starter scripts are available in the [examples](examples/) folder.

```php
<?php declare(strict_types=1);

  require 'vendor/autoload.php';

  use swichers\Acsf\Client\ClientFactory;
  
  $base_config = [
    'username' => 'example.user',
    'api_key' => 'example.key',
    'site_group' => 'example.group',
    'environment' => 'live',
  ];

  // There are multiple ways to create a client, including from
  // environment variables (recommended). View the ClientFactory
  // class for details.
  $client = ClientFactory::createFromArray($base_config);

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
