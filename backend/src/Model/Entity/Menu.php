<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Menu Entity
 *
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property int $price
 * @property string|null $category
 * @property bool|null $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Branch $branch
 * @property \App\Model\Entity\TransactionItem[] $transaction_items
 */
class Menu extends Entity
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
        'name' => true,
        'price' => true,
        'category' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'branch' => true,
        'transaction_items' => true,
    ];
}
