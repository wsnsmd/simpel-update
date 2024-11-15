<table class="table table-bordered table-striped table-vcenter js-dataTable-full">
    <thead>
        <tr>
            <th class="text-center" style="width: 80px;">#</th>
            <th>Nama</th>
            <th>Tanggal Pelaksanaan</th>
            <th>Jenis Diklat</th>
            <th>Kelas</th>
            <th>Kuota</th>
            <th>Status</th>
            <th style="width: 8%;">Aksi</th>
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
                {{ formatTanggal($j->tgl_awal) }} - {{ formatTanggal($j->tgl_akhir) }}
            </td>             
            <td class="font-w600">
                {{ $j->diklat_jenis_id }}
            </td>          
            <td class="font-w600">
                {{ $j->kelas }}
            </td>
            <td class="font-w600">
                {{ $j->kuota }} Orang
            </td>
            <td class="font-w600">
                {{ $j->status }}
            </td>                                                                                      
            <td class="text-center">
                <div class="btn-group">
                    <a href="{{ route('backend.diklat.peserta.show', ['id' => $j->id, 'slug' => str_slug($j->nama)]) }}" class="btn btn-sm btn-primary mr-1 mb-3">
                        <i class="fa fa-users"></i>
                        Peserta
                    </a>                               
                </div>
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