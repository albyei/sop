<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TransactionItem $transactionItem
 * @var \Cake\Collection\CollectionInterface|string[] $transactions
 * @var \Cake\Collection\CollectionInterface|string[] $menus
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Transaction Items'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="transactionItems form content">
            <?= $this->Form->create($transactionItem) ?>
            <fieldset>
                <legend><?= __('Add Transaction Item') ?></legend>
                <?php
                    echo $this->Form->control('transaction_id', ['options' => $transactions]);
                    echo $this->Form->control('menu_id', ['options' => $menus]);
                    echo $this->Form->control('qty');
                    echo $this->Form->control('price');
                    echo $this->Form->control('subtotal');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
