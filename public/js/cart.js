// ==== Cart page: update quantity / remove / checkout ====
document.addEventListener('change', async (e) => {
    if (!e.target.classList.contains('qty-input')) return;

    const row = e.target.closest('tr');
    const menuId = row.dataset.menuId;
    const quantity = parseInt(e.target.value, 10);

    if (!Number.isInteger(quantity) || quantity < 1 || quantity > 50) {
        showToast('จำนวนไม่ถูกต้อง', 'danger');
        return;
    }

    try {
        await apiFetch('/cart/quantity', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ menu_id: menuId, quantity }),
        });
        location.reload();
    } catch (err) {
        showToast(err.message, 'danger');
    }
});

document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('remove-btn')) {
        const row = e.target.closest('tr');
        try {
            await apiFetch('/cart/remove', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ menu_id: row.dataset.menuId }),
            });
            location.reload();
        } catch (err) {
            showToast(err.message, 'danger');
        }
    }

    if (e.target.id === 'checkoutBtn') {
        e.target.disabled = true;
        try {
            const data = await apiFetch('/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            });
            showToast(data.message);
            setTimeout(() => location.href = '/orders/' + data.order_id, 800);
        } catch (err) {
            showToast(err.message, 'danger');
            e.target.disabled = false;
        }
    }
});
