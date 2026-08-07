<?php
$this->disableAutoLayout();
$identity = $this->request->getAttribute('identity');

// Check login & role
if (!$identity) {
    header("Location: " . $this->Url->build(['controller' => 'Users', 'action' => 'login']));
    exit;
}
if ($identity->get('role') !== 'admin') {
    header("Location: " . $this->Url->build(['controller' => 'Pages', 'action' => 'mobileAccount']));
    exit;
}

$TransactionsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Transactions');
$allTransactions = $TransactionsTable->find()->all();

$pendingCount = 0;
$pendingAmount = 0;
$paidTransactions = [];

foreach ($allTransactions as $t) {
    if ($t->status === 'pending') {
        $pendingCount++;
        $pendingAmount += $t->total;
    } elseif ($t->status === 'paid') {
        $paidTransactions[] = $t;
    }
}

// Daily data: last 7 days
$dailyLabels = [];
$dailyValues = [];
$dailyOrders = 0;
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dailyLabels[] = date('D', strtotime($date));
    
    $income = 0;
    foreach ($paidTransactions as $t) {
        if ($t->created->format('Y-m-d') === $date) {
            $income += $t->total;
            if ($i === 0) $dailyOrders++;
        }
    }
    $dailyValues[] = $income;
}

// Weekly data: last 6 weeks
$weeklyLabels = [];
$weeklyValues = [];
$weeklyOrders = 0;
for ($i = 5; $i >= 0; $i--) {
    $weekStart = date('Y-m-d', strtotime("monday this week -$i weeks"));
    $weekEnd = date('Y-m-d', strtotime("sunday this week -$i weeks"));
    $weeklyLabels[] = "W" . (6 - $i);
    
    $income = 0;
    foreach ($paidTransactions as $t) {
        $tDate = $t->created->format('Y-m-d');
        if ($tDate >= $weekStart && $tDate <= $weekEnd) {
            $income += $t->total;
            if ($i === 0) $weeklyOrders++;
        }
    }
    $weeklyValues[] = $income;
}
$weeklyLabels[5] = "This Wk";

// Monthly data: last 6 months
$monthlyLabels = [];
$monthlyValues = [];
$monthlyOrders = 0;
for ($i = 5; $i >= 0; $i--) {
    $monthStr = date('Y-m', strtotime("first day of this month -$i months"));
    $monthlyLabels[] = date('M', strtotime($monthStr . '-01'));
    
    $income = 0;
    foreach ($paidTransactions as $t) {
        if ($t->created->format('Y-m') === $monthStr) {
            $income += $t->total;
            if ($i === 0) $monthlyOrders++;
        }
    }
    $monthlyValues[] = $income;
}

$DATA = [
    'daily' => [
        'label' => "Today's income",
        'compareNote' => "vs yesterday",
        'chartTitle' => "Last 7 days",
        'values' => $dailyValues,
        'labels' => $dailyLabels,
        'orders' => $dailyOrders,
    ],
    'weekly' => [
        'label' => "This week's income",
        'compareNote' => "vs last week",
        'chartTitle' => "Last 6 weeks",
        'values' => $weeklyValues,
        'labels' => $weeklyLabels,
        'orders' => $weeklyOrders,
    ],
    'monthly' => [
        'label' => "This month's income",
        'compareNote' => "vs last month",
        'chartTitle' => "Last 6 months",
        'values' => $monthlyValues,
        'labels' => $monthlyLabels,
        'orders' => $monthlyOrders,
    ]
];
$PENDING = [
    'count' => $pendingCount,
    'amount' => $pendingAmount
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Admin Dashboard — Warung Kita POS</title>
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
    --green-tint:#BFDACB;
    --amber:#C98A2C;
    --amber-bg:#FBF0DD;
    --red:#C1272D;
    --radius:14px;
    --safe-bottom: env(safe-area-inset-bottom, 0px);
    --safe-top: env(safe-area-inset-top, 0px);
  }

  *{ box-sizing:border-box; }
  html,body{ margin:0; padding:0; }
  body{
    background:var(--paper);
    color:var(--ink);
    font-family:'Inter', sans-serif;
    -webkit-font-smoothing:antialiased;
    min-height:100vh;
    padding-bottom:calc(30px + var(--safe-bottom));
  }
  ::selection{ background:var(--green); color:#fff; }

  /* ---------- Header ---------- */
  header.top{
    position:sticky; top:0; z-index:20;
    background:var(--paper);
    border-bottom:1px solid var(--line);
    padding:calc(14px + var(--safe-top)) 16px 14px;
    display:flex; align-items:center; gap:12px;
  }
  .back-btn{
    width:34px; height:34px; border-radius:999px;
    background:var(--paper-dim); border:none;
    display:flex; align-items:center; justify-content:center;
    color:var(--ink); cursor:pointer; flex:0 0 auto;
  }
  .back-btn svg{ width:18px; height:18px; }
  .brand{
    font-family:'Archivo Black', sans-serif;
    font-size:1.05rem;
    line-height:1;
  }
  .brand small{
    display:block; margin-top:3px;
    font-family:'Inter'; font-weight:500; font-size:0.68rem;
    letter-spacing:0.1em; text-transform:uppercase; color:var(--ink-soft);
  }
  .admin-tag{
    margin-left:auto;
    font-family:'IBM Plex Mono', monospace; font-size:0.66rem; font-weight:600;
    letter-spacing:0.06em; text-transform:uppercase;
    color:var(--green-dark); background:var(--green-bg);
    padding:5px 10px; border-radius:999px;
    flex:0 0 auto;
  }

  main{ padding:16px; }

  /* ---------- Period tabs ---------- */
  .tabs{
    display:flex; gap:6px;
    background:var(--paper-dim);
    border-radius:999px;
    padding:4px;
    margin-bottom:16px;
  }
  .tab{
    flex:1; border:none; background:transparent;
    color:var(--ink-soft);
    font-family:'Inter'; font-weight:600; font-size:0.84rem;
    padding:9px 8px; border-radius:999px; cursor:pointer;
    transition:background .15s ease, color .15s ease;
  }
  .tab.active{ background:var(--card); color:var(--ink); box-shadow:0 1px 3px rgba(0,0,0,0.08); }

  /* ---------- Hero income card ---------- */
  .hero-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:20px 18px;
    margin-bottom:12px;
  }
  .hero-label{
    font-family:'IBM Plex Mono', monospace; font-size:0.72rem;
    letter-spacing:0.08em; text-transform:uppercase; color:var(--ink-soft);
    margin-bottom:6px;
  }
  .hero-value{
    font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:1.9rem;
    color:var(--ink);
    line-height:1.1;
  }
  .hero-compare{
    display:inline-flex; align-items:center; gap:4px;
    margin-top:10px;
    font-family:'Inter'; font-weight:700; font-size:0.78rem;
    padding:4px 10px; border-radius:999px;
  }
  .hero-compare.up{ background:var(--green-bg); color:var(--green-dark); }
  .hero-compare.down{ background:#FBE9E9; color:var(--red); }
  .hero-compare svg{ width:13px; height:13px; }
  .hero-compare-note{ color:var(--ink-soft); font-weight:500; font-size:0.78rem; margin-left:6px; }

  /* ---------- Stat row ---------- */
  .stat-row{
    display:flex; align-items:stretch;
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:14px 8px;
    margin-bottom:12px;
  }
  .stat{
    flex:1; display:flex; flex-direction:column; align-items:center; gap:3px;
    text-align:center; padding:0 6px;
  }
  .stat-value{ font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:1.1rem; }
  .stat-label{
    font-family:'Inter'; font-weight:600; font-size:0.66rem;
    letter-spacing:0.04em; text-transform:uppercase; color:var(--ink-soft);
  }
  .stat-divider{ width:1px; background:var(--line); margin:2px 0; }

  /* ---------- Trend chart ---------- */
  .chart-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:16px 14px 12px;
    margin-bottom:12px;
  }
  .chart-title{
    font-family:'IBM Plex Mono', monospace; font-size:0.72rem;
    letter-spacing:0.08em; text-transform:uppercase; color:var(--ink-soft);
    margin-bottom:14px;
  }
  .chart-bars{
    display:flex; align-items:flex-end; gap:8px;
    height:120px;
    border-bottom:1px dashed var(--line);
    padding-bottom:2px;
  }
  .bar-col{
    flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end;
    height:100%;
  }
  .bar{
    width:100%; max-width:26px;
    background:var(--green-tint);
    border-radius:5px 5px 2px 2px;
    transition:height .3s ease;
  }
  .bar.current{ background:var(--green); }
  .bar-labels{
    display:flex; gap:8px; margin-top:8px;
  }
  .bar-label{
    flex:1; text-align:center;
    font-family:'IBM Plex Mono', monospace; font-size:0.64rem;
    color:var(--ink-soft);
  }
  .bar-label.current{ color:var(--ink); font-weight:700; }

  /* ---------- Pending note ---------- */
  .pending-card{
    display:flex; align-items:center; gap:12px;
    background:var(--amber-bg);
    border:1px solid #EBD9B4;
    border-radius:var(--radius);
    padding:14px;
    text-decoration:none;
    color:var(--ink);
  }
  .pending-icon{
    width:38px; height:38px; border-radius:999px;
    background:var(--card); color:var(--amber);
    display:flex; align-items:center; justify-content:center;
    flex:0 0 auto;
  }
  .pending-icon svg{ width:19px; height:19px; }
  .pending-text{ flex:1; }
  .pending-title{ font-weight:700; font-size:0.88rem; }
  .pending-sub{ font-size:0.78rem; color:var(--ink-soft); margin-top:2px; }
  .pending-amount{
    font-family:'IBM Plex Mono', monospace; font-weight:700; font-size:0.85rem;
    color:var(--amber);
  }
  .pending-chev{ color:var(--ink-soft); font-size:0.9rem; }

  @media (min-width:560px){
    body{ display:flex; justify-content:center; }
    .app{ width:100%; max-width:480px; box-shadow:0 0 0 1px var(--line); min-height:100vh; }
  }
</style>
</head>
<body>
<div class="app">

  <header class="top">
    <a href="<?= $this->Url->build('/account') ?>" class="back-btn" aria-label="Back to Account">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
    </a>
    <div class="brand">Admin Dashboard
      <small>Income overview</small>
    </div>
    <span class="admin-tag">Admin only</span>
  </header>

  <main>
    <div class="tabs" id="periodTabs">
      <button class="tab active" data-period="daily">Daily</button>
      <button class="tab" data-period="weekly">Weekly</button>
      <button class="tab" data-period="monthly">Monthly</button>
    </div>

    <div class="hero-card">
      <div class="hero-label" id="heroLabel">Today's income</div>
      <div class="hero-value" id="heroValue">Rp 0</div>
      <div class="hero-compare up" id="heroCompare">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
        <span id="heroComparePct">0%</span>
        <span class="hero-compare-note" id="heroCompareNote">vs yesterday</span>
      </div>
    </div>

    <div class="stat-row">
      <div class="stat">
        <div class="stat-value" id="statOrders">0</div>
        <div class="stat-label">Orders</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat">
        <div class="stat-value" id="statAvg">Rp 0</div>
        <div class="stat-label">Avg. Order</div>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-title" id="chartTitle">Last 7 days</div>
      <div class="chart-bars" id="chartBars"></div>
      <div class="bar-labels" id="chartLabels"></div>
    </div>

    <a href="<?= $this->Url->build('/transactions') ?>?status=pending" class="pending-card">
      <div class="pending-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
      </div>
      <div class="pending-text">
        <div class="pending-title">Still pending</div>
        <div class="pending-sub" id="pendingSub">0 unpaid orders</div>
      </div>
      <div class="pending-amount" id="pendingAmount">Rp 0</div>
      <div class="pending-chev">›</div>
    </a>
  </main>

</div>

<script>
  const rupiah = n => "Rp " + Math.round(n).toLocaleString("id-ID");

  const DATA = <?= json_encode($DATA) ?>;
  const PENDING = <?= json_encode($PENDING) ?>;

  function renderPeriod(period){
    const d = DATA[period];
    const current = d.values[d.values.length - 1];
    const previous = d.values[d.values.length - 2];
    const pct = previous ? ((current - previous) / previous * 100) : 0;
    const isUp = pct >= 0;
    const max = Math.max(...d.values);

    document.getElementById("heroLabel").textContent = d.label;
    document.getElementById("heroValue").textContent = rupiah(current);
    document.getElementById("heroCompareNote").textContent = d.compareNote;
    document.getElementById("heroComparePct").textContent = Math.abs(pct).toFixed(1) + "%";

    const compareEl = document.getElementById("heroCompare");
    compareEl.classList.toggle("up", isUp);
    compareEl.classList.toggle("down", !isUp);
    compareEl.querySelector("svg").innerHTML = isUp
      ? '<path d="M12 19V5M5 12l7-7 7 7"/>'
      : '<path d="M12 5v14M5 12l7 7 7-7"/>';

    document.getElementById("statOrders").textContent = d.orders;
    document.getElementById("statAvg").textContent = rupiah(d.orders ? current / d.orders : 0);

    document.getElementById("chartTitle").textContent = d.chartTitle;

    const chartBars = document.getElementById("chartBars");
    const chartLabels = document.getElementById("chartLabels");
    chartBars.innerHTML = d.values.map((v, i) => {
      const heightPct = Math.max(6, max ? (v / max) * 100 : 0);
      const isLast = i === d.values.length - 1;
      return `<div class="bar-col"><div class="bar ${isLast ? 'current' : ''}" style="height:${heightPct}%"></div></div>`;
    }).join("");
    chartLabels.innerHTML = d.labels.map((l, i) => {
      const isLast = i === d.labels.length - 1;
      return `<div class="bar-label ${isLast ? 'current' : ''}">${l}</div>`;
    }).join("");
  }

  document.getElementById("periodTabs").addEventListener("click", e=>{
    const btn = e.target.closest(".tab");
    if(!btn) return;
    document.querySelectorAll(".tab").forEach(t=>t.classList.remove("active"));
    btn.classList.add("active");
    renderPeriod(btn.dataset.period);
  });

  document.getElementById("pendingSub").textContent = `${PENDING.count} unpaid orders`;
  document.getElementById("pendingAmount").textContent = rupiah(PENDING.amount);

  renderPeriod("daily");
</script>

</body>
</html>
