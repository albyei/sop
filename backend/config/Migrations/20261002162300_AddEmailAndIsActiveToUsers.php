<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddEmailAndIsActiveToUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('email', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('is_active', 'boolean', [
            'default' => true,
            'null' => false,
        ]);
        $table->addIndex(['email'], ['unique' => true]);
        $table->update();
    }
}
