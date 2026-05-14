document.addEventListener('DOMContentLoaded', function () {

    /* ================= TOOLTIP INIT ================= */
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    /* ================= AUTO ALERT CLOSE ================= */
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (typeof bootstrap !== 'undefined') {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        });
    }, 5000);

    /* ================= FORM VALIDATION ================= */
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    /* ================= PHONE FORMAT ================= */
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });
    });

    /* ================= DATE LIMIT (NO FUTURE DATE) ================= */
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (!input.hasAttribute('max')) {
            input.max = new Date().toISOString().split('T')[0];
        }
    });

    /* ================= LOADING ON BUTTON CLICK ================= */
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function () {

            if (button.form && button.form.checkValidity()) {

                let original = button.innerHTML;

                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
                button.disabled = true;

                setTimeout(() => {
                    button.innerHTML = original;
                    button.disabled = false;
                }, 2500);
            }
        });
    });

    /* ================= SEARCH DEBOUNCE ================= */
    document.querySelectorAll('.search-input').forEach(input => {
        let timer;

        input.addEventListener('input', function () {
            clearTimeout(timer);

            timer = setTimeout(() => {
                performSearch(input.value);
            }, 400);
        });
    });

});

/* ================= SEARCH FUNCTION ================= */
function performSearch(term) {
    console.log("Searching:", term);
}

/* ================= DELETE CONFIRM ================= */
function confirmDelete(name = "this item") {
    return confirm(`Are you sure you want to delete ${name}?`);
}

/* ================= PRINT ================= */
function printReport(id) {
    let content = document.getElementById(id).innerHTML;
    let original = document.body.innerHTML;

    document.body.innerHTML = content;
    window.print();
    document.body.innerHTML = original;

    location.reload();
}

/* ================= CSV EXPORT ================= */
function exportToCSV(data, filename = "report") {

    if (!data || !data.length) return;

    let keys = Object.keys(data[0]);
    let csv = keys.join(",") + "\n";

    data.forEach(row => {
        csv += keys.map(k => row[k]).join(",") + "\n";
    });

    let blob = new Blob([csv], { type: "text/csv" });
    let link = document.createElement("a");

    link.href = URL.createObjectURL(blob);
    link.download = filename + ".csv";
    link.click();
}

/* ================= AGE CALCULATOR ================= */
function calculateAge(dob) {
    let birth = new Date(dob);
    let today = new Date();

    let age = today.getFullYear() - birth.getFullYear();
    let m = today.getMonth() - birth.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }

    return age;
}

/* ================= NOTIFICATION SYSTEM ================= */
function showNotification(message, type = "info") {

    let div = document.createElement("div");

    div.className = `alert alert-${type} position-fixed top-0 end-0 m-3 shadow`;
    div.style.zIndex = "9999";
    div.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(div);

    setTimeout(() => {
        div.remove();
    }, 4000);
}