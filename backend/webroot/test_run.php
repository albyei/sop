<?php
$output = shell_exec('php ' . escapeshellarg('C:/laragon/www/cashier/test_auth.php') . ' 2>&1');
file_put_contents('C:/laragon/www/cashier/test_auth_out.txt', $output);
echo "Done";
