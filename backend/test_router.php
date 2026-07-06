<?php
require 'vendor/autoload.php';
// We need to bootstrap CakePHP to test the router and authentication
require 'config/bootstrap.php';
use Cake\Routing\Router;

echo "Router::url: " . Router::url(['controller' => 'Users', 'action' => 'login', 'plugin' => null]) . "\n";
