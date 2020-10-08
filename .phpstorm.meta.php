<?php

namespace PHPSTORM_META {

  expectedArguments(
    \swichers\Acsf\Client\ClientInterface::getAction(),
    0,
    'Audit',
    'Collections',
    'Groups',
    'PageView',
    'Roles',
    'Sites',
    'SslCertificates',
    'Stacks',
    'Stage',
    'Status',
    'Tasks',
    'Theme',
    'Update',
    'Variables',
    'Vcs'
  );

  override(
    \swichers\Acsf\Client\ClientInterface::getAction(0),
    map(
      [
        'Audit' => \swichers\Acsf\Client\Endpoints\Action\Audit::class,
        'Collections' => \swichers\Acsf\Client\Endpoints\Action\Collections::class,
        'Groups' => \swichers\Acsf\Client\Endpoints\Action\Groups::class,
        'PageView' => \swichers\Acsf\Client\Endpoints\Action\PageView::class,
        'Roles' => \swichers\Acsf\Client\Endpoints\Action\Roles::class,
        'Sites' => \swichers\Acsf\Client\Endpoints\Action\Sites::class,
        'SslCertificates' => \swichers\Acsf\Client\Endpoints\Action\SslCertificates::class,
        'Stacks' => \swichers\Acsf\Client\Endpoints\Action\Stacks::class,
        'Stage' => \swichers\Acsf\Client\Endpoints\Action\Stage::class,
        'Status' => \swichers\Acsf\Client\Endpoints\Action\Status::class,
        'Tasks' => \swichers\Acsf\Client\Endpoints\Action\Tasks::class,
        'Theme' => \swichers\Acsf\Client\Endpoints\Action\Theme::class,
        'Update' => \swichers\Acsf\Client\Endpoints\Action\Update::class,
        'Variables' => \swichers\Acsf\Client\Endpoints\Action\Variables::class,
        'Vcs' => \swichers\Acsf\Client\Endpoints\Action\Vcs::class,
        '' => '@',
      ]
    )
  );

  expectedArguments(
    \swichers\Acsf\Client\ClientInterface::getEntity(),
    0,
    'Backup',
    'Collection',
    'Group',
    'Role',
    'Site',
    'Task',
    'Update'
  );

  override(
    \swichers\Acsf\Client\ClientInterface::getEntity(0),
    map(
      [
        'Backup' => \swichers\Acsf\Client\Endpoints\Entity\Backup::class,
        'Collection' => \swichers\Acsf\Client\Endpoints\Entity\Collection::class,
        'Group' => \swichers\Acsf\Client\Endpoints\Entity\Group::class,
        'Role' => \swichers\Acsf\Client\Endpoints\Entity\Role::class,
        'Site' => \swichers\Acsf\Client\Endpoints\Entity\Site::class,
        'Task' => \swichers\Acsf\Client\Endpoints\Entity\Task::class,
        'Update' => \swichers\Acsf\Client\Endpoints\Entity\Update::class,
        '' => '@',
      ]
    )
  );

}
