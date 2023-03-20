<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    protected $with = ['mails'];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function mails()
    {
        return $this->hasMany(Mail::class);
    }
}
