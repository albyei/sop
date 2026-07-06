<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

use Authentication\PasswordHasher\DefaultPasswordHasher;
$hasher = new DefaultPasswordHasher();
$hash = '$2y$10$ZDR//wtskQq3HyifTpSZQOqhkdvTsvE3GAMpKXti1xqwmrRBPBSSS';
echo "Check password123: " . ($hasher->check('password123', $hash) ? 'true' : 'false') . "\n";
