<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Rota extends Model
{
    protected $guarded=['id'];
    protected $table='rotas';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
    
  


}
