# MERISH Program Structure & Flow

## 📁 Directory Structure

```
PROJECT-APLIN/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php ✓ (Login, Register, Logout)
│   │   ├── AdminController.php
│   │   ├── CustomerController.php
│   │   ├── ReceptionistController.php
│   │   ├── BeauticianController.php
│   │   ├── BaristaController.php
│   │   └── ... (other controllers)
│   │
│   ├── Core/
│   │   ├── Auth.php (Authentication helper)
│   │   ├── Database.php (DB Connection)
│   │   ├── Helper.php
│   │   └── Session.php (Session management)
│   │
│   ├── Models/
│   │   ├── UsersModel.php ✓ (User CRUD)
│   │   ├── ServicesModel.php
│   │   ├── MenusModel.php
│   │   ├── OrdersModel.php
│   │   ├── ReservationsModel.php
│   │   ├── BeauticiansModel.php
│   │   ├── TransactionsModel.php
│   │   └── ReviewsModel.php
│   │
│   └── Views/
│       ├── Auth/ ✓
│       │   ├── login.php (HTML Putihan)
│       │   └── register.php (HTML Putihan)
│       ├── Admin/
│       ├── Barista/
│       ├── Beautician/
│       ├── Customer/
│       ├── Home/
│       ├── Layout/
│       ├── QrOrder/
│       ├── Receptionist/
│       └── UnifiedBilling/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
│
├── uploads/
├── bootstrap.php ✓ (Autoload & Session)
├── index.php ✓ (Router)
├── db_merish_fix.sql ✓ (Database schema)
├── IMPLEMENTATION_LOG.md ✓ (Documentation)
└── CHECKLIST.md ✓ (Completion checklist)
```

---

## 🔄 Request Flow

### 1. Entry Point: index.php

```php
// index.php
$page = $_GET['page'] ?? 'home';  // page dari URL
$action = $_GET['action'] ?? 'index';  // action dari URL

// Instantiate controller based on page
$controller = match($page) {
    'login' => new AuthController(),
    'register' => new AuthController(),
    'admin' => new AdminController(),
    'customer' => new CustomerController(),
    // ... dll
};

// Route POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'login' && $action === 'login') {
        $controller->login();  // Handle login
    } else if ($page === 'register' && $action === 'register') {
        $controller->register();  // Handle register
    } else if ($page === 'login' && $action === 'logout') {
        $controller->logout();  // Handle logout
    }
}
```

---

## 🔑 Auth Flow Diagram

```
┌─────────────────────┐
│   User Homepage     │
│ (index.php?page=) │
└──────────┬──────────┘
           │
       ┌───┴────┬──────────┬──────────┐
       │        │          │          │
       ▼        ▼          ▼          ▼
    Register  Login      [Other]    Logout
       │        │                     │
       │        │                     │
   ┌───┴────┐  ┌────────┐         ┌────────────┐
   │ POST   │  │ POST   │         │ POST/GET   │
   │Register│  │ Login  │         │  Logout    │
   └───┬────┘  └────┬───┘         └─────┬──────┘
       │            │                   │
       │            │                   │
   AuthController   AuthController  AuthController
       │            │                   │
       │        ┌───┴────┐              │
       ▼        ▼        ▼              ▼
   Validate Validate Password   Destroy Session
   Register  Login   Verify
       │        │        │
       │    ┌───┴────┐   │
       │    ▼        ▼   │
       │  Success  Error │
       │    │        │   │
       └────┼────┬───┘   │
            │    │       │
        ┌───┴┐   │   ┌───┴────┐
        ▼    ▼   ▼   ▼        ▼
     Insert Create Session  Redirect
      User Profile   Set     Login
        │                     
    ┌───┴────────────────────┐
    ▼                        ▼
Set Success           Set Error
Message              Message
    │                    │
    └────────┬───────────┘
             │
          Redirect
        to Login Page
```

---

## 🗂️ MVC Architecture

### Model (Data Layer)
```php
// app/Models/UsersModel.php
class UsersModel {
    - register($email, $password, $fullName, $phone, $role)
    - login($email, $password)
    - findByEmail($email)
    - findById($id)
    - update($id, $data)
    - delete($id)
}
```

### Controller (Logic Layer)
```php
// app/Controllers/AuthController.php
class AuthController {
    - index() → Load view
    - login() → Handle login logic
    - register() → Handle register logic
    - logout() → Destroy session
}
```

### View (Presentation Layer)
```php
// app/Views/Auth/login.php
// app/Views/Auth/register.php
// HTML + inline CSS (putihan)
```

---

## 📝 Register Process

### 1. User Opens Register Page
```
GET /index.php?page=register
  ↓
AuthController->index()
  ↓
Render: app/Views/Auth/register.php
```

### 2. User Submits Register Form
```
POST /index.php?page=register&action=register
  ↓
AuthController->register()
  ↓
Validation:
  ├─ full_name required
  ├─ email required & unique
  ├─ role required
  ├─ password required & min 6 chars
  └─ password == confirm_password
  ↓
UsersModel->register($email, $password, $fullName, '', $role)
  ├─ Check email unique
  ├─ Hash password: password_hash($password, PASSWORD_BCRYPT)
  └─ INSERT into users table
  ↓
If role='beautician':
  └─ Create staff_profile
  ↓
Success: Set $_SESSION['success']
  ↓
Redirect: /index.php?page=login
```

### 3. Form Validation Errors
```
If validation fails:
  ├─ Email required → Error: "Email harus diisi"
  ├─ Password < 6 chars → Error: "Password minimal 6 karakter"
  ├─ Password mismatch → Error: "Password tidak sesuai"
  └─ Email exists → Error: "Email sudah terdaftar"
  ↓
Set $_SESSION['error']
  ↓
Redirect: /index.php?page=register
  ↓
Display error message & keep form data
```

---

## 🔐 Login Process

### 1. User Opens Login Page
```
GET /index.php?page=login
  ↓
AuthController->index()
  ↓
Render: app/Views/Auth/login.php
```

### 2. User Submits Login Form
```
POST /index.php?page=login&action=login
  ↓
AuthController->login()
  ↓
Validation:
  ├─ email required
  └─ password required
  ↓
UsersModel->login($email, $password)
  ├─ Find user by email
  ├─ Verify password: password_verify($password, $user['PASSWORD'])
  └─ Return user data or false
  ↓
If success:
  ├─ Set $_SESSION['user_id'] = $user['user_id']
  ├─ Set $_SESSION['user_login'] = $user['email']
  ├─ Set $_SESSION['role'] = $user['ROLE']
  └─ Set $_SESSION['full_name'] = $user['NAME']
  ↓
Redirect based on role:
  ├─ Admin → /index.php?page=admin
  ├─ Customer → /index.php?page=customer
  ├─ Barista → /index.php?page=barista
  ├─ Beautician → /index.php?page=beautician
  └─ Receptionist → /index.php?page=receptionist
  ↓
If failed:
  ├─ Set $_SESSION['error'] = "Email atau password salah"
  └─ Redirect: /index.php?page=login
```

---

## 🚪 Logout Process

```
POST /index.php?page=login&action=logout
  ↓
AuthController->logout()
  ↓
session_destroy()
  ↓
Set $_SESSION['success'] = "Logout berhasil"
  ↓
Redirect: /index.php?page=login
```

---

## 💾 Database Schema (Relevant Tables)

### users table
```sql
user_id INT PRIMARY KEY AUTO_INCREMENT
NAME VARCHAR(100)
email VARCHAR(100) UNIQUE
PASSWORD VARCHAR(255)
ROLE ENUM('Admin','Receptionist','Barista','Beautician','Customer')
loyalty_stage INT DEFAULT 1
total_spent DECIMAL(12,2) DEFAULT 0
reward_points INT DEFAULT 0
```

### staff_profiles table (untuk Beautician)
```sql
profile_id INT PRIMARY KEY AUTO_INCREMENT
user_id INT UNIQUE (FK to users)
specialization VARCHAR(50)
work_status ENUM('Online','Offline') DEFAULT 'Offline'
hire_date DATE
```

---

## 🔑 Session Variables

```php
// After successful login
$_SESSION['user_id']      // int: user ID
$_SESSION['user_login']   // string: email
$_SESSION['role']         // string: User role
$_SESSION['full_name']    // string: User name

// Flash messages (auto-unset after display)
$_SESSION['error']        // string: Error message
$_SESSION['success']      // string: Success message
```

---

## ✅ Validations

### Frontend (HTML5)
- `required` attribute on all inputs
- `type="email"` for email validation
- `type="password"` for password fields
- `type="text"` for name fields
- Select dropdown for role selection

### Backend (PHP)
1. **Register**
   - Check all fields filled
   - Check password >= 6 chars
   - Check password == confirm_password
   - Check email is unique (database)
   - Hash password before storing

2. **Login**
   - Check email & password filled
   - Check email exists (database)
   - Verify password hash
   - Return appropriate error messages

---

## 🎨 View Components

### All Views Use:
- HTML5 semantic markup
- Inline CSS (no external CSS framework)
- Responsive design (100% width, max-width constraint)
- Alert boxes for messages
- Form validation feedback

### Styling System:
- Color: Grayscale (#333, #666, #999, #ddd, white)
- Spacing: 20px standard margin/padding
- Border: 1px solid #ddd, rounded 4px
- Shadow: 0 2px 10px rgba(0,0,0,0.1)
- Font: Arial, sans-serif

---

## 📋 Next Implementation Steps

### Phase 2: Customer Module
- [ ] Customer dashboard
- [ ] Loyalty stage display
- [ ] Appointment booking
- [ ] QR F&B ordering
- [ ] Order history
- [ ] Profile management

### Phase 3: Barista Module
- [ ] Live order tracking
- [ ] Kanban board (New → In Progress → Done)
- [ ] Out of stock toggle

### Phase 4: Receptionist Module
- [ ] Check-in with QR scan
- [ ] Reservation management
- [ ] Order viewing

### Phase 5: Beautician Module
- [ ] Today's schedule
- [ ] Upcoming schedule
- [ ] Service completion

### Phase 6: Admin Module
- [ ] Dashboard with analytics
- [ ] User management
- [ ] Service management
- [ ] Menu management
- [ ] Staff management
- [ ] Reports

---

## 🚀 How to Use

### 1. Setup
```bash
# Ensure database is imported
mysql -u root < db_merish_fix.sql

# Ensure autoload is generated
composer dump-autoload
```

### 2. Navigate
```
Home: http://localhost/SIB/PROJECT-APLIN/
Login: http://localhost/SIB/PROJECT-APLIN/index.php?page=login
Register: http://localhost/SIB/PROJECT-APLIN/index.php?page=register
```

### 3. Test
- Register new account with role selection
- Login with credentials
- Check redirect based on role
- Logout

---

## 📚 Reference Documentation

- `IMPLEMENTATION_LOG.md` - Detailed implementation notes
- `CHECKLIST.md` - Completion status
- `PROGRAM_STRUCTURE.md` - This file

