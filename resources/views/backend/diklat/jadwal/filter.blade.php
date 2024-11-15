<table class="table table-bordered table-striped table-vcenter js-dataTable-full">
    <thead>
        <tr>
            <th class="text-center" style="width: 80px;">#</th>
            <th>Nama</th>
            <th>Jenis Pelatihan</th>
            <th>Tanggal Pelatihan</th>
            <th>Kelas</th>
            <th>Kuota (Peserta)</th>
            <th>Status</th>
            <th style="width: 1%;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jadwal as $j)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="font-w600">
                {{ $j->nama }}
            </td>
            <td class="font-w600">
                {{ $j->jenis }}
            </td>
            <td class="font-w600">
                {{formatTgl2($j->tgl_awal)}} - {{formatTgl2($j->tgl_akhir)}}
            </td>
            <td class="font-w600">
                {{ $j->kelas }}
            </td>
            <td class="font-w600">
                {{ $j->kuota }}
            </td>
            <td class="font-w600">
                @switch($j->status_jadwal)
                    @case(1)
                        <span class="badge badge-success">Berjalan</span>
                        @break
                    @case(2)
                        <span class="badge badge-primary">Akan Datang</span>
                        @break
                    @default
                        <span class="badge badge-danger">Selesai</span>
                @endswitch
            </td>
            <td class="text-center">
                {{-- @if (Auth::user()->can('update', $j)) --}}
                <form action="{{ route('backend.diklat.jadwal.destroy', $j->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="btn-group">
                        <a href="{{ route('backend.diklat.jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-cog"></i>
                        </a>
                        @can('isCreator', $j)
                        <a href="{{ route('backend.diklat.jadwal.edit', $j->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                        <a href="javascript:;" onclick="return showAlert($(this).closest('form'));" class="btn btn-sm btn-danger">
                            <i class="far fa-trash-alt"></i>
                        </a>
                        @endcan
                    </div>
                </form>
                {{-- @endif --}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(request()->ajax())
<script>
    jQuery(function(){
        jQuery('.js-dataTable-full').dataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            autoWidth: false,
        });
    });
</script>
@endif
