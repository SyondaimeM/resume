<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $table = 'skills';
    public $fillable = [
        'name',
        'description',
        'isShown',
        'user_id',
        'seed_key',
        'group_name',
        'group_description',
        'group_order',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
