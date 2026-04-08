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

// ==================== LOGIKA FITUR KASIR (POS) ====================
let cart = []; // Menyimpan data keranjang

// Fungsi menambah obat ke keranjang
function addToCart(id, nama, harga) {
    // Cek apakah obat sudah ada di keranjang
    let existingItem = cart.find((item) => item.obat_id === id);

    if (existingItem) {
        existingItem.qty += 1; // Tambah jumlah jika sudah ada
    } else {
        cart.push({ obat_id: id, nama: nama, harga: harga, qty: 1 });
    }

    renderCart(); // Perbarui tampilan keranjang
}

// Fungsi mengurangi atau menghapus obat dari keranjang
function removeFromCart(id) {
    let itemIndex = cart.findIndex((item) => item.obat_id === id);

    if (itemIndex !== -1) {
        if (cart[itemIndex].qty > 1) {
            cart[itemIndex].qty -= 1;
        } else {
            cart.splice(itemIndex, 1); // Hapus jika sisa 1
        }
    }

    renderCart();
}

// Fungsi merender HTML keranjang
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
            <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                <div style="flex: 1;">
                    <div style="font-weight: bold; font-size: 14px;">${item.nama}</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Rp ${item.harga.toLocaleString("id-ID")} x ${item.qty}</div>
                </div>
                <div style="font-weight: bold; color: var(--primary);">Rp ${subtotal.toLocaleString("id-ID")}</div>
                <button onclick="removeFromCart(${item.obat_id})" style="background: none; border: none; color: var(--danger); font-size: 16px; margin-left: 10px; cursor: pointer;">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </li>
        `;
    });

    html += "</ul>";

    cartContainer.innerHTML = html;
    totalContainer.innerText = "Rp " + total.toLocaleString("id-ID");

    // Simpan data keranjang ke dalam input hidden berbentuk JSON untuk dikirim ke Controller
    itemsInput.value = JSON.stringify(cart);
}

// Fitur Pencarian Cepat di Kasir
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

// Validasi sebelum submit
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
