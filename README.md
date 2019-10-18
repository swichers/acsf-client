# Acquia Cloud Site Factory Client Library

> A Symfony-based PHP library for working with the ACSF platform.

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.com/swichers/acsf-client.svg?token=Lm6gQQWsBsnzoGah2JXY&branch=master)](https://travis-ci.com/swichers/acsf-client)
[![Codacy Quality](https://api.codacy.com/project/badge/Grade/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)
[![Codacy Coverage](https://api.codacy.com/project/badge/Coverage/f8600bde4f684cf98b8255c814d753c3)](https://www.codacy.com/manual/swichers/acsf-client)

Acquia Cloud Site Factory is a hosting platform provided by Acquia. As part of that platform an API is provided for developers to use. The goal of this library is to wrap that API in such a way that it becomes trivial to leverage it in PHP applications.

![Backporting sites to dev demonstration](https://user-images.githubusercontent.com/5890607/66103462-b6579d00-e56a-11e9-88c3-bc10936afb94.gif)

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

Example scripts are available in the [examples](examples/) folder.

```php
<?php declare(strict_types=1);

  require 'vendor/autoload.php';

  use swichers\Acsf\Client\ServiceLoader;
  
  $base_config = [
    'username' => 'example.user',
    'api_key' => 'example.key',
    'site_group' => 'example.group',
    'environment' => 'live',
  ];

  $client = ServiceLoader::buildFromConfig(['acsf.client.connection' => $base_config])
    ->get('acsf.client');

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
