# Deep Link Guide untuk IDS Native App

## 📖 Overview

Deep link memungkinkan magic link dari email membuka aplikasi native Electron secara langsung, bukan di browser.

**URL Format:**
- Browser: `http://localhost:8000/auth/verify?token=xxx`  
- Deep Link: `idsapp://auth/verify?token=xxx`

---

## ✅ Implementasi yang Sudah Ada

### 1. Konfigurasi (.env)
```bash
NATIVEPHP_DEEPLINK_SCHEME=idsapp
NATIVEPHP_APP_ID=com.egsis.idsapp
NATIVEPHP_APP_AUTHOR="EGSIS"
```

### 2. NativeAppServiceProvider
Handler untuk menangkap deep link URL dan navigate ke halaman yang sesuai.

### 3. Email Template
Email sekarang menyertakan 2 tombol:
- **Masuk ke Aplikasi Native** → `idsapp://` (deeplink)
- **Buka di Browser** → `http://localhost:8000`

### 4. Command Helper
```bash
# Generate deeplink untuk native app
php artisan auth:magic-link email@example.com --native

# Generate regular link untuk browser  
php artisan auth:magic-link email@example.com
```

---

## 🚀 Cara Kerja di Development Mode (`composer native:dev`)

### ⚠️ Limitasi Development Mode

**Di mode development**, URL scheme `idsapp://` **BELUM otomatis terdaftar di OS**.

Ini berarti:
- ✅ Code deeplink sudah siap
- ✅ Handler sudah ada di NativeAppServiceProvider
- ❌ OS belum tahu aplikasi mana yang handle `idsapp://`
- ❌ Klik link di email akan gagal buka aplikasi

### ✅ Solusi untuk Development

#### Opsi 1: Gunakan Tombol "Buka di Browser" (Paling Mudah)

Saat development, gunakan tombol **"Buka di Browser"** di email:
1. Email masuk dengan 2 tombol
2. Klik **"Buka di Browser"**
3. Browser buka `http://localhost:8000/auth/verify?token=xxx`
4. Login berhasil ✅

#### Opsi 2: Manual Copy-Paste Token

```bash
# 1. Generate magic link
php artisan auth:magic-link me@ojiepermana.com

# 2. Copy token dari output
# Token: YTnTyxwokNoUKC5iheCHXKzUq4nRKa1jU6Nh0zs3...

# 3. Buka di browser
http://localhost:8000/auth/verify?token=PASTE_TOKEN_HERE
```

#### Opsi 3: Test Deep Link via Terminal (Advanced)

**macOS:**
```bash
# Generate deeplink
php artisan auth:magic-link me@ojiepermana.com --native

# Output: idsapp://auth/verify?token=xxx

# Test buka deeplink (jika app sudah running)
open "idsapp://auth/verify?token=xxx"
```

**⚠️ Catatan:** Command `open` di macOS akan gagal jika URL scheme belum registered. Ini normal di development mode.

---

## 🏗️ Deep Link di Production (Built App)

Ketika aplikasi di-**build untuk production**, deep link akan bekerja sempurna:

### 1. Build Aplikasi

```bash
# Build untuk macOS
php artisan native:build

# Build untuk Windows  
php artisan native:build --os=windows

# Build untuk Linux
php artisan native:build --os=linux
```

### 2. Setelah Build

- URL scheme `idsapp://` **otomatis terdaftar** di OS
- Klik deep link di email → **langsung buka aplikasi** ✅
- OS tahu aplikasi IDS adalah handler untuk `idsapp://`

### 3. User Experience di Production

```
1. User terima email magic link
2. Klik tombol "Masuk ke Aplikasi Native"
3. OS deteksi idsapp:// → buka IDS App
4. App navigate ke halaman verify
5. Login otomatis ✅
```

---

## 🧪 Testing Deep Link

### Test di Development Mode

1. **Start aplikasi native:**
   ```bash
   composer native:dev
   ```

2. **Generate magic link:**
   ```bash
   php artisan auth:magic-link me@ojiepermana.com
   ```

3. **Login via browser** (gunakan URL yang di-generate)

4. **Test handler** (cek log untuk verify event handler berfungsi):
   ```bash
   php artisan pail
   ```

### Test di Production Build

1. **Build aplikasi:**
   ```bash
   php artisan native:build
   ```

2. **Install aplikasi** dari folder `dist/`

3. **Generate deeplink:**
   ```bash
   php artisan auth:magic-link me@ojiepermana.com --native
   ```

4. **Test via terminal:**
   ```bash
   # macOS
   open "idsapp://auth/verify?token=xxx"
   
   # Windows
   start "idsapp://auth/verify?token=xxx"
   
   # Linux
   xdg-open "idsapp://auth/verify?token=xxx"
   ```

5. **Aplikasi harus terbuka otomatis** dan navigate ke halaman verify

---

## 🔧 Troubleshooting

### Problem: Deep link tidak buka aplikasi

**Di Development Mode:**
- ✅ Normal - URL scheme belum registered
- 💡 Gunakan browser URL atau build aplikasi untuk test

**Di Production:**
- Check apakah app sudah di-build dengan benar
- Reinstall aplikasi (kadang URL scheme cache)
- Check `config/nativephp.php` pastikan `deeplink_scheme` sudah set

### Problem: Deep link buka browser, bukan aplikasi

- URL scheme conflict dengan aplikasi lain
- Change `NATIVEPHP_DEEPLINK_SCHEME` ke nilai yang unique
- Rebuild aplikasi

### Problem: Aplikasi buka tapi tidak navigate ke halaman verify

- Check `NativeAppServiceProvider::boot()` handler
- Verify `app.url` di config benar
- Check log di `storage/logs/laravel.log`

---

## 📝 Summary

| Mode | Deep Link | Cara Login |
|------|-----------|------------|
| **Development** (`composer native:dev`) | ❌ Belum registered | Gunakan browser URL atau tombol "Buka di Browser" |
| **Production** (Built app) | ✅ Fully working | Klik tombol "Masuk ke Aplikasi Native" di email |

### Rekomendasi:

- **Development**: Gunakan browser-based login (lebih cepat)
- **Testing Deep Link**: Build aplikasi untuk test proper deep link behavior
- **Production**: Deep link bekerja otomatis tanpa konfigurasi tambahan

---

## 🎯 Next Steps

1. ✅ Code deeplink sudah ready
2. ✅ Email template sudah support deeplink
3. ⏳ Untuk test deeplink proper: Build aplikasi
4. ⏳ Untuk production: Deploy built app ke user

**Untuk development sehari-hari**: Gunakan browser-based login, lebih praktis! 🚀
