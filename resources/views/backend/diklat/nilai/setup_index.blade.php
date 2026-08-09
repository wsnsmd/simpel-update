@extends('layouts.backend')

@section('sidebar')
    @include('layouts.sidebar_jadwal')
@endsection

@section('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .aspek-card {
            border-left: 4px solid #1d4ed8;
            border-radius: 8px;
            border: 1px solid #e4e9f0;
            margin-bottom: .75rem
        }

        .aspek-head {
            padding: .6rem .875rem;
            background: #f8faff;
            border-bottom: 1px solid #e4e9f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0
        }

        .sub-row {
            padding: .4rem .875rem .4rem 2rem;
            border-bottom: 1px solid #f8faff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .8rem
        }

        .sub-row:last-child {
            border-bottom: none
        }

        .bdg-operator {
            background: #dbeafe;
            color: #1e40af;
            font-size: .65rem
        }

        .bdg-penguji {
            background: #fef3c7;
            color: #92400e;
            font-size: .65rem
        }

        .bdg-fase {
            background: #d1fae5;
            color: #065f46;
            font-size: .65rem
        }

        .token-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: .6rem .875rem
        }

        .token-url {
            font-family: monospace;
            font-size: .7rem;
            background: #e2e8f0;
            padding: .2rem .4rem;
            border-radius: 4px;
            word-break: break-all;
            display: block;
            margin: .3rem 0
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        @if (session('notifikasi'))
            $.notify({ icon: 'fa fa-check mr-1', message: "{{ session('notifikasi') }}" }, {
                allow_dismiss: false, type: 'success', placement: { from: 'top', align: 'center' }
            });
        @endif

            function confirmLock() {
                Swal.fire({
                    title: 'Kunci Data Nilai?',
                    text: 'Setelah dikunci nilai tidak bisa diubah dan sertifikat dapat diterbitkan.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kunci',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                }).then(function (r) { if (r.value) document.getElementById('form-lock').submit(); });
            }

        function copyLink(url) {
            navigator.clipboard.writeText(url);
            Swal.fire({ title: 'Link disalin!', type: 'success', timer: 1000, showConfirmButton: false });
        }
    </script>
@endsection

@section('content')
    <div class="content">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap:.5rem">
            <div>
                <h2 class="h3 my-2">Penilaian</h2>
                <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem">
                @if ($setup && !$setup->is_locked)
                    <a href="{{ route('backend.diklat.nilai.input', $jadwal->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit mr-1"></i> Input Nilai
                    </a>
                    <a href="{{ route('backend.diklat.nilai.rekap', $jadwal->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-table mr-1"></i> Rekap
                    </a>
                    <form id="form-lock" action="{{ route('backend.diklat.nilai.lock', $jadwal->id) }}" method="POST"
                        class="d-inline">
                        @csrf
                        <button type="button" onclick="confirmLock()" class="btn btn-danger btn-sm">
                            <i class="fa fa-lock mr-1"></i> Kunci Data
                        </button>
                    </form>
                @elseif ($setup && $setup->is_locked)
                    <span class="badge badge-danger px-3 py-2" style="font-size:.8rem">
                        <i class="fa fa-lock mr-1"></i>
                        Dikunci — {{ \Carbon\Carbon::parse($setup->locked_at)->format('d M Y H:i') }}
                    </span>
                    <a href="{{ route('backend.diklat.nilai.rekap', $jadwal->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-table mr-1"></i> Rekap
                    </a>
                    <a href="{{ route('backend.diklat.nilai.export', $jadwal->id) }}" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel mr-1"></i> Export
                    </a>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">

            {{-- Kolom kiri: Setup template --}}
            <div class="col-lg-5">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-cog text-primary mr-1"></i> Setup Template Penilaian
                        </h3>
                    </div>
                    <div class="block-content">

                        @if ($setup && $setup->is_locked)
                            <div class="alert alert-warning font-size-sm mb-3">
                                <i class="fa fa-lock mr-1"></i>
                                Data dikunci — template tidak bisa diubah.
                            </div>
                        @else
                            <form action="{{ route('backend.diklat.nilai.setup.store', $jadwal->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="font-w600 font-size-sm">
                                        Template Komponen Nilai <span class="text-danger">*</span>
                                    </label>
                                    <select name="template_id" class="form-control form-control-sm" required>
                                        <option value="">-- Pilih Template --</option>
                                        @foreach ($templates as $t)
                                            <option value="{{ $t->id }}" {{ ($setup && $setup->template_id == $t->id) ? 'selected' : '' }}>
                                                {{ $t->nama }}
                                                @if ($t->tahun)({{ $t->tahun }})@endif
                                                — PG Aspek: {{ $t->passing_grade_aspek }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        Belum ada template?
                                        <a href="{{ route('backend.diklat.nilai.template') }}" target="_blank">
                                            Buat / kelola template
                                        </a>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label class="font-w600 font-size-sm">Passing Grade per Aspek (%)</label>
                                    <input type="number" name="passing_grade_aspek" class="form-control form-control-sm"
                                        step="0.01" min="0" max="100"
                                        value="{{ $setup ? $setup->passing_grade_aspek : '70.01' }}">
                                    <small class="text-muted">
                                        Nilai minimum per aspek untuk lulus (peraturan LAN: &gt; 70.00)
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-save mr-1"></i> Simpan Setup
                                </button>
                            </form>
                        @endif

                        {{-- Preview komponen template --}}
                        @if ($setup && $aspekList->count() > 0)
                            <hr class="my-3">
                            <p class="font-w600 font-size-sm mb-2">Komponen Penilaian Aktif:</p>
                            @php $totalBobot = 0; @endphp
                            @foreach ($aspekList as $aspek)
                                @php $totalBobot += $aspek->bobot; @endphp
                                <div class="aspek-card">
                                    <div class="aspek-head">
                                        <span class="font-w600 font-size-sm">{{ $aspek->nama }}</span>
                                        <div class="d-flex align-items-center" style="gap:.3rem">
                                            <span class="badge bdg-{{ $aspek->penilai === 'penguji' ? 'penguji' : 'operator' }}">
                                                {{ $aspek->penilai }}
                                            </span>
                                            @if ($aspek->fase)
                                                <span class="badge bdg-fase">{{ $aspek->fase }}</span>
                                            @endif
                                            <span class="badge badge-primary">{{ round($aspek->bobot * 100) }}%</span>
                                        </div>
                                    </div>
                                    @foreach ($aspek->sub as $s)
                                        <div class="sub-row">
                                            <span class="text-muted">{{ $s->nama }}</span>
                                            <span class="badge badge-secondary">{{ round($s->bobot * 100, 1) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-between font-w700 font-size-sm py-2 border-top">
                                <span>Total Bobot</span>
                                <span class="{{ abs($totalBobot - 1) < 0.001 ? 'text-success' : 'text-danger' }}">
                                    {{ round($totalBobot * 100, 2) }}%
                                    @if (abs($totalBobot - 1) >= 0.001)
                                        <i class="fa fa-exclamation-triangle ml-1" title="Total harus 100%"></i>
                                    @endif
                                </span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- Kolom kanan: Token penguji --}}
            <div class="col-lg-7">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-key text-warning mr-1"></i> Token Akses Penguji
                        </h3>
                    </div>
                    <div class="block-content">

                        @if (!$setup)
                            <p class="text-muted font-size-sm">Setup template terlebih dahulu.</p>

                        @elseif ($seminarList->isEmpty())
                                            <p class="text-muted font-size-sm">
                                                Belum ada data kelompok seminar.
                                                <a href="{{ route('backend.diklat.jadwal.detail', [
                                'jadwal' => $jadwal->id,
                                'slug' => str_slug($jadwal->nama),
                                'page' => 'seminar'
                            ]) }}">
                                                    Tambah kelompok seminar →
                                                </a>
                                            </p>

                        @else
                            <p class="font-size-sm text-muted mb-3">
                                Token digenerate per kelompok. Penguji membuka link dan langsung input nilai
                                tanpa perlu login ke sistem.
                                Token berlaku sampai
                                <strong>{{ \Carbon\Carbon::parse($jadwal->tgl_akhir)->format('d M Y') }}</strong>.
                            </p>

                            @foreach ($seminarList as $s)
                                @php $tok = isset($tokenMap[$s->id]) ? $tokenMap[$s->id] : null; @endphp
                                <div class="border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:.5rem">
                                        <div>
                                            <div class="font-w600 font-size-sm">{{ $s->kelompok }}</div>
                                            <div class="text-muted" style="font-size:.72rem">
                                                <i class="fa fa-user-tie mr-1"></i>
                                                Penguji: <strong>{{ $s->penguji }}</strong>
                                            </div>
                                            <div class="text-muted" style="font-size:.72rem">
                                                <i class="fa fa-chalkboard-teacher mr-1"></i>
                                                Coach: {{ $s->coach }}
                                            </div>
                                        </div>

                                        @if (!$tok)
                                            <form action="{{ route('backend.diklat.nilai.token.generate', $jadwal->id) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="seminar_id" value="{{ $s->id }}">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-key mr-1"></i> Generate Token
                                                </button>
                                            </form>
                                        @else
                                            <div class="d-flex align-items-center" style="gap:.4rem">
                                                <span class="badge {{ $tok->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $tok->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                                @if ($tok->last_login_at)
                                                    <small class="text-muted">
                                                        Login: {{ \Carbon\Carbon::parse($tok->last_login_at)->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    @if ($tok)
                                        <div class="token-box mt-2">
                                            <span class="token-url">{{ url('/nilai/penguji/' . $tok->token) }}</span>
                                            <div class="d-flex flex-wrap" style="gap:.3rem;margin-top:.4rem">
                                                <button type="button" onclick="copyLink('{{ url('/nilai/penguji/' . $tok->token) }}')"
                                                    class="btn btn-xs btn-outline-primary">
                                                    <i class="fa fa-copy mr-1"></i> Salin Link
                                                </button>
                                                <a href="{{ url('/nilai/penguji/' . $tok->token) }}" target="_blank"
                                                    class="btn btn-xs btn-outline-secondary">
                                                    <i class="fa fa-external-link-alt mr-1"></i> Buka
                                                </a>
                                                @if ($tok->is_active)
                                                    <form action="{{ route('backend.diklat.nilai.token.revoke', $tok->id) }}" method="POST"
                                                        class="d-inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                                            <i class="fa fa-ban mr-1"></i> Nonaktifkan
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('backend.diklat.nilai.token.regenerate', $tok->id) }}"
                                                        method="POST" class="d-inline-flex"
                                                        onsubmit="return confirm('Token lama akan hangus. Penguji harus pakai link baru. Lanjutkan?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-xs btn-outline-warning">
                                                            <i class="fa fa-sync-alt mr-1"></i> Regenerate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.diklat.nilai.token.activate', $tok->id) }}"
                                                        method="POST" class="d-inline-flex">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-xs btn-outline-success">
                                                            <i class="fa fa-check-circle mr-1"></i> Aktifkan
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('backend.diklat.nilai.token.regenerate', $tok->id) }}"
                                                        method="POST" class="d-inline-flex"
                                                        onsubmit="return confirm('Token lama akan hangus. Penguji harus pakai link baru. Lanjutkan?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-xs btn-outline-warning">
                                                            <i class="fa fa-sync-alt mr-1"></i> Regenerate
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size:.65rem">
                                                Expired: {{ \Carbon\Carbon::parse($tok->token_expired_at)->format('d M Y') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection