<?php
$this->disableAutoLayout();
$TransactionsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Transactions');
$identity = $this->request->getAttribute('identity');
$branchId = $identity ? $identity->get('branch_id') : null;

$query = $TransactionsTable->find()
    ->contain(['TransactionItems' => ['Menus']])
    ->order(['Transactions.created' => 'DESC'])
    ->limit(200);

if ($branchId) {
    $query->where(['Transactions.branch_id' => $branchId]);
}
$transactions = $query->all();

$transactionsData = [];
foreach ($transactions as $t) {
    $items = [];
    foreach ($t->transaction_items as $ti) {
        $items[] = [
            'name' => $ti->menu ? $ti->menu->name : 'Unknown',
            'qty' => $ti->qty,
            'price' => $ti->price
        ];
    }
    $transactionsData[] = [
        'id' => (string)$t->id,
        'customer' => $t->customer_name ?: 'Guest',
        'datetime' => $t->created->format('Y-m-d\TH:i:s'),
        'status' => $t->status,
        'method' => 'Cash',
        'items' => $items
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Transactions — Warung Kita POS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --paper:#FAF9F5;
    --paper-dim:#F1EFE7;
    --ink:#1E211C;
    --ink-soft:#6B7066;
    --line:#D8D4C8;
    --card:#FFFFFF;
    --green:#2F6F4F;
    --green-dark:#245A3E;
    --green-bg:#E7F1EB;
    --amber:#C98A2C;
    --amber-bg:#FBF0DD;
    --red:#C1272D;
    --radius:14px;
    --safe-bottom: env(safe-area-inset-bottom, 0px);
    --nav-h: 60px;
  }

  *{ box-sizing:border-box; }
  html,body{ margin:0; padding:0; }
  body{
    background:var(--paper);
    color:var(--ink);
    font-family:'Inter', sans-serif;
    -webkit-font-smoothing:antialiased;
    min-height:100vh;
    padding-bottom: calc(var(--nav-h) + var(--safe-bottom) + 20px);
  }
  ::selection{ background:var(--green); color:#fff; }

  /* ---------- Header ---------- */
  header.top{
    position:sticky; top:0; z-index:20;
    background:var(--paper);
    border-bottom:1px solid var(--line);
    padding:14px 16px 12px;
  }
  .top-row{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:12px;
  }
  .brand{
    font-family:'Archivo Black', sans-serif;
    font-size:1.1rem;
    line-height:1;
  }
  .brand small{
    display:block; margin-top:3px;
    font-family:'Inter'; font-weight:500; font-size:0.68rem;
    letter-spacing:0.12em; text-transform:uppercase; color:var(--ink-soft);
  }
  .today-chip{
    font-family:'IBM Plex Mono', monospace; font-size:0.72rem;
    color:var(--ink-soft); background:var(--paper-dim);
    border-radius:999px; padding:6px 12px;
  }

  /* ---------- Search ---------- */
  .search-wrap{
    position:relative;
    margin-bottom:12px;
  }
  .search-wrap svg{
    position:absolute; left:13px; top:50%; transform:translateY(-50%);
    width:17px; height:17px; color:var(--ink-soft);
  }
  .search-input{
    width:100%;
    background:var(--card);
    border:1px solid var(--line);
    border-radius:999px;
    padding:11px 14px 11px 38px;
    font-family:'Inter'; font-size:0.9rem; color:var(--ink);
    outline:none;
  }
  .search-input:focus{ border-color:var(--green); }
  .search-input::placeholder{ color:var(--ink-soft); }
  .clear-search{
    position:absolute; right:8px; top:50%; transform:translateY(-50%);
    width:22px; height:22px; border-radius:999px; border:none;
    background:var(--paper-dim); color:var(--ink-soft);
    font-size:0.8rem; cursor:pointer; display:none;
  }

  /* ---------- Status tabs ---------- */
  .tabs{
    display:flex; gap:6px;
    background:var(--paper-dim);
    border-radius:999px;
    padding:4px;
  }
  .tab{
    flex:1;
    border:none; background:transparent;
    color:var(--ink-soft);
    font-family:'Inter'; font-weight:600; font-size:0.82rem;
    padding:9px 8px;
    border-radius:999px;
    cursor:pointer;
    display:flex; align-items:center; justify-content:center; gap:6px;
    transition:background .15s ease, color .15s ease;
  }
  .tab.active{ background:var(--card); color:var(--ink); box-shadow:0 1px 3px rgba(0,0,0,0.08); }
  .tab .count{
    font-family:'IBM Plex Mono', monospace; font-size:0.7rem;
    background:var(--line); color:var(--ink);
    border-radius:999px; padding:1px 6px;
  }
  .tab.active .count{ background:var(--ink); color:var(--paper); }

  /* ---------- Summary Bar ---------- */
  .summary-bar{
    display:flex; align-items:stretch;
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:14px 8px;
    margin-bottom:14px;
  }
  .summary-stat{
    flex:1;
    display:flex; flex-direction:column; align-items:center; gap:3px;
    text-align:center;
    padding:0 6px;
  }
  .summary-value{
    font-family:'IBM Plex Mono', monospace;
    font-weight:700; font-size:1.15rem;
    color:var(--ink);
    white-space:nowrap;
  }
  .summary-value.accent{ color:var(--green-dark); }
  .summary-label{
    font-family:'Inter'; font-weight:600; font-size:0.66rem;
    letter-spacing:0.04em; text-transform:uppercase;
    color:var(--ink-soft);
  }
  .summary-divider{ width:1px; background:var(--line); margin:2px 0; }

  /* ---------- List ---------- */
  main{ padding:14px 16px 40px; }

  .txn-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:14px;
    margin-bottom:12px;
  }
  .txn-head{
    display:flex; align-items:flex-start; justify-content:space-between;
    gap:10px;
    margin-bottom:8px;
  }
  .txn-customer{
    font-weight:700; font-size:0.98rem; line-height:1.2;
  }
  .txn-meta{
    font-family:'IBM Plex Mono', monospace; font-size:0.72rem;
    color:var(--ink-soft); margin-top:3px;
  }
  .status-badge{
    flex:0 0 auto;
    font-family:'Inter'; font-weight:700; font-size:0.7rem;
    letter-spacing:0.03em; text-transform:uppercase;
    padding:5px 10px; border-radius:999px;
    white-space:nowrap;
  }
  .status-paid{ background:var(--green-bg); color:var(--green-dark); }
  .status-pending{ background:var(--amber-bg); color:var(--amber); }

  .txn-items{
    font-size:0.85rem; color:var(--ink);
    line-height:1.5;
    padding:8px 0;
    border-top:1px dashed var(--line);
    border-bottom:1px dashed var(--line);
    margin-bottom:10px;
  }
  .txn-items .more{ color:var(--ink-soft); }

  .txn-foot{
    display:flex; align-items:center; justify-content:space-between;
    gap:10px;
  }
  .txn-total{
    font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:1rem;
  }
  .txn-total small{
    display:block; font-family:'Inter'; font-weight:500; font-size:0.65rem;
    letter-spacing:0.08em; text-transform:uppercase; color:var(--ink-soft);
  }
  .txn-actions{ display:flex; gap:8px; }

  .btn-sm{
    border:none; border-radius:999px;
    font-family:'Inter'; font-weight:700; font-size:0.82rem;
    padding:9px 16px;
    cursor:pointer;
    transition:transform .1s ease;
  }
  .btn-sm:active{ transform:scale(0.96); }
  .btn-view{
    background:var(--paper-dim); color:var(--ink);
    border:1px solid var(--line);
  }
  .btn-pay{
    background:var(--green); color:#fff;
  }
  .btn-pay:disabled {
      opacity: 0.5;
      cursor: not-allowed;
  }

  .empty-state{
    text-align:center; padding:60px 20px; color:var(--ink-soft);
  }
  .empty-state .icon{ font-size:2rem; margin-bottom:8px; }
  .empty-state .msg{ font-size:0.9rem; }

  /* ---------- Bottom navbar ---------- */
  .bottom-nav{
    position:fixed; left:0; right:0; bottom:0; z-index:25;
    background:var(--card);
    border-top:1px solid var(--line);
    display:flex;
    padding-bottom:var(--safe-bottom);
  }
  .nav-item{
    flex:1;
    display:flex; flex-direction:column; align-items:center; gap:3px;
    padding:9px 0 8px;
    text-decoration:none;
    color:var(--ink-soft);
    font-family:'Inter'; font-weight:600; font-size:0.68rem;
  }
  .nav-item svg{ width:21px; height:21px; }
  .nav-item.active{ color:var(--green-dark); }

  /* ---------- Detail drawer ---------- */
  .overlay{
    position:fixed; inset:0; background:rgba(20,20,17,0.45);
    z-index:35; opacity:0; pointer-events:none;
    transition:opacity .2s ease;
  }
  .overlay.show{ opacity:1; pointer-events:auto; }

  .drawer{
    position:fixed; left:0; right:0; bottom:0; z-index:40;
    background:var(--card);
    border-top-left-radius:20px; border-top-right-radius:20px;
    max-height:86vh;
    transform:translateY(100%);
    transition:transform .25s cubic-bezier(.32,.72,0,1);
    display:flex; flex-direction:column;
    padding-bottom:var(--safe-bottom);
  }
  .drawer.show{ transform:translateY(0); }
  .drawer-handle{ width:36px; height:4px; background:var(--line); border-radius:999px; margin:10px auto 4px; }
  .drawer-head{
    display:flex; align-items:center; justify-content:space-between;
    padding:8px 18px 10px;
  }
  .drawer-title{ font-family:'Archivo Black'; font-size:1rem; }
  .drawer-close{
    border:none; background:var(--paper-dim); color:var(--ink);
    width:30px; height:30px; border-radius:999px;
    font-size:1rem; cursor:pointer;
  }
  .receipt-edge{
    height:10px;
    background-image: radial-gradient(circle at 8px 5px, var(--paper) 5px, transparent 5.5px);
    background-size:16px 10px; background-repeat:repeat-x;
    margin:0 14px;
  }
  .drawer-body{ flex:1; overflow-y:auto; padding:16px 18px 6px; }

  .detail-row{
    display:flex; justify-content:space-between; align-items:center;
    font-size:0.85rem; padding:5px 0; color:var(--ink-soft);
  }
  .detail-row span:last-child{ color:var(--ink); font-weight:600; }

  .detail-status{ margin:10px 0 4px; }

  .items-block{
    margin-top:14px;
    border-top:1px dashed var(--line);
    padding-top:10px;
  }
  .items-block-label{
    font-family:'IBM Plex Mono', monospace; font-size:0.7rem;
    letter-spacing:0.08em; text-transform:uppercase; color:var(--ink-soft);
    margin-bottom:8px;
  }
  .detail-item-row{
    display:flex; justify-content:space-between; align-items:center;
    font-family:'IBM Plex Mono', monospace; font-size:0.82rem;
    padding:6px 0;
  }
  .detail-item-name{ font-family:'Inter'; font-weight:600; font-size:0.87rem; color:var(--ink); flex:1; }
  .detail-item-qty{ color:var(--ink-soft); width:34px; text-align:center; }
  .detail-item-price{ width:82px; text-align:right; color:var(--ink); }

  .drawer-summary{
    padding:12px 0 0;
    font-family:'IBM Plex Mono', monospace; font-size:0.85rem;
    border-top:1px dashed var(--line);
    margin-top:10px;
  }
  .summary-line{ display:flex; justify-content:space-between; padding:4px 0; color:var(--ink-soft); }
  .summary-total{
    display:flex; justify-content:space-between;
    padding:8px 0 2px; font-size:1.05rem; font-weight:700; color:var(--ink);
  }
  .summary-total .amt{ color:var(--red); }

  .drawer-actions{ display:flex; gap:10px; padding:16px 18px 18px; }
  .btn{
    flex:1; border:none; border-radius:999px;
    font-family:'Inter'; font-weight:700; font-size:0.95rem;
    padding:14px 10px; cursor:pointer;
    transition:transform .1s ease, opacity .15s ease;
  }
  .btn:active{ transform:scale(0.97); }
  .btn-close-only{ background:var(--paper-dim); color:var(--ink); border:1.5px solid var(--line); }
  .btn-pay-full{ background:var(--green); color:#fff; box-shadow:0 4px 14px rgba(47,111,79,0.35); }
  .btn-pay-full:disabled{ opacity: 0.5; cursor:not-allowed; }

  .toast{
    position:fixed; left:50%; bottom:30px; transform:translateX(-50%) translateY(20px);
    background:var(--ink); color:var(--paper);
    padding:10px 18px; border-radius:999px;
    font-size:0.85rem; font-weight:600;
    z-index:60; opacity:0; pointer-events:none;
    transition:opacity .2s ease, transform .2s ease;
  }
  .toast.show{ opacity:1; transform:translateX(-50%) translateY(0); }

  /* --- START NEW PAYMENT DRAWER (Can be deleted if not needed) --- */
  .payment-summary {
    background: var(--paper-dim);
    border-radius: var(--radius);
    padding: 16px;
    text-align: center;
    margin-bottom: 20px;
  }
  .stats-title { font-size: 0.85rem; color: var(--ink-soft); font-weight: 600; margin-bottom: 6px; }
  .payment-total { font-family: 'IBM Plex Mono', monospace; font-size: 1.6rem; font-weight: 700; color: var(--ink); }
  .payment-change { font-family: 'IBM Plex Mono', monospace; font-size: 1rem; margin-top: 8px; font-weight: 600; color: var(--amber); }
  
  .form-group { margin-bottom: 20px; }
  .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--ink-soft); margin-bottom: 10px; }
  
  .quick-cash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .quick-cash-btn {
    background: var(--card); border: 1px solid var(--line); border-radius: 10px;
    padding: 12px; font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 0.95rem;
    color: var(--ink); cursor: pointer; transition: all 0.15s ease;
  }
  .quick-cash-btn:active, .quick-cash-btn.selected {
    border-color: var(--green); background: var(--green); color: #fff;
  }
  
  .input-wrapper {
    display: flex; align-items: center;
    background: var(--card); border: 1px solid var(--line);
    border-radius: 10px; overflow: hidden; padding-left: 14px;
    transition: border-color 0.2s;
  }
  .input-wrapper:focus-within { border-color: var(--green); }
  .currency-prefix { font-family: 'Inter'; font-weight: 600; font-size: 1rem; color: var(--ink-soft); }
  #pay-received {
    flex: 1; border: none; outline: none; background: transparent;
    padding: 14px; font-family: 'IBM Plex Mono', monospace; font-size: 1.1rem; font-weight: 600; color: var(--ink);
  }
  #pay-received::placeholder { color: var(--line); }
  /* --- END NEW PAYMENT DRAWER --- */

  @media (min-width:560px){
    body{ display:flex; justify-content:center; }
    .app{ width:100%; max-width:480px; box-shadow:0 0 0 1px var(--line); min-height:100vh; position:relative; }
    .drawer, .overlay, .bottom-nav{ max-width:480px; margin:0 auto; }
    .bottom-nav{ position:absolute; }
  }
</style>
</head>
<body>
<div class="app">

  <header class="top">
    <div class="top-row">
      <div class="brand">Transactions
        <small>Warung Kita POS</small>
      </div>
      <div class="today-chip" id="todayChip"></div>
    </div>

    <div class="search-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" class="search-input" id="searchInput" placeholder="Search by customer name...">
      <button class="clear-search" id="clearSearch">✕</button>
    </div>

    <div class="tabs" id="tabs">
      <button class="tab active" data-status="all">All <span class="count" id="countAll">0</span></button>
      <button class="tab" data-status="pending">Pending <span class="count" id="countPending">0</span></button>
      <button class="tab" data-status="paid">Paid <span class="count" id="countPaid">0</span></button>
    </div>
  </header>

  <main>
    <div class="summary-bar" id="summaryBar">
      <div class="summary-stat">
        <div class="summary-value" id="sumCountVal">0</div>
        <div class="summary-label">Transactions</div>
      </div>
      <div class="summary-divider"></div>
      <div class="summary-stat">
        <div class="summary-value accent" id="sumAmountVal">Rp 0</div>
        <div class="summary-label" id="sumAmountLabel">Total Collected</div>
      </div>
    </div>
    <div id="txnList"></div>
  </main>

  <nav class="bottom-nav">
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobilePos']) ?>" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
      <span>Order</span>
    </a>
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobileTransactions']) ?>" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h9l3 3v17H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      <span>Transactions</span>
    </a>
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobileAccount']) ?>" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      <span>Account</span>
    </a>
  </nav>

</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-handle"></div>
  <div class="drawer-head">
    <div class="drawer-title">Transaction Detail</div>
    <button class="drawer-close" id="closeDrawer">✕</button>
  </div>
  <div class="receipt-edge"></div>
  <div class="drawer-body" id="drawerBody"></div>
  <div class="drawer-actions" id="drawerActions"></div>
</div>

<!-- --- START NEW PAYMENT DRAWER (Can be deleted if not needed) --- -->
<div class="overlay" id="paymentOverlay" style="z-index:45;"></div>
<div class="drawer" id="paymentDrawer" style="z-index:50;">
  <div class="drawer-handle"></div>
  <div class="drawer-head">
    <div class="drawer-title">Process Payment</div>
    <button class="drawer-close" id="closePaymentDrawer">✕</button>
  </div>
  
  <div class="drawer-body">
    <div class="payment-summary">
      <div class="stats-title">Total Due</div>
      <div class="payment-total" id="pay-total">Rp 0</div>
      <div class="payment-change" id="pay-change-wrapper" style="display:none;">Change: <span id="pay-change">Rp 0</span></div>
    </div>
    
    <div class="form-group">
      <label>Quick Cash</label>
      <div class="quick-cash-grid" id="quick-cash-container">
        <!-- JS will populate these buttons automatically -->
      </div>
    </div>
    
    <div class="form-group">
      <label>Custom Amount Received</label>
      <div class="input-wrapper">
        <span class="currency-prefix">Rp</span>
        <input type="text" id="pay-received" placeholder="0" autocomplete="off" inputmode="numeric">
      </div>
    </div>
  </div>
  
  <div class="drawer-actions">
    <button class="btn btn-close-only" id="cancelPaymentBtn">Cancel</button>
    <button class="btn btn-pay-full" id="confirmPaymentBtn" disabled>Confirm Payment</button>
  </div>
</div>
<!-- --- END NEW PAYMENT DRAWER --- -->

<div class="toast" id="toast"></div>

<script>
  // Inject real transactions from PHP
  const TRANSACTIONS = <?= json_encode($transactionsData) ?>;
  
  // Setup API config for backend calls
  window.POS_CONFIG = {
      apiBase: '<?= $this->Url->build('/') ?>',
      csrfToken: '<?= $this->request->getAttribute('csrfToken') ?>'
  };

  const API_BASE = window.POS_CONFIG.apiBase;

  async function api(path, method = 'GET', body = null) {
      const csrf = window.POS_CONFIG.csrfToken;
      const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': csrf };
      const res = await fetch(API_BASE + path, { credentials: 'same-origin', method, headers, body: body ? JSON.stringify(body) : null });
      if (res.status === 401 || res.status === 403) {
          window.location.href = API_BASE + 'users/login';
          return null;
      }
      return res.ok ? res.json() : null;
  }

  let activeStatus = "all";
  let searchQuery = "";
  let activeTxnId = null;

  const rupiah = n => "Rp " + n.toLocaleString("id-ID");
  const fmtDateTime = iso => {
    const d = new Date(iso);
    const date = d.toLocaleDateString("id-ID", { day:"2-digit", month:"short", year:"numeric" });
    const time = d.toLocaleTimeString("id-ID", { hour:"2-digit", minute:"2-digit" });
    return `${date} · ${time}`;
  };
  const txnTotal = t => t.items.reduce((s,i)=> s + i.qty*i.price, 0);

  document.getElementById("todayChip").textContent =
    new Date().toLocaleDateString("id-ID", { weekday:"short", day:"2-digit", month:"short" });

  const txnList = document.getElementById("txnList");
  const overlay = document.getElementById("overlay");
  const drawer = document.getElementById("drawer");
  const drawerBody = document.getElementById("drawerBody");
  const drawerActions = document.getElementById("drawerActions");
  const toast = document.getElementById("toast");

  function getBusinessDayStart() {
    const now = new Date();
    const currentHour = now.getHours();
    
    // Create a date object for 03:00:00 today
    const businessStart = new Date(now);
    businessStart.setHours(3, 0, 0, 0);

    // If current time is before 03:00 AM, the business day actually started yesterday at 03:00 AM
    if (currentHour < 3) {
      businessStart.setDate(businessStart.getDate() - 1);
    }
    
    return businessStart;
  }

  function getTodayTransactions() {
    const businessDayStart = getBusinessDayStart();
    return TRANSACTIONS.filter(t => new Date(t.datetime) >= businessDayStart);
  }

  function updateCounts(){
    const todayData = getTodayTransactions();
    document.getElementById("countAll").textContent = todayData.length;
    document.getElementById("countPending").textContent = todayData.filter(t=>t.status==="pending").length;
    document.getElementById("countPaid").textContent = todayData.filter(t=>t.status==="paid").length;
  }

  function filteredList(){
    const todayData = getTodayTransactions();
    return todayData
      .filter(t => activeStatus === "all" ? true : t.status === activeStatus)
      .filter(t => t.customer.toLowerCase().includes(searchQuery.toLowerCase()) || t.id.toString().toLowerCase().includes(searchQuery.toLowerCase()))
      .sort((a,b)=> new Date(b.datetime) - new Date(a.datetime));
  }

  function itemsSummary(items){
    const names = items.map(i => i.qty > 1 ? `${i.name} x${i.qty}` : i.name);
    if(names.length <= 2) return names.join(", ");
    return `${names.slice(0,2).join(", ")} <span class="more">+${names.length-2} more</span>`;
  }

  function updateSummary(list){
    const count = list.length;
    
    // Always sum today's PAID transactions for "Total Collected"
    const todayPaid = getTodayTransactions().filter(t => t.status === "paid");
    const amount = todayPaid.reduce((s, t) => s + txnTotal(t), 0);
    
    document.getElementById("sumCountVal").textContent = count;
    document.getElementById("sumAmountVal").textContent = rupiah(amount);
  }

  function renderList(){
    updateCounts();
    const list = filteredList();
    updateSummary(list);

    if(list.length === 0){
      txnList.innerHTML = `
        <div class="empty-state">
          <div class="icon">🧾</div>
          <div class="msg">No transactions match your search.</div>
        </div>`;
      return;
    }

    txnList.innerHTML = list.map(t => `
      <div class="txn-card">
        <div class="txn-head">
          <div>
            <div class="txn-customer">${t.customer}</div>
            <div class="txn-meta">#${t.id} · ${fmtDateTime(t.datetime)}</div>
          </div>
          <span class="status-badge status-${t.status}">${t.status}</span>
        </div>
        <div class="txn-items">${itemsSummary(t.items)}</div>
        <div class="txn-foot">
          <div class="txn-total"><small>Total</small>${rupiah(txnTotal(t))}</div>
          <div class="txn-actions">
            <button class="btn-sm btn-view" data-view="${t.id}">View</button>
            ${t.status === "pending" ? `<button class="btn-sm btn-pay" data-pay="${t.id}" id="payBtn-${t.id}">Pay</button>` : ``}
          </div>
        </div>
      </div>
    `).join("");
  }

  function openDetail(id){
    const t = TRANSACTIONS.find(tx => tx.id === id);
    if(!t) return;
    activeTxnId = id;
    const total = txnTotal(t);

    drawerBody.innerHTML = `
      <div class="detail-row"><span>Order ID</span><span>#${t.id}</span></div>
      <div class="detail-row"><span>Customer</span><span>${t.customer}</span></div>
      <div class="detail-row"><span>Date &amp; time</span><span>${fmtDateTime(t.datetime)}</span></div>
      <div class="detail-row"><span>Payment method</span><span>${t.method || "—"}</span></div>
      <div class="detail-row detail-status"><span>Status</span><span class="status-badge status-${t.status}">${t.status}</span></div>

      <div class="items-block">
        <div class="items-block-label">Items ordered</div>
        ${t.items.map(i => `
          <div class="detail-item-row">
            <div class="detail-item-name">${i.name}</div>
            <div class="detail-item-qty">x${i.qty}</div>
            <div class="detail-item-price">${rupiah(i.qty*i.price)}</div>
          </div>
        `).join("")}
      </div>

      <div class="drawer-summary">
        <div class="summary-line"><span>Subtotal</span><span>${rupiah(total)}</span></div>
        <div class="summary-total"><span>Total</span><span class="amt">${rupiah(total)}</span></div>
      </div>
    `;

    drawerActions.innerHTML = t.status === "pending"
      ? `<button class="btn btn-close-only" id="drawerCloseBtn">Close</button>
         <button class="btn btn-pay-full" id="drawerPayBtn">Pay now</button>`
      : `<button class="btn btn-close-only" id="drawerCloseBtn" style="flex:1;">Close</button>`;

    document.getElementById("drawerCloseBtn").addEventListener("click", closeDrawer);
    const payBtn = document.getElementById("drawerPayBtn");
    if(payBtn) payBtn.addEventListener("click", () => openPaymentForTxn(t.id, true));

    openDrawer();
  }

  async function markPaid(id, fromDrawer, customPaidAmount = null, customChangeAmount = null){
    const t = TRANSACTIONS.find(tx => tx.id === id);
    if(!t || t.status === "paid") return;
    
    // Disable buttons to prevent double click
    const drawerBtn = document.getElementById("drawerPayBtn");
    const listBtn = document.getElementById(`payBtn-${id}`);
    if (drawerBtn) drawerBtn.disabled = true;
    if (listBtn) listBtn.disabled = true;

    // Send API request
    const payload = {
        status: 'paid',
        paid_amount: customPaidAmount !== null ? customPaidAmount : txnTotal(t),
        change_amount: customChangeAmount !== null ? customChangeAmount : 0
    };
    
    const res = await api(`transactions/edit/${id}.json`, 'POST', payload);
    
    if (res && res.success) {
        t.status = "paid";
        t.method = t.method || "Cash";
        showToast(`${t.customer}'s order marked as paid ✓`);
        renderList();
        if(fromDrawer) closeDrawer();
    } else {
        console.error('Update failed:', res);
        showToast('Failed to update status. Check console.');
        if (drawerBtn) drawerBtn.disabled = false;
        if (listBtn) listBtn.disabled = false;
    }
  }

  function openDrawer(){ drawer.classList.add("show"); overlay.classList.add("show"); }
  function closeDrawer(){ drawer.classList.remove("show"); overlay.classList.remove("show"); activeTxnId = null; }
  function showToast(msg){
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 1800);
  }

  document.getElementById("tabs").addEventListener("click", e=>{
    const btn = e.target.closest(".tab");
    if(!btn) return;
    document.querySelectorAll(".tab").forEach(b=>b.classList.remove("active"));
    btn.classList.add("active");
    activeStatus = btn.dataset.status;
    renderList();
  });

  const searchInput = document.getElementById("searchInput");
  const clearSearch = document.getElementById("clearSearch");
  searchInput.addEventListener("input", e=>{
    searchQuery = e.target.value;
    clearSearch.style.display = searchQuery ? "block" : "none";
    renderList();
  });
  clearSearch.addEventListener("click", ()=>{
    searchInput.value = "";
    searchQuery = "";
    clearSearch.style.display = "none";
    renderList();
  });

  txnList.addEventListener("click", e=>{
    const viewBtn = e.target.closest("[data-view]");
    const payBtn = e.target.closest("[data-pay]");
    if(viewBtn) openDetail(viewBtn.dataset.view);
    if(payBtn) openPaymentForTxn(payBtn.dataset.pay, false);
  });

  document.getElementById("closeDrawer").addEventListener("click", closeDrawer);
  overlay.addEventListener("click", closeDrawer);

  // --- START NEW PAYMENT DRAWER LOGIC (Can be deleted if not needed) ---
  const paymentDrawer = document.getElementById("paymentDrawer");
  const paymentOverlay = document.getElementById("paymentOverlay");
  const payReceivedInput = document.getElementById("pay-received");
  const confirmPaymentBtn = document.getElementById("confirmPaymentBtn");
  const payChangeWrapper = document.getElementById("pay-change-wrapper");
  const payChangeAmount = document.getElementById("pay-change");
  
  let currentPaymentTxnId = null;
  let currentTotalDue = 0;
  let amountReceived = 0;
  let paymentFromDetailDrawer = false;

  function closePaymentDrawer() {
    paymentDrawer.classList.remove("show");
    paymentOverlay.classList.remove("show");
    currentPaymentTxnId = null;
  }

  function openPaymentForTxn(id, fromDrawer) {
    const t = TRANSACTIONS.find(tx => tx.id === id);
    if(!t || t.status === "paid") return;

    currentPaymentTxnId = id;
    paymentFromDetailDrawer = fromDrawer;
    currentTotalDue = txnTotal(t);
    amountReceived = 0;

    document.getElementById("pay-total").textContent = rupiah(currentTotalDue);
    payReceivedInput.value = "";
    payChangeWrapper.style.display = "none";
    confirmPaymentBtn.disabled = true;

    const exact = currentTotalDue;
    const fifty = Math.ceil(currentTotalDue / 50000) * 50000;
    const hundred = Math.ceil(currentTotalDue / 100000) * 100000;
    const options = [...new Set([exact, fifty, hundred])];
    
    document.getElementById("quick-cash-container").innerHTML = options.map(amt => `
      <button class="quick-cash-btn" data-amt="${amt}">
        ${amt === exact ? 'Exact ' : ''}${rupiah(amt)}
      </button>
    `).join("");

    paymentDrawer.classList.add("show");
    paymentOverlay.classList.add("show");
  }

  payReceivedInput.addEventListener("input", (e) => {
    let rawValue = e.target.value.replace(/[^0-9]/g, "");
    amountReceived = rawValue ? parseInt(rawValue, 10) : 0;
    e.target.value = rawValue ? amountReceived.toLocaleString("id-ID") : "";
    document.querySelectorAll(".quick-cash-btn").forEach(b => b.classList.remove("selected"));
    calculateChange();
  });

  document.getElementById("quick-cash-container").addEventListener("click", (e) => {
    const btn = e.target.closest(".quick-cash-btn");
    if (!btn) return;
    document.querySelectorAll(".quick-cash-btn").forEach(b => b.classList.remove("selected"));
    btn.classList.add("selected");
    amountReceived = parseInt(btn.dataset.amt, 10);
    payReceivedInput.value = amountReceived.toLocaleString("id-ID");
    calculateChange();
  });

  function calculateChange() {
    if (amountReceived >= currentTotalDue) {
      const change = amountReceived - currentTotalDue;
      payChangeAmount.textContent = rupiah(change);
      payChangeWrapper.style.display = "block";
      confirmPaymentBtn.disabled = false;
    } else {
      payChangeWrapper.style.display = "none";
      confirmPaymentBtn.disabled = true;
    }
  }

  confirmPaymentBtn.addEventListener("click", () => {
     markPaid(currentPaymentTxnId, paymentFromDetailDrawer, amountReceived, amountReceived - currentTotalDue);
     closePaymentDrawer();
  });

  document.getElementById("cancelPaymentBtn").addEventListener("click", closePaymentDrawer);
  document.getElementById("closePaymentDrawer").addEventListener("click", closePaymentDrawer);
  paymentOverlay.addEventListener("click", closePaymentDrawer);
  // --- END NEW PAYMENT DRAWER LOGIC ---

  renderList();
</script>

</body>
</html>
