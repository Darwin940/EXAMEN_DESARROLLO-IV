<?php
$container->set('config_bd', function() {
    $password = $_ENV['DB_PASSW'] ?? '12345';
    if (isset($_ENV['DB_PASSWORD_FILE']) && file_exists($_ENV['DB_PASSWORD_FILE'])) {
        $password = trim(file_get_contents($_ENV['DB_PASSWORD_FILE']));
    }
    return (object) [
        'host' => $_ENV['DB_HOST'],
        'username' => $_ENV['DB_USER'],
        'password' => $password,
        'database' => $_ENV['DB_NAME'],
        'charset' =>  'utf8mb4'
    ];
});