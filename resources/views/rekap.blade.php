@extends('layouts.dashboard')

@section('title', 'Rekapitulasi Perjalanan')
@section('header_title', 'Daftar Perjalanan Dinas//(DATA DI BAWAH INI ADALAH DATA DUMMY/ PERCOBAAN)//')
@section('header_subtitle', 'Manajemen data dan detail perjalanan pegawai')

<style>
    /* Button Micro-interactions */
    .btn-action {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        filter: brightness(1.05);
    }
    .btn-action:active {
        transform: translateY(0);
    }

    /* Table Row Interactions */
    .table-wrapper tr.main-row {
        transition: background-color 0.2s ease;
    }
    .table-wrapper tr.main-row:hover {
        background-color: #f8faff !important;
    }

    /* Action Icons */
    .icon-btn {
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem;
        border-radius: 0.5rem;
    }
    .icon-btn:hover {
        background: #f3f4f6;
        transform: scale(1.1);
    }
    .icon-btn.edit:hover { color: var(--primary) !important; background: #eef2ff; }
    .icon-btn.delete:hover { color: #ef4444 !important; background: #fef2f2; }

    /* Search Input Focus */
    .search-input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        width: 100% !important;
    }

    /* Trip Card Hover */
    .trip-compact-card {
        transition: all 0.3s ease;
    }
    .trip-compact-card:hover {
        border-color: var(--primary) !important;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>

@section('content')
<div class="section-card">
    <div class="section-title" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2>Data Rekapitulasi</h2>
        </div>
        
        <div style="display: flex; gap: 1rem; flex: 1; justify-content: flex-end; align-items: center; min-width: 300px;">
            <!-- Import Form -->
            <form action="{{ url('/import') }}" method="POST" enctype="multipart/form-data" id="importForm" style="display: none;">
                @csrf
                <input type="file" name="file" id="importFile" onchange="document.getElementById('importForm').submit()">
            </form>

            <!-- Search Form -->
            <form action="{{ url('/') }}" method="GET" style="position: relative; flex: 1; max-width: 400px;">
                <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 16px; color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama pegawai..." list="search_list" class="search-input"
                    style="width: 100%; padding: 0.6rem 1rem 0.6rem 2.8rem; border-radius: 0.5rem; border: 1px solid var(--border); font-size: 0.875rem; outline: none; transition: all 0.3s ease;">
                <datalist id="search_list">
                    @foreach($pegawaiList as $nama)
                        <option value="{{ $nama }}">
                    @endforeach
                </datalist>
                @if($search)
                    <a href="{{ url('/') }}" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); text-decoration: none;" class="icon-btn">
                        <i data-lucide="x" style="width: 14px;"></i>
                    </a>
                @endif
            </form>

            <div style="display: flex; gap: 0.75rem;">
                <button onclick="document.getElementById('importFile').click()" class="btn-action" style="background: #f3f4f6; color: var(--text-main); border: 1px solid var(--border); padding: 0.6rem 1.2rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <i data-lucide="upload" style="width: 18px;"></i>
                    Import
                </button>
                <a href="{{ url('/export') }}" class="btn-action" style="background: var(--secondary); color: white; padding: 0.6rem 1.2rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <i data-lucide="download" style="width: 18px;"></i>
                    Export
                </a>
                <a href="{{ url('/tambah') }}" class="btn-action" style="background: var(--primary); color: white; padding: 0.6rem 1.2rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                    <i data-lucide="plus" style="width: 18px;"></i>
                    Tambah
                </a>
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Unit Kerja</th>
                    <th>SPPD (DN)</th>
                    <th>SPPD (DK)</th>
                    <th>SPPD (DLN)</th>
                    <th>Hari (DN)</th>
                    <th>Hari (DK)</th>
                    <th>Hari (DLN)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr class="main-row">
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight: 600; color: var(--primary);">{{ $item->nama }}</td>
                    <td>{{ $item->unit_kerja }}</td>
                    <td>{{ $item->sppd_dn }}</td>
                    <td>{{ $item->sppd_dk }}</td>
                    <td>{{ $item->sppd_dln }}</td>
                    <td>{{ $item->hari_dn }} hari</td>
                    <td>{{ $item->hari_dk }} hari</td>
                    <td>{{ $item->hari_dln }} hari</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <button onclick="toggleHistory('history-{{ $item->id }}')" class="btn-action" style="background: #eef2ff; color: var(--primary); border: none; padding: 0.4rem 0.8rem; border-radius: 0.4rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                <i data-lucide="eye" style="width: 14px;"></i>
                                Detail
                            </button>
                            <a href="{{ url('/edit/' . $item->id) }}" class="icon-btn edit" style="color: var(--text-muted);" title="Edit"><i data-lucide="pencil" style="width: 16px;"></i></a>
                            <a href="{{ url('/hapus/' . $item->id) }}" onclick="return confirm('Hapus data ini?')" class="icon-btn delete" style="color: var(--text-muted);" title="Hapus"><i data-lucide="trash-2" style="width: 16px;"></i></a>
                        </div>
                    </td>
                </tr>
                <tr id="history-{{ $item->id }}" class="history-row" style="display: none;">
                    <td colspan="10" style="padding: 0;">
                        <div style="background: #f9fafb; padding: 1.5rem; border-bottom: 1px solid var(--border);">
                            <h4 style="font-size: 0.8125rem; font-weight: 700; color: var(--text-main); margin-bottom: 1rem; text-transform: uppercase; display: flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="history" style="width: 16px;"></i>
                                Riwayat Perjalanan ({{ $item->perjalanan->count() }})
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                                @foreach($item->perjalanan as $trip)
                                <div class="trip-compact-card" style="background: white; border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.8125rem; font-weight: 700; color: var(--primary);">{{ $trip->kota }}</span>
                                        <span style="font-size: 0.75rem; background: #eef2ff; color: var(--primary); padding: 0.125rem 0.5rem; border-radius: 100px;">{{ $trip->durasi }} Hari</span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem;">
                                        <i data-lucide="calendar" style="width: 12px;"></i>
                                        {{ \Carbon\Carbon::parse($trip->tanggal_mulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($trip->tanggal_selesai)->translatedFormat('d M Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; font-family: monospace; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                                        Nota: {{ $trip->notadinas }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 3rem; color: var(--text-muted);">Belum ada data tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($search)
    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.8125rem; color: var(--text-muted);">
        Ditemukan <strong>{{ $data->count() }}</strong> hasil untuk "<strong>{{ $search }}</strong>"
    </div>
    @endif
</div>
@endsection

<script>
    function toggleHistory(id) {
        const row = document.getElementById(id);
        const allHistory = document.querySelectorAll('.history-row');
        
        // Optionally close other rows
        // allHistory.forEach(r => { if(r.id !== id) r.style.display = 'none'; });

        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>
