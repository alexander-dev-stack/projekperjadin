<?php

namespace App\Http\Controllers;

use App\Models\Perjadin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerjadinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Load perjadin with their trip history and optional search filter
        $query = Perjadin::with(['perjalanan' => function($query) {
            $query->orderBy('tanggal_mulai', 'desc');
        }]);

        if ($search) {
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        $data = $query->get();
        
        // Fetch all names for autocomplete suggestions
        $pegawaiList = Perjadin::select('nama')->distinct()->pluck('nama');
        
        return view('rekap', compact('data', 'search', 'pegawaiList'));
    }

    public function create()
    {
        $balaiList = [
            'Sekretariat Badan Standardisasi dan Kebijakan Jasa Industri',
        'Pusat Industri Hijau',
        'Pusat Pengawasan Standardisasi Industri',
        'Pusat Optimalisasi Pemanfaatan Teknologi Industri dan Kebijakan Jasa Industri',
        'Pusat Perumusan, Penerapan, dan Pemberlakuan Standardisasi Industri',
        'Balai Besar Standardisasi dan Pelayanan Jasa Industri Kimia, Farmasi, dan Kemasan',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Agro',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Keramik dan Mineral Nonlogam',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Tekstil',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Bahan dan Barang Teknik',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Selulosa',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Logam dan Mesin',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Kulit, Karet, dan Plastik',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Kerajinan dan Batik',
            'Balai Besar Standardisasi dan Pelayanan Jasa Pencegahan Pencemaran Industri',
            'Balai Besar Standardisasi dan Pelayanan Jasa Industri Hasil Perkebunan, Mineral Logam dan Maritim',
            'BSPJI Aceh',
            'BSPJI Medan',
            'BSPJI Padang',
            'BSPJI Pekanbaru',
            'BSPJI Palembang',
            'BSPJI Lampung',
            'BSPJI Jakarta',
            'BSPJI Surabaya',
            'BSPJI Banjarbaru',
            'BSPJI Pontianak',
            'BSPJI Samarinda',
            'BSPJI Manado',
            'BSPJI Ambon',
        ];

        // Fetch distinct employee names from the database for autocomplete
        $pegawaiList = Perjadin::select('nama')->distinct()->pluck('nama');

        return view('input', compact('balaiList', 'pegawaiList'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'nama_pegawai' => 'required|string',
            'tujuan' => 'required|string',
            'notadinas' => 'required|string',
            'tanggal_range' => 'required|string',
        ]);

        // 1. Calculate Duration and parse dates
        $rangeString = $request->tanggal_range;
        if (strpos($rangeString, ' to ') !== false) {
            $dates = explode(' to ', $rangeString);
        } else {
            $dates = explode(' - ', $rangeString);
        }
        
        if (count($dates) >= 1) {
            $start = Carbon::parse(trim($dates[0]));
            $end = isset($dates[1]) ? Carbon::parse(trim($dates[1])) : $start->copy();
            $duration = $start->diffInDays($end) + 1;
        } else {
            return back()->withErrors(['tanggal_range' => 'Invalid date range']);
        }

        // 2. Find or Create Employee Record
        $perjadin = Perjadin::firstOrCreate(
            ['nama' => $request->nama_pegawai],
            [
                'unit_kerja' => '-', // Use placeholder if employee is brand new
                'sppd_dn' => 0, 'hari_dn' => 0,
                'sppd_dk' => 0, 'hari_dk' => 0,
                'sppd_dln' => 0, 'hari_dln' => 0,
            ]
        );

        // 3. Update aggregated counters based on trip type (DN, DK, DLN)
        $jenis = $request->jenis_perjalanan; // DN, DK, DLN
        
        if ($jenis == 'DN') {
            $perjadin->sppd_dn += 1;
            $perjadin->hari_dn += $duration;
        } elseif ($jenis == 'DK') {
            $perjadin->sppd_dk += 1;
            $perjadin->hari_dk += $duration;
        } elseif ($jenis == 'DLN') {
            $perjadin->sppd_dln += 1;
            $perjadin->hari_dln += $duration;
        }
        $perjadin->save();

        // 4. Create detailed trip record
        \App\Models\Perjalanan::create([
            'perjadin_id' => $perjadin->id,
            'tanggal_mulai' => $start->toDateString(),
            'tanggal_selesai' => $end->toDateString(),
            'kota' => $request->tujuan,
            'notadinas' => $request->notadinas,
            'durasi' => $duration,
            'jenis' => $jenis, 
        ]);
        
        return redirect('/')->with('success', 'Data perjalanan berhasil disimpan!');
    }

    public function edit($id)
    {
        $perjadin = Perjadin::with('perjalanan')->findOrFail($id);
        return view('edit', compact('perjadin'));
    }

    public function update(Request $request, $id)
    {
        $perjadin = Perjadin::findOrFail($id);
        
        $request->validate([
            'nama' => 'required',
            'unit_kerja' => 'required',
        ]);

        // 1. Update basic info
        $perjadin->update([
            'nama' => $request->nama,
            'unit_kerja' => $request->unit_kerja,
        ]);

        // 2. Update individual trip details if provided
        if ($request->has('trips')) {
            $request->validate([
                'trips.*.kota' => 'required|string',
                'trips.*.notadinas' => 'required|string',
                'trips.*.tanggal_range' => 'required|string',
            ]);

            foreach ($request->trips as $tripId => $tripData) {
                $trip = \App\Models\Perjalanan::findOrFail($tripId);
                
                $rangeString = $tripData['tanggal_range'];
                if (strpos($rangeString, ' to ') !== false) {
                    $dates = explode(' to ', $rangeString);
                } else {
                    $dates = explode(' - ', $rangeString);
                }
                
                if (count($dates) >= 1) {
                    $start = Carbon::parse(trim($dates[0]));
                    $end = isset($dates[1]) ? Carbon::parse(trim($dates[1])) : $start->copy();
                    $duration = $start->diffInDays($end) + 1;
                    
                    $trip->update([
                        'tanggal_mulai' => $start->toDateString(),
                        'tanggal_selesai' => $end->toDateString(),
                        'kota' => $tripData['kota'],
                        'notadinas' => $tripData['notadinas'],
                        'durasi' => $duration
                    ]);
                }
            }
        }

        // 3. AUTOMATIC RECONCILIATION: Recalculate everything from the official trips
        $stats = [
            'DN' => ['sppd' => 0, 'hari' => 0],
            'DK' => ['sppd' => 0, 'hari' => 0],
            'DLN' => ['sppd' => 0, 'hari' => 0],
        ];

        // Fetch all trips after updates
        $allTrips = \App\Models\Perjalanan::where('perjadin_id', $perjadin->id)->get();
        foreach ($allTrips as $t) {
            $jenis = strtoupper($t->jenis);
            if (isset($stats[$jenis])) {
                $stats[$jenis]['sppd'] += 1;
                $stats[$jenis]['hari'] += $t->durasi;
            }
        }

        // Apply to master record
        $perjadin->update([
            'sppd_dn' => $stats['DN']['sppd'],
            'hari_dn' => $stats['DN']['hari'],
            'sppd_dk' => $stats['DK']['sppd'],
            'hari_dk' => $stats['DK']['hari'],
            'sppd_dln' => $stats['DLN']['sppd'],
            'hari_dln' => $stats['DLN']['hari'],
        ]);

        return redirect('/')->with('success', 'Data berhasil diupdate dan rekapitulasi telah disinkronkan otomatis.');
    }

    public function destroy($id)
    {
        Perjadin::destroy($id);
        return redirect('/')->with('success', 'Data berhasil dihapus!');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PerjadinExport, 'rekap_perjadin.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PerjadinImport, $request->file('file'));
            return redirect('/')->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Delete a specific trip and update the parent's aggregate counters.
     */
    public function destroyPerjalanan($id)
    {
        $trip = \App\Models\Perjalanan::findOrFail($id);
        $perjadin = Perjadin::find($trip->perjadin_id);

        if ($perjadin) {
            $jenis = strtolower($trip->jenis); // dn, dk, dln
            $sppdField = "sppd_{$jenis}";
            $hariField = "hari_{$jenis}";

            // Reconcile: reduce counters in main table
            if ($perjadin->$sppdField > 0) {
                $perjadin->$sppdField -= 1;
            }
            if ($perjadin->$hariField >= $trip->durasi) {
                $perjadin->$hariField -= $trip->durasi;
            } else {
                $perjadin->$hariField = 0;
            }
            $perjadin->save();
        }

        $trip->delete();
        return back()->with('success', 'Detail perjalanan berhasil dihapus dan rekapitulasi telah diperbarui.');
    }
}
