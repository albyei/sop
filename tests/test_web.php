<?php
require __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/backend/config/bootstrap.php';

use Cake\ORM\TableRegistry;
use Authentication\PasswordHasher\DefaultPasswordHasher;

$users = TableRegistry::getTableLocator()->get('Users');
$user = $users->find()->where(['username' => 'superadmin'])->first();

if (!$user) {
    echo "User superadmin not found!<br>";
    exit;
}

echo "User found: " . $user->username . "<br>";
echo "Password hash in DB: " . $user->password . "<br>";

$hasher = new DefaultPasswordHasher();
$isValid = $hasher->check('password123', $user->password);

echo "Is password123 valid? " . ($isValid ? 'YES' : 'NO') . "<br>";
