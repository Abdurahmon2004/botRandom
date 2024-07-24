<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function codes(){
        return $this->hasMany(Code::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
