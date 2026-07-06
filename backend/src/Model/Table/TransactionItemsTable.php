<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TransactionItems Model
 *
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\BelongsTo $Transactions
 * @property \App\Model\Table\MenusTable&\Cake\ORM\Association\BelongsTo $Menus
 *
 * @method \App\Model\Entity\TransactionItem newEmptyEntity()
 * @method \App\Model\Entity\TransactionItem newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TransactionItem> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TransactionItem get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TransactionItem findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TransactionItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TransactionItem> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TransactionItem|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TransactionItem saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TransactionItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TransactionItem>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TransactionItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TransactionItem> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TransactionItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TransactionItem>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TransactionItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TransactionItem> deleteManyOrFail(iterable $entities, array $options = [])
 */
class TransactionItemsTable extends Table
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

        $this->setTable('transaction_items');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Transactions', [
            'foreignKey' => 'transaction_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Menus', [
            'foreignKey' => 'menu_id',
            'joinType' => 'INNER',
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
            ->integer('transaction_id')
            ->notEmptyString('transaction_id');

        $validator
            ->integer('menu_id')
            ->notEmptyString('menu_id');

        $validator
            ->integer('qty')
            ->requirePresence('qty', 'create')
            ->notEmptyString('qty');

        $validator
            ->integer('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->integer('subtotal')
            ->requirePresence('subtotal', 'create')
            ->notEmptyString('subtotal');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['transaction_id'], 'Transactions'), ['errorField' => 'transaction_id']);
        $rules->add($rules->existsIn(['menu_id'], 'Menus'), ['errorField' => 'menu_id']);

        return $rules;
    }
}
