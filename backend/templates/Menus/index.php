<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Menu> $menus
 */
?>
<main class="view active" style="display: flex; flex: 1; width: 100%; flex-direction: column; padding: 2rem; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.5rem;">Menu Items</h3>
        <?php if ($this->request->getAttribute('identity')->role === 'admin'): ?>
        <button class="btn btn-pay" style="padding: 0.75rem 1.5rem;" onclick="window.location.href='<?= $this->Url->build(['action' => 'add']) ?>'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Menu
        </button>
        <?php endif; ?>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id', 'No') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('category') ?></th>
                    <th><?= $this->Paginator->sort('price') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                    <th class="actions"><?= __('Action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= $this->Number->format($menu->id) ?></td>
                    <td><strong style="font-weight: 600;"><?= h($menu->name) ?></strong></td>
                    <td><?= h($menu->category) ?></td>
                    <td>Rp <?= $this->Number->format($menu->price) ?></td>
                    <td>
                        <?php if ($menu->is_active): ?>
                            <span class="status-badge status-active">Active</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <div style="display: flex; gap: 0.5rem;">
                            <?php if ($this->request->getAttribute('identity')->role === 'admin'): ?>
                            <button class="btn btn-save" style="padding: 0.5rem 1rem;" onclick="window.location.href='<?= $this->Url->build(['action' => 'edit', $menu->id]) ?>'">Edit</button>
                            <?= $this->Form->postLink(
                                __('Delete'),
                                ['action' => 'delete', $menu->id],
                                [
                                    'confirm' => __('Are you sure you want to delete # {0}?', $menu->id),
                                    'class' => 'btn btn-danger',
                                    'style' => 'padding: 0.5rem 1rem;'
                                ]
                            ) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem;">
        <div><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></div>
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('Previous')) ?>
            <?= $this->Paginator->next(__('Next') . ' >') ?>
        </ul>
    </div>
</main>