<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClockTimes extends Model
{
    protected $guarded=['id'];
    protected $table='clock_times';
}
