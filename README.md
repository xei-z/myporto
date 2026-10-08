# ZeriKo — Full-Stack Portfolio & CMS

Aplikasi Portofolio Full-Stack interaktif yang dilengkapi dengan Content Management System (CMS) mandiri dan Role-Based Access Control (RBAC).

## 🚀 Fitur Utama

- **Public Portfolio Frontend**: Tampilan responsif dengan filter kategori proyek dinamis dan render data via Fetch API (RESTful).
- **Authentication System**: Fitur Login & Register berbasis sesi PHP yang aman dengan enkripsi password (`password_hash`).
- **Role-Based Access Control (RBAC)**:
  - **Member**: Akses tingkat dasar.
  - **Admin**: Akses pengelolaan proyek (CRUD) via Modal Pop-up.
  - **Super Admin**: Hak akses penuh termasuk Panel Manajemen Pengguna (pengaturan role & hapus akun) dengan proteksi *self-action*.
- **Local File Upload**: Fitur unggah gambar/thumbnail proyek lokal ke server dengan dukungan penanganan file otomatis.

## 🛠️ Tech Stack

- **Backend**: PHP (PDO)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Modern JavaScript (Fetch API, Async/Await)

## 💻 Cara Install & Menjalankan (Localhost)

1. **Clone Repositori**:
   ```bash
   git clone [https://github.com/xei-z/myporto.git](https://github.com/xei-z/myporto.git)