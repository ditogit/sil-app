# Laporan Implementasi — SIL App (Task Management)

Tanggal: 16 Oktober 2026
Basis: `PRD_desain_basisdata_task_managementApp.md` + 9 layar Stitch (`docs/`)

## Ringkasan

| Item | Status |
|---|---|
| Layar Stitch ter-wiring | 8 dari 9 (dashboard, buat-tugas, tugas-saya, detail-tugas, proyek, board, laporan, notifikasi) |
| Layar masih mock statis | 1 file dipakai 2 route: tim + pengaturan |
| Test | 62 test, semua lolos (`php artisan test`) |
| Code style | Pint bersih |
| Frontend build | `npm run build` OK |

Akun demo (password semua: `password`): `admin@example.com`, `pm@example.com`, `member@example.com`, `viewer@example.com`.
Login menampilkan panel klik-isi otomatis, **hanya saat `APP_ENV=local`**.

---

## Yang Sudah Dikerjakan

### 1. Fondasi
- Auth satu pintu (`/login`, remember, throttle, logout) dipakai semua role termasuk admin.
- Layout Stitch penuh: sidebar 240px + 3 grup nav + badge dinamis, header (breadcrumb, search → `/tasks?q=`, +Buat Tugas, dot notif, dropdown user + Keluar), responsif + mode cetak.
- Token desain Stitch jadi satu sumber (`resources/css/tokens.css`, dipakai app + theme Filament). Konflik radius `design-system.md` vs 9 file HTML diputuskan: ikut file HTML.
- Icon Material → RuneIcons (`runeicons@0.1.0` via npm, 60+ SVG di `resources/svg/`, komponen `<x-rune-icon>`). Layar wired sudah Rune; 2 mock tersisa masih Material.
- Filament dikembalikan default di `/admin` (ruang mesin, belum diisi Users/Roles).

### 2. Layanan A — Manajemen Tugas (PRD §3A)
- Buat task + validasi (`due_date ≥ hari ini`, milestone se-proyek, member hanya boleh assign ke diri sendiri).
- Alur status To Do → In Progress → Review → Done (dropdown, drag-drop board, centang selesai, buka-ulang khusus `reopen tasks`).
- Task done terkunci (hanya Admin/PM).
- Komentar (tulis/hapus sesuai permission), lampiran (unggah/unduh/hapus, maks 25 MB), sub-task (tambah + checklist + progres).
- Hapus task (soft delete, PM/Admin).
- Kolom ekstra di luar PRD: `tags`, `story_points`, `estimate_hours`, `due_time`, prioritas `critical`.

### 3. Layanan B — Manajemen Proyek (PRD §3B)
- Daftar proyek (Aktif/Diarsipkan + search), buat proyek + anggota awal, edit, arsipkan/aktifkan kembali.
- Workspace per proyek: Ringkasan (KPI + task kritis + workload + aktivitas), Daftar Tugas, Milestone (CRUD), Dokumen, Log, Pengaturan (edit + kelola anggota + danger zone).
- **Progress proyek otomatis** via `TaskObserver` (rasio done, termasuk data lama yang di-backfill).
- Keanggotaan: undang/keluarkan (pembuat tak bisa dikeluarkan), non-anggota 403.

### 4. Dashboard & Laporan (PRD §4)
- Dashboard: sapaan, 4 KPI + delta periode, filter Hari/Minggu/Bulan, tabel Tugas Saya + filter prioritas, kartu proyek, deadline, workload, aktivitas.
- Task Board: Kanban 4 kolom, drag-drop HTML5 murni, filter proyek/search/assignee/prioritas/label/overdue.
- Laporan: filter 7/30/90 hari + proyek, 4 KPI, grafik SVG server-side (bar tren, donat status, prioritas, workload), tabel SLA per proyek, ekspor **CSV** (middleware `permission:export reports`), cetak PDF via browser.

### 5. Notifikasi (PRD §3A.5)
- 4 notifikasi channel database: penugasan, ubah status (ke follower kecuali pelaku), komentar baru, reminder H-1 + overdue (`notifications:send-deadline-reminders` terjadwal 07:00, anti-duplikat 20 jam — butuh cron scheduler aktif).
- Halaman `/notifications`: 6 tab + counter, search, tandai dibaca, hapus, arsip >30 hari. Bell/badge otomatis. Buka detail menandai dibaca.

### 6. Otorisasi (PRD §D–F)
- 22 permission + matriks 4 role (seeder), `Gate::before` admin.
- Policy: `TaskPolicy`, `ProjectPolicy`, `CommentPolicy::delete`, `AttachmentPolicy::delete` (own + manage-all).
- Scoping anggota proyek di list (proyek, board, laporan) + 403 non-anggota.

---

## Yang Belum Dikerjakan

| # | Item | Keterangan |
|---|---|---|
| 1 | Layar **Tim + Pengaturan** | Masih mock statis (`pengaturan_tim.html` untuk `/team` + `/settings`): kelola anggota global, profil, preferensi |
| 2 | Filament backoffice | UI kelola Users/Roles (`manage users`, `manage roles` ada di matriks tapi tanpa UI); user baru hanya via seeder. Opsi: Shield vs resource manual (belum diputuskan) |
| 3 | Export native | `.xlsx` (phpspreadsheet) + PDF native (dompdf) — perlu persetujuan tambah dependensi. Sekarang: CSV + cetak |
| 4 | Audit log penuh | Aktivitas masih turunan (dibuat/komentar/selesai); belum tabel `audit_logs` |
| 5 | Kode task cantik | Masih `TSK-{id}`; Stitch pakai `BSI-402`. Butuh kolom + generator sekuens per proyek |
| 6 | Registrasi user | Tidak ada halaman registrasi; default-role-on-register belum berlaku |
| 7 | Mention `@nama` | Tombol Stitch dibuang; tidak ada sistem mention |
| 8 | Pencatatan jam kerja | Grafik "jam terpakai" Stitch dibuang; PRD tidak punya timelog |
| 9 | Penjadwalan laporan & auto-rebalance workload | Tombol Stitch dibuang; tidak ada di PRD |
| 10 | Push/email notifikasi | Channel baru database saja; mail/push belum ada |
| 11 | Spatie Teams | Tetap role global sesuai PRD; diputuskan ulang hanya jika requirement berubah |

## Deviasi Desain yang Disepakati (catat agar tak dianggap bug)
- Detail task Stitch berupa drawer geser → dirender halaman penuh (konten sama). Drawer overlay kembali saat dibutuhkan.
- Tombol tanpa backend di Stitch dibuang, bukan dipajang mati (bookmark, filter lanjutan, pengaturan board, pusat bantuan → ke pengaturan).
- Prioritas Stitch 4 level (Rendah/Sedang/Tinggi/Kritis); PRD 3 level → DB diperluas + `critical`.
- Logo/avatar URL sementara Google diganti blok inisial lokal.
- Tidak ada layar login di Stitch → didesain sendiri seirama sistem.
- Tombol "Simpan sebagai Draf" + "Simpan Perubahan" (JS simulasi) dihapus; semua aksi simpan langsung + toast.

## Cara Menjalankan
```bash
composer setup        # install + key + migrate + build (sekali saja)
composer dev          # atau: php artisan serve + npm run dev
php artisan test      # 62 test
php artisan schedule:run   # reminder deadline (jadwalkan tiap menit di server)
```
