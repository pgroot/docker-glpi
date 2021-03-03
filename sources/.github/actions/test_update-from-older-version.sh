#!/bin/bash -e

bin/console glpi:database:configure \
  --config-dir=./tests --ansi --no-interaction \
  --reconfigure --db-name=glpitest0723 --db-host=db --db-user=root

# Execute update
## First run should do the migration.
bin/console glpi:database:update --config-dir=./tests --ansi --no-interaction --allow-unstable | tee ~/migration.log
if [[ -n $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:database:update command FAILED" && exit 1;
fi
## Second run should do nothing.
bin/console glpi:database:update --config-dir=./tests --ansi --no-interaction --allow-unstable | tee ~/migration.log
if [[ -z $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:database:update command FAILED" && exit 1;
fi

# Execute myisam_to_innodb migration
## First run should do the migration.
bin/console glpi:migration:myisam_to_innodb --config-dir=./tests --ansi --no-interaction | tee ~/migration.log
if [[ -n $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:migration:myisam_to_innodb command FAILED" && exit 1;
fi
## Second run should do nothing.
bin/console glpi:migration:myisam_to_innodb --config-dir=./tests --ansi --no-interaction | tee ~/migration.log
if [[ -z $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:migration:myisam_to_innodb command FAILED" && exit 1;
fi

# Execute timestamps migration
## First run should do the migration.
bin/console glpi:migration:timestamps --config-dir=./tests --ansi --no-interaction | tee ~/migration.log
if [[ -n $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:migration:timestamps command FAILED" && exit 1;
fi
## Second run should do nothing.
bin/console glpi:migration:timestamps --config-dir=./tests --ansi --no-interaction | tee ~/migration.log
if [[ -z $(grep "No migration needed." ~/migration.log) ]];
  then echo "bin/console glpi:migration:timestamps command FAILED" && exit 1;
fi

# Test that updated DB has same schema as newly installed DB
bin/console glpi:database:configure \
  --config-dir=./tests --no-interaction \
  --reconfigure --db-name=glpi --db-host=db --db-user=root
vendor/bin/atoum \
  -p 'php -d memory_limit=512M' \
  --debug \
  --force-terminal \
  --use-dot-report \
  --bootstrap-file tests/bootstrap.php \
  --no-code-coverage \
  --fail-if-skipped-methods \
  --max-children-number 1 \
  -d tests/database
