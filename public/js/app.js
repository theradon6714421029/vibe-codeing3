// ==== Shared helpers: CSRF, Toast, fetch wrapper ====
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const el = document.createElement('div');
    el.className = `toast align-items-center text-bg-${type} border-0`;
    el.setAttribute('role', 'alert');
    // ใช้ textContent ไม่ใช่ innerHTML สำหรับข้อความจากตัวแปร ป้องกัน DOM-based XSS
    const body = document.createElement('div');
    body.className = 'd-flex';
    const msg = document.createElement('div');
    msg.className = 'toast-body';
    msg.textContent = message;
    body.appendChild(msg);
    el.appendChild(body);
    container.appendChild(el);
    const toast = new bootstrap.Toast(el, { delay: 3500 });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

async function apiFetch(url, options = {}) {
    options.headers = Object.assign({
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json',
    }, options.headers || {});

    const res = await fetch(url, options);
    let data = {};
    try { data = await res.json(); } catch (e) { /* no body */ }

    if (!res.ok) {
        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'เกิดข้อผิดพลาด');
        throw new Error(msg);
    }
    return data;
}

// ==== Add to cart (ใช้ทั้งหน้า menu index และ menu show) ====
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.add-to-cart-btn');
    if (!btn) return;

    try {
        const data = await apiFetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ menu_id: btn.dataset.menuId, quantity: 1 }),
        });
        showToast(data.message);
    } catch (err) {
        showToast(err.message, 'danger');
    }
});
