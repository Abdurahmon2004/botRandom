<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WinnerUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function group(){
        return $this->belongsTo(WinnerGroup::class,'winner_group_id');
    }
    public function person(){
        return $this->belongsTo(TgUser::class,'user_id');
    }
    public function code(){
        return $this->belongsTo(Code::class,'code_id');
    }
}
