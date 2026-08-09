@extends('layouts.backend')
@section('sidebar') @include('layouts.sidebar_jadwal') @endsection

@section('css_before')
    <style>
        .input-tbl {
            font-size: .78rem;
            min-width: 100%
        }

        .input-tbl thead th {
            position: sticky;
            top: 0;
            background: #f8faff;
            z-index: 3;
            padding: .45rem .55rem;
            border: 1px solid #a0a0a0;
            white-space: nowrap;
            vertical-align: bottom;
            text-align: center
        }

        .input-tbl thead th:nth-child(1),
        .input-tbl thead th:nth-child(2) {
            position: sticky;
            z-index: 4;
            background: #f8faff
        }

        .input-tbl tbody td {
            padding: .35rem .45rem;
            border: 1px solid #a0a0a0;
            vertical-align: middle
        }

        .input-tbl tbody td:nth-child(1) {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1;
            text-align: center;
            color: #94a3b8
        }

        .input-tbl tbody td:nth-child(2) {
            position: sticky;
            left: 35px;
            background: #fff;
            z-index: 1;
            border: 1px solid #a0a0a0
        }

        .nilai-input {
            width: 72px;
            text-align: center;
            border: 1px solid #e4e9f0;
            border-radius: 5px;
            padding: .2rem .3rem;
            font-size: .78rem;
            transition: border-color .15s
        }

        .nilai-input:focus {
            border-color: #1d4ed8;
            outline: none
        }

        .nilai-input.saving {
            border-color: #f59e0b;
            background: #fffbeb
        }

        .nilai-input.saved {
            border-color: #22c55e;
            background: #f0fdf4
        }

        .nilai-input.error {
            border-color: #ef4444;
            background: #fef2f2
        }

        .aspek-header {
            background: #eff6ff !important;
            text-align: center;
            font-size: .68rem;
            font-weight: 700;
            color: #1d4ed8;
        }

        .badge-penguji-info {
            background: #fef3c7;
            color: #92400e;
            font-size: .6rem
        }

        .badge-operator-info {
            background: #dbeafe;
            color: #1e40af;
            font-size: .6rem
        }

        .wrap {
            overflow-x: auto;
            border: 1px solid #a0a0a0;
            border-radius: 0px
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        var saveUrl = "{{ route('backend.diklat.nilai.save', $jadwal->id) }}";
        var csrfToken = "{{ csrf_token() }}";
        var saveTimer = {};

        function onNilaiChange(input) {
            var id = input.dataset.id;
            clearTimeout(saveTimer[id]);
            input.classList.remove('saved', 'error');
            input.classList.add('saving');

            saveTimer[id] = setTimeout(function () {
                saveNilai(input);
            }, 700);
        }

        function saveNilai(input) {
            var pesertaId = input.dataset.peserta;
            var komponenId = input.dataset.komponen;
            var nilai = input.value;

            if (nilai === '' || isNaN(nilai)) {
                input.classList.remove('saving');
                return;
            }

            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    peserta_id: pesertaId,
                    komponen_id: komponenId,
                    nilai: nilai,
                })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        input.classList.remove('saving');
                        input.classList.add('saved');
                        setTimeout(function () { input.classList.remove('saved'); }, 2000);

                        // Update nilai aspek di header kolom jika tersedia
                        if (data.nilai_aspek !== undefined && data.nilai_aspek !== null) {
                            var aspekEl = document.getElementById('aspek-val-' + data.aspek_id + '-' + pesertaId);
                            if (aspekEl) aspekEl.textContent = parseFloat(data.nilai_aspek).toFixed(2);
                        }
                    } else {
                        input.classList.remove('saving');
                        input.classList.add('error');
                    }
                })
                .catch(function () {
                    input.classList.remove('saving');
                    input.classList.add('error');
                });
        }
    </script>
@endsection

@section('content')
    <div class="content">
        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap" style="gap:.5rem">
            <div>
                <h2 class="h3 my-2">Input Nilai</h2>
                <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem">
                <a href="{{ route('backend.diklat.nilai.rekap', $jadwal->id) }}" class="btn btn-info btn-sm">
                    <i class="fa fa-table mr-1"></i> Rekap
                </a>
                <a href="{{ route('backend.diklat.nilai.setup', $jadwal->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Setup
                </a>
            </div>
        </div>

        <div class="alert alert-info font-size-sm">
            <i class="fa fa-info-circle mr-1"></i>
            Operator dapat menginput dan mengoreksi <strong>semua komponen</strong> — termasuk komponen yang biasanya
            diinput oleh penguji.
            Nilai tersimpan otomatis saat Anda berhenti mengetik.
            <span class="badge badge-penguji-info ml-2">penguji</span> = komponen seminar (dapat dikoreksi operator)
            <span class="badge badge-operator-info ml-1">operator</span> = komponen non-seminar
        </div>

        @if ($pesertaList->isEmpty())
            <div class="alert alert-warning">Belum ada peserta terverifikasi.</div>
        @elseif ($aspekList->isEmpty())
            <div class="alert alert-warning">Komponen penilaian belum dikonfigurasi.</div>
        @else

            <div class="wrap">
                <table class="input-tbl table table-sm mb-0">
                    <thead>
                        {{-- Baris 1: Aspek induk (colspan sub-komponen) --}}
                        <tr>
                            <th rowspan="2" style="left: 0;min-width: 35px; z-index: 9; border: 1px solid #a0a0a0;">#</th>
                            <th rowspan="2"
                                style="text-align: left; left: 35px; min-width: 250px;border: 1px solid #a0a0a0; z-index: 9;">
                                Peserta
                            </th>
                            @foreach ($aspekList as $aspek)
                                @php $colspan = $aspek->sub->count() ?: 1; @endphp
                                <th colspan="{{ $colspan }}" class="aspek-header" style="border: 1px solid #a0a0a0;">
                                    {{ $aspek->nama }}
                                    ({{ round($aspek->bobot * 100) }}%)
                                    <span
                                        class="badge {{ $aspek->penilai === 'penguji' ? 'badge-penguji-info' : 'badge-operator-info' }} ml-1">
                                        {{ $aspek->penilai }}
                                    </span>
                                    @if ($aspek->fase)
                                        <span class="badge badge-secondary" style="font-size:.55rem">{{ $aspek->fase }}</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                        {{-- Baris 2: Sub-komponen --}}
                        <tr>
                            @foreach ($aspekList as $aspek)
                                @if ($aspek->sub->count() > 0)
                                    @foreach ($aspek->sub as $sub)
                                        <th style="font-size:.65rem;font-weight:600;border: 1px solid #a0a0a0;">
                                            {{ \Illuminate\Support\Str::limit($sub->nama, 22) }}<br>
                                            <span style="font-weight:400;color:#94a3b8">({{ round($sub->bobot * 100, 1) }}%)</span>
                                        </th>
                                    @endforeach
                                @else
                                    <th style="font-size:.65rem">&nbsp;</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesertaList as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-w600">{{ $p->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size:.68rem">{{ $p->nip ?: '-' }}</div>
                                </td>
                                @foreach ($aspekList as $aspek)
                                    @if ($aspek->sub->count() > 0)
                                        @foreach ($aspek->sub as $sub)
                                            @php
                                                $val = isset($nilaiMap[$p->id][$sub->id]) ? $nilaiMap[$p->id][$sub->id] : '';
                                                $inputBy = isset($inputByMap[$p->id][$sub->id]) ? $inputByMap[$p->id][$sub->id] : '';
                                                $byPenguji = str_contains($inputBy, 'penguji:');
                                            @endphp
                                            <td class="text-center">
                                                <input type="number" class="nilai-input" data-peserta="{{ $p->id }}"
                                                    data-komponen="{{ $sub->id }}" data-id="{{ $p->id }}_{{ $sub->id }}"
                                                    data-aspek="{{ $aspek->id }}" value="{{ $val }}" min="0" max="100" step="0.01"
                                                    placeholder="—" {{ $setup->is_locked ? 'disabled' : '' }} onchange="onNilaiChange(this)"
                                                    oninput="onNilaiChange(this)">
                                                @if ($byPenguji)
                                                    <div style="font-size:.55rem;color:#d97706;margin-top:1px" title="{{ $inputBy }}">
                                                        <i class="fa fa-user-check"></i> penguji
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    @else
                                        @php
                                            $val = isset($nilaiMap[$p->id][$aspek->id]) ? $nilaiMap[$p->id][$aspek->id] : '';
                                            $inputBy = isset($inputByMap[$p->id][$aspek->id]) ? $inputByMap[$p->id][$aspek->id] : '';
                                        @endphp
                                        <td>
                                            <input type="number" class="nilai-input" data-peserta="{{ $p->id }}"
                                                data-komponen="{{ $aspek->id }}" data-id="{{ $p->id }}_{{ $aspek->id }}"
                                                data-aspek="{{ $aspek->id }}" value="{{ $val }}" min="0" max="100" step="0.01"
                                                placeholder="—" {{ $setup->is_locked ? 'disabled' : '' }} onchange="onNilaiChange(this)"
                                                oninput="onNilaiChange(this)">
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($setup->is_locked)
                <div class="alert alert-warning mt-3 font-size-sm">
                    <i class="fa fa-lock mr-1"></i> Data nilai sudah dikunci dan tidak dapat diubah.
                </div>
            @endif

        @endif
    </div>
@endsection