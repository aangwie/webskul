# TODO: Filter Tahun Ajaran pada Halaman Pembayaran Komite

## Plan Overview
Menambahkan filter tahun ajaran pada halaman /admin/committee/payments agar admin dapat melihat data pembayaran siswa berdasarkan tahun ajaran yang dipilih.

## Status: COMPLETED ✓

## Steps Completed

1. [x] Update CommitteeController.php - Modifikasi method indexPayments() untuk menerima parameter academic_year_id
2. [x] Update CommitteeController.php - Modifikasi method studentPayments() untuk menerima parameter academic_year_id
3. [x] Update CommitteeController.php - Modifikasi method recordPayment() untuk menerima parameter academic_year_id
4. [x] Update CommitteeController.php - Update method storePayment(), updatePayment(), destroyPayment() untuk meneruskan parameter academic_year_id
5. [x] Route web.php - Route sudah mendukung query parameter (GET) - tidak perlu diubah
6. [x] Update index.blade.php - Tambahkan dropdown filter tahun ajaran
7. [x] Update students.blade.php - Tampilkan tahun ajaran yang dipilih dan link kembali dengan tahun ajaran
8. [x] Update record.blade.php - Tampilkan tahun ajaran dan update link kembali
9. [x] Update edit.blade.php - Tampilkan tahun ajaran dan update link kembali

## Implementation Details

### Controller Changes:
- `indexPayments(Request $request)` - Menerima parameter `academic_year_id` opsional
  - Jika ada parameter, gunakan tahun ajaran yang dipilih
  - Jika tidak ada, gunakan tahun ajaran aktif
  - Jika tidak ada tahun ajaran aktif, gunakan tahun ajaran pertama yang tersedia
  
- `studentPayments(SchoolClass $schoolClass, Request $request)` - Menerima parameter `academic_year_id` opsional
  - Menggunakan tahun ajaran yang dipilih untuk menampilkan data pembayaran

- `recordPayment(Student $student, Request $request)` - Menerima parameter `academic_year_id` opsional
  - Menggunakan tahun ajaran yang dipilih untuk menampilkan riwayat pembayaran

- `storePayment()`, `updatePayment()`, `destroyPayment()` - Meneruskan parameter academic_year_id di redirect

### View Changes:
- Dropdown filter tahun ajaran ditambahkan di halaman index payments
- Link ke halaman students, record, dan edit menyertakan parameter academic_year_id
- Info tahun ajaran ditampilkan di halaman students dan record

### Cara Penggunaan:
1. Buka halaman /admin/committee/payments
2. Pilih tahun ajaran dari dropdown
3. Data pembayaran akan ditampilkan berdasarkan tahun ajaran yang dipilih
4. Link kembali akan mempertahankan filter tahun ajaran
