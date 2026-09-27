const catModalEl = document.getElementById('catModal');

function openCategoryModal(row = null) {
    document.getElementById('categoryForm').reset();
    if (row) {
        document.getElementById('cat_id').value = row.dataset.id;
        document.getElementById('cat_name').value = row.dataset.name;
        document.getElementById('cat_sort').value = row.dataset.sort;
        document.getElementById('cat_active').checked = row.dataset.active === '1';
    } else {
        document.getElementById('cat_id').value = '';
    }
}

document.getElementById('categoryForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('cat_id').value;
    const payload = {
        name: document.getElementById('cat_name').value,
        sort_order: parseInt(document.getElementById('cat_sort').value || '0', 10),
        is_active: document.getElementById('cat_active').checked,
    };

    try {
        if (id) {
            await apiFetch(`/admin/categories/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
        } else {
            await apiFetch('/admin/categories', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
        }
        location.reload();
    } catch (err) {
        showToast(err.message, 'danger');
    }
});

document.addEventListener('click', async (e) => {
    if (!e.target.classList.contains('delete-category-btn')) return;
    if (!confirm('ยืนยันการลบหมวดหมู่นี้?')) return;

    const row = e.target.closest('tr');
    try {
        await apiFetch(`/admin/categories/${row.dataset.id}`, { method: 'DELETE' });
        row.remove();
        showToast('ลบสำเร็จ');
    } catch (err) {
        showToast(err.message, 'danger');
    }
});
