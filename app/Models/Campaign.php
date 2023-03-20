<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mail_template_id',
        'status'
    ];

    public function mailLists()
    {
        return $this->belongsToMany(MailList::class)->with('mails');
    }

    public function getMailsAttribute() {
        return $this->mailLists->pluck('mails')->collapse()->pluck('mail')->unique();
    }

    public function mailTemplate() {
        return $this->belongsTo(MailTemplate::class);
    }
}
