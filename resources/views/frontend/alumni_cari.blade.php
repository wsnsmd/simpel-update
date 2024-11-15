<table class="table table-hover table-bordered table-striped table-sm js-dataTable-full">
    <thead>
        <tr>
            <th class="font-w700 text-center">#</th>
            <th class="font-w700">NIP</th>
            <th class="font-w700">Nama Peserta</th>
            <th class="font-w700 text-center">Instansi</th>
            <th class="font-w700 text-center">Pelatihan</th>
            <th class="font-w700 text-center">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($alumni as $a)
        <tr>
            <td class="font-w400 text-center">{{$loop->iteration}}</td>
            <td class="font-w400 text-center">
			@php
				if(strlen($a->nip) == 18)
				{
					$count = strlen($a->nip) - 8;
					$output = substr_replace($a->nip, str_repeat('*', $count), 2, $count);
					echo $output;
				}
				else 
					echo $a->nip;
			@endphp
			</td>
            <td class="font-w400 text-center">{{$a->nama_lengkap}}</td>
            <td class="font-w400 text-center">{{$a->instansi}}</td>
            <td class="font-w400 text-center">{{$a->tipe . ' ' . $a->nama}}</td>
            <td class="font-w400 text-center">{{$a->tgl_awal . ' s/d ' . $a->tgl_akhir}}</td>
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