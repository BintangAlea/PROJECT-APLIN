# 🎨 UI/UX Setup - Merish Homepage

## ✅ Struktur yang Sudah Dibuat

### 1. **Asset Folders** 📁
```
projectaplin/
├── assets/
│   ├── css/
│   │   └── style.css (Main stylesheet)
│   ├── js/
│   │   └── script.js (Main JavaScript)
│   └── images/
│       └── (Hero background & assets)
```

### 2. **Layout Files** 📄
- `app/Views/Layout/base.php` - Master layout template
- `app/Views/Home/index.php` - Homepage (sudah updated)

### 3. **CSS Features** 🎨
- ✓ Responsive design (mobile, tablet, desktop)
- ✓ Color scheme sesuai design: Primary color #c97fa6 (pink)
- ✓ Header dengan navigation & buttons
- ✓ Hero section dengan gradient overlay
- ✓ Footer section
- ✓ Toast notifications
- ✓ Smooth animations & transitions

### 4. **Helper Functions** 🔧
Tambahan di `Helper.php`:
- `get_dashboard_route()` - Route berdasarkan role
- `has_role()` - Check user role
- `format_currency()` - Format IDR
- `format_date()` - Format tanggal Indonesia
- `get_status_badge()` - Status visual badges
- `sanitize()` - Input sanitization
- `redirect()` - Safe redirects
- `set_message()` / `get_message()` - Session messages

---

## 🖼️ Setup Background Image

### Opsi 1: Menggunakan Image File
1. **Siapkan background image** (recommended: 1920x1080, JPG)
   - Nama: `hero-bg.jpg`
   - Lokasi: `assets/images/hero-bg.jpg`

2. **Letakkan file di:**
   ```
   projectaplin/assets/images/hero-bg.jpg
   ```

3. **Atau gunakan path yang berbeda**, edit di `style.css`:
   ```css
   .hero {
       background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.2)),
                   url('YOUR_IMAGE_PATH') center/cover no-repeat;
   }
   ```

### Opsi 2: Menggunakan Warna Solid (Sementara)
Jika tidak ada image, hero section akan menggunakan background color:
```css
background-color: #e8d7d7; /* Beige-ish color dari screenshot */
```

---

## 🎯 Color Scheme

```css
--primary-color: #c97fa6;      /* Pink - Main accent */
--primary-dark: #b06b8f;       /* Darker pink - Hover states */
--secondary-color: #f5f3f0;    /* Light beige - Backgrounds */
--text-dark: #333333;          /* Dark gray - Main text */
--text-light: #666666;         /* Medium gray - Secondary text */
--border-light: #e0e0e0;       /* Light gray - Borders */
--white: #ffffff;              /* White */
```

---

## 📱 Responsive Breakpoints

- **Desktop:** 1024px+
- **Tablet:** 768px - 1023px
- **Mobile:** 480px - 767px
- **Small Mobile:** < 480px

---

## 🔗 Navigation Routes

Routing sudah terintegrasi dengan `index.php`. Berikut routes utama:

### Public Routes
- `/` → Home
- `/login` → Login page
- `/register` → Register page

### Customer Routes (Protected)
- `/customer` → Customer dashboard
- `/customer/appointment` → Book appointment

### Admin Routes (Protected)
- `/admin` → Admin dashboard
- `/admin/manage-users` → Manage users

---

## 📝 Cara Menggunakan di Views Lain

### Menggunakan Base Layout (Recommended)
```php
<?php
// In your controller
$page_title = 'My Page Title';
$additional_css = ['path/to/extra.css'];
$content = 'Page content here';

require __DIR__ . '/../Layout/base.php';
?>
```

### Manual Header/Footer
Jika tidak mau menggunakan base layout:

```php
<?php include __DIR__ . '/../Layout/_header.php'; ?>

<!-- Your content here -->

<?php include __DIR__ . '/../Layout/_footer.php'; ?>
```

---

## 🎨 CSS Classes Tersedia

### Buttons
```html
<!-- Primary button -->
<a href="#" class="btn btn-primary">Click me</a>

<!-- Login button (outline) -->
<a href="#" class="btn btn-login">Login</a>

<!-- Large primary button with arrow -->
<a href="#" class="btn btn-primary btn-primary-lg">
    Book Appointment
    <span class="arrow">→</span>
</a>
```

### Typography
```html
<!-- Large heading -->
<h1>Elevate Your Style,</h1>

<!-- Highlight text (italic, colored) -->
<span class="highlight">Savor the Moment.</span>
```

### Utilities
```html
<div class="container">Content</div>
<div class="hero">Hero section</div>
```

---

## 🎬 JavaScript Functions

Available di `script.js`:

```javascript
// Toast notification
showToast('Hello!', 'success');  // Types: success, error, info, warning

// Check authentication
isAuthenticated()

// Redirect to login
redirectToLogin()

// Format currency
formatCurrency(100000)  // Returns: Rp 100.000,00
```

---

## 🚀 Next Steps

### 1. **Tambahkan Background Image**
   - Download/siapkan image
   - Letakkan di `assets/images/hero-bg.jpg`

### 2. **Setup Auth Views** (Login/Register)
   - Update `app/Views/Auth/login.php`
   - Update `app/Views/Auth/register.php`
   - Gunakan styling dari `style.css`

### 3. **Setup Other Page Views**
   - Services page
   - Cafe menu page
   - Stylists page
   - Customer dashboard

### 4. **Add More Sections (Future)**
   - Services showcase
   - Cafe menu showcase
   - Team/Stylists
   - Reviews/Testimonials
   - Contact section

---

## 💡 Tips & Tricks

### Customize Colors
Edit di `:root` dalam `style.css`:
```css
:root {
    --primary-color: #your-color;
    --primary-dark: #darker-version;
    /* etc */
}
```

### Add Custom Fonts
Di `style.css` tambahkan:
```css
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}
```

### Add Animations
Use class `fade-in` untuk auto-animate on scroll:
```html
<div class="fade-in">
    This will animate when scrolled into view
</div>
```

### Mobile Testing
Test responsive design:
```html
<!-- Firefox DevTools: Press F12 → Responsive Design Mode (Ctrl+Shift+M) -->
<!-- Chrome DevTools: Press F12 → Toggle device toolbar (Ctrl+Shift+M) -->
```

---

## 📱 File Structure

```
projectaplin/
├── assets/
│   ├── css/
│   │   └── style.css .................. Main stylesheet
│   ├── images/
│   │   └── hero-bg.jpg ................ (Add your background image)
│   └── js/
│       └── script.js .................. Interactive scripts
├── app/
│   ├── Views/
│   │   ├── Layout/
│   │   │   └── base.php ............... Master layout
│   │   ├── Home/
│   │   │   └── index.php .............. Homepage
│   │   ├── Auth/
│   │   │   ├── login.php .............. (To update)
│   │   │   └── register.php ........... (To update)
│   │   └── ... (Other pages)
│   ├── Controllers/
│   │   └── Home.php ................... Homepage controller
│   ├── Core/
│   │   ├── Helper.php ................. Updated with new functions
│   │   ├── Database.php
│   │   ├── Auth.php
│   │   └── Session.php
│   └── Models/
├── bootstrap.php ...................... Entry point
└── index.php .......................... Front controller
```

---

## ✨ Current Design Features

✅ **Responsive Navigation**
- Logo on left
- Menu items in center
- Auth buttons on right
- Mobile-friendly

✅ **Hero Section**
- Full viewport height
- Gradient overlay on background
- Large heading with accent text
- Description paragraph
- Call-to-action button with arrow

✅ **Modern Styling**
- Smooth transitions
- Hover effects
- Color scheme consistency
- Professional typography

✅ **Performance Optimized**
- Minimal CSS
- No external dependencies
- Fast loading

---

## 🐛 Troubleshooting

### Background image tidak muncul?
1. Check path di `style.css`
2. Ensure file exists at `assets/images/hero-bg.jpg`
3. Try using full path: `/SIB/PROJECT-APLIN/assets/images/hero-bg.jpg`

### Styling tidak apply?
1. Clear browser cache (Ctrl+Shift+Delete)
2. Check if CSS file is linked correctly
3. Check browser console for errors (F12)

### Navigation links tidak jalan?
1. Check `url()` function in Helper.php
2. Verify routes in `index.php`
3. Check routing pattern

---

## 📞 Support

Untuk pertanyaan atau perbaikan:
1. Check file path consistency
2. Test in different browsers
3. Validate HTML/CSS online
4. Check console errors (F12)

Happy coding! 🚀
