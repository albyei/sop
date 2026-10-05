Tolong lakukan security audit dan refactoring pada codebase CakePHP saya berdasarkan 5 pilar keamanan berikut:
1. SQL Injection (Gunakan ORM Query Builder aman, hindari string concatenation di where/raw SQL).
2. XSS Protection (Pastikan variabel output di-escape atau disanitasi).
3. CSRF Protection (Pastikan CSRF Middleware aktif dan FormHelper/AJAX Token terpasang).
4. Session & Cookie Security (Pastikan config cookie_httponly, cookie_secure, cookie_samesite 'Lax', dan use_strict_mode diset dengan benar).
5. Password & Mass Assignment Security (Gunakan DefaultPasswordHasher pada Setter Entity dan batasi $_accessible agar field sensitif seperti role/is_admin tidak lolos).

Untuk menghemat token, fokus periksa dan perbaiki HANYA file-file pada lokasi berikut:

1. Konfigurasi Sesi & Security Middleware:
   - config/app.php
   - src/Application.php

2. Model, Entity, & Hashing (Periksa $_accessible dan _setPassword):
   - src/Model/Entity/User.php (atau Entity utama aplikasi Anda)
   - src/Model/Table/UsersTable.php

3. Controller (Periksa potensi Raw SQL / SQLi / Handling Input):
   - src/Controller/UsersController.php
   - src/Controller/AppController.php

4. Templates/Views (Periksa potensi Unescaped Output / XSS):
   - templates/Users/login.php
   - templates/Users/add.php
   - templates/Users/index.php

Tolong tunjukkan kode mana yang rentan, jelaskan alasannya singkat, dan berikan kode perbaikannya secara presisi.