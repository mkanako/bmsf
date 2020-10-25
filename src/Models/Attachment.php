<?php

namespace Cc\Bmsf\Models;

use Cc\Bmsf\Facades\Attacent;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = BMSF_ENTRY . '_attachments';
    protected $visible = [
        'id',
        'path',
        'filename',
        'url',
    ];
    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Attacent::url($this->path);
    }
}
