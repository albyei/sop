<?php
$this->disableAutoLayout();
$identity = $this->request->getAttribute('identity');
$name = $identity ? ($identity->get('username') ?? 'User') : 'Guest';
$role = $identity ? ($identity->get('role') ?? 'Staff') : 'Staff';
$email = $identity ? ($identity->get('email') ?? '') : '';
$branchName = $identity && $identity->get('branch') ? ($identity->get('branch')['name'] ?? 'Warung Kita') : 'Warung Kita';
$initials = strtoupper(substr($name, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Account — Warung Kita POS</title>
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
    --red:#C1272D;
    --red-bg:#FBE9E9;
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
  ::selection{ background:var(--green); color:#fff; }

  /* ---------- Header ---------- */
  header.top{
    position:sticky; top:0; z-index:20;
    background:var(--paper);
    border-bottom:1px solid var(--line);
    padding:14px 16px 14px;
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

  main{ padding:16px; }

  /* ---------- Profile card ---------- */
  .profile-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:20px 16px;
    display:flex; flex-direction:column; align-items:center;
    text-align:center;
    margin-bottom:14px;
  }
  .avatar{
    width:64px; height:64px; border-radius:999px;
    background:var(--green);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-family:'Archivo Black'; font-size:1.4rem;
    margin-bottom:10px;
  }
  .profile-name{ font-weight:700; font-size:1.05rem; }
  .profile-role{
    font-family:'IBM Plex Mono', monospace; font-size:0.75rem;
    color:var(--ink-soft); margin-top:3px;
  }
  .role-badge{
    display:inline-block; margin-top:8px;
    background:var(--green-bg); color:var(--green-dark);
    font-family:'Inter'; font-weight:700; font-size:0.68rem;
    letter-spacing:0.04em; text-transform:uppercase;
    padding:5px 12px; border-radius:999px;
  }

  /* ---------- Info list ---------- */
  .info-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    margin-bottom:14px;
    overflow:hidden;
  }
  .info-card-label{
    font-family:'IBM Plex Mono', monospace; font-size:0.7rem;
    letter-spacing:0.08em; text-transform:uppercase; color:var(--ink-soft);
    padding:12px 16px 6px;
  }
  .info-row{
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 16px;
    border-top:1px dashed var(--line);
    font-size:0.88rem;
  }
  .info-row .k{ color:var(--ink-soft); }
  .info-row .v{ font-weight:600; color:var(--ink); }
  .info-row .v.mono{ font-family:'IBM Plex Mono', monospace; font-weight:500; }

  /* ---------- Menu list (settings-style links) ---------- */
  .menu-card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:var(--radius);
    margin-bottom:14px;
    overflow:hidden;
  }
  .menu-link{
    display:flex; align-items:center; gap:12px;
    padding:14px 16px;
    border-top:1px dashed var(--line);
    cursor:pointer;
    color:var(--ink);
    font-size:0.9rem; font-weight:600;
  }
  .menu-link:first-child{ border-top:none; }
  .menu-link svg{ width:19px; height:19px; color:var(--ink-soft); flex:0 0 auto; }
  .menu-link .chev{ margin-left:auto; color:var(--ink-soft); font-size:0.8rem; }
  .menu-link.danger{ color:var(--red); }
  .menu-link.danger svg{ color:var(--red); }

  .app-version{
    text-align:center; font-family:'IBM Plex Mono', monospace;
    font-size:0.72rem; color:var(--ink-soft);
    margin-top:6px;
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

  /* ---------- Logout confirm drawer ---------- */
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
    transform:translateY(100%);
    transition:transform .25s cubic-bezier(.32,.72,0,1);
    padding-bottom:var(--safe-bottom);
    text-align:center;
  }
  .drawer.show{ transform:translateY(0); }
  .drawer-handle{ width:36px; height:4px; background:var(--line); border-radius:999px; margin:10px auto 4px; }
  .drawer-icon{
    width:52px; height:52px; border-radius:999px;
    background:var(--red-bg); color:var(--red);
    display:flex; align-items:center; justify-content:center;
    margin:14px auto 4px;
  }
  .drawer-icon svg{ width:24px; height:24px; }
  .drawer-title{ font-family:'Archivo Black'; font-size:1.05rem; margin:6px 0 4px; }
  .drawer-sub{ font-size:0.85rem; color:var(--ink-soft); padding:0 30px; margin-bottom:18px; }

  .drawer-actions{ display:flex; gap:10px; padding:0 18px 20px; }
  .btn{
    flex:1; border:none; border-radius:999px;
    font-family:'Inter'; font-weight:700; font-size:0.95rem;
    padding:14px 10px; cursor:pointer;
    transition:transform .1s ease;
  }
  .btn:active{ transform:scale(0.97); }
  .btn-cancel{ background:var(--paper-dim); color:var(--ink); border:1.5px solid var(--line); }
  .btn-logout{ background:var(--red); color:#fff; box-shadow:0 4px 14px rgba(193,39,45,0.3); }

  .toast{
    position:fixed; left:50%; bottom:calc(var(--nav-h) + var(--safe-bottom) + 14px); transform:translateX(-50%) translateY(20px);
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
  }
</style>
</head>
<body>
<div class="app">

  <header class="top">
    <div class="brand">Account
      <small>Profile &amp; Session</small>
    </div>
  </header>

  <main>
    <div class="profile-card">
      <div class="avatar" id="avatarInitials"><?= h($initials) ?></div>
      <div class="profile-name" id="profileName"><?= h($name) ?></div>
      <div class="profile-role" id="profileEmail"><?= h($email) ?></div>
      <span class="role-badge" id="profileRole"><?= h($role) ?></span>
    </div>

    <div class="info-card">
      <div class="info-card-label">Current session</div>
      <div class="info-row"><span class="k">Logged in</span><span class="v mono" id="loginTime">Yes</span></div>
      <div class="info-row"><span class="k">Store</span><span class="v"><?= h($branchName) ?></span></div>
    </div>

    <div class="menu-card">
<?php if ($identity && $identity->get('role') === 'admin'): ?>
      <a href="<?= $this->Url->build('/admin') ?>" class="menu-link" style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Admin Dashboard
        <span class="chev">›</span>
      </a>
<?php endif; ?>
      <div class="menu-link" id="fullscreenToggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        Toggle Fullscreen
        <span class="chev">›</span>
      </div>
      <div class="menu-link danger" id="logoutTrigger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        Log out
        <span class="chev">›</span>
      </div>
    </div>

    <div class="app-version">Albi Ariza Syafiq POS · v1.1</div>
  </main>

  <nav class="bottom-nav">
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobilePos']) ?>" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z"/></svg>
      <span>Order</span>
    </a>
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobileTransactions']) ?>" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h9l3 3v17H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
      <span>Transactions</span>
    </a>
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'mobileAccount']) ?>" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
      <span>Account</span>
    </a>
  </nav>

</div>

<div class="overlay" id="overlay"></div>

<div class="drawer" id="logoutDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
  </div>
  <div class="drawer-title">Log out?</div>
  <div class="drawer-sub">You'll need to log in again to access the POS and transactions.</div>
  <div class="drawer-actions">
    <button class="btn btn-cancel" id="cancelLogout">Cancel</button>
    <button class="btn btn-logout" id="confirmLogout">Log out</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
  const overlay = document.getElementById("overlay");
  const logoutDrawer = document.getElementById("logoutDrawer");
  const toast = document.getElementById("toast");

  function openLogoutDrawer(){
    logoutDrawer.classList.add("show");
    overlay.classList.add("show");
  }
  function closeLogoutDrawer(){
    logoutDrawer.classList.remove("show");
    overlay.classList.remove("show");
  }
  function showToast(msg){
    toast.textContent = msg;
    toast.classList.add("show");
    setTimeout(()=> toast.classList.remove("show"), 1400);
  }

  document.getElementById("logoutTrigger").addEventListener("click", openLogoutDrawer);
  document.getElementById("cancelLogout").addEventListener("click", closeLogoutDrawer);
  overlay.addEventListener("click", closeLogoutDrawer);

  const fullscreenToggle = document.getElementById("fullscreenToggle");
  if (fullscreenToggle) {
      fullscreenToggle.addEventListener("click", () => {
          if (!document.fullscreenElement) {
              document.documentElement.  .catch(err => {
                  showToast("Gagal mengaktifkan fullscreen: " + err.message);
              });
          } else {
              if (document.exitFullscreen) {
                  document.exitFullscreen();
              }
          }
      });
  }

  document.getElementById("confirmLogout").addEventListener("click", ()=>{
    showToast("Logging out…");
    setTimeout(()=>{ window.location.href = "<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>"; }, 700);
  });
</script>

</body>
</html>
