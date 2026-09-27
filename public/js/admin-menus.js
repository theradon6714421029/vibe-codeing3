function openMenuModal(card = null) {
    document.getElementById('menuForm').reset();
    if (card) {
        document.getElementById('menu_id').value = card.dataset.id;
        document.getElementById('menu_name').value = card.dataset.name;
        document.getElementById('menu_description').value = card.dataset.description;
        document.getElementById('menu_price').value = card.dataset.price;
        document.getElementById('menu_category').value = card.dataset.category;
        document.getElementById('menu_available').checked = card.dataset.available === '1';
    } else {
        document.getElementById('menu_id').value = '';
    }
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('.edit-menu-btn');
    if (btn) openMenuModal(btn.closest('[data-id]'));
});

document.getElementById('menuForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('menu_id').value;

    const formData = new FormData();
    formData.append('name', document.getElementById('menu_name').value);
    formData.append('description', document.getElementById('menu_description').value);
    formData.append('price', document.getElementById('menu_price').value);
    formData.append('category_id', document.getElementById('menu_category').value);
    formData.append('is_available', document.getElementById('menu_available').checked ? '1' : '0');
    const imgFile = document.getElementById('menu_image').files[0];
    if (imgFile) formData.append('image', imgFile);

    const url = id ? `/admin/menus/${id}` : '/admin/menus';
    if (id) formData.append('_method', 'PUT'); // Laravel method spoofing สำหรับ multipart PUT

    try {
        await apiFetch(url, { method: 'POST', body: formData }); // ไม่ตั้ง Content-Type เอง ให้ browser ใส่ boundary ให้
        location.reload();
    } catch (err) {
        showToast(err.message, 'danger');
    }
});

document.addEventListener('click', async (e) => {
    if (!e.target.classList.contains('delete-menu-btn')) return;
    if (!confirm('ยืนยันการลบเมนูนี้?')) return;

    const card = e.target.closest('[data-id]');
    try {
        await apiFetch(`/admin/menus/${card.dataset.id}`, { method: 'DELETE' });
        card.remove();
        showToast('ลบสำเร็จ');
    } catch (err) {
        showToast(err.message, 'danger');
    }
});
