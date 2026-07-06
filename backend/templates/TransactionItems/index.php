<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TransactionItem> $transactionItems
 */
?>
<div class="transactionItems index content">
    <?= $this->Html->link(__('New Transaction Item'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Transaction Items') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('transaction_id') ?></th>
                    <th><?= $this->Paginator->sort('menu_id') ?></th>
                    <th><?= $this->Paginator->sort('qty') ?></th>
                    <th><?= $this->Paginator->sort('price') ?></th>
                    <th><?= $this->Paginator->sort('subtotal') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactionItems as $transactionItem): ?>
                <tr>
                    <td><?= $this->Number->format($transactionItem->id) ?></td>
                    <td><?= $transactionItem->hasValue('transaction') ? $this->Html->link($transactionItem->transaction->id, ['controller' => 'Transactions', 'action' => 'view', $transactionItem->transaction->id]) : '' ?></td>
                    <td><?= $transactionItem->hasValue('menu') ? $this->Html->link($transactionItem->menu->name, ['controller' => 'Menus', 'action' => 'view', $transactionItem->menu->id]) : '' ?></td>
                    <td><?= $this->Number->format($transactionItem->qty) ?></td>
                    <td><?= $this->Number->format($transactionItem->price) ?></td>
                    <td><?= $this->Number->format($transactionItem->subtotal) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $transactionItem->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $transactionItem->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $transactionItem->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $transactionItem->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>