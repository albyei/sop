<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 * @var string[]|\Cake\Collection\CollectionInterface $branches
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<main class="view active" style="display: flex; flex: 1; width: 100%; flex-direction: column; padding: 2rem; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.5rem;">Edit Transaction #<?= h($transaction->id) ?></h3>
        <button class="btn btn-save" onclick="window.location.href='<?= $this->Url->build(['action' => 'index']) ?>'">Back</button>
    </div>
    
    <div style="background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); padding: 2rem; max-width: 600px; margin: 0 auto; box-shadow: var(--shadow-soft);">
        <?= $this->Form->create($transaction) ?>
        <div class="form-group">
            <label>Customer Name</label>
            <?= $this->Form->control('customer_name', ['label' => false, 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        <div class="form-group">
            <label>Status</label>
            <?= $this->Form->control('status', ['label' => false, 'options' => ['pending' => 'Pending', 'paid' => 'Paid'], 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        <div class="form-group">
            <label>Total (Rp)</label>
            <?= $this->Form->control('total', ['label' => false, 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        <div class="form-group">
            <label>Paid Amount (Rp)</label>
            <?= $this->Form->control('paid_amount', ['label' => false, 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        <div class="form-group">
            <label>Change Amount (Rp)</label>
            <?= $this->Form->control('change_amount', ['label' => false, 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        
        <?php if ($this->request->getAttribute('identity')->role === 'superadmin'): ?>
        <div class="form-group">
            <label>Branch</label>
            <?= $this->Form->control('branch_id', ['options' => $branches, 'label' => false, 'class' => 'form-control', 'style' => 'width: 100%;']) ?>
        </div>
        <?php endif; ?>

        <div style="margin-top: 2rem;">
            <?= $this->Form->button('Save Changes', ['class' => 'btn btn-pay', 'style' => 'width: 100%;']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</main>
