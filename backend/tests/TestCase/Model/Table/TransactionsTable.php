<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransactionsTable;
    use Cake\TestSuite\TestCase;
    use PHPUnit\Framework\Attributes\Test;

    /**
     * App\Model\Table\TransactionsTable Test Case
     */
    class TransactionsTableTest extends TestCase
    {
        /**
         * Test subject
         *
         * @var \App\Model\Table\TransactionsTable
         */
        protected $Transactions;

        /**
         * Fixtures yang dibutuhkan (Dependencies Database)
         * Ini penting agar foreign key di buildRules bisa berjalan.
         *
         * @var list<string>
         */
        protected array $fixtures = [
            'app.Transactions',
            'app.Branches',
            'app.Users',
            'app.TransactionItems',
        ];

        /**
         * setUp method: Bagian dari fase ARRANGE otomatis sebelum setiap test berjalan.
         *
         * @return void
         */
        protected function setUp(): void
        {
            parent::setUp();
            $config = $this->getTableLocator()->exists('Transactions') ? [] : ['className' => TransactionsTable::class];
            $this->Transactions = $this->getTableLocator()->get('Transactions', $config);
        }

        /**
         * tearDown method: Membersihkan memory setelah test selesai.
         *
         * @return void
         */
        protected function tearDown(): void
        {
            unset($this->Transactions);
            parent::tearDown();
        }

        #[Test]
        public function testValidationDefaultSuccess(): void
        {
            // 1. ARRANGE: Siapkan data yang 100% valid
            $data = [
                'branch_id' => 1,
                'user_id' => 1,
                'customer_name' => 'Budi Santoso',
                'status' => 'completed',
                'total' => 150000,
                'paid_amount' => 200000,
                'change_amount' => 50000,
            ];

            // 2. ACT: Buat entitas (akan memicu validationDefault)
            $entity = $this->Transactions->newEntity($data);

            // 3. ASSERT
            // Penjelasan: Kita berharap tidak ada error validasi di entitas ini.
            // assertEmpty memverifikasi bahwa array errors yang dikembalikan kosong.
            $this->assertEmpty($entity->getErrors(), 'Entitas seharusnya valid dan tidak mengembalikan error.');
        }

        #[Test]
        public function testValidationDefaultMissingRequiredFields(): void
        {
            // 1. ARRANGE: Siapkan data kosong
            $data = [];

            // 2. ACT
            $entity = $this->Transactions->newEntity($data);
            $errors = $entity->getErrors();

            // 3. ASSERT
            // Penjelasan: assertArrayHasKey memverifikasi field tertentu dilaporkan sebagai error.
            $this->assertArrayHasKey('branch_id', $errors);
            $this->assertArrayHasKey('user_id', $errors);
            $this->assertArrayHasKey('total', $errors);
        }

        #[Test]
        public function testValidationCustomerNameMaxLength(): void
        {
            // 1. ARRANGE: Buat string > 255 karakter
            $data = [
                'total' => 100, // Harus ada agar tidak error di field ini
                'customer_name' => str_repeat('a', 256)
            ];

            // 2. ACT
            $entity = $this->Transactions->newEntity($data);
            $errors = $entity->getErrors();

            // 3. ASSERT
            $this->assertArrayHasKey('customer_name', $errors);
            // Memastikan errornya spesifik terkait max length
            $this->assertTrue(isset($errors['customer_name']['maxLength']));
        }

        #[Test]
        public function testBuildRulesSuccess(): void
        {
            // 1. ARRANGE: Asumsikan di fixture ada branch_id = 1 dan user_id = 1
            $data = [
                'branch_id' => 1,
                'user_id' => 1,
                'total' => 10000
            ];
            $entity = $this->Transactions->newEntity($data);

            // 2. ACT: save() akan memicu buildRules()
            $result = $this->Transactions->save($entity);

            // 3. ASSERT
            // Penjelasan: assertNotFalse memastikan proses save berhasil mengembalikan objek entitas (bukan boolean false).
            $this->assertNotFalse($result, 'Data seharusnya berhasil disimpan karena aturan terpenuhi.');
        }

        #[Test]
        public function testBuildRulesFailInvalidForeignKeys(): void
        {
            // 1. ARRANGE: Gunakan ID fiktif yang pasti tidak ada di tabel Branches/Users
            $data = [
                'branch_id' => 9999,
                'user_id' => 9999,
                'total' => 10000
            ];
            $entity = $this->Transactions->newEntity($data);

            // 2. ACT
            $result = $this->Transactions->save($entity);
            $errors = $entity->getErrors();

            // 3. ASSERT
            // Penjelasan: assertFalse karena gagal simpan, dan rules check mengembalikan error pada field terkait.
            $this->assertFalse($result);
            $this->assertArrayHasKey('branch_id', $errors);
            $this->assertArrayHasKey('user_id', $errors);
        }
    }