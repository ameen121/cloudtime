<?php

use Illuminate\Database\Seeder;
use App\ClockTimes;
class ClockTimeSeeder extends Seeder
{

    public function run()
    {
        $start = "00:00"; //you can write here 00:00:00 but not need to it
        $end = "23:30";

        $tStart = strtotime($start);
        $tEnd = strtotime($end);
        $tNow = $tStart;
        ClockTimes::query()->truncate();
        while($tNow <= $tEnd){
            ClockTimes::create([
                'c_times'=>date("H:i",$tNow),
            ]);
            $tNow = strtotime('+30 minutes',$tNow);
        }
    }
}
