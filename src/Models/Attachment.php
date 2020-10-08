<?php

namespace Cc\Labems\Models;

use Cc\Labems\Facades\Attacent;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = LABEMS_ENTRY . '_attachments';
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
