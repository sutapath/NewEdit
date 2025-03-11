<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class MemberImport implements ToModel
{
    public function model(array $row)
    {
        Member::create([
            'citizen_id'  => $row['id_card'],
     ]);
    }
}
