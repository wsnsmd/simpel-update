
Hai {{ $name }},

Status verifikasi peserta
{{ $jadwal->tipe . ' ' . $jadwal->nama }}
pada tanggal {{ $jadwal->tgl_awal}} s/d {{ $jadwal->tgl_akhir}}
anda sedang direview oleh BPSDM Prov. Kaltim

Untuk melihat status peserta pelatihan anda bisa buka tautan berikut

{{ route('jadwal.detail', ['jadwal' => $jadwal->id, 'slug' => str_slug($jadwal->nama)]) }}