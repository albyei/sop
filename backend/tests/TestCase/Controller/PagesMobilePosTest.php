<?php
    declare(strict_types=1);

    namespace App\Test\TestCase\Controller;

    use Cake\TestSuite\IntegrationTestTrait;
    use Cake\TestSuite\TestCase;
    use Cake\Core\Configure;

    class PagesMobilePosTest extends TestCase
    {
        use IntegrationTestTrait;

        protected array $fixtures = [
            // 'app.Users', // Uncomment jika suatu saat menggunakan fixture table
        ];

        /**
         * SETUP: Dipanggil sebelum setiap test case berjalan
         */
        public function setUp(): void
        {
            parent::setUp();

            // SIMULASI LOGIN: 
            // Menulis data user ke session agar AuthenticationMiddleware
            // menganggap kita sudah login dan tidak me-redirect (302) ke halaman login.
            $this->session([
                'Auth' => [
                    'id' => 1,
                    'username' => 'cashier_test',
                    'role' => 'cashier', // Mengikuti role dari AppController
                    'branch_id' => 1
                ]
            ]);
        }

        /**
         * [SUCCESS PATH]
         */
        public function testMobilePosPageLoadSuccess(): void
        {
            $this->enableCsrfToken();

            $this->get('/pages/mobile_pos');

            // Sekarang harusnya berhasil return 200 OK karena sudah login
            $this->assertResponseOk();

            // Validasi elemen dirender
            $this->assertResponseContains('<title>Warung Kita — POS</title>');
            $this->assertResponseContains('window.POS_CONFIG = {');
            $this->assertResponseContains('id="menuList"');
            $this->assertResponseContains('id="paymentDrawer"');
        }

        /**
         * [FAILURE/EDGE CASE]
         */
        public function testMobilePosCsrfProtectionEnforced(): void
        {
            // Tetap gagal 403 Forbidden / Error walau sudah login karena CSRF tidak valid/tidak dikirim
            $this->post('/pages/mobile_pos', ['test' => 'data']);
            $this->assertResponseCode(403);
        }

        /**
         * [FAILURE/EDGE CASE]
         */
        public function testMobilePosMissingTemplate(): void
        {
            Configure::write('debug', false);

            // Memanggil template yang tidak ada
            $this->get('/pages/mobile_poss');

            // Karena sudah login, middleware akan membiarkan lewat,
            // lalu Controller akan melemparkan Error MissingTemplate (404/500).
            $this->assertResponseError();
        }
    }