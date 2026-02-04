<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Trip;
use Carbon\Carbon;

class ProfessionalDataSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Employees
        $employees = [
            ['nama' => 'Budi Santoso', 'nip' => '198501012010121001', 'jabatan' => 'Kepala Seksi', 'unit_kerja' => 'Pusat Industri Hijau'],
            ['nama' => 'Siti Aminah', 'nip' => '199005122015032002', 'jabatan' => 'Analist Kebijakan', 'unit_kerja' => 'Sekretariat BSKJI'],
            ['nama' => 'Andi Wijaya', 'nip' => '198203152008011005', 'jabatan' => 'Pranata Komputer', 'unit_kerja' => 'Pusat Optimalisasi Teknologi'],
            ['nama' => 'Dewi Lestari', 'nip' => '199211202019022001', 'jabatan' => 'Administrasi Umum', 'unit_kerja' => 'BSPJI Jakarta'],
        ];

        foreach ($employees as $emp) {
            $employee = Employee::create($emp);

            // Create 3-5 random trips for each employee
            for ($i = 0; $i < rand(3, 8); $i++) {
                $start = Carbon::today()->subDays(rand(1, 150));
                $end = (clone $start)->addDays(rand(2, 5));
                
                Trip::create([
                    'employee_id' => $employee->id,
                    'tanggal_mulai' => $start,
                    'tanggal_selesai' => $end,
                    'durasi' => $start->diffInDays($end) + 1,
                    'tujuan' => ['Surabaya', 'Semarang', 'Bandung', 'Medan', 'Makassar'][rand(0, 4)],
                    'notadinas' => 'ND-' . rand(100, 999) . '/BSKJI/' . $start->format('Y'),
                    'jenis' => ['DN', 'DK', 'DLN'][rand(0, 2)],
                    'status' => ['completed', 'completed', 'ongoing', 'planned'][rand(0, 3)],
                    'keterangan' => 'Kunjungan kerja teknis dan koordinasi balai.'
                ]);
            }
        }
    }
}
