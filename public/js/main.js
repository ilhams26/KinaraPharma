// LOGIN
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.querySelector(".toggle-password");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
} // Sidebar Mobile
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
    document.querySelector(".sidebar-overlay").classList.toggle("active");
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
    setTimeout(() => {
        let alerts = document.querySelectorAll(".alert-auto-close");
        alerts.forEach((alert) => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
    elements.forEach((el) => observer.observe(el));
});

// LOGOUT
function showLogoutModal(event) {
    event.preventDefault();
    document.getElementById("logoutModal").classList.add("active");
}

function hideLogoutModal() {
    document.getElementById("logoutModal").classList.remove("active");
}

//  TAMBAH OBAT
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

        modal.querySelector("form").reset();
    }
}

// MODAL EDIT OBAT
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
    if (modal) {
        modal.classList.remove("active");
    }
}

//KASIR
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

// Fitur Pencarian  Kasir
function filterObat() {
    let input = document.getElementById("searchObat").value.toLowerCase();
    let cards = document.getElementsByClassName("obat-item");

    for (let i = 0; i < cards.length; i++) {
        let namaObat = cards[i]
            .getElementsByClassName("obat-nama")[0]
            .innerText.toLowerCase();
        if (namaObat.includes(input)) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}

function processCheckout() {
    if (cart.length === 0) {
        alert("Keranjang masih kosong!");
        return;
    }

    let uangDiterima = document.getElementById("pembayaranInput").value;
    let total = cart.reduce((sum, item) => sum + item.harga * item.qty, 0);

    if (uangDiterima < total) {
        alert("Uang pembayaran kurang dari total belanja!");
        return;
    }

    document.getElementById("checkoutForm").submit();
}

// PENCARIAN TABEL GLOBAL
function searchTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();

    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach((row) => {
        let text = row.innerText.toLowerCase();

        row.style.display = text.includes(input) ? "" : "none";
    });
}
// MODAL KELOLA STOK
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

function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
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