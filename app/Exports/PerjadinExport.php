<?php

namespace App\Exports;

use App\Models\Perjadin;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PerjadinExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new RekapSheetExport(),
            new DetailSheetExport(),
        ];
    }
}

class RekapSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithMapping
{
    public function title(): string
    {
        return 'Rekapitulasi';
    }

    public function collection()
    {
        // Eager load perjalanan to avoid N+1 issues
        return Perjadin::with('perjalanan')->orderBy('no', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            ['REKAPITULASI PERJALANAN DINAS PEGAWAI'],
            [''],
            [
                'No', 
                'Nama Pegawai', 
                'Unit Kerja', 
                'SPPD DN', 
                'SPPD DK', 
                'SPPD DLN', 
                'Hari DN', 
                'Hari DK', 
                'Hari DLN', 
                'Total SPPD', 
                'Total Hari', 
                'Detail Perjalanan' // New Column
            ]
        ];
    }

    public function map($row): array
    {
        // Build the Multi-line Trip Details string
        $tripDetails = "";
        if ($row->perjalanan && $row->perjalanan->count() > 0) {
            $trips = [];
            $i = 1;
            foreach ($row->perjalanan as $trip) {
                $startDate = $trip->tanggal_mulai ? $trip->tanggal_mulai->format('d M Y') : '-';
                $endDate = $trip->tanggal_selesai ? $trip->tanggal_selesai->format('d M Y') : '-';
                $jenis = strtoupper($trip->jenis ?? 'DN');
                
                $trips[] = "{$i}. [{$jenis}] {$trip->kota}\n" .
                           "   {$trip->durasi} Hari\n" .
                           "   {$startDate} - {$endDate}\n" .
                           "   Nota: {$trip->notadinas}";
                $i++;
            }
            $tripDetails = implode("\n\n", $trips);
        }

        return [
            $row->no,
            $row->nama,
            $row->unit_kerja,
            $row->sppd_dn,
            $row->sppd_dk,
            $row->sppd_dln,
            $row->hari_dn,
            $row->hari_dk,
            $row->hari_dln,
            ($row->sppd_dn + $row->sppd_dk + $row->sppd_dln),
            ($row->hari_dn + $row->hari_dk + $row->hari_dln),
            $tripDetails // Data for new column
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:L1'); // Updated to L because of new column
        
        $lastRow = $sheet->getHighestRow();
        
        // Apply borders and alignment to all data
        $sheet->getStyle("A3:L{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP, // Top alignment for better reading of multi-line text
                'wrapText' => true // Enable text wrap for the Detail column
            ]
        ]);

        // Fix column width for Detail Perjalanan as it can be long
        $sheet->getColumnDimension('L')->setWidth(50);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]
        ];
    }
}

class DetailSheetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithMapping
{
    public function title(): string
    {
        return 'Detail Riwayat';
    }

    public function collection()
    {
        return \App\Models\Perjalanan::with('perjadin')->orderBy('tanggal_mulai', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            ['DETAIL RIWAYAT PERJALANAN DINAS'],
            [''],
            ['No', 'Nama Pegawai', 'Unit Kerja', 'Tgl Mulai', 'Tgl Selesai', 'Tujuan/Kota', 'No. Nota Dinas', 'Jenis', 'Durasi']
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->perjadin->nama ?? '-',
            $row->perjadin->unit_kerja ?? '-',
            $row->tanggal_mulai ? $row->tanggal_mulai->format('d/m/Y') : '',
            $row->tanggal_selesai ? $row->tanggal_selesai->format('d/m/Y') : '',
            $row->kota,
            $row->notadinas,
            $row->jenis,
            $row->durasi
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:I1');
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A3:I{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]
        ];
    }
}
