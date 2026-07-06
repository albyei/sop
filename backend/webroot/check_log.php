<?php
$log = file_get_contents(dirname(__DIR__) . '/logs/error.log');
preg_match_all('/Login URL.*?did not match.*/i', $log, $matches);
print_r($matches);
