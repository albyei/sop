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

let menus = [];
let categories = [];
let currentCart = {};
let activeCategory = 'ALL';
let currentPage = 1;
let hasNextPage = false;
let isLoading = false;

async function loadData(page = 1, append = false) {
    if (isLoading) return;
    isLoading = true;
    
    let url = `menus.json?page=${page}`;
    if (activeCategory !== 'ALL') {
        url += `&category=${encodeURIComponent(activeCategory)}`;
    }
    
    const mRes = await api(url);
    
    if (mRes) {
        if (append) {
            menus = [...menus, ...(mRes.menus || [])];
        } else {
            menus = mRes.menus || [];
        }
        
        if (mRes.categories) {
            categories = mRes.categories;
        }
        
        if (mRes.paging) {
            hasNextPage = mRes.paging.nextPage;
            currentPage = mRes.paging.page;
        } else {
            hasNextPage = false;
        }
    }
    
    isLoading = false;
    renderFilters();
    render();
}

function loadMore() {
    if (hasNextPage && !isLoading) {
        loadData(currentPage + 1, true);
    }
}

const formatIDR = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
const parseNumber = (str) => parseInt(str.toString().replace(/[^0-9]/g, ''), 10) || 0;

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = type === 'success'
        ? `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> ${message}`
        : `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> ${message}`;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 2500);
}

// --- Cart ---
function addToCart(menuId) {
    const menu = menus.find(m => m.id === menuId);
    if (!menu) return;
    if (currentCart[menuId]) currentCart[menuId].qty++;
    else currentCart[menuId] = { ...menu, qty: 1 };
    renderCart(true);
}

function updateCartQty(menuId, delta) {
    if (!currentCart[menuId]) return;
    currentCart[menuId].qty += delta;
    if (currentCart[menuId].qty <= 0) delete currentCart[menuId];
    renderCart();
}

function getCartTotal() { return Object.values(currentCart).reduce((sum, item) => sum + (item.price * item.qty), 0); }

function renderCart(pulse) {
    const cartHtml = Object.values(currentCart).map(item => `
        <div class="order-item">
            <div class="order-item-info">
                <div class="order-item-name">${item.name}</div>
                <div class="order-item-price">${formatIDR(item.price)} x ${item.qty}</div>
            </div>
            <div class="order-item-total">${formatIDR(item.price * item.qty)}</div>
            <div class="order-item-actions">
                <button onclick="updateCartQty(${item.id}, -1)" aria-label="Decrease ${item.name} quantity">-</button>
                <div class="order-item-qty">${item.qty}</div>
                <button onclick="updateCartQty(${item.id}, 1)" aria-label="Increase ${item.name} quantity">+</button>
            </div>
        </div>
    `).join('');

    const itemsContainer = document.getElementById('cart-items');
    if (Object.keys(currentCart).length === 0) {
        itemsContainer.innerHTML = '<div style="text-align:center; color:var(--n-500); margin-top:2rem; font-size: var(--fs-body);">Cart is empty. Select items from the left.</div>';
    } else {
        itemsContainer.innerHTML = cartHtml;
    }

    const totalEl = document.getElementById('cart-total');
    totalEl.innerText = formatIDR(getCartTotal());
    if (pulse) {
        totalEl.classList.remove('pulse');
        void totalEl.offsetWidth; // restart animation
        totalEl.classList.add('pulse');
    }
}

async function saveOrder() {
    if (Object.keys(currentCart).length === 0) return showToast('Cart is empty', 'error');

    const res = await api('transactions/add.json', 'POST', {
        customer_name: document.getElementById('customer-name').value.trim() || 'Guest',
        total: getCartTotal(),
        status: 'pending',
        transaction_items: Object.values(currentCart).map(i => ({ menu_id: i.id, qty: i.qty, price: i.price, subtotal: i.price * i.qty }))
    });

    if (res && res.success) {
        resetOrder();
        showToast('Order saved. You can pay later.');
    } else {
        console.error('Save failed:', res);
        const errorMsg = res && res.errors ? JSON.stringify(res.errors) : 'Network or server error';
        showToast('Failed to save order. See console.', 'error');
        alert('Validation Error: ' + errorMsg);
    }
}

function resetOrder() {
    currentCart = {};
    document.getElementById('customer-name').value = '';
    renderCart();
}

// --- Payment ---
function generateQuickCashOptions(total) {
    const options = new Set([total]);
    if (total < 20000) options.add(20000);
    if (total < 50000) options.add(50000);
    if (total < 100000) options.add(100000);
    const next10k = Math.ceil(total / 10000) * 10000;
    if (next10k > total && next10k <= 100000) options.add(next10k);

    document.getElementById('quick-cash-container').innerHTML = Array.from(options).sort((a, b) => a - b).slice(0, 4).map(amount => `
        <button class="quick-cash-btn" onclick="setReceivedAmount(${amount})">
            ${amount === total ? 'Exact: ' : ''}${formatIDR(amount)}
        </button>
    `).join('');
}

function setReceivedAmount(amount) {
    document.getElementById('pay-received').value = formatIDR(amount);
    calcChange();
}

function openPaymentModal() {
    const total = getCartTotal();
    if (total === 0) return showToast('Cart is empty', 'error');

    document.getElementById('pay-total').innerText = formatIDR(total);
    document.getElementById('pay-received').value = '';
    document.getElementById('pay-change-wrapper').style.display = 'none';
    document.getElementById('pay-change').innerText = 'Rp 0';

    generateQuickCashOptions(total);
    document.getElementById('payment-modal').classList.add('active');
    setTimeout(() => document.getElementById('pay-received').focus(), 100);
}

document.getElementById('pay-received').addEventListener('input', function (e) {
    let val = parseNumber(e.target.value);
    e.target.value = val > 0 ? formatIDR(val).replace(',00', '') : '';
    calcChange();
});

function calcChange() {
    const received = parseNumber(document.getElementById('pay-received').value);
    const total = parseNumber(document.getElementById('pay-total').innerText);
    const changeWrapper = document.getElementById('pay-change-wrapper');

    if (received >= total) {
        changeWrapper.style.display = 'block';
        document.getElementById('pay-change').innerText = formatIDR(received - total);
        document.getElementById('pay-change').style.color = 'var(--success)';
    } else if (received > 0) {
        changeWrapper.style.display = 'block';
        document.getElementById('pay-change').innerText = 'Insufficient';
        document.getElementById('pay-change').style.color = 'var(--danger)';
    } else {
        changeWrapper.style.display = 'none';
    }
}

function closeModal(id) { document.getElementById(id).classList.remove('active'); }

async function processPayment() {
    const received = parseNumber(document.getElementById('pay-received').value);
    const total = parseNumber(document.getElementById('pay-total').innerText);

    if (!received || received < total) return showToast('Insufficient amount received', 'error');

    const txData = { status: 'paid', paid_amount: received, change_amount: received - total };

    const res = await api('transactions/add.json', 'POST', {
        customer_name: document.getElementById('customer-name').value.trim() || 'Guest',
        total: total,
        transaction_items: Object.values(currentCart).map(i => ({ menu_id: i.id, qty: i.qty, price: i.price, subtotal: i.price * i.qty })),
        ...txData
    });

    if (res && res.success) {
        closeModal('payment-modal');
        showToast(`Payment successful! Change: ${formatIDR(received - total)}`);
        resetOrder();
    } else {
        console.error('Payment failed:', res);
        const errorMsg = res && res.errors ? JSON.stringify(res.errors) : 'Network or server error';
        showToast('Failed to process payment. See console.', 'error');
        alert('Validation Error: ' + errorMsg);
    }
}

// --- Search + category filter ---
function renderFilters() {
    const chips = ['ALL', ...categories].map(cat => {
        const isActive = cat === activeCategory;
        return `<button class="cat-chip${isActive ? ' active' : ''}" role="tab" aria-selected="${isActive}" onclick="setCategory('${cat.replace(/'/g, "\\'")}')">${cat === 'ALL' ? 'All' : cat}</button>`;
    }).join('');
    document.getElementById('cat-filter').innerHTML = chips;
}

function setCategory(cat) {
    if (activeCategory === cat) return;
    activeCategory = cat;
    loadData(1, false); // Reload from page 1
}

const catColors = {
    'SALAD & SOP BUAH': '#10b981',    
    'MENU YAKULT + UHT': '#3b82f6',   
    'ORIGINAL JUS': '#f97316',        
    'MIX JUS': '#8b5cf6',             
    'MINUMAN LAIN': '#64748b'         
};

function render() {
    const posGrid = menus.map(m => {
        const color = catColors[m.category] || '#e2e8f0';
        return `
        <div class="menu-card" onclick="addToCart(${m.id})" style="--card-color: ${color};">
            <div class="card-content">
                <h3>${m.name}</h3>
                <p>${formatIDR(m.price)}</p>
            </div>
        </div>
        `;
    }).join('');
    
    let finalHtml = posGrid || '<div style="color:var(--text-muted); grid-column: 1/-1; padding: 16px 0;">No menu items found.</div>';
    
    if (hasNextPage) {
        finalHtml += `<div style="grid-column: 1/-1; text-align: center; margin-top: 1rem; margin-bottom: 2rem;">
            <button class="btn btn-outline" style="width:100%; border: 1px dashed var(--primary); color: var(--primary);" onclick="loadMore()">${isLoading ? 'Loading...' : 'Load More'}</button>
        </div>`;
    }
    
    document.getElementById('pos-menu-grid').innerHTML = finalHtml;
}

// --- Keyboard shortcuts ---
document.addEventListener('keydown', function (e) {
    const paymentOpen = document.getElementById('payment-modal').classList.contains('active');
    const tag = (document.activeElement.tagName || '').toLowerCase();
    const inTextInput = tag === 'input' || tag === 'select' || tag === 'textarea';

    if (e.key === 'Escape') {
        if (paymentOpen) closeModal('payment-modal');
        return;
    }
    if (e.key === 'Enter') {
        if (paymentOpen) {
            e.preventDefault();
            processPayment();
            return;
        }
        if (!inTextInput && Object.keys(currentCart).length > 0) {
            e.preventDefault();
            openPaymentModal();
        }
    }
});

renderCart();
loadData();
