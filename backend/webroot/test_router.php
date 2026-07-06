<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';
use Cake\Routing\Router;

echo "Router::url: " . Router::url(['controller' => 'Users', 'action' => 'login', 'plugin' => null]) . "\n";
