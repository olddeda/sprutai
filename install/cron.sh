#!/usr/bin/env bash

crontab -r
(crontab -l 2>/dev/null; echo "@daily /usr/bin/php7.2 /var/www/admin/data/www/sprut.ai/yii audit/cleanup --interactive=0 --age=3 --entry") | crontab -
(crontab -l 2>/dev/null; echo "* * * * * /usr/bin/php7.2 /var/www/admin/data/www/sprut.ai/yii social/cron/index") | crontab -
(crontab -l 2>/dev/null; echo "* * * * * /usr/bin/php7.2 /var/www/admin/data/www/sprut.ai/yiiuser/cron/telegram-username-update") | crontab -
(crontab -l 2>/dev/null; echo "* * * * * /usr/bin/php7.2 /var/www/admin/data/www/sprut.ai/yiiuser/cron/github-username-update") | crontab -