<?php

namespace App\Imports;

use App\Models\Perjadin;
use App\Models\Perjalanan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class PerjadinImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new RekapSheetImport(), // Process sheet 1 (Rekap)
            1 => new DetailSheetImport(), // Process sheet 2 (Detail) if exists
        ];
    }
}

class RekapSheetImport implements ToCollection, WithStartRow
{
    public function startRow(): int { return 4; }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row[1])) continue; // Skip if name empty

            Perjadin::updateOrCreate(
                ['nama' => trim($row[1])],
                [
                    'no' => $row[0] ?? null,
                    'unit_kerja' => $row[2] ?? '-',
                    'sppd_dn' => (int)($row[3] ?? 0),
                    'sppd_dk' => (int)($row[4] ?? 0),
                    'sppd_dln' => (int)($row[5] ?? 0),
                    'hari_dn' => (int)($row[6] ?? 0),
                    'hari_dk' => (int)($row[7] ?? 0),
                    'hari_dln' => (int)($row[8] ?? 0),
                ]
            );
        }
    }
}

class DetailSheetImport implements ToCollection, WithStartRow
{
    public function startRow(): int { return 4; }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // In Detail Sheet: 1=Nama, 2=Unit, 3=TglMulai, 4=TglSelesai, 5=Kota, 6=Nota, 7=Jenis, 8=Durasi
            if (empty($row[1]) || empty($row[6])) continue;

            $employee = Perjadin::where('nama', trim($row[1]))->first();
            if (!$employee) continue;

            try {
                $start = $this->parseDate($row[3]);
                $notadinas = trim($row[6]);

                // Avoid duplicate trips based on Nota Dinas
                $exists = Perjalanan::where('perjadin_id', $employee->id)
                                    ->where('notadinas', $notadinas)
                                    ->exists();

                if (!$exists) {
                    Perjalanan::create([
                        'perjadin_id' => $employee->id,
                        'tanggal_mulai' => $start ? $start->toDateString() : now()->toDateString(),
                        'tanggal_selesai' => $this->parseDate($row[4]) ? $this->parseDate($row[4])->toDateString() : ($start ? $start->toDateString() : now()->toDateString()),
                        'kota' => $row[5] ?? '-',
                        'notadinas' => $notadinas,
                        'jenis' => strtoupper($row[7] ?? 'DN'),
                        'durasi' => (int)($row[8] ?? 1),
                    ]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;
        try {
            if (is_numeric($date)) return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }
}
