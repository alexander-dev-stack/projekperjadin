@extends('layouts.dashboard')

@section('title', 'Edit Data')
@section('header_title', 'Edit Data Perjalanan')
@section('header_subtitle', 'Modifikasi informasi pegawai dan riwayat perjalanan')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .edit-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    .edit-card:hover {
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
    }
    .form-section { margin-bottom: 2rem; }
    .section-label { 
        font-size: 0.9375rem; 
        font-weight: 700; 
        color: var(--primary); 
        margin-bottom: 1.25rem; 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        text-transform: uppercase; 
        letter-spacing: 0.025em; 
        transition: all 0.3s ease;
    }
    .edit-card:hover .section-label { transform: translateX(5px); }

    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; }
    .trip-grid { display: grid; grid-template-columns: 100px 1fr 1.5fr 1.2fr; gap: 1rem; align-items: stretch; }
    .form-group { margin-bottom: 0; display: flex; flex-direction: column; justify-content: flex-end; }
    .form-group.main { margin-bottom: 1.25rem; }
    label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem; transition: color 0.2s ease; }
    
    input[type="text"], input[type="number"] {
        width: 100%;
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 0.625rem;
        padding: 0.6rem 0.875rem;
        font-size: 0.875rem;
        color: var(--text-main);
        outline: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    input:focus { 
        border-color: var(--primary); 
        background: white; 
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); 
        transform: translateY(-1px);
    }
    input:focus + label { color: var(--primary); }

    .trip-item {
        background: white;
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }
    .trip-item:hover { 
        border-color: var(--primary); 
        transform: scale(1.005);
        background: #fafbff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .btn-save {
        background: var(--primary);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.625rem;
        border: none;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-save:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px -5px rgba(79, 70, 229, 0.4); 
        filter: brightness(1.1);
    }
    .btn-save:active { transform: translateY(0); }

    .btn-back {
        transition: all 0.3s ease;
    }
    .btn-back:hover {
        color: var(--primary) !important;
        transform: translateX(-5px);
    }

    .trash-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .trash-btn:hover {
        transform: rotate(10deg) scale(1.1);
        background: #fee2e2 !important;
        border-color: #ef4444 !important;
    }
</style>

<form action="{{ url('/update/' . $perjadin->id) }}" method="POST">
    @csrf
    
    <div class="edit-card">
        <!-- Pegawai Info -->
        <div class="form-section">
            <h3 class="section-label"><i data-lucide="user"></i> Profil Pegawai</h3>
            <div class="form-grid">
                <div class="form-group main">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $perjadin->nama }}" required>
                </div>
                <div class="form-group main">
                    <label>Unit Kerja</label>
                    <input type="text" name="unit_kerja" value="{{ $perjadin->unit_kerja }}" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-label"><i data-lucide="bar-chart-2"></i> Rekapitulasi Akumulasi</h3>
            <div class="form-grid">
                <div class="form-group main"><label>SPPD (DN)</label><input type="number" id="sum_sppd_dn" name="sppd_dn" value="{{ $perjadin->sppd_dn }}" readonly></div>
                <div class="form-group main"><label>SPPD (DK)</label><input type="number" id="sum_sppd_dk" name="sppd_dk" value="{{ $perjadin->sppd_dk }}" readonly></div>
                <div class="form-group main"><label>SPPD (DLN)</label><input type="number" id="sum_sppd_dln" name="sppd_dln" value="{{ $perjadin->sppd_dln }}" readonly></div>
                <div class="form-group main"><label>Hari (DN)</label><input type="number" id="sum_hari_dn" name="hari_dn" value="{{ $perjadin->hari_dn }}" readonly></div>
                <div class="form-group main"><label>Hari (DK)</label><input type="number" id="sum_hari_dk" name="hari_dk" value="{{ $perjadin->hari_dk }}" readonly></div>
                <div class="form-group main"><label>Hari (DLN)</label><input type="number" id="sum_hari_dln" name="hari_dln" value="{{ $perjadin->hari_dln }}" readonly></div>
            </div>
        </div>

        @if($perjadin->perjalanan && $perjadin->perjalanan->count() > 0)
        <div class="form-section">
            <h3 class="section-label"><i data-lucide="history"></i> Detail Tiap Perjalanan</h3>
            @foreach($perjadin->perjalanan as $trip)
            <div class="trip-item">
                <div style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div class="trip-grid" style="flex: 1;">
                        <input type="hidden" class="trip-jenis-hidden" value="{{ $trip->jenis }}">
                        
                        <div class="form-group">
                            <label>Jenis & Durasi</label>
                            <div style="display: flex; gap: 0.4rem; height: 38px; align-items: center;">
                                <span style="font-size: 0.7rem; font-weight: 800; color: var(--primary); background: #f0f2ff; padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid rgba(79, 70, 229, 0.1);">{{ $trip->jenis }}</span>
                                <span class="trip-duration-badge" style="font-size: 0.7rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid rgba(5, 150, 105, 0.1);">{{ $trip->durasi }} Hari</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Rentang Tanggal</label>
                            <input type="text" class="datepicker trip-date-input" 
                                   data-jenis="{{ $trip->jenis }}"
                                   name="trips[{{ $trip->id }}][tanggal_range]" 
                                   value="{{ $trip->tanggal_mulai ? $trip->tanggal_mulai->format('Y-m-d') : '' }} - {{ $trip->tanggal_selesai ? $trip->tanggal_selesai->format('Y-m-d') : '' }}" required>
                        </div>

                        <div class="form-group">
                            <label>Tujuan / Kota</label>
                            <input type="text" name="trips[{{ $trip->id }}][kota]" value="{{ $trip->kota }}" required>
                        </div>

                        <div class="form-group">
                            <label>Nota Dinas</label>
                            <input type="text" name="trips[{{ $trip->id }}][notadinas]" value="{{ $trip->notadinas }}" required>
                        </div>
                    </div>
                    
                    <a href="{{ url('/hapus-perjalanan/' . $trip->id) }}" 
                       onclick="return confirm('Hapus detail perjalanan ini?')"
                       class="trash-btn"
                       style="background: #fff5f5; color: #ef4444; width: 34px; height: 34px; border-radius: 6px; border: 1px solid #fee2e2; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; margin-bottom: 2px;" title="Hapus">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn-save shadow-sm">
                <i data-lucide="save"></i> Simpan Perubahan
            </button>
            <a href="{{ url('/') }}" class="btn-back" style="padding: 1rem 2rem; color: var(--text-muted); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Batal
            </a>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
    function calculateTotals() {
        const stats = {
            DN: { sppd: 0, hari: 0 },
            DK: { sppd: 0, hari: 0 },
            DLN: { sppd: 0, hari: 0 }
        };

        document.querySelectorAll('.trip-item').forEach(card => {
            const dateInput = card.querySelector('.trip-date-input');
            const durationBadge = card.querySelector('.trip-duration-badge');
            const jenis = dateInput.dataset.jenis;
            const range = dateInput.value;
            
            let tripDays = 0;

            if (range && range.includes(' - ')) {
                const dates = range.split(' - ');
                const start = new Date(dates[0]);
                const end = new Date(dates[1]);
                const diffTime = Math.abs(end - start);
                tripDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            } else if (range && range.length >= 10) {
                // Single day
                tripDays = 1;
            }

            if (tripDays > 0) {
                stats[jenis].sppd += 1;
                stats[jenis].hari += tripDays;
                if (durationBadge) durationBadge.textContent = tripDays + ' Hari';
            }
        });

        // Update main summary inputs
        document.getElementById('sum_sppd_dn').value = stats.DN.sppd;
        document.getElementById('sum_hari_dn').value = stats.DN.hari;
        document.getElementById('sum_sppd_dk').value = stats.DK.sppd;
        document.getElementById('sum_hari_dk').value = stats.DK.hari;
        document.getElementById('sum_sppd_dln').value = stats.DLN.sppd;
        document.getElementById('sum_hari_dln').value = stats.DLN.hari;
    }

    flatpickr(".datepicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        locale: "id",
        conjunction: " - ", // Explicit separator for display
        onChange: function() {
            calculateTotals();
        }
    });

    // Run once on load to ensure sync
    window.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endsection
