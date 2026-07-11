<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking / Walk-In - Receptionist Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f8f2f3;
            --panel: #ffffff;
            --line: #e7dadc;
            --ink: #4e3e45;
            --muted: #8b7c82;
            --accent: #8b6472;
            --accent-dark: #744d5b;
            --success-color: #2e7d32;
            --danger-color: #c62828;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
        }

        .booking-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
        }

        .booking-card {
            background: var(--panel);
            border: 1px solid var(--line);
            box-shadow: 0 16px 36px rgba(78, 54, 61, 0.06);
            width: 100%;
            max-width: 620px;
            padding: 2.5rem;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 2.2rem;
            margin: 0 0 0.2rem;
            font-weight: 600;
        }

        .brand-sub {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            margin: 0;
        }

        .form-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6a5960;
            margin-bottom: 0.4rem;
        }

        .form-control,
        .form-select {
            border: 1px solid #e2d5d6;
            border-radius: 0;
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            color: var(--ink);
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(139, 100, 114, 0.15);
            outline: none;
        }

        .action-btn {
            background: var(--accent);
            color: #fff;
            border: 0;
            padding: 0.9rem 1.8rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            width: 100%;
            transition: background-color 0.2s;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.75rem;
            color: var(--muted);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .cancel-link:hover {
            color: var(--accent);
        }

        .badge-status {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-online { background: #eafaf1; color: var(--success-color); }
        .badge-offline { background: #ffebee; color: var(--danger-color); }

        .helper-text {
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }
    </style>
</head>
<body>

<div class="booking-shell">
    <div class="booking-card">
        <div class="brand-header">
            <h1 class="brand-title">Merish Portal</h1>
            <p class="brand-sub">New Booking / Walk-In Check-In</p>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger mb-4 rounded-0 small"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=receptionist&action=createWalkInBooking" id="walkInForm">
            <!-- Customer Name -->
            <div class="mb-3">
                <label class="form-label" for="guest_name">Nama Pelanggan (Customer Name)</label>
                <input type="text" class="form-control" id="guest_name" name="guest_name" placeholder="Contoh: Alina Customer" required>
            </div>

            <div class="row mb-3">
                <!-- Date -->
                <div class="col-md-6">
                    <label class="form-label" for="schedule_date">Tanggal</label>
                    <input type="date" class="form-control" id="schedule_date" name="schedule_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <!-- Time -->
                <div class="col-md-6">
                    <label class="form-label" for="schedule_time">Jam Reservasi</label>
                    <select class="form-select" id="schedule_time" name="schedule_time" required>
                        <?php
                        $start = strtotime('09:00');
                        $end = strtotime('20:30');
                        while ($start <= $end) {
                            $timeStr = date('H:i', $start);
                            $display = date('h:i A', $start);
                            echo "<option value='{$timeStr}'>{$display}</option>";
                            $start = strtotime('+30 minutes', $start);
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Treatment Category -->
            <div class="mb-3">
                <label class="form-label" for="category_select">Kategori Treatment</label>
                <select class="form-select" id="category_select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Hair">Hair</option>
                    <option value="Nails">Nails</option>
                    <option value="Lashes">Lashes</option>
                    <option value="Wax & Eyebrows">Wax & Eyebrows</option>
                </select>
            </div>

            <!-- Service Selection -->
            <div class="mb-3">
                <label class="form-label" for="service_select">Layanan / Treatment</label>
                <select class="form-select" id="service_select" name="service_id" required disabled>
                    <option value="">-- Pilih Layanan --</option>
                </select>
            </div>

            <!-- Beautician / Stylist -->
            <div class="mb-3">
                <label class="form-label" for="beautician_select">Beautician / Kapster</label>
                <select class="form-select" id="beautician_select" name="beautician_id" required disabled>
                    <option value="">-- Pilih Beautician --</option>
                </select>
            </div>

            <!-- Salon Seat -->
            <div class="mb-4">
                <label class="form-label" for="seat_select">Alokasi Kursi Salon</label>
                <select class="form-select" id="seat_select" name="seat_id" required disabled>
                    <option value="">-- Pilih Kursi --</option>
                </select>
            </div>

            <button type="submit" class="action-btn">Simpan Booking & Check-In</button>
            <a href="index.php?page=receptionist" class="cancel-link">Batal</a>
        </form>
    </div>
</div>

<script>
    // Load database collections from PHP
    const services = <?php echo json_encode($services); ?>;
    const beauticians = <?php echo json_encode($beauticians); ?>;
    const seats = <?php echo json_encode($seats); ?>;

    const categorySelect = document.getElementById('category_select');
    const serviceSelect = document.getElementById('service_select');
    const beauticianSelect = document.getElementById('beautician_select');
    const seatSelect = document.getElementById('seat_select');
    const dateInput = document.getElementById('schedule_date');
    const timeInput = document.getElementById('schedule_time');

    // Check time slots in the past
    function updateTimeSlots() {
        const selectedDate = dateInput.value;
        const todayStr = new Date().toLocaleDateString('en-CA'); // Matches Y-m-d local format
        const isToday = (selectedDate === todayStr);

        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        Array.from(timeInput.options).forEach(opt => {
            if (!isToday) {
                opt.disabled = false;
                return;
            }
            const [optHour, optMinute] = opt.value.split(':').map(Number);
            if (optHour < currentHour || (optHour === currentHour && optMinute <= currentMinute)) {
                opt.disabled = true;
            } else {
                opt.disabled = false;
            }
        });

        // If the currently selected option is now disabled, select the first enabled option
        if (timeInput.selectedOptions[0] && timeInput.selectedOptions[0].disabled) {
            const firstEnabled = Array.from(timeInput.options).find(opt => !opt.disabled);
            if (firstEnabled) {
                timeInput.value = firstEnabled.value;
            } else {
                timeInput.value = '';
            }
        }
    }

    // On category change: filter services and beauticians
    categorySelect.addEventListener('change', () => {
        const cat = categorySelect.value;
        serviceSelect.innerHTML = '<option value="">-- Pilih Layanan --</option>';
        beauticianSelect.innerHTML = '<option value="">-- Pilih Beautician --</option>';
        seatSelect.innerHTML = '<option value="">-- Pilih Kursi --</option>';
        serviceSelect.disabled = true;
        beauticianSelect.disabled = true;
        seatSelect.disabled = true;

        if (!cat) return;

        // 1. Filter services
        const filteredServices = services.filter(s => s.category === cat);
        filteredServices.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.service_id;
            opt.textContent = `${s.service_name} (IDR ${parseInt(s.base_tariff).toLocaleString('id-ID')} / ${s.est_duration} min)`;
            serviceSelect.appendChild(opt);
        });
        serviceSelect.disabled = false;

        // 2. Filter beauticians
        // Map category to employee specialization
        const specMap = {
            'Hair': 'Hair Stylist',
            'Nails': 'Nailist',
            'Lashes': 'Lash Technician',
            'Wax & Eyebrows': 'Wax & Threading Specialist'
        };
        const targetSpec = specMap[cat];
        const filteredBeauticians = beauticians.filter(b => b.specialization === targetSpec);
        filteredBeauticians.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.user_id;
            opt.dataset.workStatus = b.work_status;
            opt.textContent = `${b.name} (${b.work_status})`;
            beauticianSelect.appendChild(opt);
        });
        beauticianSelect.disabled = false;
        
        // 3. Populate seats
        seats.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.seat_id;
            opt.textContent = `${s.seat_id} - ${s.seat_name}`;
            seatSelect.appendChild(opt);
        });
        seatSelect.disabled = false;

        // Check availability immediately
        checkAvailability();
    });

    // Check availability dynamically
    async function checkAvailability() {
        const date = dateInput.value;
        const time = timeInput.value;
        const serviceId = serviceSelect.value;

        if (!date || !time) return;

        try {
            const res = await fetch(`index.php?page=receptionist&action=getAvailability&date=${date}&time=${time}&service_id=${serviceId}`);
            const data = await res.json();

            const occupiedSeats = data.occupied_seats || [];
            const busyBeauticians = data.busy_beauticians || [];

            // 1. Update seats options
            Array.from(seatSelect.options).forEach((opt, idx) => {
                if (idx === 0) return;
                const seatId = opt.value;
                const isOccupied = occupiedSeats.includes(seatId);
                opt.disabled = isOccupied;
                if (isOccupied) {
                    opt.textContent = `${seatId} - (Terpakai/Occupied ❌)`;
                    opt.style.color = 'var(--danger-color)';
                } else {
                    const originalSeat = seats.find(s => s.seat_id === seatId);
                    opt.textContent = `${seatId} - ${originalSeat.seat_name} (Tersedia/Available 🟢)`;
                    opt.style.color = 'var(--success-color)';
                }
            });

            // 2. Update beauticians options
            Array.from(beauticianSelect.options).forEach((opt, idx) => {
                if (idx === 0) return;
                const bId = parseInt(opt.value);
                const isBusy = busyBeauticians.includes(bId);
                const workStatus = opt.dataset.workStatus;
                
                opt.disabled = isBusy;
                
                const bName = beauticians.find(b => b.user_id === bId).name;
                if (isBusy) {
                    opt.textContent = `${bName} (Sedang Sibuk/Busy ❌)`;
                    opt.style.color = 'var(--danger-color)';
                } else {
                    if (workStatus === 'Offline') {
                        opt.textContent = `${bName} (Offline ⚪ - Tersedia)`;
                        opt.style.color = 'var(--muted)';
                    } else {
                        opt.textContent = `${bName} (Online 🟢 - Tersedia)`;
                        opt.style.color = 'var(--success-color)';
                    }
                }
            });

        } catch (err) {
            console.error("Gagal memeriksa ketersediaan:", err);
        }
    }

    // Trigger check on changes
    serviceSelect.addEventListener('change', checkAvailability);
    dateInput.addEventListener('change', () => {
        updateTimeSlots();
        checkAvailability();
    });
    timeInput.addEventListener('change', checkAvailability);

    // Initial setup
    updateTimeSlots();
    checkAvailability();
</script>

</body>
</html>
