# Library Usage Examples

This folder contains some examples of how to use the library to script ACSF deployments and associated tasks. To use
these examples copy the `.env.dist` file to `.env` in this folder. Then edit and configure each value in that file.

```sh
cp ../.env.dist .env
```

The provided scripts allow you to perform many of the common tasks you might need right out of the box, but the real
power in this library comes from using these scripts as a starting point for your own more complex scripts.

## Examples provided

### backport.php

Usage: `php backport.php test tags/2.7.0-beta.1-build`

Copies the production environment down to the given environment, and then deploys the tag or branch provided.

### backups-create.php

Usage: `php backups-create.php live database`

Creates a database backup of each site on the given environment.

### backups-prune.php

Usage: `php backups-prune.php live 14`

Delete backups older than the given number of days.

### cc.php

Usage: `php cc.php uat`

Runs the ACSF cache clear process on the given environment.

### deploy.php

Usage: `php deploy.php live tags/2.7.0-beta.1-build`

Deploys a new tag or branch to the target environment.

### prod-release.php

Usage: `php prod-release.php example tags/2.7.0-build tags/2.7.0-beta.1-build`

Perform a production release. Deploys code to both the production and UAT environments after creating a backup. Shows
how to combine the example scripts to perform complex tasks.

### redeploy.php

Usage: `php redeploy.php dev`

Redeploys the currently checked out code on top of itself.

### simple.php

A simple script to demonstrate how to backport and deploy code with minimal options set.
