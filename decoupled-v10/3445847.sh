#!/usr/bin/env bash

# Issue https://www.drupal.org/project/drupal/issues/3445847
# MR: https://git.drupalcode.org/project/drupal/-/merge_requests/7952#re-install-via-create-project

rm -rf docroot
composer create-project drupal/recommended-project:11.0.x-dev@dev docroot
cd docroot

composer require cweagans/composer-patches:dev-main
cp decoupled-v10/patches.lock.json .
composer prl && composer prp

composer show --format=json drupal/core-recommended \
| jq '@sh "name: \(.name), version: \(.versions[0]),  hash: \(.dist.reference)"'


export SIMPLETEST_BASE_URL="https://d10ee.lndo.site"
export SIMPLETEST_DB="mysql://drupalX:drupalX@database:3306/drupal10_simpletest"

vendor/bin/phpunit \
--testdox \
--no-coverage \
--bootstrap web/core/tests/bootstrap.php \
--configuration web/core/phpunit.xml.dist \
--display-errors \
--do-not-cache-result \
--group phpunit10 \
web/modules/contrib/musica/tests
