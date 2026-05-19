# MERISH - Implementation Log

**Tanggal**: 19 May 2026  
**Status**: Auth System Completed

---

## 1. Analisis Referensi

### Referensi Tutor M7:
- Pattern: POST request dengan `isset($_POST['btnName'])`
- Session management: `$_SESSION` untuk menyimpan user data
- Database: PDO prepared statements
- Password: `password_hash()` dan `password_verify()`
- Redirect flow untuk control alur

### PDF MERISH Analysis:
**Customer Flow:**
- Register/Login → Browse Services/Cafe → Booking/QR Order → Payment
- 3 Loyalty Stage: Regular → Loyal → VIP

**Barista Flow:**
- Live Order Tracking → In Progress → Fulfillment
- Out of Stock management

**Receptionist Flow:**
- Check-in QR → Manage schedules

**Beautician Flow:**
- View today/upcoming schedule

**Admin Flow:**
- Dashboard management

---

## 2. Database Schema

Tabel utama yang sudah dibuat:
- `users` - User dengan roles (Admin, Receptionist, Barista, Beautician, Customer)
- `services` - Layanan salon (Hair, Nails, Lashes, Wax & Eyebrows)
- `menus` - Menu cafe
- `seats` - Kursi/meja dengan QR code
- `reservations` - Booking appointments
- `orders` - Pesanan F&B
- `transactions` - Transaksi pembayaran
- `staff_profiles` - Profile staff dengan specialization
- `reward_catalog` - Loyalty rewards
- Dan tabel support lainnya

---

## 3. Authentication System

### 3.1 Register Logic
**File**: `app/Controllers/AuthController.php`

```php
1. Validasi input (full_name, email, password, confirm_password, role)
2. Cek password minimal 6 karakter
3. Cek password dan confirm_password match
4. Cek email belum terdaftar
5. Hash password dengan PASSWORD_BCRYPT
6. Insert ke table users dengan:
   - NAME: full_name
   - email: email
   - PASSWORD: hashed password
   - ROLE: role (customer/barista/beautician)
   - loyalty_stage: 1 (default untuk customer)
   - total_spent: 0
   - reward_points: 0
7. Jika role = 'beautician':
   - Create staff_profile dengan specialization 'Hair Stylist'
8. Redirect ke login dengan success message
```

### 3.2 Login Logic
**File**: `app/Controllers/AuthController.php`

```php
1. Validasi input (email, password)
2. Cari user by email
3. Verify password dengan password_verify()
4. Set session:
   - $_SESSION['user_id'] = user_id
   - $_SESSION['user_login'] = email
   - $_SESSION['role'] = ROLE
   - $_SESSION['full_name'] = NAME
5. Redirect berdasarkan role:
   - Admin → /index.php?page=admin
   - Receptionist → /index.php?page=receptionist
   - Barista → /index.php?page=barista
   - Beautician → /index.php?page=beautician
   - Customer → /index.php?page=customer
```

### 3.3 Logout Logic
**File**: `app/Controllers/AuthController.php`

```php
1. Destroy session dengan session_destroy()
2. Redirect ke login page
```

---

## 4. View (HTML Sederhana - Putihan)

### 4.1 Login View
**File**: `app/Views/Auth/login.php`
- Styling: Inline CSS (minimalis, putihan)
- Form: Email + Password
- Error/Success alert message
- Link: Register, Home

### 4.2 Register View
**File**: `app/Views/Auth/register.php`
- Styling: Inline CSS (minimalis, putihan)
- Form: Full Name, Email, Role (dropdown), Password, Confirm Password
- Error/Success alert message
- Link: Login, Home

---

## 5. Flow di index.php

```
GET /index.php?page=login
  ↓
→ AuthController->index() → Load login.php

POST /index.php?page=login&action=login
  ↓
→ AuthController->login() → Validate & Set Session → Redirect per role

GET /index.php?page=register
  ↓
→ AuthController->index() → Load register.php

POST /index.php?page=register&action=register
  ↓
→ AuthController->register() → Validate & Insert → Redirect login

POST /index.php?page=login&action=logout
  ↓
→ AuthController->logout() → Destroy Session → Redirect login
```

---

## 6. Model yang Digunakan

### UsersModel
- `register($email, $password, $fullName, $phone, $role)` - Insert user
- `login($email, $password)` - Verify login
- `findByEmail($email)` - Cari user by email
- `findById($id)` - Cari user by id
- `findByRole($role)` - Cari users by role

---

## 7. Validasi yang Diimplementasikan

### Register:
- [ ] Email harus ada
- [ ] Password harus ada
- [ ] Full name harus ada
- [ ] Password minimal 6 karakter
- [ ] Password dan confirm_password harus sama
- [ ] Email belum terdaftar di sistem

### Login:
- [ ] Email harus ada
- [ ] Password harus ada
- [ ] Email harus registered
- [ ] Password harus match

---

## 8. Struktur File

```
app/
├── Controllers/
│   └── AuthController.php ✓ (Lengkap)
├── Core/
│   ├── Auth.php (Helper)
│   ├── Database.php (Connection)
│   └── Session.php (Session management)
├── Models/
│   └── UsersModel.php ✓ (Lengkap)
└── Views/
    └── Auth/
        ├── login.php ✓ (HTML Putihan)
        └── register.php ✓ (HTML Putihan)

bootstrap.php (Autoload & Session start)
index.php (Router)
```

---

## 9. Cara Testing

### Test Register:
```
1. Buka: http://localhost/SIB/PROJECT-APLIN/index.php?page=register
2. Isi form:
   - Nama Lengkap: Test User
   - Email: test@example.com
   - Role: Customer
   - Password: password123
   - Confirm Password: password123
3. Klik Daftar
4. Harusnya redirect ke login dengan success message
```

### Test Login:
```
1. Buka: http://localhost/SIB/PROJECT-APLIN/index.php?page=login
2. Isi form:
   - Email: test@example.com
   - Password: password123
3. Klik Masuk
4. Harusnya redirect ke customer dashboard (jika role = customer)
```

### Test Error Case:
```
1. Password tidak cocok → Error message
2. Email belum terdaftar → Error message saat login
3. Password terlalu pendek → Error message saat register
4. Email sudah terdaftar → Error message saat register
```

---

## 10. Next Steps

Setelah auth system selesai, buat:

1. **Customer Module**
   - Dashboard (loyalty stage, rewards)
   - Booking appointment (dengan promo bundling)
   - QR Ordering (F&B)
   - Order history
   - Profile management

2. **Barista Module**
   - Live order dashboard (Kanban view)
   - Order status update (New → In Progress → Done)
   - Out of stock toggle

3. **Receptionist Module**
   - Check-in (scan QR)
   - Manage reservations
   - View orders

4. **Beautician Module**
   - Today schedule
   - Upcoming schedule
   - Service completion

5. **Admin Module**
   - User management
   - Service management
   - Menu management
   - Staff management
   - Reports

---

## 11. Notes

- Database sudah normalized dengan foreign keys
- Password tersimpan dengan BCRYPT hashing (aman)
- Session management menggunakan PHP native session
- Views menggunakan HTML murni tanpa framework
- Styling: Inline CSS untuk kemudahan (bisa dipindah ke CSS file nanti)

