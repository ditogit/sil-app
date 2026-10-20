# Rencana Aksi: Role & Permission dengan Spatie Laravel Permission

**Proyek:** Aplikasi Manajemen Tugas (Task Management App)
**Disusun oleh:** Achmad Dito, UPN "Veteran" Yogyakarta
**Versi:** 2.0 (revisi sesuai keputusan desain)
**Tanggal:** 20 Oktober 2026

---

## 0. Keputusan Desain (Final)

| # | Keputusan | Dampak pada Implementasi |
|---|---|---|
| 1 | **Role bersifat global + keanggotaan proyek.** Satu user punya satu set role global (Spatie), dan akses ke proyek ditentukan oleh tabel `project_user`. | Fitur *Teams* Spatie **tidak dipakai**. Aturan "hanya anggota proyek" ditangani Policy. |
| 2 | **Team Member tidak boleh meng-assign task ke orang lain.** Task yang dibuat Team Member otomatis ditugaskan ke dirinya sendiri. | Permission `task.assign` (assign ke orang lain) hanya untuk Admin & PM. Self-assign dipaksa di server. |
| 3 | **Viewer boleh mengekspor laporan.** | `report.export` diberikan ke Viewer. |

**Prinsip pembagian tanggung jawab:**

- **Spatie** menjawab: *"Role ini boleh melakukan jenis aksi apa?"*
- **Policy + `project_user`** menjawab: *"Apakah user ini boleh melakukannya pada proyek/task yang ini?"*

---

## 1. Fase 1: Setup Awal

- [ ] `composer require spatie/laravel-permission`
- [ ] `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
- [ ] `php artisan migrate`
- [ ] Tambahkan trait `HasRoles` pada model `User`
- [ ] Pastikan guard konsisten (`web`) di `config/auth.php` dan seeder
- [ ] Pastikan `config/permission.php` memiliki `'teams' => false` (default)
- [ ] Laravel 11/12: daftarkan alias middleware di `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

---

## 2. Fase 2: Definisi Role & Permission

### 2.1 Role

| Role | Slug | Keterangan |
|---|---|---|
| Admin | `admin` | Akses penuh ke seluruh sistem |
| Project Manager | `project-manager` | Mengelola proyek tempat ia menjadi anggota |
| Team Member | `team-member` | Mengerjakan task miliknya sendiri |
| Viewer / Klien | `viewer` | Read-only, boleh berkomentar dan mengekspor laporan |

### 2.2 Daftar Permission (konvensi `modul.aksi`)

| Modul | Permission |
|---|---|
| Project | `project.view`, `project.create`, `project.update`, `project.archive`, `project.manage-members` |
| Task | `task.view`, `task.create`, `task.assign`, `task.update`, `task.update-status`, `task.delete`, `task.reopen` |
| Sub-task | `subtask.create`, `subtask.update` |
| Komentar | `comment.create`, `comment.delete-any` |
| Attachment | `attachment.upload`, `attachment.delete` |
| Dashboard & Laporan | `dashboard.view`, `report.view`, `report.export` |
| Sistem | `user.manage`, `role.manage`, `system.configure` |

> **Catatan `task.assign`:** permission ini berarti *menugaskan task ke orang lain / mengubah assignee*. Team Member tidak memilikinya. Penugasan ke diri sendiri bukan permission, tetapi perilaku default yang dipaksa di server (lihat Fase 5).

### 2.3 Matriks Role → Permission

| Permission | Admin | PM | Team Member | Viewer |
|---|:-:|:-:|:-:|:-:|
| `project.view` | ✅ | ✅ | ✅ | ✅ |
| `project.create` | ✅ | ✅ | ❌ | ❌ |
| `project.update` | ✅ | ✅ | ❌ | ❌ |
| `project.archive` | ✅ | ✅ | ❌ | ❌ |
| `project.manage-members` | ✅ | ✅ | ❌ | ❌ |
| `task.view` | ✅ | ✅ | ✅ | ✅ |
| `task.create` | ✅ | ✅ | ✅ | ❌ |
| `task.assign` (ke orang lain) | ✅ | ✅ | ❌ | ❌ |
| `task.update` | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| `task.update-status` | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| `task.reopen` (ubah task Done) | ✅ | ✅ | ❌ | ❌ |
| `task.delete` | ✅ | ✅ | ❌ | ❌ |
| `subtask.create` / `subtask.update` | ✅ | ✅ | ✅ | ❌ |
| `attachment.upload` | ✅ | ✅ | ✅ | ❌ |
| `attachment.delete` | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| `comment.create` | ✅ | ✅ | ✅ | ✅ |
| `comment.delete-any` | ✅ | ✅ | ❌ | ❌ |
| `dashboard.view` | ✅ | ✅ | ✅ | ✅ |
| `report.view` | ✅ | ✅ | ❌ | ✅ |
| `report.export` | ✅ | ✅ | ❌ | ✅ |
| `user.manage` / `role.manage` / `system.configure` | ✅ | ❌ | ❌ | ❌ |

> Kolom "(milik sendiri)" artinya permission Spatie hanya membuka pintu; pembatasan kepemilikan dilakukan oleh Policy.

---

## 3. Fase 3: Seeder

- [ ] Buat `database/seeders/RolePermissionSeeder.php`
- [ ] Gunakan `firstOrCreate` agar aman dijalankan berulang
- [ ] Reset cache permission di awal seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'project.view', 'project.create', 'project.update', 'project.archive', 'project.manage-members',
            'task.view', 'task.create', 'task.assign', 'task.update', 'task.update-status', 'task.delete', 'task.reopen',
            'subtask.create', 'subtask.update',
            'comment.create', 'comment.delete-any',
            'attachment.upload', 'attachment.delete',
            'dashboard.view', 'report.view', 'report.export',
            'user.manage', 'role.manage', 'system.configure',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Admin: semua permission
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
            ->syncPermissions(Permission::all());

        // Project Manager
        Role::firstOrCreate(['name' => 'project-manager', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view', 'project.create', 'project.update', 'project.archive', 'project.manage-members',
                'task.view', 'task.create', 'task.assign', 'task.update', 'task.update-status',
                'task.delete', 'task.reopen',
                'subtask.create', 'subtask.update',
                'comment.create', 'comment.delete-any',
                'attachment.upload', 'attachment.delete',
                'dashboard.view', 'report.view', 'report.export',
            ]);

        // Team Member (TIDAK punya task.assign)
        Role::firstOrCreate(['name' => 'team-member', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view',
                'task.view', 'task.create', 'task.update', 'task.update-status',
                'subtask.create', 'subtask.update',
                'comment.create',
                'attachment.upload', 'attachment.delete',
                'dashboard.view',
            ]);

        // Viewer (boleh report.view & report.export)
        Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web'])
            ->syncPermissions([
                'project.view', 'task.view',
                'comment.create',
                'dashboard.view', 'report.view', 'report.export',
            ]);
    }
}
```

- [ ] Buat `UserSeeder`: 1 akun per role untuk testing (admin, pm, member, viewer)
- [ ] Daftarkan keduanya di `DatabaseSeeder` (Role dulu, baru User)

---

## 4. Fase 4: Keanggotaan Proyek & Policy

### 4.1 Keanggotaan Proyek

- [ ] Migrasi tabel pivot `project_user` (`project_id`, `user_id`, `timestamps`, unique `[project_id, user_id]`)
- [ ] Relasi `Project::members()` dan `User::projects()` (`belongsToMany`)
- [ ] Saat PM membuat proyek, **otomatis tambahkan PM sebagai anggota** proyek tersebut
- [ ] Validasi: assignee sebuah task harus anggota proyek task tersebut
- [ ] Hanya user ber-role `admin`/`project-manager` yang boleh `project.manage-members`

### 4.2 Admin Bypass

- [ ] Di `AppServiceProvider::boot()`:

```php
Gate::before(fn (User $user) => $user->hasRole('admin') ? true : null);
```

### 4.3 `ProjectPolicy`

| Method | Aturan |
|---|---|
| `view` | Admin, atau user yang menjadi anggota proyek |
| `create` | `can('project.create')` (Admin, PM) |
| `update` / `archive` / `manageMembers` | `can('project.xxx')` **dan** anggota proyek tersebut (PM hanya mengelola proyek yang ia ikuti) |

### 4.4 `TaskPolicy`

| Method | Aturan |
|---|---|
| `view` | Anggota proyek task tersebut |
| `create` | `can('task.create')` dan anggota proyek |
| `assign` | `can('task.assign')` dan anggota proyek (Admin/PM) |
| `updateStatus` | Anggota proyek **dan** (PM, atau `assignee_id === user.id` dengan `task.update-status`). Jika status `Done` → hanya yang punya `task.reopen` |
| `update` | Sama seperti `updateStatus`; Team Member hanya task miliknya |
| `delete` | `can('task.delete')` dan anggota proyek |

```php
public function updateStatus(User $user, Task $task): bool
{
    // Admin sudah di-bypass lewat Gate::before
    if (! $task->project->members->contains($user)) {
        return false;
    }

    // Task Done hanya boleh diubah yang punya task.reopen (PM/Admin)
    if ($task->status === TaskStatus::Done) {
        return $user->can('task.reopen');
    }

    return $user->hasRole('project-manager')
        || ($user->can('task.update-status') && $task->assignee_id === $user->id);
}
```

### 4.5 `CommentPolicy` & `AttachmentPolicy`

- [ ] `CommentPolicy::create`: `can('comment.create')` + anggota proyek (Viewer termasuk)
- [ ] `CommentPolicy::delete`: pemilik komentar, atau `can('comment.delete-any')`
- [ ] `AttachmentPolicy::delete`: pengunggah file, atau `can('attachment.delete')` untuk PM/Admin

---

## 5. Fase 5: Penerapan di Aplikasi

### 5.1 Route

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // kelola user, role, permission, konfigurasi sistem
});

Route::middleware(['auth', 'permission:report.view'])->get('/reports', ...);
Route::middleware(['auth', 'permission:report.export'])->get('/reports/export', ...);
```

### 5.2 Controller

- [ ] Gunakan `$this->authorize('updateStatus', $task)` atau `Gate::authorize(...)` di setiap aksi yang menyentuh data proyek/task

### 5.3 Aturan Assign Task (Keputusan #2)

- [ ] Di `StoreTaskRequest` / controller, **paksa** assignee untuk Team Member:

```php
$assigneeId = $request->user()->can('task.assign')
    ? $request->input('assignee_id', $request->user()->id)
    : $request->user()->id;   // Team Member: selalu diri sendiri
```

- [ ] Abaikan/tolak (403 atau validasi gagal) bila Team Member mengirim `assignee_id` milik orang lain
- [ ] Di UI, sembunyikan dropdown assignee dengan `@can('task.assign')`; untuk Team Member tampilkan "Ditugaskan ke: Saya"
- [ ] Team Member tidak boleh mengubah assignee task yang sudah ada

### 5.4 Form Request & Aturan PRD

- [ ] Deadline tidak boleh di masa lalu saat dibuat: `after_or_equal:today`
- [ ] Assignee harus anggota proyek: `Rule::exists('project_user', 'user_id')->where('project_id', $projectId)`
- [ ] Task `Done` tidak bisa diubah kecuali oleh user dengan `task.reopen`
- [ ] Task melewati deadline otomatis ditandai overdue (scheduler/accessor)

### 5.5 Blade / Frontend

- [ ] Sembunyikan elemen dengan `@can('task.create')`, `@can('task.assign')`, `@role('admin')`, dsb.
- [ ] **Ingat:** UI hanya kenyamanan; keamanan sepenuhnya di server (policy/middleware)

### 5.6 Query Scoping

- [ ] Daftar proyek/task hanya menampilkan proyek yang diikuti user (`whereHas('members', ...)`), kecuali Admin
- [ ] **Dashboard per role:**
  - Team Member: *My Tasks* dan ringkasan proyek yang diikuti
  - PM: workload anggota dan progress proyek yang ia ikuti
  - Viewer: progress proyek read-only
  - Admin: seluruh data
- [ ] **Laporan:** PM dan Viewer hanya melihat/mengekspor data proyek yang mereka ikuti; Admin semua

---

## 6. Fase 6: Manajemen Role oleh Admin

- [ ] Halaman CRUD user + assign role (`syncRoles`)
- [ ] Halaman kelola permission per role (`syncPermissions`), diproteksi `role.manage`
- [ ] Halaman kelola anggota proyek (PM/Admin)
- [ ] Lindungi agar Admin terakhir tidak dapat dihapus atau diturunkan role-nya
- [ ] Setelah mengubah permission: `app(PermissionRegistrar::class)->forgetCachedPermissions()`
- [ ] Opsional: audit log perubahan role dengan `spatie/laravel-activitylog`

---

## 7. Fase 7: Testing

### 7.1 Setup Test

```php
protected function setUp(): void
{
    parent::setUp();
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    $this->seed(RolePermissionSeeder::class);
}
```

### 7.2 Skenario Wajib

| # | Skenario | Hasil |
|---|---|---|
| 1 | Team Member bukan assignee mengubah status task | **403** |
| 2 | Team Member assignee mengubah status task miliknya | **200** |
| 3 | Team Member mengubah task berstatus `Done` | **403** |
| 4 | PM / Admin mengubah task berstatus `Done` | **200** |
| 5 | User bukan anggota proyek membuka task proyek itu | **403/404** |
| 6 | Team Member membuat task dengan `assignee_id` orang lain | **Ditolak (403/422)** |
| 7 | Team Member membuat task tanpa assignee | Assignee = dirinya sendiri |
| 8 | PM meng-assign task ke anggota proyek | **200** |
| 9 | PM meng-assign task ke user non-anggota proyek | **422** |
| 10 | Viewer membuat task | **403** |
| 11 | Viewer membuat komentar | **200** |
| 12 | Viewer menghapus komentar orang lain | **403** |
| 13 | Viewer mengakses `/reports` dan export | **200** |
| 14 | Team Member mengakses `/reports` | **403** |
| 15 | Team Member membuat / mengarsip proyek | **403** |
| 16 | PM mengarsip proyek yang bukan miliknya (bukan anggota) | **403** |
| 17 | Deadline di masa lalu saat membuat task | **422** |
| 18 | Hanya Admin mengakses halaman kelola user/role | PM/Member/Viewer **403** |

### 7.3 Test Seeder

- [ ] Jumlah role = 4, jumlah permission sesuai daftar
- [ ] Sinkronisasi tiap role sesuai matriks (khususnya `team-member` **tanpa** `task.assign`, dan `viewer` **dengan** `report.export`)

---

## 8. Fase 8: Deploy & Maintenance

- [ ] Jalankan `php artisan migrate --force` dan `php artisan db:seed --class=RolePermissionSeeder`
- [ ] Jalankan `php artisan permission:cache-reset` setelah mengubah permission di production
- [ ] Dokumentasikan matriks role–permission di `README`
- [ ] Review berkala: tambahkan permission baru lewat seeder, bukan manual di database

---

## 9. Ringkasan Perubahan dari Versi 1

| Item | Versi 1 | Versi 2 (Final) |
|---|---|---|
| Model PM | Belum diputuskan (global vs per-proyek) | Role global + keanggotaan proyek, tanpa Spatie Teams |
| `task.assign` untuk Team Member | Opsional ("diri sendiri / sesuai kebijakan") | **Dihapus**; Team Member hanya self-assign otomatis |
| `report.export` untuk Viewer | Perlu konfirmasi | **Diizinkan** |
| Pembatasan PM pada proyek | Dipertimbangkan | **Wajib**: PM hanya mengelola proyek yang ia ikuti |
| Validasi assignee | Belum ada | Assignee harus anggota proyek |
| Skenario test | 5 skenario | 18 skenario |

---

## 10. Checklist Ringkas Urutan Pengerjaan

1. [ ] Install & publish Spatie, migrasi, trait `HasRoles`
2. [ ] Seeder role & permission + user testing
3. [ ] Tabel `project_user` + relasi
4. [ ] `Gate::before` Admin + semua Policy
5. [ ] Middleware route + `authorize()` di controller
6. [ ] Form Request (deadline, assignee, self-assign)
7. [ ] Query scoping + dashboard/laporan per role
8. [ ] Halaman manajemen user/role/anggota proyek
9. [ ] Feature test 18 skenario
10. [ ] Deploy, reset cache, dokumentasi
