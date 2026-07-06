<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MenusFixture
 */
class MenusFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'menus';
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
                'name' => 'Lorem ipsum dolor sit amet',
                'price' => 1,
                'category' => 'Lorem ipsum dolor sit amet',
                'is_active' => 1,
                'created' => '2026-06-25 04:56:43',
                'modified' => '2026-06-25 04:56:43',
            ],
        ];
        parent::init();
    }
}
