# 📝 Notes API - Backend Laravel 12

Assesmen PT. Gencidev Prisma Teknologi.

---

## 🔧 Persiapan Lingkungan
Pastikan sudah terpasang di komputer Anda:
- **PHP** >= 8.2  
- **Composer**  
- **MySQL 
- **Laravel 12**  
- **Postman**  

---

## 🚀 Cara Menjalankan Project

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/notes-backend.git
   cd notes-backend
   ```

2. **Install Dependency**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   - Duplikat file `.env.example` menjadi `.env`
   - Atur konfigurasi database:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=notes_gencidev
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Generate Key Laravel**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi & Seeder (opsional)**
   ```bash
   php artisan migrate --seed
   ```

6. **Menjalankan Server**
   ```bash
   php artisan serve
   ```
   Server akan berjalan di:  
   👉 http://127.0.0.1:8000

---

## 📮 Pengujian API dengan Postman

1. Buka **Postman**  
2. Klik **Import**  
3. Pilih file `NotesAPI.postman_collection.json` yang ada di folder project  
4. Koleksi API akan muncul di sidebar Postman  
5. Pilih request (contoh: `GET All Notes`)  
6. Klik **Send** untuk mengirim request dan melihat response  

---

## ✅ Catatan
- Pastikan server lokal berjalan dengan `php artisan serve` sebelum testing.  
- Sesuaikan konfigurasi database di `.env` sesuai environment Anda.  
- File koleksi Postman (`NotesAPI.postman_collection.json`) sudah disiapkan agar testing lebih mudah.  

---
