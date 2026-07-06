<?php
$html = file_get_contents("http://localhost/cashier/backend/users/login");
file_put_contents(dirname(__DIR__) . "/logs/login_html.txt", $html);
echo "Done";
