<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/cashier/backend/users/login");
curl_setopt($ch, CURLOPT_POST, 1);
// Note: CakePHP 4+ requires CSRF token. Since CSRF is enabled, a direct POST will fail with Missing CSRF Token.
// We can just bypass CSRF for this test or do a GET to the login page first to get the token.
// Actually, let me just check the login URL manually by reading the code.
