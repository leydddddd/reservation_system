<?php

include 'api/connection.php';

$query = "SELECT * FROM rooms ORDER BY id ASC";
$result = mysqli_query($ineedthis, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tekape Workspace</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;1,600&display=swap" rel="stylesheet">

    <!-- CSS Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Sedikit Style Saja -->
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
        }

        .time-btn {
            min-width: 80px;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 500;
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            background-color: #ffffff33;
            /* transparent white */
            border-radius: 50%;
            color: white;
            font-size: 30px;
        }
    </style>

</head>

<body>
    <!-- JS scripts -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

    <header class="header container-fluid px-0">
        <!--Navbar-->
        <nav class="navbar navbar-expand-lg navbar-light shadow-lg fixed-top" style="background-color: #fd7e14;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <!-- ini logo dah lumayan lah -->
                    <img src="./res/tkp_logo2.png" alt="Tekape Workspace Logo" height="35" class="d-inline-block align-text-top ms-5">
                </a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- progress 0/4 page -->
                    <ul class="navbar-nav ms-auto me-5">
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#banner">Home</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#rooms">Rooms</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
                            <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-light"></div>
                            <hr class="d-lg-none my-2 text-light" />
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="pages/dashboard/LoginAdmin.php">Login Admin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--Navbar Ends-->

        <!--Banner-->
        <section class="banner" id="banner">
            <div class="jumbotro">
                <div
                    class="container-fluid position-relative p-0"
                    style="min-height: 100vh; overflow: hidden">
                    <!-- jaga-jaga harus diganti ke delete image -->
                    <img
                        src="./res/banner1.jpg"
                        alt="akudiriku"
                        class="img-fluid w-100"
                        style="object-fit: cover; height: 100vh; filter: brightness(0.6)" />
                    <div
                        class="position-absolute top-50 start-50 translate-middle text-center w-100">
                        <h1 class="text-warning display-6 mx-auto" style="font-size:4vw; line-height:1; max-width:80vw; font-family: 'Montserrat', sans-serif;">Ruang Meeting dan Event Space</h1>
                        <p class="text-white fs-4 mt-4 mx-auto">
                            Ruang Pertemuan Untuk Setiap Pekerjaan Anda
                        </p>
                        <a href="#rooms">
                            <button type="button" class="btn btn-warning">Booking Sekarang!</button>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!--Banner Ends-->
    </header>
    <!-- Header Ends-->

    <!--Content-->
    <section class="main-content container-fluid px-0">
        <div class="row" id="rooms">
            <!-- Title -->
            <div class="text-center mb-4 mt-5 pt-4">
                <h1 class="fw-bold" style="font-family: 'Montserrat', sans;">Booking Room</h1>
                <p class="fw-normal">Kami menyediakan ruang kerja yang nyaman dan fasilitas lengkap untuk mendukung produktivitas Anda.</p>
            </div>
            <!-- Cards -->
            <?php
            include 'api/connection.php';
            $count = 0;
            while ($count < 3 && ($row = mysqli_fetch_assoc($result))) :
                $count++;
            ?>
                <div class="row justify-content-center mb-5">
                    <div class="col-10 justify-content-center mb-3 gx-3">
                        <div class="card h-100 text-center border-0 shadow-lg">
                            <div class="card-body" id="rooms1">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-4">
                                                <img src="./res/<?= htmlspecialchars($row['imageloc']) ?>" class="card-img-top rounded" alt="<?= htmlspecialchars($row['nama_ruangan']) ?>" height="300">
                                            </div>
                                            <!-- Nama Meeting Room, Luas, Harga dan Lokasi -->
                                            <div class="col-8 d-flex flex-column justify-content-between">
                                                <div class="d-flex justify-content-start">
                                                    <h2 class="fw-bold"><?= htmlspecialchars($row['nama_ruangan']) ?></h2>
                                                </div>
                                                <div class="d-flex justify-content-start">
                                                    <p class="fs-6">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-fill" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.3 1.3 0 0 0-.37.265.3.3 0 0 0-.057.09V14l.002.008.016.033a.6.6 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.6.6 0 0 0 .146-.15l.015-.033L12 14v-.004a.3.3 0 0 0-.057-.09 1.3 1.3 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465s-2.462-.172-3.34-.465c-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411" />
                                                        </svg>
                                                        <?= htmlspecialchars($row['alamat']) ?>
                                                    </p>
                                                </div>
                                                <!-- Bottom Content: Harga / Kapasitas / Ukuran -->
                                                <div class="d-flex justify-content-start mt-auto gap-3">
                                                    <div class="d-flex align-items-center p-2 rounded-3" style="background:#f7f7f7;">
                                                        <div class="me-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                                                                <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z" />
                                                                <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z" />
                                                            </svg>
                                                        </div>
                                                        <div class="d-flex flex-column lh-sm align-items-start">
                                                            <span class="text-muted small fs-6">Harga</span>
                                                            <span class="fw-bold text-warning fs-6">Rp<?= htmlspecialchars($row['harga_per_jam']) ?>/Jam</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center p-2 rounded-3" style="background:#f7f7f7;">
                                                        <div class="me-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                                            </svg>
                                                        </div>
                                                        <div class="d-flex flex-column lh-sm align-items-start">
                                                            <span class="text-muted small fs-6">Kapasitas</span>
                                                            <span class="fw-bold text-warning fs-6"><?= htmlspecialchars($row['kapasitas']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center p-2 rounded-3" style="background:#f7f7f7;">
                                                        <div class="me-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-rulers" viewBox="0 0 16 16">
                                                                <path d="M1 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h5v-1H2v-1h4v-1H4v-1h2v-1H2v-1h4V9H4V8h2V7H2V6h4V2h1v4h1V4h1v2h1V2h1v4h1V4h1v2h1V2h1v4h1V1a1 1 0 0 0-1-1z" />
                                                            </svg>
                                                        </div>
                                                        <div class="d-flex flex-column lh-sm align-items-start">
                                                            <span class="text-muted small fs-6">Ukuran</span>
                                                            <span class="fw-bold text-warning fs-6"><?= htmlspecialchars($row['ukuran']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="d-flex flex-row mb-3">
                                            <!-- Pilih Tanggal -->
                                            <div class="d-flex flex-column mb-2 justify-content-start">
                                                <label for="booking_date<?= $count ?>" class="form-label fw-bold fs-6 text-start">Pilih Tanggal:</label>
                                                <input type="date" class="form-control" id="booking_date<?= $count ?>" name="tanggal">
                                            </div>
                                        </div>

                                        <!-- Pilih Jam -->
                                        <form action="pages/bookingform.php" method="POST" id="bookingForm<?= $count ?>">

                                            <!-- Hidden inputs filled later by JS -->
                                            <input type="hidden" name="selected_date" id="selected_date_<?= $count ?>">
                                            <input type="hidden" name="hours" id="selected_hours_<?= $count ?>">
                                            <input type="hidden" name="total_price" id="total_price_<?= $count ?>">
                                            <input type="hidden" name="room_id" value="<?= intval($row['id']) ?>">

                                            <div class="d-flex flex-wrap gap-2 mt-3" id="hoursContainer<?= $count ?>">

                                                <!-- Generate hour buttons (08:00 - 17:00) -->
                                                <?php
                                                $hours = range(8, 17);
                                                foreach ($hours as $h) :
                                                    $hourLabel = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
                                                ?>
                                                    <div class="d-flex flex-column justify-content-start">
                                                        <input type="checkbox" class="hour-checkbox btn-check" value="<?= $hourLabel ?>" id="btn-check<?= $count ?><?= $h ?>" autocomplete="off">
                                                        <label class="time-btn btn btn-outline-dark me-2 fs-6 px-5" for="btn-check<?= $count ?><?= $h ?>"><?= $hourLabel ?></label>
                                                    </div>
                                                <?php endforeach; ?>

                                            </div>

                                            <!-- Bottom Section -->
                                            <div class="d-flex align-items-center p-2 rounded-3 mt-3" style="background:#f7f7f7;">
                                                <div class="d-flex flex-column lh-sm align-items-start">
                                                    <span class="fw-bold small fs-6">Waktu Dipilih :</span>
                                                    <span id="totalPriceLabel_<?= $count ?>" class="fw-bold text-warning fs-6">
                                                        Total Harga : Rp0
                                                    </span>
                                                </div>
                                                <div class="d-flex ms-auto">
                                                    <button type="submit" class="btn btn-primary rounded-3 fs-6 px-5">
                                                        Booking Sekarang
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                        <script>
                                            (function() {
                                                const container = document.getElementById("hoursContainer<?= $count ?>").closest("li");
                                                const hourCheckboxes = container.querySelectorAll(".hour-checkbox");
                                                const selectedHoursInput = document.getElementById("selected_hours_<?= $count ?>");
                                                const totalPriceInput = document.getElementById("total_price_<?= $count ?>");
                                                const totalPriceLabel = document.getElementById("totalPriceLabel_<?= $count ?>");
                                                const selectedDateInput = document.getElementById("selected_date_<?= $count ?>");
                                                const datePicker = document.getElementById("booking_date<?= $count ?>");
                                                const form = document.getElementById("bookingForm<?= $count ?>");

                                                const pricePerHour = <?= doubleval($row['price']) ?>;
                                                const roomId = <?= intval($row['id']) ?>;

                                                function updateHours() {
                                                    let selected = [];
                                                    hourCheckboxes.forEach(cb => {
                                                        if (cb.checked) selected.push(cb.value);
                                                    });

                                                    selectedHoursInput.value = JSON.stringify(selected);
                                                    let total = selected.length * pricePerHour;
                                                    totalPriceInput.value = total;
                                                    totalPriceLabel.textContent = "Total Harga : Rp" + total.toLocaleString("id-ID");
                                                }

                                                function enableAllHours() {
                                                    hourCheckboxes.forEach(cb => {
                                                        cb.disabled = false;
                                                        cb.checked = false;
                                                        const btnLabel = container.querySelector(`label[for="${cb.id}"]`);
                                                        if (btnLabel) {
                                                            btnLabel.classList.remove("disabled", "btn-secondary");
                                                            btnLabel.classList.add("btn-outline-dark");
                                                        }
                                                    });
                                                    updateHours();
                                                }

                                                function disableBookedHours(bookedList) {
                                                    hourCheckboxes.forEach(cb => {
                                                        const btnLabel = container.querySelector(`label[for="${cb.id}"]`);

                                                        if (bookedList.includes(cb.value)) {
                                                            cb.disabled = true;
                                                            cb.checked = false;

                                                            if (btnLabel) {
                                                                btnLabel.classList.add("disabled", "btn-secondary");
                                                                btnLabel.classList.remove("btn-outline-dark");
                                                            }
                                                        } else {
                                                            cb.disabled = false;

                                                            if (btnLabel) {
                                                                btnLabel.classList.remove("disabled", "btn-secondary");
                                                                btnLabel.classList.add("btn-outline-dark");
                                                            }
                                                        }
                                                    });

                                                    updateHours();
                                                }

                                                function fetchBookedHours(date) {
                                                    if (!date) {
                                                        // If date cleared, re-enable all hours and clear selected date value
                                                        selectedDateInput.value = "";
                                                        enableAllHours();
                                                        return;
                                                    }

                                                    fetch("api/_fetchBookDate.php", {
                                                            method: "POST",
                                                            headers: {
                                                                "Content-Type": "application/x-www-form-urlencoded"
                                                            },
                                                            body: new URLSearchParams({
                                                                room_id: roomId,
                                                                date: date
                                                            })
                                                        })
                                                        .then(response => {
                                                            if (!response.ok) {
                                                                throw new Error(`HTTP error! status: ${response.status}`);
                                                            }
                                                            return response.json();
                                                        })
                                                        .then(data => {
                                                            if (data && data.booked && Array.isArray(data.booked)) {
                                                                disableBookedHours(data.booked);
                                                            } else {
                                                                // no bookings for that date => enable all
                                                                enableAllHours();
                                                            }
                                                        })
                                                        .catch(err => {
                                                            console.error("Fetch error:", err);
                                                            alert("Failed to load booked hours. Please try again.");
                                                        });
                                                }

                                                // wire up checkbox changes
                                                hourCheckboxes.forEach(cb => cb.addEventListener("change", updateHours));

                                                // date change -> fetch or reset
                                                if (datePicker) {
                                                    datePicker.addEventListener("change", function() {
                                                        const selectedDate = this.value;
                                                        selectedDateInput.value = selectedDate;
                                                        fetchBookedHours(selectedDate);
                                                    });
                                                }

                                                // prevent submit if date not selected or no hours chosen
                                                if (form) {
                                                    form.addEventListener("submit", function(e) {
                                                        const dateVal = (datePicker && datePicker.value) ? datePicker.value.trim() : (selectedDateInput.value || "").trim();
                                                        if (!dateVal) {
                                                            e.preventDefault();
                                                            alert("Silakan pilih tanggal sebelum melanjutkan booking.");
                                                            if (datePicker) datePicker.focus();
                                                            return false;
                                                        }

                                                        // ensure at least one hour is selected
                                                        const checkedHours = Array.from(hourCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                                                        if (checkedHours.length === 0) {
                                                            e.preventDefault();
                                                            alert("Silakan pilih minimal satu jam sebelum melanjutkan booking.");
                                                            const firstAvailable = Array.from(hourCheckboxes).find(cb => !cb.disabled);
                                                            if (firstAvailable) firstAvailable.focus();
                                                            return false;
                                                        }

                                                        // ensure selected_date and selected_hours are populated before submit
                                                        selectedHoursInput.value = JSON.stringify(checkedHours);
                                                        selectedDateInput.value = dateVal;
                                                    });
                                                }

                                                // Auto-load if date already selected
                                                if (datePicker && datePicker.value) {
                                                    selectedDateInput.value = datePicker.value;
                                                    fetchBookedHours(datePicker.value);
                                                }

                                            })();
                                        </script>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <!--Content Ends-->

    <!--Footer -->
    <section class="footer container-fluid bg-light text-white px-0" id="contact">

        <div class="row px-0" style="height: 68vh;">
            <div class="col-12 d-flex flex-column justify-content-center bg-info">
                <div class="row justify-content-center text-center">

                    <div class="col-12">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-chat-square-text-fill" viewBox="0 0 16 16">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2.5a1 1 0 0 0-.8.4l-1.9 2.533a1 1 0 0 1-1.6 0L5.3 12.4a1 1 0 0 0-.8-.4H2a2 2 0 0 1-2-2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z" />
                        </svg>
                    </div>

                    <div class="col-12 text-white">
                        <h2 class="fw-bold">Butuh Bantuan?</h2>
                        <p class="fs-6">Hubungi kami melalui email atau telepon, kami siap membantu Anda!</p>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-success rounded-3 fs-6 px-5 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                            </svg>
                            Hubungi Kami
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 text-dark" style="background-color: #583804ff;">
                <div class="row g-5 justify-content-center py-5 mx-5 text-white">
                    <div class="col-2">
                        <p class="fw-bold text-warning">Tekape Workspace</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    </div>
                    <div class="col-2">
                        <p class="fw-bold text-warning">Quick Links</p>
                        <p>Home</p>
                        <p>Rooms</p>
                        <p>Contact</p>
                    </div>
                    <div class="col-3">
                        <p class="fw-bold text-warning">Layanan</p>
                        <p>Meeting Room</p>
                        <p>Workshop Space</p>
                        <p>Event Hall</p>
                        <p>Coworking Space</p>
                    </div>
                    <div class="col-2">
                        <p class="fw-bold text-warning">Bisnis Terpadu</p>
                        <p>Legalitas dan Perizinan</p>
                        <p>Pengelolaan Keuangan</p>
                        <p>Jasa Laporan Pajak</p>
                    </div>
                    <div class="col-2">
                        <p class="fw-bold text-warning">Kontak Kami</p>
                        <p>FAQ</p>
                        <p>Contact Us</p>
                        <div class="d-flex flex-row">
                            <div class="icon-circle d-flex justify-content-center p-2 align-items-center me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                                    <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                                </svg>
                            </div>
                            <div class="icon-circle d-flex justify-content-center p-2 align-items-center me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                    <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                                </svg>
                            </div>
                            <div class="icon-circle d-flex justify-content-center p-2 align-items-center me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                </svg>
                            </div>
                        </div>
                        <!-- <div class="icon-circle d-flex justify-content-start align-items-center">
                            <i class="bi bi-chat-square-text-fill"></i>
                        </div>
                        <div class="icon-circle d-flex justify-content-start align-items-center">
                            <i class="bi bi-chat-square-text-fill"></i>
                        </div>
                        <div class="icon-circle d-flex justify-content-start align-items-center">
                            <i class="bi bi-chat-square-text-fill"></i>
                        </div> -->
                    </div>
                    <div class="col-12 text-center">
                        &copy; 2025 Tekape Workspace. All rights reserved.
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!--Footer Ends-->
</body>

<!-- need to work and refine more -->
<!-- coloring harus diganti sesuai sama design -->
<!-- font harus diganti sesuai design -->
<!-- hover navbar belum done -->
<!-- font navbar besarkan sikit -->
<!-- kudu di bold sikit biar bisa di lihat pula -->
<!-- chao ni ma delete aja text dibawah konten page 2, biar bisa fit viewport desktop 100 -->
<!--- icon di card di perbesar sedikit lagi  -->
<!--- text di banner kecilkan sikit, font musti bold pula -->
<!--- navbar need to be bigger kah? logo pun must be bigger -->
<!-- apa pulak lah tu color scheme -->

</html>