<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Education;

class Section extends Model
{
    use HasFactory;
    protected $table = 'sections';
    public $fillable = [
        'title',
        'details',
        'country',
        'city',
        'startDate',
        'isActive',
        'endDate',
        'isShown',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }
    public function education()
    {
        return $this->hasMany(Education::class);
    }

}
