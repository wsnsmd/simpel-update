<table class="table table-sm table-bordered table-striped table-vcenter">
    <thead>
        <tr>
            <th class="font-w700 text-center" style="width: 30px;">#</th>
            <th class="font-w700 text-center" style="">Dokumen</th>
            <th class="font-w700 text-center" style="">Keterangan</th>
            <th class="font-w700 text-center" style="">Status</th>
            <th class="font-w700 text-center" style="width: 10%;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($checklist as $c)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="font-w600">
                {{ $c->dokumen }}
            </td>
            <td class="font-w600">
                {{ $c->keterangan }}
            </td>
            <td class="font-w600 text-center">
                @if (is_null($c->path))
                <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Belum</span>
                @else
                <span class="badge badge-success"><i class="fa fa-check"></i> Sudah</span>
                @endif
            </td>            
            <td class="font-w600 text-center">
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-sm btn-warning" title="Upload" onclick="showUpload({{$c->id}})">
                        <i class="fa fa-upload"></i> 
                    </a>
                    @if (!is_null($c->path))
                    <a href="{{ asset(\Storage::url($c->path)) }}" class="btn btn-sm btn-success" title="Download" target="_blank">
                        <i class="fa fa-download"></i> 
                    </a>
                    @endif
                </div>
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-sm btn-primary" onclick="showEdit({{$c->id}})" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-sm btn-danger" onclick="showHapus({{$c->id}})" title="Hapus">
                        <i class="far fa-trash-alt"></i>
                    </a>                   
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
