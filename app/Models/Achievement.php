<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'date',
        'credential_url',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
