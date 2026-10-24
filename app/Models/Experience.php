<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Experience extends Model
{
    use HasFactory;
    protected $table = 'experiences';
    public $fillable = [
        'companyName',
        'section_id',
        'seed_key',
        'image',
        'brand',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

}
