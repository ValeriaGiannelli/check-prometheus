<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogIgnore extends Model
{
    use HasFactory;

    protected $table = 'log_ignore';

    protected $fillable = ['client_id', 'description'];
}