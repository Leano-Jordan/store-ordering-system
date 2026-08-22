<?php

declare(strict_types=1);

if (!defined('APP_ENV')) {
    define(
        'APP_ENV',
        getenv('SWIFTORDER_APP_ENV') ?: 'development'
    );
}
