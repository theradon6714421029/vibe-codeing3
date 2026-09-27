document.addEventListener('change', async (e) => {
    if (!e.target.classList.contains('status-select')) return;

    const row = e.target.closest('tr');
    const newStatus = e.target.value;

    try {
        await apiFetch(`/admin/orders/${row.dataset.id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus }),
        });
        location.reload();
        showToast('อัปเดตสถานะสำเร็จ');
    } catch (err) {
        showToast(err.message, 'danger');
        location.reload(); // เพื่อรีเซ็ต dropdown กลับสถานะเดิมถ้า transition ไม่ถูกต้อง
    }
});
