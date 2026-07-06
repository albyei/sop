<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionsFixture
 */
class TransactionsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'branch_id' => 1,
                'user_id' => 1,
                'customer_name' => 'Lorem ipsum dolor sit amet',
                'status' => 'Lorem ipsum dolor sit amet',
                'total' => 1,
                'paid_amount' => 1,
                'change_amount' => 1,
                'created' => '2026-06-25 04:56:44',
                'modified' => '2026-06-25 04:56:44',
            ],
        ];
        parent::init();
    }
}
