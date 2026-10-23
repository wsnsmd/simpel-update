{{-- peserta/laporan_seminar/_slot.blade.php
     Variables: $fase, $label, $icon, $colorClass, $laporan, $jadwalId, $isLocked
--}}

<div>
    {{-- Label fase --}}
    <p class="font-w600 mb-3">
        <span class="badge badge-{{ $colorClass }} font-size-sm px-2 py-1">
            <i class="fa {{ $icon }} mr-1"></i>{{ $label }}
        </span>
    </p>

    {{-- Sudah ada laporan --}}
    @if ($laporan)
        <div class="rounded border border-{{ $colorClass }} p-3 mb-3" style="background:var(--{{ $colorClass }}-lighter, #fffbeb)">
            <p class="font-size-sm text-muted mb-1">
                <i class="fa fa-file-alt mr-1"></i>Judul Laporan
            </p>
            <p class="font-w600 mb-2" style="word-break:break-word">{{ $laporan->judul }}</p>

            <p class="font-size-sm text-muted mb-1">
                <i class="fa fa-link mr-1"></i>Tautan
            </p>
            <a href="{{ $laporan->url }}" target="_blank" rel="noopener noreferrer"
               class="text-{{ $colorClass }} font-size-sm d-block text-truncate mb-3"
               style="max-width:100%">
                {{ $laporan->url }}
            </a>

            <p class="font-size-xs text-muted mb-0">
                <i class="fa fa-clock mr-1"></i>
                Diperbarui: {{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y H:i') }}
            </p>
        </div>

        {{-- Tombol aksi --}}
        @if (!$isLocked)
            <div class="d-flex" style="gap:.5rem">
                <button type="button"
                    class="btn btn-sm btn-outline-{{ $colorClass }} flex-grow-1"
                    data-toggle="modal"
                    data-target="#modal-laporan-{{ $jadwalId }}-{{ $fase }}">
                    <i class="fa fa-edit mr-1"></i>Ubah
                </button>
                <form action="{{ route('peserta.laporan.destroy', [$jadwalId, $fase]) }}"
                      method="POST" class="flex-shrink-0"
                      onsubmit="return confirm('Hapus laporan {{ $label }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        @else
            <p class="text-muted font-size-sm mb-0">
                <i class="fa fa-lock mr-1"></i>Data dikunci — laporan tidak dapat diubah.
            </p>
        @endif

    {{-- Belum ada laporan --}}
    @else
        <div class="rounded border border-dashed p-3 text-center mb-3"
             style="border-color:#ccc;background:#fafafa">
            <i class="fa fa-cloud-upload-alt text-muted fa-2x mb-2"></i>
            <p class="text-muted font-size-sm mb-0">Belum ada laporan yang diunggah.</p>
        </div>

        @if (!$isLocked)
            <button type="button"
                class="btn btn-sm btn-{{ $colorClass }} btn-block"
                data-toggle="modal"
                data-target="#modal-laporan-{{ $jadwalId }}-{{ $fase }}">
                <i class="fa fa-plus mr-1"></i>Unggah Laporan {{ $label }}
            </button>
        @else
            <p class="text-muted font-size-sm mb-0 text-center">
                <i class="fa fa-lock mr-1"></i>Data dikunci — tidak dapat mengunggah laporan.
            </p>
        @endif
    @endif
</div>

{{-- ── Modal Form ──────────────────────────────────────── --}}
@if (!$isLocked)
<div class="modal fade" id="modal-laporan-{{ $jadwalId }}-{{ $fase }}"
     tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header py-3 bg-{{ $colorClass }}-lighter border-bottom">
                <h5 class="modal-title font-w700 text-{{ $colorClass }}">
                    <i class="fa {{ $icon }} mr-1"></i>
                    {{ $laporan ? 'Ubah' : 'Unggah' }} Laporan {{ $label }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('peserta.laporan.upsert', [$jadwalId, $fase]) }}"
                  method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Judul --}}
                    <div class="form-group mb-3">
                        <label class="font-w600 font-size-sm">
                            Judul Laporan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="judul"
                               class="form-control form-control-sm @error('judul') is-invalid @enderror"
                               placeholder="cth: Rancangan Aksi Perubahan — Optimalisasi Pelayanan Publik"
                               value="{{ old('judul', $laporan->judul ?? '') }}"
                               maxlength="300"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- URL --}}
                    <div class="form-group mb-0">
                        <label class="font-w600 font-size-sm">
                            Tautan Laporan <span class="text-danger">*</span>
                        </label>
                        <input type="url"
                               name="url"
                               class="form-control form-control-sm @error('url') is-invalid @enderror"
                               placeholder="https://drive.google.com/..."
                               value="{{ old('url', $laporan->url ?? '') }}"
                               maxlength="2000"
                               required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="text-muted font-size-xs mt-1 mb-0">
                            <i class="fa fa-info-circle mr-1"></i>
                            Layanan yang diterima: Google Drive, OneDrive, Dropbox, Notion, Canva.
                            Pastikan tautan sudah diatur <strong>anyone with link</strong>.
                        </p>
                    </div>

                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-sm btn-{{ $colorClass }}">
                        <i class="fa fa-save mr-1"></i>Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endif
