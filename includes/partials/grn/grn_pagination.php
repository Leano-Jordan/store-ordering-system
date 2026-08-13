<?php

require_once __DIR__.'/../../auth.php';
require_once __DIR__.'/../../permissions.php';

require_once __DIR__.'/../../logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once __DIR__.'/../../db.php';
