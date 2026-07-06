<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TransactionItem Entity
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $menu_id
 * @property int $qty
 * @property int $price
 * @property int $subtotal
 *
 * @property \App\Model\Entity\Transaction $transaction
 * @property \App\Model\Entity\Menu $menu
 */
class TransactionItem extends Entity
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
        'transaction_id' => true,
        'menu_id' => true,
        'qty' => true,
        'price' => true,
        'subtotal' => true,
        'transaction' => true,
        'menu' => true,
    ];
}
