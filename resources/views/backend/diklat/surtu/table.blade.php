<table class="table table-sm table-bordered table-striped table-vcenter">
    <thead>
        <tr>
            <th class="font-w700 text-center" style="width: 30px;">#</th>
            <th class="font-w700 text-center" style="">Keterangan</th>
            <th class="font-w700 text-center" style="width: 10%;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($surtu as $s)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="font-w600">
                {{ $s->keterangan }}
            </td>
            <td class="font-w600 text-center">
                <div class="btn-group">
                    <a href="{{ route('backend.diklat.surtu.detail', ['jadwal' => $s->jid, 'slug' => str_slug($jadwal->nama), 'id' => $s->id]) }}" class="btn btn-sm btn-success" title="Detail">
                        <i class="fa fa-cog"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
