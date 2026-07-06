<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Transaction> $transactions
 * @var array $statusCounts
 */
$currentStatus = $this->request->getQuery('status') ?: 'pending';
$searchQuery = $this->request->getQuery('search') ?: '';
?>
<style>
    .tx-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
    .tx-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; margin-bottom: 8px; }
    
    .tx-filters { display: flex; gap: 16px; flex-wrap: wrap; align-items: center; width: 100%; justify-content: space-between; }
    
    .tx-tabs { display: flex; gap: 8px; background: var(--bg-surface); padding: 4px; border-radius: var(--radius-pill); border: 1px solid var(--border-light); box-shadow: var(--shadow-soft); }
    .tx-tab { padding: 8px 16px; border-radius: var(--radius-pill); font-size: 0.9rem; font-weight: 600; color: var(--text-muted); cursor: pointer; text-decoration: none; transition: var(--transition); display: flex; align-items: center; gap: 6px; }
    .tx-tab:hover { color: var(--text-main); background: var(--bg-body); }
    .tx-tab.active { background: var(--primary); color: white; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2); }
    .tx-count { background: rgba(0,0,0,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; }
    .tx-tab.active .tx-count { background: rgba(255,255,255,0.2); color: white; }

    .tx-search { position: relative; max-width: 320px; width: 100%; }
    .tx-search input { width: 100%; padding: 10px 16px 10px 40px; border-radius: var(--radius-pill); border: 1px solid var(--border-light); background: var(--bg-surface); font-size: 0.95rem; font-weight: 500; color: var(--text-main); outline: none; transition: var(--transition); box-shadow: var(--shadow-soft); }
    .tx-search input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    .tx-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-light); pointer-events: none; }

    .tx-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; }
    
    .tx-card { background: var(--bg-surface); border-radius: var(--radius-md); border: 1px solid var(--border-light); padding: 12px 16px; box-shadow: var(--shadow-soft); display: flex; flex-direction: column; gap: 10px; transition: var(--transition); position: relative; overflow: hidden; }
    .tx-card:hover { box-shadow: var(--shadow-hover); border-color: var(--border-focus); transform: translateY(-1px); }
    .tx-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--status-color, var(--border-light)); }
    .tx-card.status-pending { --status-color: var(--warning); }
    .tx-card.status-paid { --status-color: var(--success); }
    
    .tx-top { display: flex; justify-content: space-between; align-items: center; }
    .tx-info h4 { font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px; margin-top: 0; }
    .tx-meta { color: var(--text-muted); font-size: 0.8rem; display: flex; gap: 12px; font-weight: 500; }
    .tx-meta span { display: flex; align-items: center; gap: 4px; }
    
    .tx-amount { text-align: right; display: flex; flex-direction: column; align-items: flex-end; }
    .tx-total { font-size: 1.25rem; font-weight: 800; color: var(--primary); letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
    
    .tx-items { background: var(--bg-body); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); font-size: 0.85rem; color: var(--text-main); display: flex; flex-wrap: wrap; gap: 6px; }
    .tx-item-pill { background: var(--bg-surface); border: 1px solid var(--border-light); padding: 2px 8px; border-radius: var(--radius-pill); font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .tx-item-qty { color: var(--primary); font-weight: 800; }
    
    .tx-actions { display: flex; justify-content: flex-end; gap: 6px; border-top: 1px solid var(--border-light); padding-top: 10px; margin-top: auto; }
    .tx-actions .btn { padding: 6px 12px; font-size: 0.85rem; }
    
    .empty-state { padding: 48px 24px; text-align: center; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px dashed var(--border-focus); color: var(--text-muted); font-size: 1.1rem; }

    @media (min-width: 768px) {
        .tx-card { flex-direction: row; padding: 0; align-items: stretch; }
        .tx-content-wrapper { display: flex; flex: 1; align-items: center; padding: 12px 16px; padding-left: 20px; gap: 16px; }
        .tx-top { flex-direction: column; align-items: flex-start; flex: 0 0 200px; }
        .tx-amount { display: none; } /* Hide mobile amount in row layout */
        .tx-items { flex: 1; margin: 0; background: transparent; border: none; padding: 0; align-self: center; }
        .tx-desktop-actions { display: flex; flex-direction: column; align-items: flex-end; justify-content: center; flex: 0 0 160px; gap: 6px; border-left: 1px solid var(--border-light); padding-left: 16px; }
        .tx-actions { display: none; } /* Hide mobile actions */
    }
    @media (max-width: 767px) {
        .tx-desktop-actions { display: none; }
    }
</style>

<main class="view active" style="display: flex; flex: 1; width: 100%; flex-direction: column; padding: 24px; overflow-y: auto;">
    
    <div class="tx-header">
        <div>
            <h1 class="tx-title">Transactions</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Manage and process customer orders</p>
        </div>
    </div>

    <div class="tx-filters" style="margin-bottom: 24px;">
        <div class="tx-tabs">
            <a href="<?= $this->Url->build(['?' => ['status' => 'pending', 'search' => $searchQuery]]) ?>" class="tx-tab <?= $currentStatus === 'pending' ? 'active' : '' ?>">
                Pending <span class="tx-count"><?= $statusCounts['pending'] ?? 0 ?></span>
            </a>
            <a href="<?= $this->Url->build(['?' => ['status' => 'paid', 'search' => $searchQuery]]) ?>" class="tx-tab <?= $currentStatus === 'paid' ? 'active' : '' ?>">
                Paid <span class="tx-count"><?= $statusCounts['paid'] ?? 0 ?></span>
            </a>
            <a href="<?= $this->Url->build(['?' => ['status' => 'all', 'search' => $searchQuery]]) ?>" class="tx-tab <?= $currentStatus === 'all' ? 'active' : '' ?>">
                All <span class="tx-count"><?= $statusCounts['all'] ?? 0 ?></span>
            </a>
        </div>
        
        <form class="tx-search" method="get" action="<?= $this->Url->build() ?>">
            <input type="hidden" name="status" value="<?= h($currentStatus) ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" name="search" placeholder="Search customer or ID..." value="<?= h($searchQuery) ?>">
        </form>
    </div>

    <?php if ($transactions->isEmpty()): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 16px; color: var(--border-focus);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <p>No transactions found for the selected filters.</p>
        </div>
    <?php else: ?>
        <div class="tx-list">
            <?php foreach ($transactions as $transaction): ?>
            <div class="tx-card status-<?= h($transaction->status) ?>">
                <div class="tx-content-wrapper">
                    <div class="tx-top">
                        <div class="tx-info">
                            <h4><?= h($transaction->customer_name ?: 'Guest') ?></h4>
                            <div class="tx-meta">
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?= h($transaction->created->format('d M, H:i')) ?>
                                </span>
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    #<?= $this->Number->format($transaction->id) ?>
                                </span>
                            </div>
                        </div>
                        <div class="tx-amount">
                            <div class="tx-total">Rp <?= $this->Number->format($transaction->total) ?></div>
                            <?php if ($transaction->status === 'paid'): ?>
                                <span class="status-badge status-paid" style="margin-top: 2px;">Paid</span>
                            <?php else: ?>
                                <span class="status-badge status-pending" style="margin-top: 2px;">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($transaction->transaction_items)): ?>
                    <div class="tx-items">
                        <?php foreach ($transaction->transaction_items as $item): ?>
                            <div class="tx-item-pill">
                                <span class="tx-item-qty"><?= h($item->qty) ?>x</span>
                                <?= h($item->menu->name ?? 'Unknown Item') ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="tx-desktop-actions">
                        <div class="tx-total">Rp <?= $this->Number->format($transaction->total) ?></div>
                        <?php if ($transaction->status === 'paid'): ?>
                            <span class="status-badge status-paid" style="margin-bottom: 6px;">Paid</span>
                        <?php else: ?>
                            <span class="status-badge status-pending" style="margin-bottom: 6px;">Pending</span>
                        <?php endif; ?>
                        <div style="display: flex; gap: 4px; width: 100%;">
                            <button class="btn btn-save" style="flex:1; padding: 4px 8px; font-size: 0.8rem;" onclick="window.location.href='<?= $this->Url->build(['action' => 'view', $transaction->id]) ?>'">View</button>
                            <?php if ($transaction->status !== 'paid'): ?>
                                <button class="btn btn-pay" style="flex:1; padding: 4px 8px; font-size: 0.8rem;" onclick="window.location.href='<?= $this->Url->build(['action' => 'edit', $transaction->id]) ?>'">Pay</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="tx-actions">
                    <button class="btn btn-save" onclick="window.location.href='<?= $this->Url->build(['action' => 'view', $transaction->id]) ?>'">
                        View
                    </button>
                    <?php if ($transaction->status !== 'paid'): ?>
                        <button class="btn btn-pay" onclick="window.location.href='<?= $this->Url->build(['action' => 'edit', $transaction->id]) ?>'">
                            Process Payment
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem; background: var(--bg-surface); padding: 12px 20px; border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></div>
            <ul class="pagination">
                <?= $this->Paginator->prev('< ' . __('Previous')) ?>
                <?= $this->Paginator->next(__('Next') . ' >') ?>
            </ul>
        </div>
    <?php endif; ?>
</main>