<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Branches Model
 *
 * @property \App\Model\Table\MenusTable&\Cake\ORM\Association\HasMany $Menus
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Branch newEmptyEntity()
 * @method \App\Model\Entity\Branch newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Branch> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Branch get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Branch findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Branch patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Branch> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Branch|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Branch saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Branch>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Branch>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Branch>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Branch> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Branch>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Branch>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Branch>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Branch> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BranchesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('branches');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Menus', [
            'foreignKey' => 'branch_id',
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'branch_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'branch_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('business_type')
            ->maxLength('business_type', 50)
            ->notEmptyString('business_type');

        return $validator;
    }
}
