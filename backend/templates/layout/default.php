<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?= $this->fetch('title') ?> - Kasir</title>
    <style>
        /* ============================================================
           FRESH JUICE POS DESIGN SYSTEM
           Modern, warm, dense, fast, orange-accented
           ============================================================ */
        :root {
            --primary: #f97316;       /* Vibrant Orange */
            --primary-hover: #ea580c;
            --primary-light: #ffedd5;
            --bg-body: #f8fafc;       /* Very light cool gray */
            --bg-surface: #ffffff;
            --bg-cart: #ffffff;
            
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            
            --border-light: #e2e8f0;
            --border-focus: #cbd5e1;
            
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 14px;
            --radius-pill: 99px;

            --shadow-soft: 0 2px 8px rgba(15, 23, 42, 0.04);
            --shadow-hover: 0 8px 16px rgba(15, 23, 42, 0.08);
            --shadow-cart: -4px 0 24px rgba(15, 23, 42, 0.03);
            
            --transition: all 150ms ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-body); 
            color: var(--text-main); 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            -webkit-font-smoothing: antialiased; 
        }

        /* --- HEADER --- */
        header { 
            background: var(--bg-surface); 
            padding: 10px 24px; 
            border-bottom: 1px solid var(--border-light); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            height: 54px;
            z-index: 20; 
        }
        
        .logo { font-size: 1.2rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; letter-spacing: -0.01em; }
        .logo svg { color: var(--primary); }
        
        .nav-btns { display: flex; gap: 4px; }
        .nav-btns button { padding: 6px 12px; cursor: pointer; border: none; background: transparent; border-radius: var(--radius-sm); font-weight: 600; color: var(--text-muted); transition: var(--transition); font-size: 0.85rem; }
        .nav-btns button.active { background: var(--primary-light); color: var(--primary); }
        .nav-btns button:hover:not(.active) { color: var(--text-main); background: var(--bg-body); }

        /* --- LAYOUT --- */
        .content-wrapper { flex: 1; display: flex; min-height: 0; overflow: hidden; position: relative; }

        /* --- LEFT PANEL --- */
        .left-panel { flex: 1; padding: 16px 24px; overflow-y: auto; }
        
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 16px; flex-wrap: wrap; }
        
        .cat-filter { display: flex; gap: 8px; overflow-x: auto; flex: 1; padding-bottom: 4px; }
        .cat-chip { 
            padding: 6px 14px; border-radius: var(--radius-pill); font-size: 0.85rem; font-weight: 600;
            border: 1px solid var(--border-light); background: var(--bg-surface); color: var(--text-muted);
            cursor: pointer; white-space: nowrap; transition: var(--transition); box-shadow: var(--shadow-soft);
        }
        .cat-chip:hover { border-color: var(--border-focus); color: var(--text-main); transform: translateY(-1px); }
        .cat-chip.active { background: var(--primary); border-color: var(--primary); color: white; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2); }

        .search-box { position: relative; max-width: 240px; width: 100%; }
        .search-box svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 16px; height: 16px; pointer-events: none; }
        .search-box input {
            width: 100%; padding: 8px 12px 8px 36px; border: 1px solid var(--border-light);
            background: var(--bg-surface); border-radius: var(--radius-pill); font-size: 0.9rem; outline: none;
            color: var(--text-main); font-weight: 500; transition: var(--transition); box-shadow: var(--shadow-soft);
        }
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

        /* --- MENU GRID --- */
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; padding-bottom: 24px; }
        
        .menu-card { 
            background: var(--bg-surface); 
            border-radius: var(--radius-md); 
            border: 1px solid var(--border-light); 
            cursor: pointer; 
            transition: var(--transition); 
            box-shadow: var(--shadow-soft); 
            display: flex; 
            flex-direction: column; 
            position: relative;
            overflow: hidden;
            height: 100px;
        }
        
        /* Category colored strip at the top */
        .menu-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--card-color, var(--border-light));
        }

        .menu-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); border-color: var(--border-focus); }
        .menu-card:active { transform: translateY(0); }
        
        .card-content { padding: 12px; display: flex; flex-direction: column; height: 100%; justify-content: space-between; }
        .menu-card h3 { font-size: 16px; font-weight: 600; color: var(--text-main); line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .menu-card p { color: var(--primary); font-weight: 700; font-size: 1.1rem; }

        /* --- RIGHT PANEL (CART) --- */
        .right-panel { width: 340px; background: var(--bg-cart); border-left: 1px solid var(--border-light); display: flex; flex-direction: column; box-shadow: var(--shadow-cart); z-index: 10; flex-shrink: 0; }
        
        .order-header { padding: 12px 16px; border-bottom: 1px solid var(--border-light); background: var(--bg-body); }
        .order-header input { width: 100%; padding: 8px 12px; border: 1px solid var(--border-light); background: var(--bg-surface); border-radius: var(--radius-sm); font-size: 0.9rem; outline: none; transition: var(--transition); color: var(--text-main); font-weight: 500; }
        .order-header input::placeholder { color: var(--text-light); }
        .order-header input:focus { border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-light); }
        
        .order-items { flex: 1; overflow-y: auto; padding: 0; }
        .order-item { display: flex; justify-content: space-between; align-items: stretch; padding: 10px 16px; border-bottom: 1px solid var(--border-light); background: var(--bg-surface); transition: background 150ms; }
        .order-item:hover { background: var(--bg-body); }
        
        .order-item-info { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
        .order-item-name { font-weight: 600; color: var(--text-main); font-size: 0.95rem; line-height: 1.2; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .order-item-price { color: var(--text-muted); font-size: 0.85rem; font-variant-numeric: tabular-nums; }
        
        .order-item-right { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: 8px; }
        .order-item-total { font-weight: 700; font-size: 0.95rem; color: var(--text-main); font-variant-numeric: tabular-nums; }
        
        .order-item-actions { display: flex; align-items: center; border: 1px solid var(--border-light); border-radius: var(--radius-sm); background: var(--bg-surface); overflow: hidden; }
        .order-item-actions button { width: 28px; height: 26px; border: none; background: transparent; cursor: pointer; font-weight: bold; color: var(--text-muted); font-size: 1.1rem; transition: var(--transition); display: flex; align-items: center; justify-content: center; }
        .order-item-actions button:hover { background: var(--bg-body); color: var(--text-main); }
        .order-item-qty { font-weight: 600; width: 32px; text-align: center; font-size: 0.9rem; color: var(--text-main); font-variant-numeric: tabular-nums; }

        /* --- TOTAL & CHECKOUT --- */
        .order-summary { padding: 16px; border-top: 1px solid var(--border-light); background: var(--bg-surface); }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .summary-row span:first-child { font-size: 1rem; color: var(--text-muted); font-weight: 600; }
        .summary-row span:last-child { font-size: 1.75rem; font-weight: 800; color: var(--primary); font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
        
        .action-btns { display: grid; grid-template-columns: 1fr 2fr; gap: 8px; }
        
        .btn { padding: 14px 16px; border: none; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; font-size: 1rem; transition: var(--transition); display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn:active { transform: scale(0.98); }
        .btn-pay { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25); text-transform: uppercase; letter-spacing: 0.05em; font-size: 1.1rem; }
        .btn-pay:hover { background: var(--primary-hover); box-shadow: 0 6px 16px rgba(249, 115, 22, 0.35); }
        .btn-save { background: var(--bg-body); color: var(--text-main); border: 1px solid var(--border-light); }
        .btn-save:hover { background: var(--border-light); }

        /* --- GENERAL COMPONENTS --- */
        .table-container { background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); overflow-x: auto; box-shadow: var(--shadow-soft); margin-bottom: 24px; width: 100%; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-light); }
        th { background: var(--bg-body); font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr { transition: var(--transition); }
        tbody tr:hover { background: var(--bg-body); }
        
        .status-badge { padding: 4px 10px; border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700; display: inline-block; letter-spacing: 0.05em; text-transform: uppercase; }
        .status-active, .status-paid { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #fef3c7; color: #92400e; }

        .stats-card { background: var(--bg-surface); border: 1px solid var(--border-light); padding: 20px; border-radius: var(--radius-lg); margin-bottom: 24px; box-shadow: var(--shadow-soft); display: inline-block; min-width: 260px; border-left: 4px solid var(--primary); }
        .stats-title { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .stats-value { font-size: 2rem; font-weight: 800; color: var(--text-main); }

        .pagination { display: flex; gap: 4px; list-style: none; margin: 0; padding: 0; }
        .pagination li a, .pagination li span { padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); background: var(--bg-surface); color: var(--text-main); text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: var(--transition); display: inline-block; cursor: pointer; }
        .pagination li a:hover { border-color: var(--border-focus); background: var(--bg-body); }
        .pagination li.active a { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination li.disabled a, .pagination li.disabled span { color: var(--text-light); background: var(--bg-body); cursor: not-allowed; }

        #toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 1000; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
        .toast { background: var(--text-main); color: white; padding: 12px 20px; border-radius: var(--radius-sm); box-shadow: var(--shadow-hover); display: flex; align-items: center; gap: 12px; font-weight: 500; font-size: 0.95rem; pointer-events: auto; }
        .toast.success { border-left: 4px solid var(--success); }
        .toast.error { border-left: 4px solid var(--danger); }

        /* --- MODAL --- */
        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); z-index: 100; justify-content: center; align-items: center; padding: 16px; }
        .modal.active { display: flex; animation: fadeIn 150ms ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content { background: var(--bg-surface); padding: 24px; border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border: 1px solid var(--border-light); animation: slideUp 200ms ease; max-height: 90vh; overflow-y: auto; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--text-main); }
        .close-btn { background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light); transition: var(--transition); display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: var(--radius-sm); }
        .close-btn:hover { background: var(--bg-body); color: var(--text-main); }
        
        .payment-summary { background: var(--bg-body); border-radius: var(--radius-md); padding: 20px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-light); }
        .payment-total { font-size: 2.25rem; font-weight: 800; color: var(--primary); margin: 4px 0; }
        .payment-change { font-size: 1.1rem; font-weight: 600; color: var(--success); background: #ecfdf5; padding: 6px 16px; border-radius: var(--radius-pill); display: inline-block; margin-top: 8px; }
        
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.02em; }
        .form-group input, .form-group select { width: 100%; padding: 12px 16px; border: 1px solid var(--border-focus); border-radius: var(--radius-sm); font-size: 1.1rem; outline: none; transition: var(--transition); background: var(--bg-surface); color: var(--text-main); font-weight: 600; }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        
        .quick-cash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .quick-cash-btn { padding: 12px; border: 1px solid var(--border-focus); background: var(--bg-surface); border-radius: var(--radius-sm); cursor: pointer; font-weight: 600; font-size: 1.05rem; transition: var(--transition); color: var(--text-main); }
        .quick-cash-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

        /* --- MOBILE --- */
        @media (max-width: 992px) {
            body { overflow-y: auto; overflow-x: hidden; }
            .content-wrapper { display: block; overflow: visible; }
            header { padding: 12px 16px; }
            /* .nav-btns { display: none; } */ /* Hide on small screens for POS space, or move to hamburger */
            
            .left-panel { flex: none; overflow-y: visible; padding: 16px; padding-bottom: 24px; }
            .right-panel { flex: none; width: 100%; border-left: none; border-top: 1px solid var(--border-light); position: relative; z-index: 30; height: auto; }
            
            .order-items { max-height: 40vh; overflow-y: auto; border-bottom: 1px solid var(--border-light); }
            
            .menu-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
            .action-btns { grid-template-columns: 1fr; }
        }
    </style>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <?php $identity = $this->request->getAttribute('identity'); ?>
    <?php if ($identity): ?>
    <header>
        <div class="logo">
            <svg width="22" height="22" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="13" cy="13" r="12" fill="#D9600A"/>
                <g stroke="#FFFFFF" stroke-width="1.3" stroke-linecap="round">
                    <line x1="13" y1="3" x2="13" y2="23"/>
                    <line x1="4.6" y1="7.5" x2="21.4" y2="18.5"/>
                    <line x1="4.6" y1="18.5" x2="21.4" y2="7.5"/>
                </g>
                <circle cx="13" cy="13" r="3.2" fill="#FFFFFF"/>
            </svg>
            Kasir
        </div>
        <div class="nav-btns">
            <button class="<?= $this->request->getParam('action') === 'pos' ? 'active' : '' ?>" onclick="window.location.href='<?= $this->Url->build('/pages/pos') ?>'">Point of Sale</button>
            <button class="<?= $this->request->getParam('controller') === 'Transactions' && $this->request->getQuery('status') === 'pending' ? 'active' : '' ?>" onclick="window.location.href='<?= $this->Url->build(['controller' => 'Transactions', 'action' => 'index', '?' => ['status' => 'pending']]) ?>'">Belum Membayar</button>
            <?php if ($identity->role === 'admin'): ?>
            <button class="<?= $this->request->getParam('controller') === 'Transactions' && !$this->request->getQuery('status') ? 'active' : '' ?>" onclick="window.location.href='<?= $this->Url->build(['controller' => 'Transactions', 'action' => 'index']) ?>'">Transactions</button>
            <button class="<?= $this->request->getParam('controller') === 'Menus' ? 'active' : '' ?>" onclick="window.location.href='<?= $this->Url->build(['controller' => 'Menus', 'action' => 'index']) ?>'">Manage Menu</button>
            <?php endif; ?>
            <button onclick="window.location.href='<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>'">Logout</button>
        </div>
    </header>
    <?php endif; ?>

    <div id="toast-container">
        <?= $this->Flash->render() ?>
    </div>

    <div class="content-wrapper">
        <?= $this->fetch('content') ?>
    </div>

</body>
</html>