#!/usr/bin/env bash

php ../yii migrate/up
php ../yii migrate/up --migrationPath=@common/modules/user/migrations

sh ./rbac.sh