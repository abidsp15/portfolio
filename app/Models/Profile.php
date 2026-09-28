<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'headline',
        'photo_path',
        'phone',
        'email',
        'linkedin_url',
        'instagram_url',
        'cv_path',
        'about_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
