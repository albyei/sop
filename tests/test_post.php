<?php
$url = 'http://cashier.test/backend/users/login';
$data = ['username' => 'superadmin', 'password' => 'password123'];
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "Response headers:\n";
print_r($http_response_header);
echo "\nBody:\n";
echo substr(strip_tags($result), 0, 500); // output snippet of body to avoid huge payload
