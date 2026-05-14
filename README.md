# NexaCRM - Premium SaaS Client Workspace

NexaCRM adalah platform Client Workspace & CRM berbasis SaaS modern yang dirancang untuk agensi, freelancer, dan startup. Aplikasi ini menggabungkan fitur terbaik dari Notion, ClickUp, dan Slack untuk meningkatkan produktivitas tim dan kolaborasi klien.

## 🚀 Fitur Utama

- **Premium UI/UX:** Tampilan modern dengan Dark Mode, Glassmorphism, dan animasi halus.
- **Multi-Workspace:** Kelola beberapa bisnis atau tim dalam satu akun.
- **Kanban Board:** Manajemen tugas dengan fitur drag & drop yang interaktif.
- **Project Tracking:** Monitoring progress project secara realtime dengan grafik statistik.
- **Task Management:** Detail tugas lengkap dengan checklist, komentar, dan attachment.
- **File Manager:** Sistem penyimpanan file terpusat untuk setiap workspace.
- **Invoice System:** Pembuatan invoice profesional dengan fitur download PDF.
- **Internal Chat:** Ruang komunikasi tim yang terintegrasi.
- **Calendar:** Visualisasi deadline tugas dan project dalam tampilan kalender.

## 🛠️ Teknologi

- **Backend:** Laravel 13
- **Frontend:** Tailwind CSS, Alpine.js, Livewire
- **Database:** MySQL
- **Libraries:** Chart.js, FullCalendar.js, SortableJS, Barryvdh DOMPDF

## 📦 Instalasi

1. **Clone Repository**
   ```bash
   git clone [URL_REPOSITORY]
   cd Sistem-manajemen-portofolio
   ```

2. **Instal Dependensi**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin `.env.example` ke `.env` dan sesuaikan pengaturan database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi & Seed Database**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Build Assets**
   ```bash
   npm run build
   ```

6. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## 🔐 Kredensial Login (Demo)

Gunakan akun berikut untuk mencoba prototype:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@nexacrm.com` | `password` |
| **Project Manager** | `manager@nexacrm.com` | `password` |
| **Developer** | `dev1@nexacrm.com` | `password` |
| **Client** | `client@nexacrm.com` | `password` |

---

Developed by **Antigravity AI**
