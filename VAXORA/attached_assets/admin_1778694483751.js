/* ================= ADMIN JS ================= */

document.addEventListener("DOMContentLoaded", function () {

    /* ================= TOOLTIP INIT ================= */
    if (typeof bootstrap !== "undefined") {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    /* ================= ALERT AUTO CLOSE ================= */
    setTimeout(() => {
        document.querySelectorAll(".alert").forEach(alert => {
            if (typeof bootstrap !== "undefined") {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        });
    }, 4000);

    /* ================= CONFIRM DELETE GLOBAL ================= */
    window.confirmDelete = function (name = "this item") {
        return confirm(`Are you sure you want to delete ${name}?`);
    };

    /* ================= LOADING BUTTON STATE ================= */
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function () {

            let btn = form.querySelector("button[type='submit']");

            if (btn) {
                let original = btn.innerHTML;

                btn.innerHTML = "⏳ Processing...";
                btn.disabled = true;

                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.disabled = false;
                }, 2500);
            }
        });
    });

    /* ================= TABLE ROW HOVER EFFECT ================= */
    document.querySelectorAll("table tbody tr").forEach(row => {
        row.addEventListener("mouseenter", () => {
            row.style.transition = "0.2s";
            row.style.backgroundColor = "#f1f4ff";
        });

        row.addEventListener("mouseleave", () => {
            row.style.backgroundColor = "";
        });
    });

});

/* ================= NOTIFICATION ================= */
function adminNotify(message, type = "info") {

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

/* ================= QUICK DELETE ACTION ================= */
function deleteRecord(url, name) {
    if (confirm(`Delete ${name}? This cannot be undone.`)) {
        window.location.href = url;
    }
}