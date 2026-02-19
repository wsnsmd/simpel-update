<table class="table table-sm table-bordered table-striped table-vcenter">
    <thead>
        <tr>
            <th class="font-w700 text-center" style="width: 50px;">#</th>
            <th class="font-w700 text-center" style="width: 30%;">Nama Survei</th>
            <th class="font-w700 text-center" style="width: 18%;">Kode Survei</th>
            <th class="font-w700 text-center" style="width: 27%;">Params (JSON)</th>
            <th class="font-w700 text-center" style="width: 10%;">Wajib</th>
            <th class="font-w700 text-center" style="width: 5%;">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($surveys as $s)
            @php
                $rawParams = (string) ($s->params ?? '');
                $rawParamsTrim = trim($rawParams);

                $prettyParams = $rawParamsTrim;
                if ($rawParamsTrim !== '') {
                    $decoded = json_decode($rawParamsTrim, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $prettyParams = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                }

                // tampilan ringkas di tabel
                $shortParams = $rawParamsTrim === '' ? '-' : \Illuminate\Support\Str::limit($rawParamsTrim, 60);
            @endphp

            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>

                <td class="font-w600">
                    {{ $s->survey_name }}
                </td>

                <td class="font-w600">
                    <span class="badge badge-secondary">{{ $s->survey_code }}</span>
                </td>

                <td class="font-w600">
                    @if ($rawParamsTrim === '')
                        <span class="text-muted">-</span>
                    @else
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-monospace" title="{{ $prettyParams }}"
                                style="max-width: 260px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $shortParams }}
                            </span>

                            <button type="button" class="btn btn-sm btn-light ml-2" title="Copy Params"
                                onclick="navigator.clipboard.writeText(@json($rawParamsTrim))">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block">JSON, contoh: {"nama_widyaiswara":"Bahrun"}</small>
                    @endif
                </td>

                <td class="font-w600 text-center">
                    @if ((int) $s->is_mandatory === 1)
                        <span class="badge badge-success"><i class="fa fa-check"></i> Ya</span>
                    @else
                        <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Tidak</span>
                    @endif
                </td>

                <td class="font-w600 text-center">
                    <div class="btn-group">
                        <a href="{{ \App\Services\SurveiLinkService::generateSecureDownloadUrl($s->jadwal_id, $s->survey_code) }}"
                            class="btn btn-sm btn-success" title="Download Hasil Survei" target="_blank">
                            <i class="fa fa-download"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-primary" onclick="showEdit({{ $s->id }})" title="Edit">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-sm btn-danger" onclick="showHapus({{ $s->id }})"
                            title="Hapus">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    Belum ada data survei.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>