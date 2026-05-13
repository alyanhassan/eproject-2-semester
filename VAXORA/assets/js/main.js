document.addEventListener('DOMContentLoaded', function () {

    // Tooltip init
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }

    // Auto-close alerts after 5s
    setTimeout(() => {
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Alert.getOrCreateInstance(alert)?.close();
            } else {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        });
    }, 5000);

    // Form submit loading state
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.dataset.noload) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                btn.disabled = true;
            }
        });
    });

    // Date min today for appointment booking
    document.querySelectorAll('input[data-min-today]').forEach(input => {
        input.min = new Date().toISOString().split('T')[0];
    });

    // Live table search
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // Number counter animation for hero stats
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target || el.textContent);
        let current = 0;
        const step = Math.ceil(target / 60);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current.toLocaleString();
            if (current >= target) clearInterval(timer);
        }, 20);
    });
});

function confirmDelete(msg) {
    return confirm(msg || 'Are you sure you want to delete this record? This cannot be undone.');
}

function printSection(id) {
    const content = document.getElementById(id).innerHTML;
    const orig = document.body.innerHTML;
    document.body.innerHTML = content;
    window.print();
    document.body.innerHTML = orig;
    location.reload();
}
