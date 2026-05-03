//FUNGSI GLOBAL & UTILITIES

function showNotification(message, type = "success") {
    let toast = document.getElementById("global-toast");
    let icon = document.getElementById("global-toast-icon");
    let msgElement = document.getElementById("global-toast-message");

    if (!toast) return;

    msgElement.innerText = message;

    if (type === "success") {
        toast.style.background = "var(--success, #28a745)";
        icon.className = "fas fa-check-circle";
    } else if (type === "error" || type === "danger") {
        toast.style.background = "var(--danger, #dc3545)";
        icon.className = "fas fa-times-circle";
    }

    toast.style.display = "block";
    setTimeout(() => {
        toast.style.opacity = "1";
    }, 10);

    // Hilang otomatis setelah 3 detik
    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => {
            toast.style.display = "none";
        }, 300);
    }, 3000);
}

//Sidebar Mobile
function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    let overlay = document.querySelector(".sidebar-overlay");
    if (sidebar) sidebar.classList.toggle("active");
    if (overlay) overlay.classList.toggle("active");
}

//Modal Logout
function showLogoutModal(event) {
    if (event) event.preventDefault();
    let modal = document.getElementById("logoutModal");
    if (modal) modal.classList.add("active", "show");
}

function hideLogoutModal() {
    let modal = document.getElementById("logoutModal");
    if (modal) modal.classList.remove("active", "show");
}

function searchTable() {
    let input = document.getElementById("searchInput");
    if (!input) return;

    let filter = input.value.toLowerCase();
    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach((row) => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}

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

    // Menutup alert bawaan lama (jika masih ada yang pakai class .alert-auto-close)
    setTimeout(() => {
        let alerts = document.querySelectorAll(".alert-auto-close");
        alerts.forEach((alert) => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
});

// FITUR LOGIN
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.querySelector(".toggle-password");

    if (!passwordInput || !toggleIcon) return;

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}

// PESANAN & VALIDASI RESEP

function prosesResep(id, url, method, actionType) {
    let pesanConfirm =
        actionType === "ACC"
            ? "Yakin ingin menyetujui resep ini?"
            : "Yakin ingin menolak dan menghapus resep ini?";

    if (confirm(pesanConfirm)) {
        let metaToken = document.querySelector('meta[name="csrf-token"]');
        let csrfToken = metaToken ? metaToken.getAttribute("content") : "";

        fetch(url, {
            method: method,
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    let pesanNotif =
                        actionType === "ACC"
                            ? "Resep berhasil disetujui!"
                            : "Resep berhasil ditolak & dihapus.";
                    showNotification(pesanNotif, "success");

                    let card = document.getElementById("resep-card-" + id);
                    if (card) {
                        card.style.opacity = "0";
                        setTimeout(() => {
                            card.remove();
                            cekSisaResep();
                        }, 300);
                    }
                } else {
                    showNotification(
                        "Terjadi kesalahan: " + data.message,
                        "error",
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification("Gagal menghubungi server.", "error");
            });
    }
}

function cekSisaResep() {
    let sisaCard = document.querySelectorAll(".resep-card").length;
    if (sisaCard === 0) {
        let listContainer = document.getElementById("resep-list");
        if (listContainer) listContainer.style.display = "none";

        let emptyState = document.getElementById("empty-resep-state");
        if (emptyState) emptyState.style.display = "flex";
    }
}

// KASIR

let cart = [];

function addToCart(id, nama, harga) {
    let existingItem = cart.find((item) => item.obat_id === id);
    if (existingItem) {
        existingItem.qty += 1;
    } else {
        cart.push({ obat_id: id, nama: nama, harga: harga, qty: 1 });
    }
    renderCart();
}

function removeFromCart(id) {
    let itemIndex = cart.findIndex((item) => item.obat_id === id);
    if (itemIndex !== -1) {
        if (cart[itemIndex].qty > 1) {
            cart[itemIndex].qty -= 1;
        } else {
            cart.splice(itemIndex, 1);
        }
    }
    renderCart();
}

function addCartQty(id) {
    let item = cart.find((item) => item.obat_id === id);
    if (item) {
        item.qty += 1;
        renderCart();
    }
}

function renderCart() {
    const cartContainer = document.getElementById("cartItems");
    const totalContainer = document.getElementById("cartTotal");
    const itemsInput = document.getElementById("itemsInput");

    if (!cartContainer) return; // Cegah error jika bukan di halaman kasir

    if (cart.length === 0) {
        cartContainer.innerHTML =
            '<div style="text-align: center; color: var(--text-muted); margin-top: 50px;">Keranjang Kosong</div>';
        totalContainer.innerText = "Rp 0";
        itemsInput.value = "";
        return;
    }

    let html = '<ul style="padding: 0;">';
    let total = 0;

    cart.forEach((item) => {
        let subtotal = item.harga * item.qty;
        total += subtotal;

        html += `
            <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <div style="font-weight: bold; font-size: 14px;">${item.nama}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Rp ${item.harga.toLocaleString("id-ID")}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-right: 15px;">
                    <button type="button" onclick="removeFromCart(${item.obat_id})" style="background: none; border: none; color: var(--danger); font-size: 16px; cursor: pointer;">
                        <i class="fas fa-minus-circle"></i>
                    </button>
                    <span style="font-weight: bold; font-size: 14px; width: 20px; text-align: center;">${item.qty}</span>
                    <button type="button" onclick="addCartQty(${item.obat_id})" style="background: none; border: none; color: var(--success); font-size: 16px; cursor: pointer;">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                </div>
                <div style="font-weight: bold; color: var(--primary); min-width: 80px; text-align: right;">
                    Rp ${subtotal.toLocaleString("id-ID")}
                </div>
            </li>
        `;
    });

    html += "</ul>";
    cartContainer.innerHTML = html;
    totalContainer.innerText = "Rp " + total.toLocaleString("id-ID");
    itemsInput.value = JSON.stringify(cart);
}

function filterObat() {
    let inputEl = document.getElementById("searchObat");
    if (!inputEl) return;

    let input = inputEl.value.toLowerCase();
    let cards = document.getElementsByClassName("obat-item");

    for (let i = 0; i < cards.length; i++) {
        let namaObat = cards[i]
            .getElementsByClassName("obat-nama")[0]
            .innerText.toLowerCase();
        cards[i].style.display = namaObat.includes(input) ? "" : "none";
    }
}

function processCheckout() {
    if (cart.length === 0) {
        showNotification("Keranjang masih kosong!", "error");
        return;
    }

    let uangDiterima = document.getElementById("pembayaranInput").value;
    let total = cart.reduce((sum, item) => sum + item.harga * item.qty, 0);

    if (uangDiterima < total) {
        showNotification("Uang pembayaran kurang dari total belanja!", "error");
        return;
    }

    document.getElementById("checkoutForm").submit();
}

//5. KELOLA OBAT
function showAddModal() {
    const modal = document.getElementById("addObatModal");
    if (modal) modal.classList.add("active");
}

function hideAddModal() {
    const modal = document.getElementById("addObatModal");
    if (modal) {
        modal.classList.remove("active");
        modal.querySelector("form").reset();
    }
}

function showEditModal(id, nama, kategoriId, jenis, harga) {
    const modal = document.getElementById("editObatModal");
    if (modal) {
        modal.querySelector("#edit_id").value = id;
        modal.querySelector("#edit_nama").value = nama;
        modal.querySelector("#edit_kategori_id").value = kategoriId;
        modal.querySelector("#edit_jenis").value = jenis;
        modal.querySelector("#edit_harga").value = harga;
        modal.querySelector("form").action = `/kelola-obat/${id}`;
        modal.classList.add("active");
    }
}

function hideEditModal() {
    const modal = document.getElementById("editObatModal");
    if (modal) modal.classList.remove("active");
}

//6. KELOLA STOK
function showAddStokModal() {
    const modal = document.getElementById("addStokModal");
    if (modal) modal.classList.add("active");
}

function hideAddStokModal() {
    const modal = document.getElementById("addStokModal");
    if (modal) {
        modal.classList.remove("active");
        modal.querySelector("form").reset();
    }
}

// MODAL KELOLA USER

// TAMBAH USER
function showAddUserModal() {
    const modal = document.getElementById("addUserModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function hideAddUserModal() {
    const modal = document.getElementById("addUserModal");
    if (modal) {
        modal.classList.remove("active");
        modal.querySelector("form").reset();
    }
}

// EDIT USER
function showEditUserModal(id, username, no_hp, role, tanggal_lahir) {
    const modal = document.getElementById("editUserModal");

    if (modal) {
        modal.querySelector("#edit_username").value = username;
        modal.querySelector("#edit_no_hp").value = no_hp;
        modal.querySelector("#edit_role").value = role;
        modal.querySelector("#edit_tanggal_lahir").value = tanggal_lahir;

        modal.querySelector("#editUserForm").action = `/users/${id}`;

        modal.classList.add("active");
    }
}

function hideEditUserModal() {
    const modal = document.getElementById("editUserModal");
    if (modal) {
        modal.classList.remove("active");
    }
}