<?php

require_once __DIR__.'/../config.php';
require_once dirname(__DIR__, 3).'/swiftorder-config.php';

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if ($conn->connect_error) {
    error_log('SwiftOrder database connection failed: '.$conn->connect_error);
    exit('Database connection failed. Please contact the administrator.');
}

if (!$conn->set_charset('utf8mb4')) {
    error_log(
        'SwiftOrder database charset configuration failed: '
        .$conn->error
    );

    $conn->close();

    exit(
        'Database configuration failed. Please contact the administrator.'
    );
}
