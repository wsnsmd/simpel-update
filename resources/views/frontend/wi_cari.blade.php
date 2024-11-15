<table class="table table-hover table-bordered table-striped table-sm js-dataTable-full">
    <thead>
        <tr>
            <th class="font-w700 text-center">#</th>
            <th class="font-w700">Kegiatan</th>
            <th class="font-w700">Mata Pelatihan</th>
            <th class="font-w700 text-center">Tanggal</th>
            <th class="font-w700 text-center">Jam Mulai</th>
            <th class="font-w700 text-center">Jam Akhir</th>
            <th class="font-w700 text-center">JP</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jadwal as $j)
        <tr>
            <td class="font-w400 text-center">{{$loop->iteration}}</td>
            <td class="font-w400">{{$j->jid_nama}}</td>
            <td class="font-w400">{{$j->nama_mapel}}</td>
            <td class="font-w400 text-center"><span class="badge badge-primary">{{$j->tanggal}}</span></td>
            <td class="font-w400 text-center"><span class="badge badge-success">{{$j->jam_mulai}}</span></td>
            <td class="font-w400 text-center"><span class="badge badge-danger">{{$j->jam_akhir}}</span></td>
            <td class="font-w400 text-center">{{$j->jp}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    jQuery('.js-dataTable-full').dataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        autoWidth: false,
    });
</script>
