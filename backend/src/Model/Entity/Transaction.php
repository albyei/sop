<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $branch_id
 * @property int $user_id
 * @property string|null $customer_name
 * @property string|null $status
 * @property int $total
 * @property int|null $paid_amount
 * @property int|null $change_amount
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Branch $branch
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\TransactionItem[] $transaction_items
 */
class Transaction extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'branch_id' => true,
        'user_id' => true,
        'customer_name' => true,
        'status' => true,
        'total' => true,
        'paid_amount' => true,
        'change_amount' => true,
        'created' => true,
        'modified' => true,
        'branch' => true,
        'user' => true,
        'transaction_items' => true,
    ];
}
