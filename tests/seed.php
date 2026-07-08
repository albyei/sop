<?php
require __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/backend/config/bootstrap.php';

use Cake\ORM\TableRegistry;

$branchesTable = TableRegistry::getTableLocator()->get('Branches');
$usersTable = TableRegistry::getTableLocator()->get('Users');

// Clear existing
$usersTable->deleteAll([]);
$branchesTable->deleteAll([]);

// Create Branch A
$branchA = $branchesTable->newEntity(['name' => 'Branch A - Downtown Coffee', 'business_type' => 'Cafe']);
$branchesTable->save($branchA);

// Create Branch B
$branchB = $branchesTable->newEntity(['name' => 'Branch B - Uptown Retail', 'business_type' => 'Retail']);
$branchesTable->save($branchB);

// Super Admin
$super = $usersTable->newEntity([
    'branch_id' => $branchA->id,
    'username' => 'superadmin',
    'password' => 'password123',
    'role' => 'SuperAdmin'
]);
$usersTable->save($super);

// Admin Branch A
$adminA = $usersTable->newEntity([
    'branch_id' => $branchA->id,
    'username' => 'adminA',
    'password' => 'password123',
    'role' => 'Admin'
]);
$usersTable->save($adminA);

// Admin Branch B
$adminB = $usersTable->newEntity([
    'branch_id' => $branchB->id,
    'username' => 'adminB',
    'password' => 'password123',
    'role' => 'Admin'
]);
$usersTable->save($adminB);

// Cashier Branch A
$cashierA = $usersTable->newEntity([
    'branch_id' => $branchA->id,
    'username' => 'cashierA',
    'password' => 'password123',
    'role' => 'Cashier'
]);
$usersTable->save($cashierA);

echo "Seed complete!\n";
