<table class="table table-sm table-bordered table-striped table-vcenter">
    <thead>
        <tr>
            <th class="font-w700 text-center" style="width: 30px;">#</th>
            <th class="font-w700 text-center" style="">NIP</th>
            <th class="font-w700 text-center" style="">Nama</th>
            <th class="font-w700 text-center" style="">Pangkat</th>
            <th class="font-w700 text-center" style="">Jabatan</th>
            <th class="font-w700 text-center" style="width: 5%;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pegawai as $p)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="font-w600">
                {{ $p->nip }}
            </td>
            <td class="font-w600">
                {{ $p->nama }}
            </td>
            <td class="font-w600">
                {{ $p->pangkat }}
            </td>
            <td class="font-w600">
                {{ $p->jabatan }}
            </td>
            <td class="font-w600 text-center">
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-sm btn-danger" onclick="showHapus({{$p->id}})" title="Hapus">
                        <i class="far fa-trash-alt"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
