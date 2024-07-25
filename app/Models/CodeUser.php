<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user(){
        return $this->belongsTo(TgUser::class,'user_id');
    }
    public function code(){
        return $this->belongsTo(Code::class,'code_id');
    }
}
