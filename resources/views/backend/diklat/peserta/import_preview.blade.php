<form action="{{ route('backend.diklat.peserta.import.simpan', ['jadwal' => $jadwal->id]) }}" method="POST" autocomplete="off">
    @csrf
    <table class="table table-vcenter table-hover table-bordered table-striped js-table-checkable">
        <thead>
            <tr>
                <th class="text-center" style="width: 70px;">
                    <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                        <input type="checkbox" class="custom-control-input" id="check-all" name="check-all">
                        <label class="custom-control-label" for="check-all"></label>
                    </div>
                </th>
                <th class="font-w700" style="width: 60px;">Foto</th>
                <th class="font-w700" style="width: 12%;">NIP</th>
                <th class="font-w700" style="width: 20%;">Nama</th>
                <th class="font-w700">Instansi</th>
                <th class="font-w700">Satker</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $p)
            <tr>
                <td class="text-center">
                    <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                        <input type="checkbox" class="custom-control-input" id="row_{{$loop->iteration}}" name="pid[]" value="{{$p->id}}">
                        <label class="custom-control-label" for="row_{{$loop->iteration}}"></label>
                    </div>
                </td>
                <td>
                    <img src="{{ is_null($p->foto) ? asset('media/avatars/avatar8.jpg') :  asset(\Storage::url($p->foto)) }}" class="img-avatar img-avatar-thumb img-avatar-rounded" style="height: auto;">
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $p->nip }}
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $p->nama_lengkap }}
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $p->instansi }}
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $p->satker_nama }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="block-content block-content-sm block-content-full bg-body-light rounded-bottom">
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-block btn-primary"{{ count($peserta) == 0 ? 'disabled' : ''}}>
                    Import Peserta
                </button>                                        
            </div>
        </div>
    </div>
</form>

<script>jQuery(function(){ Dashmix.helpers(['table-tools-checkable']); });</script>
