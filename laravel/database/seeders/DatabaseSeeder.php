<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Lga;
use App\Models\State;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $edo = State::firstOrCreate(['name' => 'Edo State']);

        $lgas = [
            'Akoko-Edo','Egor','Esan Central','Esan North-East','Esan South-East','Esan West',
            'Etsako Central','Etsako East','Etsako West','Igueben','Ikpoba-Okha','Oredo',
            'Orhionmwon','Ovia North-East','Ovia South-West','Owan East','Owan West','Uhunmwode',
        ];

        foreach ($lgas as $name) {
            Lga::firstOrCreate(['state_id' => $edo->id, 'name' => $name]);
        }

        $owanEast = Lga::where('state_id', $edo->id)->where('name', 'Owan East')->firstOrFail();
        $wards = [
            ['name' => 'EMAI 1', 'code' => '01'],
            ['name' => 'EMAI II', 'code' => '02'],
            ['name' => 'IHIEVEBE I', 'code' => '03'],
            ['name' => 'IHIEVBE II', 'code' => '04'],
            ['name' => 'UOKHA/AKE', 'code' => '05'],
            ['name' => 'IGUE/IKAO', 'code' => '06'],
            ['name' => 'IVBIANION', 'code' => '07'],
            ['name' => 'OTUO I', 'code' => '08'],
            ['name' => 'OTUO II', 'code' => '09'],
            ['name' => 'IVBIADAOBI', 'code' => '10'],
            ['name' => 'WARRAKE', 'code' => '11'],
        ];

        foreach ($wards as $ward) {
            Ward::firstOrCreate(['lga_id' => $owanEast->id, 'name' => $ward['name']], ['code' => $ward['code']]);
        }

        Admin::firstOrCreate(
            ['email' => env('IZOBO_ADMIN_EMAIL', 'admin@example.com')],
            ['name' => 'IZOBO Administrator', 'password' => env('IZOBO_ADMIN_PASSWORD', 'ChangeThisPassword!')]
        );
    }
}
