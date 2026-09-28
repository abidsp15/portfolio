<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'education_id',
        'image_path',
    ];

    public function education()
    {
        return $this->belongsTo(Education::class);
    }
}
