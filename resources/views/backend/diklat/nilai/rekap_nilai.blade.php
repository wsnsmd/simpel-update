@extends('layouts.backend')
@section('sidebar') @include('layouts.sidebar_jadwal') @endsection

@section('css_before')
    <style>
        .rekap-wrap {
            overflow-x: auto;
            border: 1px solid #e4e9f0;
            border-radius: 8px
        }

        .tbl-rekap {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
            font-size: .78rem
        }

        .tbl-rekap thead th {
            position: sticky;
            top: 0;
            background: #f8faff;
            z-index: 3;
            border-bottom: 2px solid #e4e9f0;
            padding: .5rem .65rem;
            white-space: nowrap;
            vertical-align: bottom
        }

        .tbl-rekap tbody td {
            padding: .4rem .65rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle
        }

        .tbl-rekap tbody tr:hover td {
            background: #f8faff
        }

        .kual-sm {
            background: #d1fae5;
            color: #065f46
        }

        .kual-m {
            background: #dbeafe;
            color: #1e40af
        }

        .kual-b {
            background: #f0fdf4;
            color: #166534
        }

        .kual-kb {
            background: #fef3c7;
            color: #92400e
        }

        .kual-tm {
            background: #fee2e2;
            color: #991b1b
        }

        .status-lulus {
            background: #d1fae5;
            color: #065f46;
            font-size: .68rem;
            font-weight: 700;
            padding: .2em .6em;
            border-radius: 12px
        }

        .status-ditunda {
            background: #fef3c7;
            color: #92400e;
            font-size: .68rem;
            font-weight: 700;
            padding: .2em .6em;
            border-radius: 12px
        }

        .status-tl {
            background: #fee2e2;
            color: #991b1b;
            font-size: .68rem;
            font-weight: 700;
            padding: .2em .6em;
            border-radius: 12px
        }

        .status-belum {
            background: #f1f5f9;
            color: #64748b;
            font-size: .68rem;
            font-weight: 700;
            padding: .2em .6em;
            border-radius: 12px
        }

        .th-sort {
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .th-sort:hover {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
        }
        .th-sort.active {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
        }
    </style>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
    @if(session('notifikasi')) $.notify({ icon: 'fa fa-check mr-1', message: "{{ session('notifikasi') }}" }, { allow_dismiss: false, type: 'success', placement: { from: 'top', align: 'center' } }); @endif

    var sortAsc = true;

    function sortByRanking() {
        var tbody  = document.getElementById('tbody-rekap');
        var rows   = Array.prototype.slice.call(tbody.querySelectorAll('tr[data-ranking]'));
        var icon   = document.getElementById('icon-ranking');
        var th     = document.getElementById('th-ranking');

        rows.sort(function(a, b) {
            var ra = parseInt(a.getAttribute('data-ranking')) || 9999;
            var rb = parseInt(b.getAttribute('data-ranking')) || 9999;
            return sortAsc ? ra - rb : rb - ra;
        });

        // Update icon
        icon.className = sortAsc
            ? 'fa fa-sort-up ml-1'
            : 'fa fa-sort-down ml-1';
        th.classList.add('active');

        // Re-append baris & update nomor urut
        rows.forEach(function(row, idx) {
            var noTd = row.querySelector('td.col-no');
            if (noTd) noTd.textContent = idx + 1;
            tbody.appendChild(row);
        });

        sortAsc = !sortAsc;
    }
    </script>
@endsection

@section('content')
    <div class="content">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap" style="gap:.5rem">
            <div>
                <h2 class="h3 my-2">Rekap Nilai</h2>
                <p class="text-muted mb-0 font-size-sm">{{ $jadwal->nama }}</p>
            </div>
            <div class="d-flex flex-wrap" style="gap:.5rem">
                <a href="{{ route('backend.diklat.nilai.export', $jadwal->id) }}" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('backend.diklat.nilai.input', $jadwal->id) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit mr-1"></i> Input Nilai
                </a>
                <a href="{{ route('backend.diklat.nilai.setup', $jadwal->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-cog mr-1"></i> Setup
                </a>
            </div>
        </div>

        @if (!$setup)
            <div class="alert alert-warning">Setup penilaian belum dikonfigurasi.</div>
        @elseif ($aspekList->isEmpty())
            <div class="alert alert-warning">Komponen penilaian belum didefinisikan.</div>
        @else

            {{-- Keterangan kualifikasi --}}
            <div class="alert alert-info font-size-sm mb-3">
                <strong>Kualifikasi per Aspek (Passing Grade: &gt; {{ $setup->passing_grade_aspek }}):</strong>
                <span class="badge kual-sm ml-1">Sangat Memuaskan</span> &gt;90 &nbsp;
                <span class="badge kual-m ml-1">Memuaskan</span> &gt;80 &nbsp;
                <span class="badge kual-b ml-1">Baik</span> &gt;70 &nbsp;
                <span class="badge kual-kb ml-1">Kurang Baik</span> &gt;60 &nbsp;
                <span class="badge kual-tm ml-1">Tidak Memenuhi</span> ≤60
                <br class="d-none d-md-block">
                <strong class="mt-1 d-block">Status Kelulusan:</strong>
                Lulus = semua aspek &gt;{{ $setup->passing_grade_aspek }} &nbsp;|&nbsp;
                Ditunda = ada 1 aspek Kurang Baik (dapat remedial, max nilai 85.00) &nbsp;|&nbsp;
                Tidak Lulus = ada aspek Tidak Memenuhi Kualifikasi
            </div>

            <div class="rekap-wrap">
                <table class="tbl-rekap">
                    <thead>
                        <tr>
                            <th style="width:35px;position:sticky;left:0;z-index:4;background:#f8faff">#</th>
                            <th
                                style="min-width:180px;position:sticky;left:35px;z-index:4;background:#f8faff;border-right:2px solid #e4e9f0">
                                Peserta
                            </th>
                            @foreach ($aspekList as $a)
                                <th class="text-center" style="min-width:100px">
                                    <div>{{ \Illuminate\Support\Str::limit($a->nama, 20) }}</div>
                                    <div style="font-size:.65rem;font-weight:400;color:#64748b">
                                        {{ round($a->bobot * 100) }}% · {{ $a->penilai }}
                                        @if ($a->fase) <span class="badge badge-secondary"
                                        style="font-size:.55rem">{{ $a->fase }}</span> @endif
                                    </div>
                                </th>
                            @endforeach
                            <th class="text-center" style="min-width:80px">Nilai Akhir</th>
                            <th class="text-center" style="min-width:80px">Status</th>
                            <th id="th-ranking"
                                class="text-center th-sort"
                                style="min-width:60px"
                                onclick="sortByRanking()"
                                title="Klik untuk urutkan berdasarkan Ranking">
                                Ranking
                                <i id="icon-ranking" class="fa fa-sort ml-1" style="font-size:.65rem;color:#94a3b8"></i>
                            </th>
                            <th class="text-center" style="min-width:80px">Remedial</th>
                            <th class="text-center" style="min-width:80px">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-rekap">
                        @forelse ($pesertaList as $i => $p)
                            @php $rk = isset($rekapAkhirMap[$p->id]) ? $rekapAkhirMap[$p->id] : null; @endphp
                            @php $rkVal = $rk && $rk->ranking ? $rk->ranking : 9999; @endphp
                            <tr data-ranking="{{ $rkVal }}">
                                <td style="position:sticky;left:0;background:#fff;z-index:1" class="text-muted text-center col-no">
                                    {{ $i + 1 }}</td>
                                <td style="position:sticky;left:35px;background:#fff;z-index:1;border-right:2px solid #f0f0f0">
                                    <div class="font-w600" style="font-size:.8rem">{{ $p->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size:.68rem">{{ $p->nip ?: '-' }}</div>
                                </td>
                                @foreach ($aspekList as $a)
                                    @php
                                        $ra = isset($rekapAspekMap[$p->id][$a->id]) ? $rekapAspekMap[$p->id][$a->id] : null;
                                        $kualClass = '-';
                                        if ($ra) {
                                            $k = $ra->kualifikasi;
                                            $kualClass = str_contains($k, 'Sangat') ? 'kual-sm' : (str_contains($k, 'Memuas') ? 'kual-m' : (str_contains($k, 'Baik') ? 'kual-b' : (str_contains($k, 'Kurang') ? 'kual-kb' : 'kual-tm')));
                                        }
                                    @endphp
                                    <td class="text-center">
                                        @if ($ra)
                                            <span class="badge {{ $kualClass }}" style="font-size:.72rem">
                                                {{ round($ra->nilai_mentah, 2) }}
                                            </span>
                                            <div style="font-size:.6rem;color:#94a3b8">
                                                {{ \Illuminate\Support\Str::limit($ra->kualifikasi, 12) }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center font-w700">
                                    {{ $rk ? round($rk->nilai_akhir, 2) : '—' }}
                                </td>
                                <td class="text-center">
                                    @if ($rk)
                                        @php
                                            $statusMap = ['lulus' => 'status-lulus', 'ditunda' => 'status-ditunda', 'tidak_lulus' => 'status-tl', 'belum' => 'status-belum'];
                                            $statusLabel = ['lulus' => 'Lulus', 'ditunda' => 'Ditunda', 'tidak_lulus' => 'Tidak Lulus', 'belum' => 'Belum'];
                                        @endphp
                                        <span class="{{ $statusMap[$rk->status_kelulusan] ?? 'status-belum' }}">
                                            {{ $statusLabel[$rk->status_kelulusan] ?? '-' }}
                                        </span>
                                    @else
                                        <span class="status-belum">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center font-w600">{{ $rk ? $rk->ranking : '—' }}</td>
                                <td class="text-center">
                                    @if ($rk && $rk->status_kelulusan === 'ditunda' && !$rk->is_remedial && $setup->is_locked)
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#modalRemedial{{ $p->id }}">
                                            Input
                                        </button>
                                    @elseif ($rk && $rk->is_remedial)
                                        <span style="font-size:.72rem">
                                            {{ $rk->nilai_remedial }}
                                            <span class="{{ $rk->status_remedial === 'lulus' ? 'text-success' : 'text-danger' }}">
                                                ({{ $rk->status_remedial === 'lulus' ? 'Lulus' : 'TL' }})
                                            </span>
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                {{-- Catatan Penguji --}}
                                <td>
                                    @if ($rk && ($rk->catatan_rancangan || $rk->catatan_akhir))
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal"
                                            data-target="#modalCatatan{{ $p->id }}" title="Lihat catatan penguji">
                                            <i class="fa fa-comment-dots"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $aspekList->count() + 6 }}" class="text-center text-muted py-4">Belum ada peserta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Remedial per peserta --}}
            @foreach ($pesertaList as $p)
                @php $rk = isset($rekapAkhirMap[$p->id]) ? $rekapAkhirMap[$p->id] : null; @endphp
                @if ($rk && $rk->status_kelulusan === 'ditunda' && !$rk->is_remedial && $setup->is_locked)
                    <div class="modal fade" id="modalRemedial{{ $p->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-w700">Input Nilai Remedial</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <form action="{{ route('backend.diklat.nilai.remedial', [$jadwal->id, $p->id]) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p class="font-size-sm">
                                            <strong>{{ $p->nama_lengkap }}</strong><br>
                                            Nilai Akhir: <strong>{{ round($rk->nilai_akhir, 2) }}</strong><br>
                                            Status: <span class="status-ditunda">Ditunda Kelulusan</span>
                                        </p>
                                        <div class="alert alert-info font-size-sm">
                                            <i class="fa fa-info-circle mr-1"></i>
                                            Sesuai peraturan, nilai remedial <strong>maksimal 85.00</strong>.
                                            Peserta dinyatakan lulus jika nilai remedial &gt; {{ $setup->passing_grade_aspek }}.
                                        </div>
                                        <div class="form-group">
                                            <label class="font-w600">Nilai Remedial <span class="text-danger">*</span></label>
                                            <input type="number" name="nilai_remedial" class="form-control" min="0" max="85" step="0.01"
                                                required placeholder="0 – 85.00">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-warning">Simpan Nilai Remedial</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Modal Catatan Penguji --}}
            @foreach ($pesertaList as $p)
                @php $rk = isset($rekapAkhirMap[$p->id]) ? $rekapAkhirMap[$p->id] : null; @endphp
                @if ($rk && (!empty($rk->catatan_rancangan) || !empty($rk->catatan_akhir)))
                    <div class="modal fade" id="modalCatatan{{ $p->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-w700">
                                        <i class="fa fa-comment-dots text-primary mr-1"></i>
                                        Catatan Penguji — {{ $p->nama_lengkap }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    @if (!empty($rk->catatan_rancangan))
                                        <div class="mb-3">
                                            <p class="font-w600 font-size-sm mb-1" style="color:#92400e">
                                                <i class="fa fa-file-alt mr-1"></i> Catatan Seminar Rancangan
                                            </p>
                                            <div class="p-2 rounded"
                                                style="background:#fffbeb;border:1px solid #fde68a;font-size:.85rem;">
                                                {{ $rk->catatan_rancangan }}</div>
                                        </div>
                                    @endif
                                    @if (!empty($rk->catatan_akhir))
                                        <div>
                                            <p class="font-w600 font-size-sm mb-1 text-primary">
                                                <i class="fa fa-check-circle mr-1"></i> Catatan Seminar Akhir
                                            </p>
                                            <div class="p-2 rounded"
                                                style="background:#eff6ff;border:1px solid #bfdbfe;font-size:.85rem;">
                                                {{ $rk->catatan_akhir }}</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        @endif
    </div>
@endsection
