<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Menu $menu
 * @var \Cake\Collection\CollectionInterface|string[] $branches
 */
?>
<main class="view active" style="flex-direction: column; padding: 2rem; overflow-y: auto; align-items: center;">
    <div style="width: 100%; max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.5rem; color: var(--text-main);">Add Menu</h3>
            <?= $this->Html->link(__('Back to Menus'), ['action' => 'index'], ['class' => 'btn btn-save', 'style' => 'padding: 0.5rem 1rem; text-decoration: none;']) ?>
        </div>

        <div style="background: var(--surface); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
            <?= $this->Form->create($menu) ?>
            
            <div class="form-group">
                <label>Branch</label>
                <?= $this->Form->control('branch_id', ['options' => $branches, 'label' => false, 'class' => 'form-control']) ?>
            </div>
            
            <div class="form-group">
                <label>Menu Name</label>
                <?= $this->Form->control('name', ['label' => false, 'placeholder' => 'e.g. Espresso', 'class' => 'form-control', 'required' => true]) ?>
            </div>
            
            <div class="form-group">
                <label>Price (Rp)</label>
                <?= $this->Form->control('price', ['type' => 'text', 'id' => 'price-input', 'label' => false, 'placeholder' => '0', 'class' => 'form-control', 'required' => true]) ?>
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <?= $this->Form->control('category', ['label' => false, 'placeholder' => 'e.g. Coffee', 'class' => 'form-control']) ?>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <?= $this->Form->control('is_active', ['label' => false, 'type' => 'checkbox']) ?>
                <label style="margin: 0;">Active Status</label>
            </div>
            
            <button type="submit" class="btn btn-pay" style="width: 100%; margin-top: 1.5rem;">Save Menu</button>
            <?= $this->Form->end() ?>
        </div>
    </div>
</main>

<script>
    // format money dynamically
    const priceInput = document.getElementById('price-input');
    priceInput.addEventListener('input', function(e) {
        let val = e.target.value.replace(/[^0-9]/g, '');
        if (val) {
            e.target.value = new Intl.NumberFormat('id-ID').format(val);
        } else {
            e.target.value = '';
        }
    });
</script>
