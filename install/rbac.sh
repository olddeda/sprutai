#!/usr/bin/env bash

echo "----------------------------------------------"
echo "Run rights:"
echo "----------------------------------------------"
php yii base/roles/add
php yii base/rbac-audit/add
php yii user/rbac/add
php yii media/rbac/add
php yii lookup/rbac/add
php yii settings/rbac/add
php yii tag/rbac/add
php yii content/rbac/add
php yii comments/rbac/add