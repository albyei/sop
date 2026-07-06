<?php
echo password_verify('password', '$2y$10$c6ZHFFBJLRfBPqd0w/1h4eJSaMgfnbOK11rgcY2u6bSAH7ApG.Usq') ? 'YES' : 'NO';
echo "\n";
echo password_verify('admin', '$2y$10$c6ZHFFBJLRfBPqd0w/1h4eJSaMgfnbOK11rgcY2u6bSAH7ApG.Usq') ? 'YES' : 'NO';
echo "\n";
echo password_verify('123456', '$2y$10$c6ZHFFBJLRfBPqd0w/1h4eJSaMgfnbOK11rgcY2u6bSAH7ApG.Usq') ? 'YES' : 'NO';
