<table class="table table-sm table-bordered table-striped table-vcenter">
    <thead>
        <tr>
            <th class="font-w700 text-center" style="width: 50px;">#</th>
            <th class="font-w700 text-center" style="width: 25%">Titel</th>
            <th class="font-w700 text-center" style="">URL</th>
            <th class="font-w700 text-center" style="width: 5%">Aktif</th>
            <th class="font-w700 text-center" style="width: 5%;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tautan as $t)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="font-w600">
                {{ $t->title }}
            </td>
            <td class="font-w600">
                {{ $t->url }}
            </td>
            <td class="font-w600 text-center">
                @if ($t->is_active)
                <span class="badge badge-success"><i class="fa fa-check"></i> Ya</span>
                @else
                <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Tidak</span>
                @endif
            </td>
            <td class="font-w600 text-center">
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-sm btn-primary" onclick="showEdit({{$t->id}})" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-sm btn-danger" onclick="showHapus({{$t->id}})" title="Hapus">
                        <i class="far fa-trash-alt"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
