<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BranchesFixture
 */
class BranchesFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'business_type' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-06-25 04:56:41',
                'modified' => '2026-06-25 04:56:41',
            ],
        ];
        parent::init();
    }
}
