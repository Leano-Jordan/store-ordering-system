<?php

require_once 'includes/auth.php';
require_once 'includes/permissions.php';
require_once 'includes/logger.php';
requireRole([ROLE_ADMIN, ROLE_MANAGER]);

require_once 'includes/db.php';
