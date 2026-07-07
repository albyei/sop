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
    --nav-h:60px;
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
  .summary-total .amt{ color:var(--red); }

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

  @media (min-width:560px){
    body{ display:flex; justify-content:center; }
    .app{ width:100%; max-width:480px; box-shadow:0 0 0 1px var(--line); min-height:100vh; }
    .drawer, .overlay, .bottom-nav{ max-width:480px; margin:0 auto; }
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
      <button class="pill" data-cat="coffee">Coffee</button>
      <button class="pill" data-cat="food">Food</button>
      <button class="pill" data-cat="dessert">Dessert</button>
      <button class="pill" data-cat="drinks">Cold Drinks</button>
    </div>
  </header>

  <main id="menuList"></main>

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

  <nav class="bottom-nav">
    <a href="pos.md" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
      <span>Order</span>
    </a>
    <a href="pTransactions.md" class="nav-item">
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
    <div class="summary-line"><span>Subtotal</span><span id="sumSubtotal">Rp 0</span></div>
    <div class="summary-line"><span>Tax (10%)</span><span id="sumTax">Rp 0</span></div>
    <div class="summary-total"><span>Total</span><span class="amt" id="sumTotal">Rp 0</span></div>
  </div>
  <div class="drawer-actions">
    <button class="btn btn-save" id="saveBtn" disabled>Save order</button>
    <button class="btn btn-pay" id="payBtn" disabled>Pay now</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
  const MENU = [
    { id:1, name:"Espresso", cat:"coffee", price:18000, icon:"☕" },
    { id:2, name:"Cappuccino", cat:"coffee", price:24000, icon:"☕" },
    { id:3, name:"Caramel Latte", cat:"coffee", price:27000, icon:"☕" },
    { id:4, name:"Nasi Goreng", cat:"food", price:28000, icon:"🍛" },
    { id:5, name:"Mie Ayam", cat:"food", price:22000, icon:"🍜" },
    { id:6, name:"Chicken Satay (5pcs)", cat:"food", price:30000, icon:"🍢" },
    { id:7, name:"Croffle", cat:"dessert", price:19000, icon:"🥐" },
    { id:8, name:"Choco Lava Cake", cat:"dessert", price:23000, icon:"🍫" },
    { id:9, name:"Es Teh Manis", cat:"drinks", price:8000, icon:"🥤" },
    { id:10, name:"Es Jeruk", cat:"drinks", price:10000, icon:"🍊" },
    { id:11, name:"Mineral Water", cat:"drinks", price:6000, icon:"💧" },
  ];

  const CAT_LABELS = { coffee:"Coffee", food:"Food", dessert:"Dessert", drinks:"Cold Drinks" };

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

  function renderMenu(){
    const groups = {};
    MENU.forEach(item=>{
      if(activeCat !== "all" && item.cat !== activeCat) return;
      groups[item.cat] = groups[item.cat] || [];
      groups[item.cat].push(item);
    });

    if(Object.keys(groups).length === 0){
      menuList.innerHTML = `<div class="empty-state">No items in this category yet.</div>`;
      return;
    }

    let html = "";
    Object.keys(groups).forEach(cat=>{
      html += `<div class="section-label">${CAT_LABELS[cat]}</div>`;
      groups[cat].forEach(item=>{
        const qty = cart[item.id] || 0;
        html += `
          <div class="item">
            <div class="item-thumb">${item.icon}</div>
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
      count += qty;
      subtotal += item.price * qty;
    });
    const tax = Math.round(subtotal * 0.1);
    return { count, subtotal, tax, total: subtotal + tax };
  }

  function renderCart(){
    const { count, subtotal, tax, total } = cartTotals();

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
      document.getElementById("sumSubtotal").textContent = rupiah(subtotal);
      document.getElementById("sumTax").textContent = rupiah(tax);
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
  function showToast(msg){
    toast.textContent = msg;
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

  saveBtn.addEventListener("click", ()=>{
    showToast("Order saved ✓");
  });
  payBtn.addEventListener("click", ()=>{
    showToast("Payment confirmed ✓");
    setTimeout(()=>{
      cart = {};
      renderCart();
      closeDrawer();
    }, 900);
  });

  renderMenu();
  renderCart();
</script>

</body>
</html>