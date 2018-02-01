<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];
    //一对多关系
    public function user()
    {
        return $this->belongsto(User::class);
    }

}
