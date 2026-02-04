@extends('layouts.dashboard')

@section('title', 'Tambah Data')
@section('header_title', 'Tambah Perjalanan Dinas')
@section('header_subtitle', 'Masukkan detail perjalanan baru untuk pegawai')

@section('content')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .form-card {
        max-width: 800px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .form-card:hover {
        transform: scale(1.01);
        box-shadow: 0 20px 40px -20px rgba(0,0,0,0.1);
    }

    .form-group { margin-bottom: 1.5rem; position: relative; }
    label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; transition: color 0.3s; }
    
    input[type="text"], input[type="number"], select {
        width: 100%;
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        color: var(--text-main);
        outline: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    input:focus { 
        border-color: var(--primary); 
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); 
        transform: translateY(-2px);
    }
    .form-group:focus-within label { color: var(--primary); }

    .radio-container {
        display: flex; 
        gap: 1.5rem; 
        margin-top: 0.5rem; 
        background: #f9fafb; 
        padding: 1rem; 
        border-radius: 0.75rem; 
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }
    .radio-container:hover {
        border-color: var(--primary);
        background: #f5f7ff;
    }
    
    .radio-label {
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        margin-bottom: 0; 
        cursor: pointer; 
        color: var(--text-main); 
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    .radio-label:hover {
        background: white;
        color: var(--primary);
        transform: scale(1.05);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, #a855f7 100%);
        color: white;
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        width: 100%;
        margin-top: 1rem;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }
    .btn-submit:hover { 
        transform: translateY(-4px) scale(1.02); 
        box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.4); 
        filter: brightness(1.1);
    }
    .btn-submit:active { transform: translateY(0) scale(1); }
</style>

<div class="form-card">
    <form action="{{ url('/simpan') }}" method="POST" autocomplete="off">
        @csrf
        
        <div class="form-group">
            <label for="nama_pegawai">Nama Pegawai</label>
            <input type="text" id="nama_pegawai" name="nama_pegawai" list="pegawai_list" placeholder="-- Pilih atau ketik nama pegawai --" required>
            <datalist id="pegawai_list">
                @foreach($pegawaiList as $nama)
                    <option value="{{ $nama }}">
                @endforeach
            </datalist>
        </div>

        <div class="form-group">
            <label>Jenis Perjalanan</label>
            <div class="radio-container">
                <label class="radio-label">
                    <input type="radio" name="jenis_perjalanan" value="DN" checked style="width: 1.125rem; height: 1.125rem; accent-color: var(--primary);"> Dinas Negeri (DN)
                </label>
                <label class="radio-label">
                    <input type="radio" name="jenis_perjalanan" value="DK" style="width: 1.125rem; height: 1.125rem; accent-color: var(--primary);"> Dalam Kota (DK)
                </label>
                <label class="radio-label">
                    <input type="radio" name="jenis_perjalanan" value="DLN" style="width: 1.125rem; height: 1.125rem; accent-color: var(--primary);"> Luar Negeri (DLN)
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="tujuan">Tujuan (Balai / Kota)</label>
            <input type="text" id="tujuan" name="tujuan" list="balai_list" placeholder="-- Pilih atau ketik tujuan perjalanan --" required>
            <datalist id="balai_list">
                @foreach($balaiList as $balai)
                    <option value="{{ $balai }}">
                @endforeach
            </datalist>
        </div>

        <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="notadinas">Nomor Nota Dinas</label>
                <input type="text" id="notadinas" name="notadinas" placeholder="e.g. ND-001/BSKJI/2026" required>
            </div>
            <div class="form-group">
                <label for="tanggal_range">Rentang Tanggal</label>
                <input type="text" id="tanggal_range" name="tanggal_range" placeholder="Pilih tanggal..." required>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            Simpan Perjalanan Dinas
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
    // Flatpickr Initialize
    flatpickr("#tanggal_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        locale: "id",
    });

    // Simple Autocomplete Simulation (Data from controller can be injected here)
    const pegawaiNames = @json($pegawaiList ?? []);
    const balaiList = @json($balaiList ?? []);

    function setupAuto(inputId, data) {
        const input = document.getElementById(inputId);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const val = input.value;
                if (!val) return;
                const match = data.find(i => i.toLowerCase().startsWith(val.toLowerCase()));
                if (match && match.toLowerCase() !== val.toLowerCase()) {
                    e.preventDefault();
                    input.value = match;
                }
            }
        });
    }

    setupAuto('nama_pegawai', pegawaiNames);
    setupAuto('tujuan', balaiList);
</script>
@endsection
