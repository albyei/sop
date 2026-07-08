<?php
require __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/backend/config/bootstrap.php';
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use App\Application;
use Cake\Routing\Router;

$app = new Application(__DIR__ . '/backend/config');
$request = new ServerRequest([
    'url' => '/users/login',
    'base' => '/backend',
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
} else {
    echo "Identity: \n";
    print_r($result->getData());
}
