<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
logoutUser();
redirect(SITE_URL . '/');
