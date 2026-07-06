<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionItemsFixture
 */
class TransactionItemsFixture extends TestFixture
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
                'transaction_id' => 1,
                'menu_id' => 1,
                'qty' => 1,
                'price' => 1,
                'subtotal' => 1,
            ],
        ];
        parent::init();
    }
}
