<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APITransaction extends Model
{
    public $guarded = ["created_at","updated_at"];
}
