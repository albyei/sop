<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BranchesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BranchesTable Test Case
 */
class BranchesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BranchesTable
     */
    protected $Branches;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Branches',
        'app.Menus',
        'app.Transactions',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Branches') ? [] : ['className' => BranchesTable::class];
        $this->Branches = $this->getTableLocator()->get('Branches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Branches);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\BranchesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
