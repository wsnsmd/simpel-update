<?php

namespace App\Imports;

use App\Checklist;
use Maatwebsite\Excel\Concerns\ToModel;

class ChecklistImport implements ToModel
{
    public $jadwal_id;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Checklist([
            'diklat_jadwal_id' => $this->jadwal_id,
            'dokumen' => $row[1],
            'keterangan' => $row[2]
        ]);
    }
}
