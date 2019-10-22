# Library Usage Examples

This folder contains some examples of how to use the library to script ACSF 
deployments and associated tasks. To use these examples rename the
`example.config.php` file to `config.php` and add your ACSF credentials.

```sh
cp example.config.php config.php
```

## Files

### backport.php

Usage: `php backport.php test tags/2.7.0-beta.1-build`

Copies the production environment down to the given environment, and then
deploys the tag or branch provided.

### backup.php

Usage: `php backup.php live`

Creates a backup of each site on the given environment.

### deploy.php

Usage: `php deploy.php live tags/2.7.0-beta.1-build`

Deploys a new tag or branch to the target environment. Creates a backup in
the process if the target environment is `live`.

### deploy-uat.php

Usage: `php deploy-uat.php tags/2.7.0-beta.1-build`

Deploys a new tag or branch to the UAT environment. Backports production first.

### redeploy.php

Usage: `php redeploy.php dev`

Redeploys the currently checked out code on top of itself.

### simple.php

A simple script to demonstrate how to backport and deploy code with minimal
options set.
