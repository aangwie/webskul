<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\Activity;
use App\Models\Information;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'wirawan.aang5@gmail.com',
            'password' => Hash::make('A4n6w!r4w4n'),
        ]);

        // Create school profile
        SchoolProfile::create([
            'name' => 'SMP Negeri 6 Sudimoro',
            'address' => 'Jl. Pendidikan No. 1, Sudimoro, Pacitan, Jawa Timur',
            'phone' => '(0357) 123456',
            'email' => 'info@smpn6sudimoro.sch.id',
            'vision' => 'Menjadi sekolah unggulan yang menghasilkan lulusan berilmu, berakhlak mulia, berkarakter, dan berdaya saing tinggi di era global.',
            'mission' => "1. Menyelenggarakan pendidikan berkualitas yang mengembangkan potensi peserta didik secara optimal.\n2. Membentuk karakter peserta didik yang berakhlak mulia dan berbudaya.\n3. Mengembangkan lingkungan belajar yang kondusif dan menyenangkan.\n4. Meningkatkan kompetensi tenaga pendidik dan kependidikan.\n5. Menjalin kerjasama yang baik dengan masyarakat dan stakeholder.",
            'history' => 'SMP Negeri 6 Sudimoro didirikan pada tahun 2000 dengan tekad untuk memberikan pendidikan terbaik bagi generasi muda di wilayah Sudimoro dan sekitarnya. Sejak berdiri, sekolah ini telah menghasilkan banyak alumni yang sukses di berbagai bidang. Dengan dukungan tenaga pendidik yang profesional dan fasilitas yang memadai, SMP Negeri 6 Sudimoro terus berkomitmen untuk mencetak generasi unggul yang siap menghadapi tantangan masa depan.',
        ]);

        // Create sample teachers
        $teachers = [
            ['name' => 'Drs. Ahmad Santoso, M.Pd.', 'position' => 'Kepala Sekolah', 'nip' => '196507151990031003', 'education' => 'S2 Manajemen Pendidikan', 'order' => 1],
            ['name' => 'Siti Rahayu, S.Pd.', 'position' => 'Wakil Kepala Sekolah', 'nip' => '197203201998022001', 'education' => 'S1 Pendidikan', 'order' => 2],
            ['name' => 'Budi Prasetyo, S.Pd.', 'position' => 'Guru Matematika', 'nip' => '198105152005011001', 'education' => 'S1 Pendidikan Matematika', 'order' => 3],
            ['name' => 'Dewi Lestari, S.Pd.', 'position' => 'Guru Bahasa Indonesia', 'nip' => '198503152010012001', 'education' => 'S1 Pendidikan Bahasa Indonesia', 'order' => 4],
            ['name' => 'Agus Wijaya, S.Pd.', 'position' => 'Guru IPA', 'nip' => '198708202012011001', 'education' => 'S1 Pendidikan Fisika', 'order' => 5],
            ['name' => 'Sri Wahyuni, S.Pd.', 'position' => 'Guru Bahasa Inggris', 'nip' => '199001152015012001', 'education' => 'S1 Pendidikan Bahasa Inggris', 'order' => 6],
        ];

        foreach ($teachers as $teacher) {
            Teacher::create(array_merge($teacher, ['is_active' => true]));
        }

        // Create sample activities
        $activities = [
            [
                'title' => 'Upacara Bendera Memperingati Hari Kemerdekaan',
                'content' => 'Seluruh warga SMP Negeri 6 Sudimoro mengikuti upacara bendera dalam rangka memperingati Hari Kemerdekaan Republik Indonesia. Upacara berlangsung khidmat dengan petugas upacara dari siswa kelas 9. Kepala Sekolah dalam amanatnya mengajak seluruh siswa untuk mengisi kemerdekaan dengan belajar giat dan berperilaku baik.',
                'category' => 'event',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Pelaksanaan Ujian Tengah Semester Ganjil',
                'content' => 'SMP Negeri 6 Sudimoro melaksanakan Ujian Tengah Semester (UTS) Ganjil tahun ajaran 2024/2025. Ujian berlangsung selama satu minggu dengan protokol yang ketat. Seluruh siswa diharapkan mempersiapkan diri dengan baik dan menjunjung tinggi kejujuran.',
                'category' => 'news',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Workshop Pengembangan Karakter Siswa',
                'content' => 'Dalam rangka pembentukan karakter siswa, SMP Negeri 6 Sudimoro mengadakan workshop pengembangan karakter yang diikuti oleh seluruh siswa. Kegiatan ini menghadirkan narasumber dari Dinas Pendidikan dan psikolog profesional. Para siswa sangat antusias mengikuti berbagai sesi yang diselenggarakan.',
                'category' => 'event',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        // Create sample information
        $informations = [
            [
                'title' => 'Jadwal Libur Semester Ganjil',
                'content' => 'Diberitahukan kepada seluruh siswa dan orang tua bahwa libur semester ganjil dimulai tanggal 23 Desember 2024 hingga 6 Januari 2025. Kegiatan belajar mengajar akan dimulai kembali pada tanggal 7 Januari 2025.',
                'is_important' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Pembagian Rapor Semester Ganjil',
                'content' => 'Pembagian rapor semester ganjil akan dilaksanakan pada tanggal 21 Desember 2024. Orang tua/wali murid diharapkan hadir untuk mengambil rapor putra/putrinya.',
                'is_important' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Pendaftaran Ekstrakurikuler Semester Genap',
                'content' => 'Pendaftaran kegiatan ekstrakurikuler untuk semester genap dibuka mulai tanggal 8-15 Januari 2025. Siswa dapat mendaftar maksimal 2 ekstrakurikuler.',
                'is_important' => false,
                'is_active' => true,
            ],
        ];

        foreach ($informations as $info) {
            Information::create($info);
        }
    }
}
