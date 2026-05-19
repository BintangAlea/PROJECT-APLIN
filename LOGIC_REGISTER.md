# LOGIC REGISTER - Dokumentasi Teknis

## 🎯 Overview

Register adalah proses pembuatan akun baru di sistem MERISH. User memilih role (Customer, Barista, atau Beautician) saat mendaftar.

---

## 📋 Requirement dari PDF

Dari analisis PDF MERISH:
- Customer bisa register sebagai member baru
- Barista bisa register untuk mulai bekerja
- Beautician bisa register untuk profile profesional
- System track loyalty stage dan spending untuk customer
- Setiap role memiliki privilege berbeda

---

## 🔧 Implementasi Teknis

### Step 1: User Membuka Form Register
```
URL: http://localhost/SIB/PROJECT-APLIN/index.php?page=register

Flow:
  GET /index.php?page=register
    ↓
  Router: AuthController->index()
    ↓
  Render: app/Views/Auth/register.php
    ↓
  Display HTML form dengan fields:
  - Nama Lengkap (text, required)
  - Email (email, required)
  - Role (select: customer/barista/beautician, required)
  - Password (password, required, min 6 chars)
  - Konfirmasi Password (password, required)
```

### Step 2: User Submit Form

```
POST /index.php?page=register&action=register

Request Payload:
{
  'full_name': 'Siska Ramadhani',
  'email': 'siska@example.com',
  'role': 'customer',
  'password': 'password123',
  'confirm_password': 'password123'
}
```

### Step 3: AuthController->register() Execution

```php
Function Pseudocode:
──────────────────────────────────────────────────────────

FUNCTION register() {
  
  // 1. EXTRACT INPUT
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';
  $fullName = $_POST['full_name'] ?? '';
  $role = $_POST['role'] ?? 'customer';
  
  
  // 2. VALIDASI TAHAP 1 - REQUIRED FIELDS
  IF NOT $email OR NOT $password OR NOT $fullName THEN
    SET $_SESSION['error'] = 'Email, password, dan nama lengkap harus diisi'
    REDIRECT to /index.php?page=register
    EXIT
  END IF
  
  
  // 3. VALIDASI TAHAP 2 - PASSWORD MATCH
  IF $password NOT EQUAL $confirmPassword THEN
    SET $_SESSION['error'] = 'Password tidak sesuai'
    REDIRECT to /index.php?page=register
    EXIT
  END IF
  
  
  // 4. VALIDASI TAHAP 3 - PASSWORD LENGTH
  IF LENGTH($password) < 6 THEN
    SET $_SESSION['error'] = 'Password minimal 6 karakter'
    REDIRECT to /index.php?page=register
    EXIT
  END IF
  
  
  // 5. CALL MODEL - REGISTER
  $result = $this->usersModel->register(
    $email,
    $password,
    $fullName,
    '',  // phone (kosong)
    $role
  )
  
  
  // 6. MODEL EXECUTION (UsersModel->register)
  INSIDE register():
    
    // 6a. Check email unique
    IF findByEmail($email) EXISTS THEN
      RETURN false
    END IF
    
    // 6b. Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT)
    
    // 6c. Prepare SQL
    SQL = "INSERT INTO users 
           (NAME, email, PASSWORD, ROLE, loyalty_stage, total_spent, reward_points)
           VALUES (:name, :email, :password, :role, :loyalty_stage, :total_spent, :reward_points)"
    
    // 6d. Execute with parameters
    EXECUTE prepared_statement WITH:
      ':name' = $fullName
      ':email' = $email
      ':password' = $hashedPassword
      ':role' = $role
      ':loyalty_stage' = 1
      ':total_spent' = 0
      ':reward_points' = 0
    
    // 6e. Return result
    RETURN true/false (execute result)
  
  END FUNCTION
  
  
  // 7. CHECK RESULT
  IF NOT $result THEN
    SET $_SESSION['error'] = 'Email sudah terdaftar atau terjadi kesalahan'
    REDIRECT to /index.php?page=register
    EXIT
  END IF
  
  
  // 8. CREATE STAFF PROFILE (if beautician)
  IF $role == 'beautician' THEN
    
    // 8a. Find newly created user
    $user = $this->usersModel->findByEmail($email)
    
    // 8b. If found, create profile
    IF $user EXISTS THEN
      $db = Database::getConnection()
      $stmt = $db->prepare(
        "INSERT INTO staff_profiles 
         (user_id, specialization, work_status)
         VALUES (:user_id, :specialization, :status)"
      )
      
      EXECUTE $stmt WITH:
        ':user_id' = $user['user_id']
        ':specialization' = 'Hair Stylist'
        ':status' = 'Offline'
    
    END IF
  
  END IF
  
  
  // 9. SET SUCCESS MESSAGE
  SET $_SESSION['success'] = 'Registrasi berhasil! Silahkan login dengan akun anda'
  
  
  // 10. REDIRECT TO LOGIN
  REDIRECT to /index.php?page=login
  EXIT

}
```

---

## 🗄️ Database Operations

### Pseudocode di UsersModel->register()

```sql
-- 1. Check email exists
SELECT 1 FROM users WHERE email = :email

-- 2. If not exists, insert
INSERT INTO users 
(NAME, email, PASSWORD, ROLE, loyalty_stage, total_spent, reward_points)
VALUES 
(:name, :email, :hashed_password, :role, 1, 0, 0)

-- 3. If beautician, also insert
INSERT INTO staff_profiles 
(user_id, specialization, work_status)
VALUES 
(:new_user_id, 'Hair Stylist', 'Offline')
```

### Database State After Register

```sql
-- users table
INSERT INTO users VALUES (
  user_id: 1,
  NAME: 'Siska Ramadhani',
  email: 'siska@example.com',
  PASSWORD: '$2y$10$...(hashed)...',
  ROLE: 'Customer',
  loyalty_stage: 1,
  total_spent: 0.00,
  reward_points: 0
)

-- If beautician role, also:
-- staff_profiles table
INSERT INTO staff_profiles VALUES (
  profile_id: 1,
  user_id: 1,
  specialization: 'Hair Stylist',
  work_status: 'Offline',
  hire_date: NULL
)
```

---

## ✅ Validasi Checklist

### Input Validation
```
┌─────────────────────────────────┬──────────┬─────────────────────────────────┐
│ Field                           │ Required │ Rules                           │
├─────────────────────────────────┼──────────┼─────────────────────────────────┤
│ Full Name                       │ Yes      │ Non-empty string                │
│ Email                           │ Yes      │ Valid email format + unique     │
│ Role                            │ Yes      │ customer/barista/beautician     │
│ Password                        │ Yes      │ Min 6 chars                     │
│ Confirm Password                │ Yes      │ Must match password             │
└─────────────────────────────────┴──────────┴─────────────────────────────────┘
```

### Database Validation
```
- Email UNIQUE: Tidak boleh ada 2 user dengan email sama
- ROLE ENUM: Hanya allow customer, barista, beautician, admin, receptionist
- NAME NOT NULL: Nama tidak boleh kosong
- PASSWORD NOT NULL: Password tidak boleh kosong
```

### Security Validation
```
- Password: BCRYPT hashing (tidak boleh plaintext)
- SQL Injection: Prepared statements (tidak vulnerable)
- Sensitive Data: Password tidak ditampilkan di response
```

---

## 🔄 Error Handling

### Validasi Input Gagal

```
1. Empty fields
   → Error message: "Email, password, dan nama lengkap harus diisi"
   → Stay on register page
   → Form data preserved (kecuali password)

2. Password < 6 chars
   → Error message: "Password minimal 6 karakter"
   → Stay on register page
   → Form data preserved

3. Password mismatch
   → Error message: "Password tidak sesuai"
   → Stay on register page
   → Form data preserved

4. Email already exists
   → Error message: "Email sudah terdaftar atau terjadi kesalahan"
   → Stay on register page
   → Form data preserved (kecuali password)
```

### Database Error

```
1. Database connection error
   → Handled by Database::getConnection()
   → Error message: Generic message
   → Application dies gracefully

2. Prepared statement error
   → Caught in try-catch (jika ada)
   → $result returns false
   → Error message: "Email sudah terdaftar atau terjadi kesalahan"
```

---

## 📊 Success Flow

### Happy Path

```
User Input Valid
       ↓
Password Hash
       ↓
Insert to database
       ↓
Email Unique
       ↓
Create staff profile (if beautician)
       ↓
Set success message
       ↓
Redirect to login
       ↓
User sees: "Registrasi berhasil! Silahkan login dengan akun anda"
```

---

## 📝 Form Preservation After Error

```javascript
After validation fails:
- $_POST['full_name'] → value attribute pada input
- $_POST['email'] → value attribute pada input
- $_POST['role'] → selected attribute pada option

Password fields: TIDAK dipreserve (untuk security)
- $_POST['password'] → NOT displayed
- $_POST['confirm_password'] → NOT displayed
```

---

## 🔐 Password Hashing

```php
Original Password:    "password123"
                           ↓
password_hash($password, PASSWORD_BCRYPT)
                           ↓
Hashed Password:  "$2y$10$A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6"

Verification:
password_verify("password123", "$2y$10$...") → true
password_verify("password456", "$2y$10$...") → false
```

---

## 🎯 Role-Specific Behavior After Register

### Customer Role
```
- Set loyalty_stage = 1
- Set total_spent = 0
- Set reward_points = 0
- No staff profile created
- Dapat akses: dashboard, booking, order menu
```

### Barista Role
```
- Set ROLE = 'Barista'
- No special initialization
- Can login dan akses barista dashboard
- Manage orders, update status, toggle stock
```

### Beautician Role
```
- Set ROLE = 'Beautician'
- Create staff_profile dengan specialization 'Hair Stylist'
- Set work_status = 'Offline' (default)
- Can view schedule, accept bookings
```

---

## 📱 Frontend Behavior

### Register Form Display

```html
<form method="POST" action="index.php?page=register&action=register">
  
  <!-- Nama Lengkap -->
  <input type="text" name="full_name" required>
  
  <!-- Email -->
  <input type="email" name="email" required>
  
  <!-- Role Dropdown -->
  <select name="role" required>
    <option value="customer">Customer</option>
    <option value="barista">Barista</option>
    <option value="beautician">Beautician</option>
  </select>
  
  <!-- Password -->
  <input type="password" name="password" required>
  
  <!-- Confirm Password -->
  <input type="password" name="confirm_password" required>
  
  <!-- Submit Button -->
  <button type="submit">Daftar</button>
</form>
```

### Error Display

```php
<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger">
    <?php echo $_SESSION['error']; ?>
  </div>
<?php endif; ?>
```

### Success Display (at login page after redirect)

```php
<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <?php echo $_SESSION['success']; ?>
  </div>
<?php endif; ?>
```

---

## 🧪 Testing Scenarios

### Test Case 1: Successful Registration (Customer)
```
Input:
- Full Name: John Doe
- Email: john@example.com
- Role: Customer
- Password: secure123
- Confirm Password: secure123

Expected Result:
✓ User created in database
✓ Loyalty stage = 1
✓ Redirect to login page
✓ Success message displayed
```

### Test Case 2: Successful Registration (Beautician)
```
Input:
- Full Name: Sarah Beauty
- Email: sarah@example.com
- Role: Beautician
- Password: beauty123
- Confirm Password: beauty123

Expected Result:
✓ User created in database
✓ Staff profile created with specialization
✓ Work status = Offline
✓ Redirect to login page
```

### Test Case 3: Password Too Short
```
Input:
- Full Name: Jane
- Email: jane@example.com
- Role: Customer
- Password: 12345
- Confirm Password: 12345

Expected Result:
✗ Error: "Password minimal 6 karakter"
✓ Stay on register page
✓ Form data preserved (except password)
```

### Test Case 4: Password Mismatch
```
Input:
- Full Name: Bob
- Email: bob@example.com
- Role: Barista
- Password: password123
- Confirm Password: password456

Expected Result:
✗ Error: "Password tidak sesuai"
✓ Stay on register page
✓ Form data preserved
```

### Test Case 5: Email Already Exists
```
Input:
- Full Name: New User
- Email: john@example.com (sudah terdaftar)
- Role: Customer
- Password: newpass123
- Confirm Password: newpass123

Expected Result:
✗ Error: "Email sudah terdaftar atau terjadi kesalahan"
✓ Stay on register page
✓ Form data preserved
```

### Test Case 6: Missing Required Field
```
Input:
- Full Name: [empty]
- Email: user@example.com
- Role: Customer
- Password: password123
- Confirm Password: password123

Expected Result:
✗ Error: "Email, password, dan nama lengkap harus diisi"
✓ Stay on register page
✓ Form data preserved
```

---

## 🎓 Code Quality

### Standards Implemented
- ✓ Input validation (frontend & backend)
- ✓ Prepared statements (prevent SQL injection)
- ✓ Password hashing (BCRYPT)
- ✓ Session management (secure)
- ✓ Error handling (user-friendly)
- ✓ MVC pattern (separation of concerns)
- ✓ Code readability (clear variable names)
- ✓ Comments (where needed)

### Security Measures
- ✓ Password never logged or displayed
- ✓ Email verified unique in database
- ✓ BCRYPT hashing (industry standard)
- ✓ Prepared statements (no SQL injection)
- ✓ Session regeneration on login (should add)
- ✓ HTTPS recommended (implementation dependent)

---

## 📚 Reference Files

- `app/Controllers/AuthController.php` - register() method
- `app/Models/UsersModel.php` - register() method  
- `app/Views/Auth/register.php` - HTML form
- `db_merish_fix.sql` - Database schema

