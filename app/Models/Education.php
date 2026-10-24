<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    protected $table = 'education';
    public $fillable = [
        'collageName',
        'degree',
        'department',
        'gpa',
        'section_id',
        'seed_key',
        'courses',
        'achievements',
        'image',
    ];
    protected $casts = ['courses' => 'array', 'achievements' => 'array'];

    public function section()
    {
        return $this->belongsTo('App\Models\Section', 'section_id');
    }

}
