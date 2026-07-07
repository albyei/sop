<?php
$this->disableAutoLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Warung Kita — POS</title>
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
    --red:#C1272D;
    --amber:#C98A2C;
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
    padding-bottom: calc(var(--nav-h) + var(--safe-bottom) + 110px);
  }

  ::selection{ background:var(--green); color:#fff; }

  /* ---------- Header ---------- */
  header.top{
    position:sticky; top:0; z-index:20;
    background:var(--paper);
    border-bottom:1px solid var(--line);
    padding:14px 16px 10px;
  }
  .top-row{
    display:flex; align-items:center; justify-content:space-between;
  }
  .brand{
    font-family:'Archivo Black', sans-serif;
    font-size:1.15rem;
    letter-spacing:0.01em;
    line-height:1;
  }
  .brand span{ color:var(--green); }
  .brand small{
    display:block; margin-top:3px;
    font-family:'Inter'; font-weight:500; font-size:0.68rem;
    letter-spacing:0.12em; text-transform:uppercase; color:var(--ink-soft);
  }
  .cart-chip{
    position:relative;
    display:flex; align-items:center; gap:6px;
    background:var(--ink); color:var(--paper);
    border:none; border-radius:999px;
    padding:9px 14px 9px 12px;
    font-family:'Inter'; font-weight:600; font-size:0.85rem;
    cursor:pointer;
  }
  .cart-chip svg{ width:18px; height:18px; }
  .cart-badge{
    position:absolute; top:-6px; right:-6px;
    background:var(--red); color:#fff;
    font-size:0.65rem; font-weight:700;
    min-width:18px; height:18px; border-radius:999px;
    display:flex; align-items:center; justify-content:center;
    padding:0 4px;
    border:2px solid var(--paper);
  }

  /* ---------- Category filter ---------- */
  .filter-scroll{
    margin-top:12px;
    display:flex; gap:8px;
    overflow-x:auto;
    padding-bottom:2px;
    scrollbar-width:none;
  }
  .filter-scroll::-webkit-scrollbar{ display:none; }
  .pill{
    flex:0 0 auto;
    border:1px solid var(--line);
    background:var(--card);
    color:var(--ink-soft);
    font-family:'Inter'; font-weight:600; font-size:0.82rem;
    padding:8px 15px;
    border-radius:999px;
    cursor:pointer;
    white-space:nowrap;
    transition:background .15s ease, color .15s ease, border-color .15s ease;
  }
  .pill.active{
    background:var(--ink); color:var(--paper); border-color:var(--ink);
  }

  /* ---------- Menu list ---------- */
  main{ padding:16px; }
  .section-label{
    font-family:'IBM Plex Mono', monospace;
    font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase;
    color:var(--ink-soft);
    margin:18px 0 8px;
  }
  .section-label:first-child{ margin-top:0; }

  .item{
    display:flex; align-items:center; gap:12px;
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:12px;
    margin-bottom:10px;
  }
  .item-thumb{
    width:52px; height:52px; border-radius:10px;
    flex:0 0 auto;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem;
    background:var(--paper-dim);
  }
  .item-info{ flex:1; min-width:0; }
  .item-name{
    font-weight:600; font-size:0.95rem; line-height:1.25;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .item-price{
    font-family:'IBM Plex Mono', monospace;
    font-weight:500; font-size:0.85rem;
    color:var(--green-dark);
    margin-top:3px;
  }
  .item-add{
    flex:0 0 auto;
    border:1.5px solid var(--green);
    color:var(--green-dark);
    background:transparent;
    font-family:'Inter'; font-weight:700; font-size:0.82rem;
    padding:8px 14px;
    border-radius:999px;
    cursor:pointer;
    transition:background .15s ease, color .15s ease;
  }
  .item-add:active{ background:var(--green); color:#fff; }
  .item-add.added{ background:var(--green); color:#fff; }

  .empty-state{
    text-align:center; padding:40px 20px; color:var(--ink-soft);
    font-size:0.9rem;
  }

  /* ---------- Cart bar (collapsed) ---------- */
  .cart-bar{
    position:fixed; left:0; right:0; z-index:30;
    bottom:calc(var(--nav-h) + var(--safe-bottom));
    background:var(--ink); color:var(--paper);
    padding:14px 16px;
    display:flex; align-items:center; justify-content:space-between;
    cursor:pointer;
    border-radius:16px;
    box-shadow:0 -6px 24px rgba(0,0,0,0.18);
    margin:0 10px 8px;
  }
  .cart-bar-left{ display:flex; align-items:center; gap:10px; }
  .cart-bar-count{
    background:var(--green); color:#fff;
    font-family:'IBM Plex Mono'; font-weight:600; font-size:0.78rem;
    border-radius:999px; padding:4px 10px;
  }
  .cart-bar-label{ font-weight:600; font-size:0.9rem; }
  .cart-bar-total{
    font-family:'IBM Plex Mono', monospace; font-weight:600; font-size:1rem;
  }
  .cart-bar-chevron{ font-size:0.7rem; color:var(--ink-soft); margin-left:8px; }

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

  /* ---------- Cart drawer (expanded, receipt style) ---------- */
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
    max-height:82vh;
    transform:translateY(100%);
    transition:transform .25s cubic-bezier(.32,.72,0,1);
    display:flex; flex-direction:column;
    padding-bottom: var(--safe-bottom);
  }
  .drawer.show{ transform:translateY(0); }

  .drawer-handle{
    width:36px; height:4px; background:var(--line);
    border-radius:999px; margin:10px auto 4px;
  }
  .drawer-head{
    display:flex; align-items:center; justify-content:space-between;
    padding:8px 18px 12px;
  }
  .drawer-title{ font-family:'Archivo Black'; font-size:1rem; }
  .drawer-close{
    border:none; background:var(--paper-dim); color:var(--ink);
    width:30px; height:30px; border-radius:999px;
    font-size:1rem; line-height:1; cursor:pointer;
  }

  .receipt-edge{
    height:10px;
    background-image: radial-gradient(circle at 8px 5px, var(--paper) 5px, transparent 5.5px);
    background-size: 16px 10px;
    background-repeat: repeat-x;
    margin:0 14px;
  }

  .drawer-body{
    flex:1; overflow-y:auto;
    padding:14px 18px 6px;
  }
  .cart-row{
    display:flex; align-items:center; gap:10px;
    padding:9px 0;
    border-bottom:1px dashed var(--line);
    font-family:'IBM Plex Mono', monospace; font-size:0.82rem;
  }
  .cart-row-name{
    flex:1; font-family:'Inter'; font-weight:600; font-size:0.88rem;
    color:var(--ink);
  }
  .qty-control{
    display:flex; align-items:center; gap:8px;
    background:var(--paper-dim); border-radius:999px; padding:3px;
  }
  .qty-btn{
    width:22px; height:22px; border-radius:999px; border:none;
    background:var(--card); color:var(--ink);
    font-weight:700; cursor:pointer; font-size:0.85rem;
    display:flex; align-items:center; justify-content:center;
  }
  .qty-val{ min-width:16px; text-align:center; font-weight:600; font-size:0.85rem; }
  .cart-row-price{ width:78px; text-align:right; color:var(--ink-soft); }

  .cart-empty{
    text-align:center; padding:30px 10px 20px; color:var(--ink-soft); font-size:0.88rem;
  }

  .drawer-summary{
    padding:12px 18px 0;
    font-family:'IBM Plex Mono', monospace; font-size:0.85rem;
    border-top:1px dashed var(--line);
  }
  .summary-line{ display:flex; justify-content:space-between; padding:4px 0; color:var(--ink-soft); }
  .summary-total{
    display:flex; justify-content:space-between;
    padding:8px 0 2px; font-size:1.05rem; font-weight:700; color:var(--ink);
  }

  .drawer-actions{
    display:flex; gap:10px;
    padding:14px 18px 18px;
  }
  .btn{
    flex:1; border:none; border-radius:999px;
    font-family:'Inter'; font-weight:700; font-size:0.95rem;
    padding:14px 10px;
    cursor:pointer;
    transition:transform .1s ease, opacity .15s ease;
  }
  .btn:active{ transform:scale(0.97); }
  .btn-save{
    background:var(--paper-dim); color:var(--ink);
    border:1.5px solid var(--line);
  }
  .btn-pay{
    background:var(--green); color:#fff;
    box-shadow:0 4px 14px rgba(47,111,79,0.35);
  }
  .btn:disabled{ opacity:0.45; cursor:not-allowed; }

  .toast{
    position:fixed; left:50%; bottom:110px; transform:translateX(-50%) translateY(20px);
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
    .app{ width:100%; max-width:480px; box-shadow:0 0 0 1px var(--line); min-height:100vh; }
    .drawer, .cart-bar, .overlay, .bottom-nav{ max-width:480px; margin:0 auto; }
    .cart-bar{ max-width:460px; margin-left:auto; margin-right:auto; left:0; right:0; }
  }
</style>
</head>
<body>
<div class="app">

  <header class="top">
    <div class="top-row">
      <div class="brand"><span>Warung</span> Kita
        <small>Point of Sale</small>
      </div>
      <button class="cart-chip" id="peekCartBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h7.2a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
        Cart
        <span class="cart-badge" id="badgeCount">0</span>
      </button>
    </div>

    <div class="filter-scroll" id="filterScroll">
      <button class="pill active" data-cat="all">All</button>
      <button class="pill" data-cat="SALAD & FRUIT SOUP">SALAD & FRUIT SOUP</button>
      <button class="pill" data-cat="YAKULT + UHT MENU">YAKULT + UHT MENU</button>
      <button class="pill" data-cat="ORIGINAL JUICE">ORIGINAL JUICE</button>
      <button class="pill" data-cat="MIXED JUICE">MIXED JUICE</button>
      <button class="pill" data-cat="OTHER DRINKS">OTHER DRINKS</button>
    </div>
  </header>

  <main id="menuList">
      <div class="empty-state">Loading menus...</div>
  </main>

  <!-- Collapsed cart bar -->
  <div class="cart-bar" id="cartBar" style="display:none;">
    <div class="cart-bar-left">
      <span class="cart-bar-count" id="barCount">0 items</span>
      <span class="cart-bar-label">View order</span>
    </div>
    <div style="display:flex; align-items:center;">
      <span class="cart-bar-total" id="barTotal">Rp 0</span>
      <span class="cart-bar-chevron">▲</span>
    </div>
  </div>

  </div>

  <nav class="bottom-nav">
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobilePos']) ?>" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
      <span>Order</span>
    </a>
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobileTransactions']) ?>" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h9l3 3v17H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      <span>Transactions</span>
    </a>
  </nav>

</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="drawer">
  <div class="drawer-handle"></div>
  <div class="drawer-head">
    <div class="drawer-title">Your Order</div>
    <button class="drawer-close" id="closeDrawer">✕</button>
  </div>
  <div class="receipt-edge"></div>
  <div class="drawer-body" id="drawerBody"></div>
  <div class="drawer-summary" id="drawerSummary" style="display:none;">
    <!-- Customer input needed for real transactions -->
    <div style="margin-bottom:12px;">
      <input type="text" id="customerName" placeholder="Customer Name (Optional)" style="width:100%; padding:8px 12px; border:1px solid var(--line); border-radius:6px; font-family:'Inter'; font-size:0.85rem;" />
    </div>
    <div class="summary-line"><span>Subtotal</span><span id="sumSubtotal">Rp 0</span></div>
    <!-- Removed tax line as per usual POS setups if not needed, but keep it if requested. Setting tax to 0 for simplicity -->
    <div class="summary-total"><span>Total</span><span class="amt" id="sumTotal">Rp 0</span></div>
  </div>
  <div class="drawer-actions">
    <button class="btn btn-save" id="saveBtn" disabled>Save order</button>
    <button class="btn btn-pay" id="payBtn" disabled>Pay now</button>
  </div>
</div>

<!-- --- START NEW PAYMENT DRAWER (Can be deleted if not needed) --- -->
<div class="drawer" id="paymentDrawer">
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
    <button class="btn btn-save" id="cancelPaymentBtn">Cancel</button>
    <button class="btn btn-pay" id="confirmPaymentBtn" disabled>Confirm Payment</button>
  </div>
</div>
<!-- --- END NEW PAYMENT DRAWER --- -->

<div class="toast" id="toast"></div>

<script>
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

  let MENU = [];
  let activeCat = "all";
  let cart = {}; // id -> qty

  const rupiah = n => "Rp " + n.toLocaleString("id-ID");

  const menuList = document.getElementById("menuList");
  const badgeCount = document.getElementById("badgeCount");
  const cartBar = document.getElementById("cartBar");
  const barCount = document.getElementById("barCount");
  const barTotal = document.getElementById("barTotal");
  const drawer = document.getElementById("drawer");
  const overlay = document.getElementById("overlay");
  const drawerBody = document.getElementById("drawerBody");
  const drawerSummary = document.getElementById("drawerSummary");
  const saveBtn = document.getElementById("saveBtn");
  const payBtn = document.getElementById("payBtn");
  const toast = document.getElementById("toast");
  const customerName = document.getElementById("customerName");

  function getIconForCat(catName) {
      if (!catName) return "🍽️";
      const c = catName.toUpperCase();
      if (c.includes("SALAD") || c.includes("SOUP")) return "🥗";
      if (c.includes("YAKULT") || c.includes("UHT")) return "🥛";
      if (c.includes("MIXED JUICE")) return "🍹";
      if (c.includes("JUICE") && !c.includes("MIXED")) return "🍊";
      if (c.includes("DRINK")) return "🥤";
      return "🍽️";
  }

  function renderMenu(){
    const groups = {};
    MENU.forEach(item=>{
      const catName = item.category ? item.category.name : 'OTHER DRINKS';
      if(activeCat !== "all" && catName !== activeCat) return;
      groups[catName] = groups[catName] || [];
      groups[catName].push(item);
    });

    if(Object.keys(groups).length === 0){
      menuList.innerHTML = `<div class="empty-state">No items in this category yet.</div>`;
      return;
    }

    let html = "";
    Object.keys(groups).forEach(cat=>{
      html += `<div class="section-label">${cat}</div>`;
      groups[cat].forEach(item=>{
        const qty = cart[item.id] || 0;
        const icon = getIconForCat(cat);
        html += `
          <div class="item">
            <div class="item-thumb">${icon}</div>
            <div class="item-info">
              <div class="item-name">${item.name}</div>
              <div class="item-price">${rupiah(item.price)}</div>
            </div>
            <button class="item-add ${qty>0 ? 'added':''}" data-id="${item.id}">
              ${qty>0 ? qty + ' in cart' : '+ Add'}
            </button>
          </div>`;
      });
    });
    menuList.innerHTML = html;
  }

  function cartTotals(){
    let count = 0, subtotal = 0;
    Object.entries(cart).forEach(([id, qty])=>{
      const item = MENU.find(m=>m.id == id);
      if(item) {
          count += qty;
          subtotal += item.price * qty;
      }
    });
    return { count, total: subtotal };
  }

  function renderCart(){
    const { count, total } = cartTotals();

    badgeCount.textContent = count;
    barCount.textContent = count + (count === 1 ? " item" : " items");
    barTotal.textContent = rupiah(total);
    cartBar.style.display = count > 0 ? "flex" : "none";

    if(count === 0){
      drawerBody.innerHTML = `<div class="cart-empty">Your cart is empty.<br>Tap “+ Add” on any item to start an order.</div>`;
      drawerSummary.style.display = "none";
      saveBtn.disabled = true;
      payBtn.disabled = true;
    } else {
      let html = "";
      Object.entries(cart).forEach(([id, qty])=>{
        const item = MENU.find(m=>m.id == id);
        if(!item) return;
        html += `
          <div class="cart-row">
            <div class="cart-row-name">${item.name}</div>
            <div class="qty-control">
              <button class="qty-btn" data-action="dec" data-id="${item.id}">–</button>
              <span class="qty-val">${qty}</span>
              <button class="qty-btn" data-action="inc" data-id="${item.id}">+</button>
            </div>
            <div class="cart-row-price">${rupiah(item.price * qty)}</div>
          </div>`;
      });
      drawerBody.innerHTML = html;
      drawerSummary.style.display = "block";
      document.getElementById("sumSubtotal").textContent = rupiah(total);
      document.getElementById("sumTotal").textContent = rupiah(total);
      saveBtn.disabled = false;
      payBtn.disabled = false;
    }

    renderMenu();
  }

  function openDrawer(){
    drawer.classList.add("show");
    overlay.classList.add("show");
  }
  function closeDrawer(){
    drawer.classList.remove("show");
    overlay.classList.remove("show");
  }
  function showToast(msg, isError = false){
    toast.textContent = msg;
    if(isError) toast.style.background = 'var(--red)';
    else toast.style.background = 'var(--ink)';
    
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 1800);
  }

  document.getElementById("filterScroll").addEventListener("click", e=>{
    const btn = e.target.closest(".pill");
    if(!btn) return;
    document.querySelectorAll(".pill").forEach(p=>p.classList.remove("active"));
    btn.classList.add("active");
    activeCat = btn.dataset.cat;
    renderMenu();
  });

  menuList.addEventListener("click", e=>{
    const btn = e.target.closest(".item-add");
    if(!btn) return;
    const id = btn.dataset.id;
    cart[id] = (cart[id] || 0) + 1;
    renderCart();
  });

  drawerBody.addEventListener("click", e=>{
    const btn = e.target.closest(".qty-btn");
    if(!btn) return;
    const id = btn.dataset.id;
    if(btn.dataset.action === "inc") cart[id]++;
    else {
      cart[id]--;
      if(cart[id] <= 0) delete cart[id];
    }
    renderCart();
  });

  document.getElementById("peekCartBtn").addEventListener("click", openDrawer);
  cartBar.addEventListener("click", openDrawer);
  document.getElementById("closeDrawer").addEventListener("click", closeDrawer);
  overlay.addEventListener("click", closeDrawer);

  async function saveTransaction(status, customPaidAmount = null, customChangeAmount = null) {
      if (Object.keys(cart).length === 0) return showToast('Cart is empty', true);
      
      const { total } = cartTotals();
      
      const transactionItems = Object.entries(cart).map(([id, qty]) => {
          const item = MENU.find(m => m.id == id);
          return { menu_id: id, qty: qty, price: item.price, subtotal: item.price * qty };
      });
      
      const payload = {
          customer_name: customerName.value.trim() || 'Guest',
          total: total,
          status: status,
          transaction_items: transactionItems
      };
      
      if (status === 'paid') {
          payload.paid_amount = customPaidAmount !== null ? customPaidAmount : total; 
          payload.change_amount = customChangeAmount !== null ? customChangeAmount : 0;
      }
      
      saveBtn.disabled = true;
      payBtn.disabled = true;
      
      const res = await api('transactions/add.json', 'POST', payload);
      
      if (res && res.success) {
          showToast(status === 'paid' ? "Payment confirmed ✓" : "Order saved ✓");
          setTimeout(()=>{
            cart = {};
            customerName.value = '';
            renderCart();
            closeDrawer();
          }, 900);
      } else {
          console.error('Save failed:', res);
          const errorMsg = res && res.errors ? JSON.stringify(res.errors) : 'Network or server error';
          showToast('Failed to save order. Check console.', true);
          alert('Validation Error: ' + errorMsg);
          saveBtn.disabled = false;
          payBtn.disabled = false;
      }
  }

  // --- START NEW PAYMENT DRAWER LOGIC (Can be deleted if not needed) ---
  const paymentDrawer = document.getElementById("paymentDrawer");
  const payReceivedInput = document.getElementById("pay-received");
  const confirmPaymentBtn = document.getElementById("confirmPaymentBtn");
  const payChangeWrapper = document.getElementById("pay-change-wrapper");
  const payChangeAmount = document.getElementById("pay-change");
  
  let currentTotalDue = 0;
  let amountReceived = 0;

  function closePaymentDrawer() {
    paymentDrawer.classList.remove("show");
  }

  // Hook up the "Pay now" button from the main cart to open this new drawer instead
  payBtn.addEventListener("click", () => {
    const { total } = cartTotals();
    currentTotalDue = total;
    amountReceived = 0;
    
    document.getElementById("pay-total").textContent = rupiah(total);
    payReceivedInput.value = "";
    payChangeWrapper.style.display = "none";
    confirmPaymentBtn.disabled = true;

    // Generate smart "Quick Cash" buttons (Exact, next 50k, next 100k)
    const exact = total;
    const fifty = Math.ceil(total / 50000) * 50000;
    const hundred = Math.ceil(total / 100000) * 100000;
    
    // Only show unique amounts to avoid duplicates
    const options = [...new Set([exact, fifty, hundred])];
    
    document.getElementById("quick-cash-container").innerHTML = options.map(amt => `
      <button class="quick-cash-btn" data-amt="${amt}">
        ${amt === exact ? 'Exact ' : ''}${rupiah(amt)}
      </button>
    `).join("");

    paymentDrawer.classList.add("show");
  });

  // Handle typing into the Custom Amount input
  payReceivedInput.addEventListener("input", (e) => {
    let rawValue = e.target.value.replace(/[^0-9]/g, "");
    amountReceived = rawValue ? parseInt(rawValue, 10) : 0;
    e.target.value = rawValue ? amountReceived.toLocaleString("id-ID") : "";
    document.querySelectorAll(".quick-cash-btn").forEach(b => b.classList.remove("selected"));
    calculateChange();
  });

  // Handle Quick Cash button clicks
  document.getElementById("quick-cash-container").addEventListener("click", (e) => {
    const btn = e.target.closest(".quick-cash-btn");
    if (!btn) return;
    document.querySelectorAll(".quick-cash-btn").forEach(b => b.classList.remove("selected"));
    btn.classList.add("selected");
    amountReceived = parseInt(btn.dataset.amt, 10);
    payReceivedInput.value = amountReceived.toLocaleString("id-ID");
    calculateChange();
  });

  // Calculate change and enable the Confirm button
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

  // Finalize payment 
  confirmPaymentBtn.addEventListener("click", () => {
     saveTransaction('paid', amountReceived, amountReceived - currentTotalDue);
     closePaymentDrawer();
  });

  document.getElementById("cancelPaymentBtn").addEventListener("click", closePaymentDrawer);
  document.getElementById("closePaymentDrawer").addEventListener("click", closePaymentDrawer);
  // --- END NEW PAYMENT DRAWER LOGIC ---

  saveBtn.addEventListener("click", () => saveTransaction('pending'));

  async function loadAllMenus() {
      let page = 1;
      let hasMore = true;
      while (hasMore) {
          const res = await api(`menus.json?page=${page}`);
          if (res && res.menus) {
              MENU = [...MENU, ...res.menus];
              if (res.paging && res.paging.nextPage) {
                  page++;
              } else {
                  hasMore = false;
              }
          } else {
              hasMore = false;
          }
      }
      renderMenu();
  }

  loadAllMenus();
</script>

</body>
</html>
