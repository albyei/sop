<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */
?>
<main class="view active" style="display: flex; flex: 1; width: 100%; flex-direction: column; padding: 2rem; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 style="font-size: 1.5rem;">Transaction #<?= h($transaction->id) ?></h3>
            <p style="color: var(--text-muted); margin-top: 4px;">
                <?= h($transaction->created->format('d/m/Y H:i')) ?>
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-save" onclick="window.location.href='<?= $this->Url->build(['action' => 'index']) ?>'">Back</button>
            <?php if ($transaction->status !== 'paid'): ?>
            <button class="btn btn-pay" onclick="window.location.href='<?= $this->Url->build(['action' => 'edit', $transaction->id]) ?>'">Process Payment</button>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
        <div class="stats-card" style="flex: 1;">
            <div class="stats-title">Customer</div>
            <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-main);"><?= h($transaction->customer_name) ?></div>
        </div>
        <div class="stats-card" style="flex: 1;">
            <div class="stats-title">Status</div>
            <div style="margin-top: 8px;">
                <?php if ($transaction->status === 'paid'): ?>
                    <span class="status-badge status-paid" style="font-size: 1rem; padding: 6px 12px;">Paid</span>
                <?php else: ?>
                    <span class="status-badge status-pending" style="font-size: 1rem; padding: 6px 12px;">Pending</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="stats-card" style="flex: 1;">
            <div class="stats-title">Total</div>
            <div class="stats-value">Rp <?= $this->Number->format($transaction->total) ?></div>
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transaction->transaction_items)) : ?>
                    <?php foreach ($transaction->transaction_items as $item) : ?>
                    <tr>
                        <td><strong style="font-weight: 600;"><?= h($item->hasValue('menu') ? $item->menu->name : 'Menu ID: ' . $item->menu_id) ?></strong></td>
                        <td style="text-align: center; font-weight: 600;"><?= h($item->qty) ?></td>
                        <td style="text-align: right;">Rp <?= $this->Number->format($item->price) ?></td>
                        <td style="text-align: right; font-weight: 600;">Rp <?= $this->Number->format($item->subtotal) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($transaction->status === 'paid'): ?>
    <div style="background: var(--bg-surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 1.5rem; margin-top: 24px; max-width: 400px; margin-left: auto;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="color: var(--text-muted);">Total</span>
            <span style="font-weight: 600;">Rp <?= $this->Number->format($transaction->total) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="color: var(--text-muted);">Paid Amount</span>
            <span style="font-weight: 600;">Rp <?= $this->Number->format($transaction->paid_amount) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-light); padding-top: 8px; margin-top: 8px;">
            <span style="color: var(--text-muted); font-weight: 600;">Change</span>
            <span style="font-weight: 700; color: var(--success);">Rp <?= $this->Number->format($transaction->change_amount) ?></span>
        </div>
    </div>
    <?php endif; ?>
</main>