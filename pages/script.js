// Data Storage (simulating session storage)
let bookingData = {
  namaLengkap: "",
  email: "",
  nomorTelepon: "",
  organisasi: "",
  tanggalBooking: "",
  jumlahPeserta: "",
  waktuMulai: "",
  waktuSelesai: "",
  durasi: "",
  tujuanPenggunaan: "",
  catatanTambahan: "",
  layananTambahan: [],
  layananHarga: {},
  sewaRuangan: 150000,
  biayaAdmin: 5000,
  ppn: 17050,
  totalHarga: 172050,
  bookingId: "VFBP-2025-001234",
  ruangan: "Meeting Room A",
  lokasi: "Jl. Lodaya No. 13, Bandung",
  metodePembayaran: "Transfer Bank BCA",
};

// Save to sessionStorage
function saveBookingData() {
  sessionStorage.setItem("bookingData", JSON.stringify(bookingData));
}

// Load from sessionStorage
function loadBookingData() {
  const saved = sessionStorage.getItem("bookingData");
  if (saved) {
    bookingData = JSON.parse(saved);
  }
}

// Load data on page load
loadBookingData();

// Utility Functions
function formatRupiah(angka) {
  return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDate(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", options);
}

function calculateDuration(start, end) {
  if (!start || !end) return "";

  const startTime = new Date("2000-01-01 " + start);
  const endTime = new Date("2000-01-01 " + end);
  const diff = (endTime - startTime) / (1000 * 60 * 60); // in hours

  return diff > 0 ? diff + " Jam" : "";
}

// Index.html (Booking Page) Functions
if (document.getElementById("bookingForm")) {
  const form = document.getElementById("bookingForm");
  const waktuMulai = document.getElementById("waktuMulai");
  const waktuSelesai = document.getElementById("waktuSelesai");
  const durasiBooking = document.getElementById("durasiBooking");
  const btnLanjut = document.getElementById("btnLanjutPembayaran");

  // Pricing constants
  const HARGA_PER_JAM = 50000;
  const BIAYA_ADMIN = 5000;
  const PPN_PERSEN = 11;
  const HARGA_CATERING = 50000;
  const HARGA_PERALATAN = 30000;
  const HARGA_DOKUMENTASI = 100000;

  // Auto calculate duration
  function updateDuration() {
    const duration = calculateDuration(waktuMulai.value, waktuSelesai.value);
    durasiBooking.value = duration;
    calculateTotal();
  }

  // Calculate and update total price
  function calculateTotal() {
    // Get duration in hours
    let durasi = 0;
    if (waktuMulai.value && waktuSelesai.value) {
      const startTime = new Date("2000-01-01 " + waktuMulai.value);
      const endTime = new Date("2000-01-01 " + waktuSelesai.value);
      durasi = (endTime - startTime) / (1000 * 60 * 60);
    }

    // Calculate base price (0 if no time selected)
    const sewaRuangan = durasi > 0 ? durasi * HARGA_PER_JAM : 0;

    // Calculate additional services
    let layananTambahan = 0;
    const layananHarga = {};

    if (document.getElementById("catering").checked) {
      layananTambahan += HARGA_CATERING;
      layananHarga.catering = HARGA_CATERING;
    }
    if (document.getElementById("peralatan").checked) {
      layananTambahan += HARGA_PERALATAN;
      layananHarga.peralatan = HARGA_PERALATAN;
    }
    if (document.getElementById("dokumentasi").checked) {
      layananTambahan += HARGA_DOKUMENTASI;
      layananHarga.dokumentasi = HARGA_DOKUMENTASI;
    }

    // Calculate subtotal
    const subtotal = sewaRuangan + layananTambahan;

    // Calculate PPN
    const ppn = Math.round(((subtotal + BIAYA_ADMIN) * PPN_PERSEN) / 100);

    // Calculate total
    const total = subtotal + BIAYA_ADMIN + ppn;

    // Update display
    updateSummaryDisplay(sewaRuangan, durasi, layananTambahan, ppn, total);

    // Save to bookingData
    bookingData.sewaRuangan = sewaRuangan;
    bookingData.biayaAdmin = BIAYA_ADMIN;
    bookingData.ppn = ppn;
    bookingData.totalHarga = total;
    bookingData.layananHarga = layananHarga;
    bookingData.totalLayanan = layananTambahan;
    saveBookingData();
  }

  // Update summary display in sidebar
  function updateSummaryDisplay(
    sewaRuangan,
    durasi,
    layananTambahan,
    ppn,
    total
  ) {
    const summaryCard = document.querySelector(".summary-card");
    if (!summaryCard) return;

    const durasiText = durasi > 0 ? durasi + " Jam" : "-";

    summaryCard.innerHTML = `
            <h3>Ringkasan Biaya</h3>
            
            <div class="summary-item">
                <span>Sewa Ruangan ${
                  durasi > 0 ? "(" + durasiText + ")" : ""
                }</span>
                <strong>${
                  sewaRuangan > 0 ? formatRupiah(sewaRuangan) : "Rp 0"
                }</strong>
            </div>
            ${
              layananTambahan > 0
                ? `
            <div class="summary-item">
                <span>Layanan Tambahan</span>
                <strong>${formatRupiah(layananTambahan)}</strong>
            </div>`
                : ""
            }
            <div class="summary-item">
                <span>Biaya admin</span>
                <strong>${formatRupiah(BIAYA_ADMIN)}</strong>
            </div>
            <div class="summary-item">
                <span>PPN 11%</span>
                <strong>${formatRupiah(ppn)}</strong>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total">
                <span>Total Pembayaran</span>
                <strong>${total > 0 ? formatRupiah(total) : "Rp 0"}</strong>
            </div>

            <div class="info-box">
                <span class="icon">✓</span> Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi yang berlaku
            </div>

            <button type="button" class="btn-primary" id="btnLanjutPembayaran">
                Lanjut Ke Pembayaran
            </button>

            <div class="transaction-notice">
                🔒 Transaksi aman dan terenkripsi
            </div>
        `;

    // Re-attach event listener to new button
    document
      .getElementById("btnLanjutPembayaran")
      .addEventListener("click", handleLanjutPembayaran);
  }

  if (waktuMulai) waktuMulai.addEventListener("change", updateDuration);
  if (waktuSelesai) waktuSelesai.addEventListener("change", updateDuration);

  // Listen to additional services changes
  const checkboxes = document.querySelectorAll(
    '.service-item input[type="checkbox"]'
  );
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", calculateTotal);
  });

  // Initial calculation
  calculateTotal();

  // Handle form submission
  function handleLanjutPembayaran(e) {
    if (e) e.preventDefault();

    // Validate form
    const namaLengkap = document.getElementById("namaLengkap").value;
    const email = document.getElementById("email").value;
    const nomorTelepon = document.getElementById("nomorTelepon").value;
    const tanggalBooking = document.getElementById("tanggalBooking").value;
    const jumlahPeserta = document.getElementById("jumlahPeserta").value;
    const waktuMulaiVal = document.getElementById("waktuMulai").value;
    const waktuSelesaiVal = document.getElementById("waktuSelesai").value;
    const tujuanPenggunaan = document.getElementById("tujuanPenggunaan").value;

    if (
      !namaLengkap ||
      !email ||
      !nomorTelepon ||
      !tanggalBooking ||
      !jumlahPeserta ||
      !waktuMulaiVal ||
      !waktuSelesaiVal ||
      !tujuanPenggunaan
    ) {
      alert("Mohon lengkapi semua field yang wajib diisi!");
      return;
    }

    // Save booking data
    bookingData.namaLengkap = namaLengkap;
    bookingData.email = email;
    bookingData.nomorTelepon = nomorTelepon;
    bookingData.organisasi = document.getElementById("organisasi").value;
    bookingData.tanggalBooking = tanggalBooking;
    bookingData.tanggalBooking = tanggalBooking;
    bookingData.jumlahPeserta = jumlahPeserta;
    bookingData.waktuMulai = waktuMulaiVal;
    bookingData.waktuSelesai = waktuSelesaiVal;
    bookingData.durasi = durasiBooking.value;
    bookingData.tujuanPenggunaan = tujuanPenggunaan;
    bookingData.catatanTambahan =
      document.getElementById("catatanTambahan").value;

    // Get selected services
    const layanan = [];
    if (document.getElementById("catering").checked) layanan.push("Catering");
    if (document.getElementById("peralatan").checked) layanan.push("Peralatan");
    if (document.getElementById("dokumentasi").checked)
      layanan.push("Dokumentasi");
    bookingData.layananTambahan = layanan;

    // Save to sessionStorage
    saveBookingData();

    // Redirect to payment page
    window.location.href = "payment.html";
  }

  if (btnLanjut) {
    btnLanjut.addEventListener("click", handleLanjutPembayaran);
  }
}

// Payment.html Functions
// if (document.getElementById('countdown')) {
//     // Display booking summary from saved data
//     if (bookingData.tanggalBooking) {
//         document.getElementById('displayTanggal').textContent = formatDate(bookingData.tanggalBooking);
//         document.getElementById('displayWaktu').textContent = `${bookingData.waktuMulai} - ${bookingData.waktuSelesai}`;
//         document.getElementById('displayDurasi').textContent = bookingData.durasi;
//     }

//     // Update payment summary with saved data
//     const summaryHTML = `
//         <h3>RINGKASAN PEMBAYARAN</h3>

//         <div class="booking-summary">
//             <div class="summary-row">
//                 <span>Ruangan</span>
//                 <strong>Meeting Room</strong>
//             </div>
//             <div class="summary-row">
//                 <span>Tanggal</span>
//                 <strong>${bookingData.tanggalBooking ? formatDate(bookingData.tanggalBooking) : '21 Okt 2025'}</strong>
//             </div>
//             <div class="summary-row">
//                 <span>Waktu</span>
//                 <strong>${bookingData.waktuMulai && bookingData.waktuSelesai ? bookingData.waktuMulai + ' - ' + bookingData.waktuSelesai : '09:00 - 12:00'}</strong>
//             </div>
//             <div class="summary-row">
//                 <span>Durasi</span>
//                 <strong>${bookingData.durasi || '3 Jam'}</strong>
//             </div>
//         </div>

//         <div class="summary-divider"></div>

//         <div class="summary-item">
//             <span>Sewa Ruangan</span>
//             <strong>${formatRupiah(bookingData.sewaRuangan)}</strong>
//         </div>
//         ${bookingData.layananHarga?.catering ? `
//         <div class="summary-item">
//             <span>Catering</span>
//             <strong>${formatRupiah(bookingData.layananHarga.catering)}</strong>
//         </div>` : ''}
//         ${bookingData.layananHarga?.peralatan ? `
//         <div class="summary-item">
//             <span>Peralatan Tambahan</span>
//             <strong>${formatRupiah(bookingData.layananHarga.peralatan)}</strong>
//         </div>` : ''}
//         ${bookingData.layananHarga?.dokumentasi ? `
//         <div class="summary-item">
//             <span>Dokumentasi Profesional</span>
//             <strong>${formatRupiah(bookingData.layananHarga.dokumentasi)}</strong>
//         </div>` : ''}
//         <div class="summary-item">
//             <span>Biaya Admin</span>
//             <strong>${formatRupiah(bookingData.biayaAdmin)}</strong>
//         </div>
//         <div class="summary-item">
//             <span>PPN (11%)</span>
//             <strong>${formatRupiah(bookingData.ppn)}</strong>
//         </div>

//         <div class="summary-divider"></div>

//         <div class="summary-total">
//             <span>Total Pembayaran</span>
//             <strong class="total-amount">${formatRupiah(bookingData.totalHarga)}</strong>
//         </div>

//         <button type="button" class="btn-primary" id="btnBayarSekarang">
//             BAYAR SEKARANG
//         </button>

//         <button type="button" class="btn-secondary" id="btnKembaliBooking">
//             ← KEMBALI KE BOOKING
//         </button>

//         <div class="info-box" style="margin-top: 20px;">
//             🔒 Pembayaran aman dan terenkripsi
//         </div>
//     `;

//     const summaryCard = document.querySelector('.summary-card');
//     if (summaryCard) {
//         summaryCard.innerHTML = summaryHTML;
//     }

//     // Countdown timer (24 hours)
//     let timeLeft = 24 * 60 * 60; // 24 hours in seconds

//     function updateCountdown() {
//         const hours = Math.floor(timeLeft / 3600);
//         const minutes = Math.floor((timeLeft % 3600) / 60);
//         const seconds = timeLeft % 60;

//         document.getElementById('countdown').textContent =
//             `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

//         if (timeLeft > 0) {
//             timeLeft--;
//         } else {
//             clearInterval(countdownInterval);
//             alert('Waktu pembayaran habis!');
//         }
//     }

//     updateCountdown();
//     const countdownInterval = setInterval(updateCountdown, 1000);

//     // Payment method selection
//     const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
//     paymentMethods.forEach(method => {
//         method.addEventListener('change', function() {
//             const methodNames = {
//                 'bank': 'Transfer Bank BCA',
//                 'va': 'Virtual Account',
//                 'ewallet': 'E-Wallet',
//                 'card': 'Kartu Kredit/Debit',
//                 'qris': 'QRIS'
//             };
//             bookingData.metodePembayaran = methodNames[this.value];
//             saveBookingData();
//         });
//     });

//     // Handle payment button (re-attach after innerHTML update)
//     setTimeout(() => {
//         const btnBayar = document.getElementById('btnBayarSekarang');
//         if (btnBayar) {
//             btnBayar.addEventListener('click', function() {
//                 window.location.href = 'waiting.html';
//             });
//         }

//         const btnKembali = document.getElementById('btnKembaliBooking');
//         if (btnKembali) {
//             btnKembali.addEventListener('click', function() {
//                 window.location.href = 'index.html';
//             });
//         }
//     }, 100);
// }

// Waiting.html Functions
if (document.getElementById("countdownLarge")) {
  // Display booking details from saved data
  document.getElementById("bookingId").textContent = bookingData.bookingId;
  document.getElementById("displayNama").textContent =
    bookingData.namaLengkap || "Ahmad Fauzl";
  document.getElementById("displayEmail").textContent =
    bookingData.email || "ahmad.fauzl@email.com";
  document.getElementById("displayTelepon").textContent =
    bookingData.nomorTelepon || "+62 812-3456-7890";
  document.getElementById("displayRuangan").textContent = bookingData.ruangan;
  document.getElementById("displayLokasi").textContent = bookingData.lokasi;
  document.getElementById("displayTanggalDetail").textContent =
    bookingData.tanggalBooking
      ? formatDate(bookingData.tanggalBooking)
      : "10 November 2025";
  document.getElementById("displayWaktuDetail").textContent = `${
    bookingData.waktuMulai || "09:00"
  } - ${bookingData.waktuSelesai || "12:00"} (${
    bookingData.durasi || "3 Jam"
  })`;
  document.getElementById("displayKapasitas").textContent = `${
    bookingData.jumlahPeserta || "6"
  } Orang`;

  // Update price summary with saved data
  const priceSummaryHTML = `
        <div class="price-item">
            <span>Sewa Ruangan</span>
            <strong>${formatRupiah(bookingData.sewaRuangan)}</strong>
        </div>
        ${
          bookingData.totalLayanan > 0
            ? `
        <div class="price-item">
            <span>Layanan Tambahan</span>
            <strong>${formatRupiah(bookingData.totalLayanan)}</strong>
        </div>`
            : ""
        }
        <div class="price-item">
            <span>Biaya Admin</span>
            <strong>${formatRupiah(bookingData.biayaAdmin)}</strong>
        </div>
        <div class="price-item">
            <span>PPN 11%</span>
            <strong>${formatRupiah(bookingData.ppn)}</strong>
        </div>
        <div class="summary-divider"></div>
        <div class="price-item total">
            <span>Total Pembayaran</span>
            <strong>${formatRupiah(bookingData.totalHarga)}</strong>
        </div>
    `;

  const priceSummary = document.querySelector(".price-summary");
  if (priceSummary) {
    priceSummary.innerHTML = priceSummaryHTML;
  }

  // Large countdown timer
  let timeLeftLarge = 23 * 3600 + 59 * 60 + 42; // 23:59:42

  function updateLargeCountdown() {
    const hours = Math.floor(timeLeftLarge / 3600);
    const minutes = Math.floor((timeLeftLarge % 3600) / 60);
    const seconds = timeLeftLarge % 60;

    document.getElementById("countdownLarge").textContent = `${hours
      .toString()
      .padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds
      .toString()
      .padStart(2, "0")}`;

    if (timeLeftLarge > 0) {
      timeLeftLarge--;
    } else {
      clearInterval(largeCountdownInterval);
    }
  }

  updateLargeCountdown();
  const largeCountdownInterval = setInterval(updateLargeCountdown, 1000);

  // Handle proceed to payment button
  const btnLanjutPembayaran = document.getElementById("btnLanjutPembayaran");
  if (btnLanjutPembayaran) {
    btnLanjutPembayaran.addEventListener("click", function () {
      // Simulate payment completion
      setTimeout(() => {
        window.location.href = "confirmation.html";
      }, 1000);
    });
  }

  // Handle cancel button
  const btnBatalkan = document.getElementById("btnBatalkanPesanan");
  if (btnBatalkan) {
    btnBatalkan.addEventListener("click", function () {
      if (confirm("Apakah Anda yakin ingin membatalkan pesanan?")) {
        sessionStorage.clear();
        window.location.href = "index.html";
      }
    });
  }
}

// Confirmation.html Functions
if (document.getElementById("btnLihatInvoice")) {
  // Update confirmation page with saved data
  const confirmationHTML = `
        <h2>Detail Booking</h2>

        <div class="booking-info">
            <div class="info-label">D Booking</div>
            <div class="info-value">${bookingData.bookingId}</div>
        </div>

        <div class="booking-info">
            <div class="info-label">Nama Ruangan</div>
            <div class="info-value">${bookingData.ruangan}</div>
        </div>

        <div class="booking-info">
            <div class="info-label">Tanggal</div>
            <div class="info-value">${
              bookingData.tanggalBooking
                ? formatDate(bookingData.tanggalBooking)
                : "25 Oktober 2025"
            }</div>
        </div>

        <div class="booking-info">
            <div class="info-label">Waktu</div>
            <div class="info-value">${bookingData.waktuMulai || "09:00"} - ${
    bookingData.waktuSelesai || "12:00"
  }</div>
        </div>

        <div class="booking-info">
            <div class="info-label">Durasi</div>
            <div class="info-value">${bookingData.durasi || "3 Jam"}</div>
        </div>

        <div class="booking-info">
            <div class="info-label">Metode Pembayaran</div>
            <div class="info-value">${bookingData.metodePembayaran}</div>
        </div>

        <div class="payment-total">
            <span>Total Dibayar</span>
            <strong>${formatRupiah(bookingData.totalHarga)}</strong>
        </div>

        <div class="order-info">
            <h3>Informasi Pemesan</h3>
            <div class="info-row">
                <span>Nama</span>
                <strong>${bookingData.namaLengkap || "Ahmad Santoso"}</strong>
            </div>
            <div class="info-row">
                <span>Email</span>
                <strong>${
                  bookingData.email || "ahmad.santoso@email.com"
                }</strong>
            </div>
        </div>

        <div class="action-buttons-confirmation">
            <button class="btn-outline" id="btnLihatInvoice">Lihat Invoice</button>
            <button class="btn-primary-dark" id="btnKembaliDashboard">KEMBALI KE DASHBOARD</button>
        </div>

        <div class="info-notice">
            <strong>Informasi Penting</strong>
            <p>Invoice telah dikirim ke email Anda. Silakan tunjukkan invoice atau ID booking saat check-in. Jika ada pertanyaan, hubungi kami di support@tekape.space</p>
        </div>
    `;

  const confirmationCard = document.querySelector(".confirmation-card");
  if (confirmationCard) {
    confirmationCard.innerHTML = confirmationHTML;
  }

  // Re-attach event listeners after innerHTML update
  setTimeout(() => {
    const btnInvoice = document.getElementById("btnLihatInvoice");
    const btnDashboard = document.getElementById("btnKembaliDashboard");

    if (btnInvoice) {
      btnInvoice.addEventListener("click", function () {
        window.location.href = "invoice.html";
      });
    }

    if (btnDashboard) {
      btnDashboard.addEventListener("click", function () {
        sessionStorage.clear();
        window.location.href = "index.html";
      });
    }
  }, 100);
}

// Invoice.html Functions
if (document.querySelector(".invoice-card")) {
  // Load booking data
  loadBookingData();

  // Update invoice with booking data
  const today = new Date();
  const invoiceDate = today.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  // Update header info
  document.getElementById(
    "invoiceNumber"
  ).textContent = `INV/${bookingData.bookingId}`;
  document.getElementById("tanggalInvoice").textContent = invoiceDate;
  document.getElementById("statusPembayaran").textContent = "LUNAS";

  // Update customer info
  document.getElementById("custName").textContent =
    bookingData.namaLengkap || "Boboiboy Topan";
  document.getElementById("custEmail").textContent =
    bookingData.email || "Kuasatige@emel.kom";
  document.getElementById("custPhone").textContent =
    bookingData.nomorTelepon || "+99 012345678";
  document.getElementById("custCompany").textContent =
    bookingData.organisasi || "PT.Peyelamat Bumi";

  // Update booking details
  document.getElementById("bookingIdInv").textContent = bookingData.bookingId;
  document.getElementById("roomName").textContent = bookingData.ruangan;
  document.getElementById("roomLocation").textContent = bookingData.lokasi;
  document.getElementById("bookingDate").textContent =
    bookingData.tanggalBooking
      ? formatDate(bookingData.tanggalBooking)
      : "25 Oktober 2024";
  document.getElementById("bookingTime").textContent = `${
    bookingData.waktuMulai || "09:00"
  } WIB - ${bookingData.waktuSelesai || "12:00"} WIB`;

  // Update price table
  let tableHTML = "";
  const durasi = bookingData.durasi || "3 Jam";
  const hargaPerJam = 50000;

  // Sewa ruangan
  tableHTML += `
        <tr>
            <td>Sewa Ruangan (${durasi})</td>
            <td style="text-align: center;">1</td>
            <td style="text-align: right;">${formatRupiah(hargaPerJam)}</td>
            <td style="text-align: right;">${formatRupiah(
              bookingData.sewaRuangan
            )}</td>
        </tr>
    `;

  // Layanan tambahan
  if (bookingData.layananHarga?.peralatan) {
    tableHTML += `
            <tr>
                <td>Proyektor & Sound System</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">${formatRupiah(
                  bookingData.layananHarga.peralatan
                )}</td>
                <td style="text-align: right;">${formatRupiah(
                  bookingData.layananHarga.peralatan
                )}</td>
            </tr>
        `;
  }

  if (bookingData.layananHarga?.catering) {
    const pax = bookingData.jumlahPeserta || 20;
    const hargaPerPax = Math.round(bookingData.layananHarga.catering / pax);
    tableHTML += `
            <tr>
                <td>Catering Snack (${pax} Pax)</td>
                <td style="text-align: center;">${pax}</td>
                <td style="text-align: right;">${formatRupiah(hargaPerPax)}</td>
                <td style="text-align: right;">${formatRupiah(
                  bookingData.layananHarga.catering
                )}</td>
            </tr>
        `;
  }

  if (bookingData.layananHarga?.dokumentasi) {
    tableHTML += `
            <tr>
                <td>Dokumentasi Profesional</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">${formatRupiah(
                  bookingData.layananHarga.dokumentasi
                )}</td>
                <td style="text-align: right;">${formatRupiah(
                  bookingData.layananHarga.dokumentasi
                )}</td>
            </tr>
        `;
  }

  document.getElementById("priceTableBody").innerHTML = tableHTML;

  // Update totals
  const subtotal = bookingData.sewaRuangan + (bookingData.totalLayanan || 0);
  document.getElementById("subtotalAmount").textContent =
    formatRupiah(subtotal);
  document.getElementById("taxAmount").textContent = formatRupiah(
    bookingData.biayaAdmin + bookingData.ppn
  );
  document.getElementById("totalAmount").textContent = formatRupiah(
    bookingData.totalHarga
  );
}

// Download PDF Function
function downloadPDF() {
  alert(
    'Fitur download PDF akan segera tersedia!\n\nUntuk sementara, Anda dapat menggunakan fungsi Print dan pilih "Save as PDF" di printer options.'
  );
  window.print();
}

// Initialize page based on stored data
document.addEventListener("DOMContentLoaded", function () {
  // Check if we have booking data and we're on payment/waiting/confirmation page
  const currentPage = window.location.pathname.split("/").pop();

  if (
    (currentPage === "payment.html" ||
      currentPage === "waiting.html" ||
      currentPage === "confirmation.html") &&
    !bookingData.namaLengkap
  ) {
    // No booking data, redirect to index
    // window.location.href = 'index.html';
  }
});
