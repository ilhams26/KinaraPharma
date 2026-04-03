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

// ==================== FUNGSI MODAL TAMBAH OBAT ====================
function showAddModal() {
    const modal = document.getElementById("addObatModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function hideAddModal() {
    const modal = document.getElementById("addObatModal");
    if (modal) {
        modal.classList.remove("active");
        // Reset form agar bersih saat dibuka lagi
        modal.querySelector("form").reset();
    }
}

// ==================== FUNGSI MODAL EDIT OBAT ====================
function showEditModal(id, nama, kategoriId, jenis, harga) {
    const modal = document.getElementById("editObatModal");
    if (modal) {

        modal.querySelector("#edit_id").value = id;
        modal.querySelector("#edit_nama").value = nama;
        modal.querySelector("#edit_kategori_id").value = kategoriId;
        modal.querySelector("#edit_jenis").value = jenis;
        modal.querySelector("#edit_harga").value = harga;

        modal.querySelector("form").action = `/kelola-obat/${id}`;

        // 3. Tampilkan Modal
        modal.classList.add("active");
    }
}

function hideEditModal() {
    const modal = document.getElementById("editObatModal");
    if (modal) {
        modal.classList.remove("active");
    }
}
