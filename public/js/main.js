// Sidebar di Mobile
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
    document.querySelector(".sidebar-overlay").classList.toggle("active");
}

// Efek Animasi
document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll(
        ".card, .chart, .notif, .table-section",
    );

    elements.forEach((el) => el.classList.add("fade-in-up"));

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                }
            });
        },
        { threshold: 0.1 },
    );

    elements.forEach((el) => observer.observe(el));
});

// --- MODAL LOGOUT ---
function showLogoutModal(event) {
    event.preventDefault(); // Mencegah halaman reload
    document.getElementById("logoutModal").classList.add("active");
}

function hideLogoutModal() {
    document.getElementById("logoutModal").classList.remove("active");
}
