<?php
use Carbon\Carbon;

/**
* change plain number to formatted currency
*
* @param $number
* @param $currency
*/
function formatNumber($number, $currency = 'IDR')
{
   if($currency == 'USD') {
        return number_format($number, 2, '.', ',');
   }
   return number_format($number, 0, '.', '.');
}

function formatTanggal($date)
{
    $bulan = array (
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $tanggal = new Carbon($date);

    $value = sprintf('%02d', $tanggal->day) . ' ' . $bulan[$tanggal->month] . ' ' . $tanggal->year;

    return $value;
}

function formatTgl($date)
{
    $tanggal = new Carbon($date);

    $value = sprintf('%02d', $tanggal->day) . '-' . sprintf('%02d', $tanggal->month) . '-' . $tanggal->year;

    return $value;
}

function formatTgl2($date)
{
    $bulan = array (
        1 => 'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    );

    $tanggal = new Carbon($date);

    $value = sprintf('%02d', $tanggal->day) . ' ' . $bulan[$tanggal->month];

    return $value;
}

function formatJam($time)
{
    $jam = date('H:i', strtotime($time));

    return $jam;
}

function formatNIP($nip)
{
    $format = substr($nip, 0, 8) . ' ' . substr($nip, 8, 6) . ' ' . substr($nip, 14, 1) . ' ' . substr($nip, 15, 3);

    return $format;
}

function formatMKG($mkg)
{
    $tahun = (int) ($mkg / 12);
    $bulan = $mkg % 12;
    $format = $tahun . ' tahun ' . $bulan . ' bulan';

    return $format;
}

function getBulan($kode)
{
    $bulan = array (
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    return $bulan[(int)$kode];
}

function getGedung()
{
    $gedung = DB::table('gedung')->get();

    return $gedung;
}

function getHari($date, $pendek = false)
{
    $hari = Date('w', strtotime($date));

    $nama_hari = array (
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );

    return $nama_hari[$hari];
}

function getJenisPembelajaran($id)
{
    switch($id)
    {
        case 1:
            return "Klasikal Penuh";
        case 2:
            return "Blended Learning";
        case 3:
            return "E-Learning";

        default:
            return "Klasikal Penuh";
    }
}

function getLamaHari($date_from, $date_to)
{
    $start = new DateTime($date_from);
    $to = new DateTime($date_to);
    $interval = $start->diff($to);
    $hari = $interval->days + 1;
    return $hari;
}

function getKeperluan()
{
    $keperluan = DB::table('keperluan')->orderBy('keperluan', 'asc')->get();

    return $keperluan;
}

function getPangkat($pangkat)
{
    $gol = array("(I/a)", "(I/b)", "(I/c)", "(I/d)",
                "(II/a)", "(II/b)", "(II/c)", "(II/d)",
                "(III/a)", "(III/b)", "(III/c)", "(III/d)",
                "(IV/a)", "(IV/b)", "(IV/c)", "(IV/d)", "(IV/e)"
            );

    $jabatan = str_ireplace($gol, "", $pangkat);

    return $jabatan;
}

function getBayarLabel($status)
{
    if($status)
    {
        return '<span class="label label-success">Sudah</span>';
    }

    return '<span class="label label-danger">Belum</span>';
}

function getVerifikasiLabel($status)
{
    if($status)
    {
        return '<span class="label label-success">Sudah</span>';
    }

    return '<span class="label label-danger">Belum</span>';
}

function getMarital($status)
{
    $data = '';

    switch($status)
    {
        case 0: $data = 'Belum Kawin';
                break;
        case 1: $data = 'Kawin';
                break;
        case 2: $data = 'Janda';
                break;
        case 3: $data = 'Duda';
                break;
    }

    return $data;
}

function getNomorSPWI($tahun)
{
    $result = DB::table('mapel_spwi')->where('tahun', $tahun)->max('nomor');
    $nomor = $result + 1;

    return $nomor;
}

function getSex($status)
{
    return ($status == 'L' ? 'Laki-laki' : 'Perempuan');
}

function imageToBase64($relative_path)
{
    $path = $relative_path;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = \File::get($path);

	$base64 = "";

    if ($type == "svg") {
		$base64 = "data:image/svg+xml;base64,".base64_encode($data);
	} else {
		$base64 = "data:image/". $type .";base64,".base64_encode($data);
    }

	return $base64;
}

function set_active($uri, $output = 'current')
{
    if( is_array($uri) )
    {
        foreach ($uri as $u)
        {
            if (Route::is($u))
            {
                return $output;
            }
        }
    }
    else
    {
        if (strcasecmp($uri, url()->current()) == 0 )
        {
            return $output;
        }
        else
        {
            if (Route::is($uri))
            {
                return $output;
            }
        }
    }
}

function url_storage($uri)
{
    return env('APP_URL').'/public'.$uri;
}

function tt_storage($path)
{
    return realpath(storage_path('app/' . $path));
}

function simpegJK($data)
{
    switch($data)
    {
        case 1: return 'L';
        case 2: return 'P';
        default: return 'L';
    }
}

function simpegMarital($data)
{
    switch($data)
    {
        case 1: return 1;
        case 2: return 0;
        case 3: return 2;
        case 4: return 3;
        default: return 0;
    }
}

function simpegAgama($data)
{
    switch($data)
    {
        case 1: return 1;
        case 2: return 3;
        case 3: return 2;
        case 4: return 5;
        case 5: return 4;
        default: return 1;
    }
}

?>
