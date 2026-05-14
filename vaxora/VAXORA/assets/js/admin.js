document.addEventListener('DOMContentLoaded', function () {

    // Tooltip init
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }

    // Auto close alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Alert.getOrCreateInstance(alert)?.close();
            }
        });
    }, 4000);

    // Sidebar active link
    const current = window.location.pathname;
    document.querySelectorAll('.admin-sidebar a').forEach(link => {
        if (link.getAttribute('href') && current.endsWith(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });

    // Live table search
    const searchInput = document.getElementById('adminSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});

function confirmDelete(name) {
    return confirm('Delete "' + (name || 'this record') + '"? This cannot be undone.');
}
