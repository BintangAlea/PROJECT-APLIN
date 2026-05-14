# ✅ Bootstrap Integration - Status Update

## Perubahan yang Dilakukan

### 1. **Homepage** (`app/Views/Home/index.php`)
✅ **UPDATED ke Bootstrap 5.3.0**
- Navbar responsive dengan collapse toggle untuk mobile
- Grid layout menggunakan row/col-lg-6 untuk split content
- Bootstrap utilities: d-flex, gap, min-vh-100, etc.
- Custom `.btn-merish` dan `.text-merish` classes

### 2. **Login Page** (`app/Views/Auth/login.php`)
✅ **UPDATED ke Bootstrap**
- Form menggunakan `form-control`, `form-label`, `form-select`
- Card dengan shadow
- Alert components (`alert-danger`, `alert-success`)
- Responsive centered layout dengan `d-flex align-items-center justify-content-center`

### 3. **Register Page** (`app/Views/Auth/register.php`)
✅ **UPDATED ke Bootstrap**
- Sama dengan Login: form-control, form-label, form-select
- Support untuk role dropdown
- Bootstrap alerts dan spacing

### 4. **CSS** (`assets/css/style.css`)
⏳ **SEDANG DIRESTRUKTUR**
- ✅ Replaced top section dengan Bootstrap overrides only
- ⏳ Masih ada CSS lama untuk `.header`, `.nav`, `.hero` yang perlu dihapus
- Akan keep hanya: `.btn-merish`, `.text-merish`, `.hero-section`, `.navbar` overrides

---

## Bootstrap CDN yang Digunakan
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

---

## Struktur Bootstrap yang Dipakai

### Navbar
```html
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
  <div class="container-fluid px-4">
    <!-- Responsive toggle untuk mobile -->
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <!-- Collapse menu untuk mobile -->
    <div class="collapse navbar-collapse" id="navbarNav">
  </div>
</nav>
```

### Grid Layout
```html
<div class="row align-items-center min-vh-100 g-0">
  <div class="col-lg-6">Left content</div>
  <div class="col-lg-6">Right content</div>
</div>
```

### Forms
```html
<label class="form-label">Email</label>
<input type="email" class="form-control">
<select class="form-select">...</select>
```

### Cards
```html
<div class="card shadow-lg">
  <div class="card-body p-5">Content</div>
</div>
```

---

## Next Steps

1. **Finish style.css cleanup**
   - Remove old `.header`, `.nav`, `.hero`, `.btn-*` styles
   - Keep Bootstrap component overrides only
   
2. **Add dashboard pages** (Admin, Customer, Barista, etc.)
   - Use Bootstrap grid for responsive layouts
   - Use Bootstrap cards, tables, modals
   
3. **Test responsiveness**
   - Mobile: < 576px
   - Tablet: 576px - 992px
   - Desktop: > 992px

---

## Color Scheme (Merish Branding)
- Primary Pink: `#c97fa6`
- Dark Pink: `#b06b8f`
- Secondary Beige: `#f5f3f0`
- Text Dark: `#333333`
- Text Light: `#666666`

All applied via CSS variables and Bootstrap overrides. ✅
