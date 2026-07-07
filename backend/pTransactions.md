<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Warung Kita — Transactions</title>
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
    padding-bottom: calc(var(--nav-h) + var(--safe-bottom) + 20px);
  }

  /* ---------- Header ---------- */
  header.top{
    position:sticky; top:0; z-index:20;
    background:var(--paper);
    border-bottom:1px solid var(--line);
    padding:14px 16px 14px;
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

  /* ---------- Main Transactions ---------- */
  main{ padding:16px; }
  
  .info-banner {
    background: var(--paper-dim);
    border-radius: var(--radius);
    padding: 12px;
    margin-bottom: 16px;
    font-size: 0.85rem;
    color: var(--ink-soft);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .info-banner svg { width: 20px; height: 20px; flex-shrink: 0; }

  .tx-card {
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:14px;
    margin-bottom:12px;
  }
  .tx-head {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:10px;
    padding-bottom:10px;
    border-bottom:1px dashed var(--line);
  }
  .tx-id {
    font-family:'IBM Plex Mono', monospace; font-weight:600; font-size:0.85rem;
  }
  .tx-time {
    font-size:0.75rem; color:var(--ink-soft); font-weight:500;
  }
  .tx-items {
    font-size:0.85rem; color:var(--ink); line-height:1.5;
  }
  .tx-item-row {
    display:flex; justify-content:space-between; margin-bottom:4px;
  }
  .tx-total-row {
    display:flex; justify-content:space-between; align-items:center;
    margin-top:10px; padding-top:10px;
    border-top:1px dashed var(--line);
  }
  .tx-total-label {
    font-weight:600; font-size:0.85rem; color:var(--ink-soft);
  }
  .tx-total-val {
    font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:1.05rem; color:var(--green-dark);
  }

  .empty-state{
    text-align:center; padding:40px 20px; color:var(--ink-soft);
    font-size:0.9rem;
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

  @media (min-width:560px){
    body{ display:flex; justify-content:center; }
    .app{ width:100%; max-width:480px; box-shadow:0 0 0 1px var(--line); min-height:100vh; position:relative; }
    .bottom-nav{ max-width:480px; margin:0 auto; position:absolute; }
  }
</style>
</head>
<body>
<div class="app">

  <header class="top">
    <div class="top-row">
      <div class="brand"><span>Warung</span> Kita
        <small>Transactions</small>
      </div>
    </div>
  </header>

  <main>
    <div class="info-banner">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
      <span>Showing transactions for the current day. Resets daily at 03:00 AM.</span>
    </div>
    
    <div id="transactionsList"></div>
  </main>

  <nav class="bottom-nav">
    <a href="pos.md" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
      <span>Order</span>
    </a>
    <a href="pTransactions.md" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h9l3 3v17H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      <span>Transactions</span>
    </a>
  </nav>

</div>

<script>
  // Helper to format currency
  const rupiah = n => "Rp " + n.toLocaleString("id-ID");

  // Determine the start time of the current "business day" (03:00 cutoff)
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

  // Dummy transactions data
  // We'll generate a few that are "recent" (after 03:00) and one that is "old" (before 03:00) to test the filter
  function generateDummyData() {
    const now = new Date();
    
    // A recent transaction (1 hour ago)
    const d1 = new Date(now);
    d1.setHours(now.getHours() - 1);

    // Another recent transaction (2 hours ago)
    const d2 = new Date(now);
    d2.setHours(now.getHours() - 2);

    // An old transaction (yesterday, definitely before today's 03:00 cutoff)
    const d3 = new Date(now);
    d3.setDate(d3.getDate() - 1);
    d3.setHours(14, 0, 0, 0); 
    
    // Edge case transaction (e.g. at 2:00 AM today, might be hidden if current time is > 3:00 AM)
    const d4 = new Date(now);
    d4.setHours(2, 30, 0, 0);

    return [
      {
        id: "TRX-8930",
        time: d1,
        items: [{name: "Espresso", qty: 2, price: 36000}, {name: "Croffle", qty: 1, price: 19000}],
        total: 55000 + 5500 // +10% tax
      },
      {
        id: "TRX-8929",
        time: d2,
        items: [{name: "Nasi Goreng", qty: 1, price: 28000}, {name: "Es Teh Manis", qty: 1, price: 8000}],
        total: 36000 + 3600
      },
      {
        id: "TRX-8928",
        time: d4,
        items: [{name: "Mineral Water", qty: 3, price: 18000}],
        total: 18000 + 1800
      },
      {
        id: "TRX-8910",
        time: d3,
        items: [{name: "Cappuccino", qty: 1, price: 24000}],
        total: 24000 + 2400
      }
    ];
  }

  function renderTransactions() {
    const list = document.getElementById("transactionsList");
    const allData = generateDummyData();
    const businessDayStart = getBusinessDayStart();

    // Filter to only show transactions that occurred ON or AFTER the calculated 03:00 cutoff
    const filteredData = allData.filter(tx => tx.time >= businessDayStart);

    // Sort newest first
    filteredData.sort((a, b) => b.time - a.time);

    if (filteredData.length === 0) {
      list.innerHTML = `<div class="empty-state">No transactions yet for today's shift.</div>`;
      return;
    }

    let html = "";
    filteredData.forEach(tx => {
      // Format time
      const timeStr = tx.time.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
      const dateStr = tx.time.toLocaleDateString();

      let itemsHtml = "";
      tx.items.forEach(item => {
        itemsHtml += `
          <div class="tx-item-row">
            <span>${item.qty}x ${item.name}</span>
            <span style="color:var(--ink-soft)">${rupiah(item.price)}</span>
          </div>`;
      });

      html += `
        <div class="tx-card">
          <div class="tx-head">
            <span class="tx-id">${tx.id}</span>
            <span class="tx-time">${dateStr} • ${timeStr}</span>
          </div>
          <div class="tx-items">
            ${itemsHtml}
          </div>
          <div class="tx-total-row">
            <span class="tx-total-label">Total</span>
            <span class="tx-total-val">${rupiah(tx.total)}</span>
          </div>
        </div>
      `;
    });

    list.innerHTML = html;
  }

  // Initialize
  renderTransactions();
</script>

</body>
</html>