<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use App\Application;

$app = new Application(dirname(__DIR__) . '/config');
$request = new ServerRequest([
    'url' => '/users/login',
    'environment' => [
        'REQUEST_METHOD' => 'POST',
    ],
    'post' => [
        'username' => 'superadmin',
        'password' => 'password123',
    ]
]);

$service = $app->getAuthenticationService($request);
$result = $service->authenticate($request);

echo "isValid: " . ($result->isValid() ? 'true' : 'false') . "\n";
if (!$result->isValid()) {
    echo "Errors: \n";
    print_r($result->getErrors());
}
