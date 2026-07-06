<?php
/**
 * POS View
 * Uses JS cart, embedded in CakePHP layout
 */
$this->Html->css('pos', ['block' => true]);
?>

<div class="pos-container">
    <div class="left-panel">
        <div class="toolbar">

            <div id="cat-filter" class="cat-filter" role="tablist" aria-label="Menu categories"></div>
        </div>
        <div id="pos-menu-grid" class="menu-grid"><div style="color:var(--n-500); grid-column: 1/-1; padding: var(--sp-4) 0;">Loading menu...</div></div>
    </div>
    <div class="right-panel">
        <div class="order-header" id="order-header">
            <input type="text" id="customer-name" placeholder="Customer name" aria-label="Customer name">
        </div>

        <div id="cart-items" class="order-items"></div>

        <div class="order-summary">
            <div class="summary-row">
                <span>Total</span>
                <span id="cart-total">Rp 0</span>
            </div>
            <div class="action-btns">
                <button class="btn btn-secondary" onclick="saveOrder()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1-2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save
                </button>
                <button class="btn btn-primary" id="pay-now-btn" onclick="openPaymentModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="modal">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
        <div class="modal-header">
            <h3 id="payment-modal-title">Process Payment</h3>
            <button class="close-btn" onclick="closeModal('payment-modal')" aria-label="Close">×</button>
        </div>
        <div class="payment-summary">
            <div class="stats-title">Total Due</div>
            <div class="payment-total" id="pay-total">Rp 0</div>
            <div class="payment-change" id="pay-change-wrapper" style="display:none;">Change: <span id="pay-change">Rp 0</span></div>
        </div>
        <div class="form-group">
            <label>Quick Cash</label>
            <div class="quick-cash-grid" id="quick-cash-container"></div>
        </div>
        <div class="form-group">
            <label>Custom Amount Received</label>
            <input type="text" id="pay-received" placeholder="Enter amount..." autocomplete="off" inputmode="numeric">
        </div>
        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
            <button class="btn btn-outline" style="flex: 1;" onclick="closeModal('payment-modal')">Cancel</button>
            <button class="btn btn-primary" style="flex: 2;" onclick="processPayment()">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
    window.POS_CONFIG = {
        apiBase: '<?= $this->Url->build('/') ?>',
        csrfToken: '<?= $this->request->getAttribute('csrfToken') ?>'
    };
</script>
<?= $this->Html->script('pos') ?>