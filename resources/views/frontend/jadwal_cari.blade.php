<table class="table table-hover table-striped table-sm js-dataTable-full">
    <thead>
        <tr>
            <th class="font-w700 text-center">#</th>
            <th class="font-w700">Nama Pelatihan</th>
            <th class="font-w700 text-center">Jenis Pelatihan</th>
            <th class="font-w700 text-center">Tanggal Pelatihan</th>
            <th class="font-w700 text-center">Kelas</th>
            <th class="font-w700 text-center">Kuota</th>
            <th class="font-w700 text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jadwal as $j)
        <tr>
            <td class="font-w400 text-center">{{$loop->iteration}}</td>
            <td class="font-w600"><a href="{{ route('jadwal.detail', ['jadwal' => $j->id, 'slug' => str_slug($j->nama)]) }}">{{ $j->nama}}</td>
            <td class="font-w400 text-center">{{$j->jenis}}</td>
            <td class="font-w400 text-center">{{formatTgl2($j->tgl_awal)}} - {{formatTgl2($j->tgl_akhir)}}</td>
            <td class="font-w400 text-center">{{$j->kelas}}</td>
            <td class="font-w400 text-center">{{$j->kuota}}</td>
            <td class="text-center">
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
        </tr>                                                    
        @endforeach
    </tbody>
</table>

<script>
    jQuery(function(){
        jQuery('.js-dataTable-full').dataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            autoWidth: false,
        });
    });
</script>