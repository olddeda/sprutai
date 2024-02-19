<?php

$conn = mysqli_init();
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
$conn->ssl_set(NULL, NULL, __DIR__.'/root.crt', NULL, NULL);
$conn->real_connect('rc1c-1idmrsjkdklxgd7u.mdb.yandexcloud.net', 'sprut', 'P3h1W1j8', 'dev_db', 3306, NULL, MYSQLI_CLIENT_SSL);

print_r($conn);die;

